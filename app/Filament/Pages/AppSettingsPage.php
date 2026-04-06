<?php

namespace App\Filament\Pages;

use App\Models\AppSettings;
use App\Services\AppSettingsService;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class AppSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Uygulama Ayarları';
    protected static ?string $navigationGroup = 'Ayarlar';
    protected static ?int    $navigationSort  = 10;
    protected static string  $view            = 'filament.pages.app-settings-page';

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill(AppSettings::get()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Ayarlar')
                    ->tabs([
                        Tabs\Tab::make('Bakım Modu')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Toggle::make('maintenance_mode')
                                            ->label('Bakım Modunu Aktif Et')
                                            ->helperText('Aktif olduğunda /app-status hariç tüm API istekleri 503 döner. Kullanıcılar uygulamaya erişemez.'),
                                        Textarea::make('maintenance_message')
                                            ->label('Bakım Mesajı')
                                            ->rows(3)
                                            ->placeholder('Uygulama şu anda bakımda, kısa süre içinde geri döneceğiz.')
                                            ->helperText('Boş bırakılırsa varsayılan mesaj gösterilir.'),
                                    ]),
                            ]),

                        Tabs\Tab::make('Güncelleme')
                            ->icon('heroicon-o-arrow-up-circle')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Toggle::make('force_update_required')
                                            ->label('Zorunlu Güncelleme Aktif')
                                            ->helperText('Aktifken minimum versiyonun altındaki kullanıcılar güncelleme yapmadan devam edemez.'),
                                        TextInput::make('minimum_version')
                                            ->label('Minimum Versiyon')
                                            ->placeholder('1.0.0')
                                            ->helperText('Bu versiyonun altındakilere zorunlu güncelleme ekranı gösterilir.'),
                                        TextInput::make('latest_version')
                                            ->label('En Son Versiyon')
                                            ->placeholder('1.0.0')
                                            ->helperText('Bu versiyonun altındakilere isteğe bağlı güncelleme önerilir.'),
                                        Textarea::make('update_message')
                                            ->label('Güncelleme Mesajı')
                                            ->rows(2)
                                            ->placeholder('Yeni özellikler ve iyileştirmeler için lütfen güncelleyin.'),
                                        TextInput::make('update_ios_url')
                                            ->label('App Store Linki (iOS)')
                                            ->url()
                                            ->placeholder('https://apps.apple.com/...'),
                                        TextInput::make('update_android_url')
                                            ->label('Play Store Linki (Android)')
                                            ->url()
                                            ->placeholder('https://play.google.com/...'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('Duyuru')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        Toggle::make('show_announcement')
                                            ->label('Duyuruyu Göster')
                                            ->helperText('Aktifken uygulama açılışında kullanıcılara gösterilir.')
                                            ->columnSpanFull(),
                                        TextInput::make('announcement_title')
                                            ->label('Başlık')
                                            ->maxLength(255)
                                            ->placeholder('Duyuru başlığı'),
                                        Select::make('announcement_type')
                                            ->label('Tip')
                                            ->options([
                                                'info'    => 'Bilgilendirme',
                                                'warning' => 'Uyarı',
                                                'success' => 'Başarı',
                                            ])
                                            ->default('info'),
                                        Textarea::make('announcement_message')
                                            ->label('Duyuru Mesajı')
                                            ->rows(4)
                                            ->columnSpanFull()
                                            ->placeholder('Duyuru içeriğini buraya yazın.'),
                                    ])->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Kaydet')
                ->action('save'),
            Action::make('reset')
                ->label('Varsayılana Dön')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Varsayılan değerlere dön')
                ->modalDescription('Tüm ayarlar sıfırlanacak. Bu işlem geri alınamaz.')
                ->action('resetToDefaults'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Versiyon tutarlılık kontrolü
        if (
            isset($data['minimum_version'], $data['latest_version'])
            && filled($data['minimum_version'])
            && filled($data['latest_version'])
            && AppSettingsService::compareVersions($data['minimum_version'], $data['latest_version']) > 0
        ) {
            Notification::make()
                ->danger()
                ->title('Minimum versiyon, en son versiyondan büyük olamaz!')
                ->send();

            return;
        }

        AppSettings::get()->update($data);

        Notification::make()->success()->title('Ayarlar kaydedildi.')->send();
    }

    public function resetToDefaults(): void
    {
        $defaults = [
            'maintenance_mode'      => false,
            'maintenance_message'   => null,
            'force_update_required' => false,
            'minimum_version'       => '1.0.0',
            'latest_version'        => '1.0.0',
            'update_message'        => null,
            'update_ios_url'        => null,
            'update_android_url'    => null,
            'show_announcement'     => false,
            'announcement_title'    => null,
            'announcement_message'  => null,
            'announcement_type'     => 'info',
        ];

        AppSettings::get()->update($defaults);
        $this->form->fill($defaults);

        Notification::make()->success()->title('Varsayılan değerler geri yüklendi.')->send();
    }
}
