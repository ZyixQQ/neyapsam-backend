<?php

namespace Database\Seeders;

use App\Enums\SuggestionStatus;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $defaultAdmin = User::updateOrCreate(
            ['email' => 'admin@neyapsam.app'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $gmailAdmin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Gmail',
                'is_admin' => true,
                'username' => 'admin_gmail',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
            ]
        );

        $users = collect([$defaultAdmin, $gmailAdmin]);

        $categories = [
            [
                'name' => 'Ne Izlesem',
                'slug' => 'ne-izlesem',
                'icon' => '🎬',
                'color' => '#F97316',
                'subcategories' => [
                    [
                        'name' => 'Dramatik', 'slug' => 'dramatik', 'icon' => '😢',
                        'suggestions' => [
                            ['title' => 'Schindler\'in Listesi', 'description' => 'İkinci Dünya Savaşı\'nda bir Alman işadamının Yahudileri kurtarma hikayesi.'],
                            ['title' => 'Yüzüklerin Efendisi', 'description' => 'Orta Dünya\'yı kötülükten kurtarmak için çıkılan destansı yolculuk.'],
                            ['title' => 'Yeşil Yol', 'description' => 'Ölüm koridorunda beklenmedik bir mucize ile tanışan gardiyanın hikayesi.'],
                            ['title' => 'Forrest Gump', 'description' => 'Sıradan bir adamın olağanüstü hayat yolculuğu.'],
                            ['title' => 'Gladyatör', 'description' => 'Roma generali Marcus\'un intikam dolu arenaya dönüş hikayesi.'],
                            ['title' => 'Titanik', 'description' => 'Tarihin en büyük gemi felaketinde yaşanan aşk hikayesi.'],
                            ['title' => 'Esaretin Bedeli', 'description' => 'Yanlışlıkla hapse giren bir adamın umut dolu hikayesi.'],
                        ],
                    ],
                    [
                        'name' => 'Romantik', 'slug' => 'romantik', 'icon' => '❤️',
                        'suggestions' => [
                            ['title' => 'Aşk ve Gurur', 'description' => 'Jane Austen\'ın zamansız aşk ve toplumsal sınıf hikayesi.'],
                            ['title' => 'Defne ile Aşk', 'description' => 'İki farklı dünyadan insanın beklenmedik aşkı.'],
                            ['title' => 'Yarından Sonra', 'description' => 'Genç bir çiftin Paris\'teki tek gecelik büyülü hikayesi.'],
                            ['title' => 'La La Land', 'description' => 'Los Angeles\'ta hayallerinin peşinden koşan iki sanatçının aşkı.'],
                            ['title' => 'Aşkın Sonu', 'description' => 'Yıllar sonra yeniden kavuşan iki eski sevgilinin hikayesi.'],
                            ['title' => 'Bir Yıldız Doğuyor', 'description' => 'Ünlü bir müzisyen ile genç bir sanatçının aşk ve müzik yolculuğu.'],
                        ],
                    ],
                    [
                        'name' => 'Komedi', 'slug' => 'komedi', 'icon' => '😂',
                        'suggestions' => [
                            ['title' => 'Aptal ve Daha Aptal', 'description' => 'İki saf arkadaşın çılgın yolculuk macerası.'],
                            ['title' => 'Hangover', 'description' => 'Bekarlığa veda gecesinden sonra hiçbir şeyi hatırlamayan arkadaşların komik macerası.'],
                            ['title' => 'Superbad', 'description' => 'Lise son günlerinde eğlenmek isteyen iki arkadaşın komik maceraları.'],
                            ['title' => 'Borat', 'description' => 'Kazakistanlı bir muhabirin Amerika\'yı keşfetme serüveni.'],
                            ['title' => 'Bridesmaids', 'description' => 'Düğün hazırlıklarında birbirinden komik olaylar yaşayan nedimeler.'],
                            ['title' => 'Elf', 'description' => 'Noel Baba\'nın yanında büyüyen insan elfin New York maceraları.'],
                        ],
                    ],
                    [
                        'name' => 'Aksiyon', 'slug' => 'aksiyon', 'icon' => '💥',
                        'suggestions' => [
                            ['title' => 'John Wick', 'description' => 'Emekli tetikçinin köpeği için verdiği amansız mücadele.'],
                            ['title' => 'Mad Max: Fury Road', 'description' => 'Çöl distopyasında hayatta kalma mücadelesi.'],
                            ['title' => 'Mission Impossible', 'description' => 'İmkansız görevleri başaran gizli ajan Ethan Hunt\'ın maceraları.'],
                            ['title' => 'Die Hard', 'description' => 'Gökdelenin içinde yalnız kalan bir polisin teröristlerle mücadelesi.'],
                            ['title' => 'The Dark Knight', 'description' => 'Batman ile Joker arasındaki epik iyilik kötülük çatışması.'],
                            ['title' => 'Inception', 'description' => 'Rüyaların içine girerek fikir eken bir ekibin tehlikeli görevi.'],
                        ],
                    ],
                    [
                        'name' => 'Korku', 'slug' => 'korku', 'icon' => '👻',
                        'suggestions' => [
                            ['title' => 'Conjuring', 'description' => 'Gerçek bir paranormal vakaya dayanan ürkütücü hayalet hikayesi.'],
                            ['title' => 'Get Out', 'description' => 'Kız arkadaşının ailesiyle tanışmaya giden genç adamın korkutucu deneyimi.'],
                            ['title' => 'Hereditary', 'description' => 'Büyükannenin ölümünden sonra aileye musallat olan karanlık sırlar.'],
                            ['title' => 'A Quiet Place', 'description' => 'Ses çıkarırsa ölürsün! Sese duyarlı yaratıklardan kaçan bir ailenin hikayesi.'],
                            ['title' => 'It', 'description' => 'Lağım sistemlerinde yaşayan korkutucu palyaço Pennywise\'dan kaçan çocuklar.'],
                        ],
                    ],
                    [
                        'name' => 'Gerilim', 'slug' => 'gerilim', 'icon' => '🧠',
                        'suggestions' => [
                            ['title' => 'Prisoners', 'description' => 'Kaybolan kızını arayan babanın sınırları zorlayan mücadelesi.'],
                            ['title' => 'Gone Girl', 'description' => 'Eşinin kayboluşunun ardından şüpheli konuma düşen bir adamın hikayesi.'],
                            ['title' => 'Parasite', 'description' => 'Yoksul bir ailenin zengin bir aileye sızma planı.'],
                            ['title' => 'Shutter Island', 'description' => 'Bir akıl hastanesinde kaybolan hastayı arayan dedektifin karanlık yolculuğu.'],
                            ['title' => 'Zodiac', 'description' => 'Gerçek bir seri katil vakasını çözmeye çalışan gazetecinin hikayesi.'],
                        ],
                    ],
                    [
                        'name' => 'Belgesel', 'slug' => 'belgesel', 'icon' => '🎥',
                        'suggestions' => [
                            ['title' => 'Free Solo', 'description' => 'Alex Honnold\'un El Capitan\'ı hiçbir ekipman olmadan tırmanma hikayesi.'],
                            ['title' => 'The Last Dance', 'description' => 'Michael Jordan ve Chicago Bulls\'un efsanevi son sezonunun perde arkası.'],
                            ['title' => 'Making a Murderer', 'description' => 'Suçsuz yere mahkum edilen bir adamın çarpıcı gerçek hikayesi.'],
                            ['title' => 'Planet Earth', 'description' => 'Dünyanın dört bir yanından nefes kesen doğa görüntüleri.'],
                            ['title' => 'Jiro Dreams of Sushi', 'description' => 'Dünyanın en iyi suşi ustasının mükemmellik arayışı.'],
                        ],
                    ],
                    [
                        'name' => 'Animasyon', 'slug' => 'animasyon', 'icon' => '✨',
                        'suggestions' => [
                            ['title' => 'Spirited Away', 'description' => 'Hayao Miyazaki\'nin büyülü dünyasında kaybolan küçük kızın macerası.'],
                            ['title' => 'Toy Story', 'description' => 'Oyuncakların gizli dünyasını anlatan Pixar başyapıtı.'],
                            ['title' => 'Coco', 'description' => 'Müzisyen olmak isteyen çocuğun ölüler ülkesine yaptığı yolculuk.'],
                            ['title' => 'The Lion King', 'description' => 'Babasının ölümünden kaçan aslan yavrusunun büyüme hikayesi.'],
                            ['title' => 'Up', 'description' => 'Yaşlı bir adamın balonlu eviyle çıktığı macera dolu yolculuk.'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Ne Yesem',
                'slug' => 'ne-yesem',
                'icon' => '🍽',
                'color' => '#DC2626',
                'subcategories' => [
                    [
                        'name' => 'Pratik', 'slug' => 'pratik', 'icon' => '⚡',
                        'suggestions' => [
                            ['title' => 'Yumurtalı Tost', 'description' => 'Ekmeğin ortasını keserek içine yumurta kırıp pişirilen pratik kahvaltı.'],
                            ['title' => 'Ton Balıklı Makarna', 'description' => 'Konserve ton balığı, sarımsak ve zeytinyağıyla hazırlanan hızlı makarna.'],
                            ['title' => 'Peynirli Omlet', 'description' => 'İçine cheddar peyniri ve taze soğan eklenen klasik omlet.'],
                            ['title' => 'Avokadolu Ekmek', 'description' => 'Üzerine avokado ezilerek limon ve tuz serpilmiş tam buğday ekmeği.'],
                            ['title' => 'Kıymalı Pilav', 'description' => 'Kıyma ve sebzelerle hazırlanan doyurucu tek tabak yemek.'],
                            ['title' => 'Sigara Böreği', 'description' => 'Yufkayla hazırlanan peynirli veya kıymalı çıtır börek.'],
                        ],
                    ],
                    [
                        'name' => 'Saglikli', 'slug' => 'saglikli', 'icon' => '🥗',
                        'suggestions' => [
                            ['title' => 'Akdeniz Salatası', 'description' => 'Domates, salatalık, zeytin ve beyaz peynirden oluşan klasik salata.'],
                            ['title' => 'Izgara Tavuk', 'description' => 'Baharatlarla marine edilmiş ızgara tavuk göğsü ve yanında buharda sebze.'],
                            ['title' => 'Kinoa Kasesi', 'description' => 'Kinoa, avokado, nohut ve sebzelerle hazırlanan besleyici kase.'],
                            ['title' => 'Smoothie Kasesi', 'description' => 'Muz, çilek ve yoğurtla hazırlanan üzerine granola eklenen kahvaltı kasesi.'],
                            ['title' => 'Mercimek Çorbası', 'description' => 'Kırmızı mercimek ve baharatlarla hazırlanan doyurucu Türk çorbası.'],
                        ],
                    ],
                    [
                        'name' => 'Kolay', 'slug' => 'kolay', 'icon' => '👌',
                        'suggestions' => [
                            ['title' => 'Menemen', 'description' => 'Domates, biber ve yumurtayla hazırlanan klasik Türk kahvaltısı.'],
                            ['title' => 'Pasta', 'description' => 'Sarımsaklı yağ ve parmesan peyniriyle hazırlanan klasik spagetti.'],
                            ['title' => 'Sebzeli Noodle', 'description' => 'Soya sosu ve karışık sebzelerle hazırlanan Asya usulü noodle.'],
                            ['title' => 'Patates Kızartması', 'description' => 'Dışı çıtır içi yumuşak ev yapımı patates kızartması.'],
                            ['title' => 'Pizza Toast', 'description' => 'Ekmek üzerine domates sosu, peynir ve dilediğin malzemeyle hazırlanan pratik pizza.'],
                        ],
                    ],
                    [
                        'name' => 'Tatli', 'slug' => 'tatli', 'icon' => '🍰',
                        'suggestions' => [
                            ['title' => 'Çikolatalı Kek', 'description' => 'Nemli ve yoğun çikolatalı kek, üzerinde ganaj sos.'],
                            ['title' => 'Sütlaç', 'description' => 'Fırında üstü kızarmış geleneksel Türk sütlacı.'],
                            ['title' => 'Kazandibi', 'description' => 'Altı karamelleşmiş, üstü beyaz Türk tatlısı.'],
                            ['title' => 'Muhallebi', 'description' => 'Gül suyu ile tatlandırılmış klasik muhallebi.'],
                            ['title' => 'Tiramisu', 'description' => 'Kahve ve mascarpone peyniriyle hazırlanan İtalyan tatlısı.'],
                            ['title' => 'Brownie', 'description' => 'Dışı çıtır içi yumuşak çikolatalı kare.'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Ne Oynasam',
                'slug' => 'ne-oynasam',
                'icon' => '🎮',
                'color' => '#2563EB',
                'subcategories' => [
                    [
                        'name' => 'Hikaye', 'slug' => 'hikaye', 'icon' => '📖',
                        'suggestions' => [
                            ['title' => 'The Last of Us', 'description' => 'Kıyamet sonrası dünyada babaca bağ kuran iki karakterin hikayesi.'],
                            ['title' => 'Red Dead Redemption 2', 'description' => 'Vahşi Batı\'nın son demlerinde bir eşkıyanın dönüşüm hikayesi.'],
                            ['title' => 'God of War', 'description' => 'Savaş tanrısının oğluyla çıktığı mitolojik yolculuk.'],
                            ['title' => 'Cyberpunk 2077', 'description' => 'Distopik gelecekte ölümsüzlük arayan paralı askerin hikayesi.'],
                            ['title' => 'Witcher 3', 'description' => 'Kayıp evladını arayan canavarları öldüren bir savaşçının açık dünya macerası.'],
                            ['title' => 'Horizon Zero Dawn', 'description' => 'Robot hayvanlarla dolu gizemli bir dünyada savaşan avcının hikayesi.'],
                        ],
                    ],
                    [
                        'name' => 'Rekabetci', 'slug' => 'rekabetci', 'icon' => '🏆',
                        'suggestions' => [
                            ['title' => 'Valorant', 'description' => 'Taktiksel nişancı oyunu, yeteneğine güvenenlere.'],
                            ['title' => 'League of Legends', 'description' => 'Dünyanın en popüler MOBA oyunu.'],
                            ['title' => 'CS2', 'description' => 'Klasik Counter-Strike\'ın yeni nesil versiyonu.'],
                            ['title' => 'Rocket League', 'description' => 'Araçlarla oynanan futbol, hem eğlenceli hem rekabetçi.'],
                            ['title' => 'Street Fighter 6', 'description' => 'Dövüş oyunlarının efsanesi yeni nesilde.'],
                        ],
                    ],
                    [
                        'name' => 'Rahat', 'slug' => 'rahat', 'icon' => '🌿',
                        'suggestions' => [
                            ['title' => 'Stardew Valley', 'description' => 'Çiftlik kurma ve köy hayatını keşfetme oyunu, saatlerce bırakamayacaksın.'],
                            ['title' => 'Animal Crossing', 'description' => 'Kendi adanı kur, komşularınla arkadaş ol, huzurlu bir oyun.'],
                            ['title' => 'Minecraft', 'description' => 'Yaratıcılığına sınır yok, istediğini inşa et.'],
                            ['title' => 'Journey', 'description' => 'Kısa ama çok etkileyici, görsel olarak büyüleyici bir yolculuk.'],
                            ['title' => 'Unpacking', 'description' => 'Taşınma kutularını açarken bir hayatı keşfeden bulmaca oyunu.'],
                        ],
                    ],
                    [
                        'name' => 'Mobil', 'slug' => 'mobil', 'icon' => '📱',
                        'suggestions' => [
                            ['title' => 'Alto\'s Odyssey', 'description' => 'Görsel olarak büyüleyici, rahatlatıcı snowboard oyunu.'],
                            ['title' => 'Monument Valley', 'description' => 'İmkansız mimari bulmacalarla dolu sanat eseri oyun.'],
                            ['title' => 'Clash Royale', 'description' => 'Kart tabanlı strateji ve kule savunma oyunu.'],
                            ['title' => 'Subway Surfers', 'description' => 'Klasik sonsuz koşu oyunu, reflekslerini test et.'],
                            ['title' => 'Among Us', 'description' => 'Arkadaşlarınla oynayabileceğin eğlenceli dedektif oyunu.'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Ne Desem',
                'slug' => 'ne-desem',
                'icon' => '💬',
                'color' => '#7C3AED',
                'subcategories' => [
                    [
                        'name' => 'Buz Kirici', 'slug' => 'buz-kirici', 'icon' => '🧊',
                        'suggestions' => [
                            ['title' => 'Hayatında hiç pişman olmadığın an neydi?', 'description' => 'Derin bir sohbet başlatmak için mükemmel açılış sorusu.'],
                            ['title' => 'Dünyada bir günlüğüne bir yere gidebilseydin neresi olurdu?', 'description' => 'Hayalleri ve gezme tutkusunu ortaya çıkaran soru.'],
                            ['title' => 'Çocukken ne olmak istiyordun?', 'description' => 'Nostalji ve kişisel hikayeler için harika bir başlangıç.'],
                            ['title' => 'Son izlediğin diziyi anlat.', 'description' => 'Ortak ilgi alanı bulmak için kolay ve eğlenceli soru.'],
                            ['title' => 'Bir süper gücün olsaydı ne seçerdin?', 'description' => 'Kişiliği yansıtan eğlenceli bir buz kırıcı.'],
                        ],
                    ],
                    [
                        'name' => 'Flort', 'slug' => 'flort', 'icon' => '😉',
                        'suggestions' => [
                            ['title' => 'Gözlerin çok güzel, rengi ne?', 'description' => 'Doğal ve samimi bir iltifat.'],
                            ['title' => 'Seninle geçirdiğim her an çok özel geliyor.', 'description' => 'Hissettiklerini nazikçe ifade etmenin yolu.'],
                            ['title' => 'Bu akşam bir şeyin var mı?', 'description' => 'Direkt ama nazik buluşma teklifi.'],
                            ['title' => 'Seni düşünmeden yapamıyorum.', 'description' => 'Duygularını açıkça belirtmek için samimi cümle.'],
                            ['title' => 'Seninle çok iyi vakit geçiriyorum.', 'description' => 'Basit ama etkili bir his paylaşımı.'],
                        ],
                    ],
                    [
                        'name' => 'Arkadasca', 'slug' => 'arkadasca', 'icon' => '🤝',
                        'suggestions' => [
                            ['title' => 'Bu hafta sonu ne yapıyorsunuz?', 'description' => 'Arkadaş grubuna plan yapmak için klasik açılış.'],
                            ['title' => 'Çok özledim sizi, toplanmalıyız!', 'description' => 'Uzun süredir görüşülmeyen arkadaşlara özlem mesajı.'],
                            ['title' => 'Yeni bir yer keşfettim, gidelim mi?', 'description' => 'Macera ruhunu uyandıran arkadaşça davet.'],
                            ['title' => 'Nasılsın, gerçekten?', 'description' => 'Yüzeysel değil, gerçekten önemsediğini gösteren soru.'],
                            ['title' => 'Desteğin için teşekkürler, çok anlamlıydı.', 'description' => 'Minnetini ifade etmenin içten yolu.'],
                        ],
                    ],
                    [
                        'name' => 'Komik', 'slug' => 'komik-sozler', 'icon' => '🤣',
                        'suggestions' => [
                            ['title' => 'Bugün yapay zeka gibi hissediyorum, pil doldu ama motivasyon yüklenmedi.', 'description' => 'Günün yorgunluğunu espriyle anlatan cümle.'],
                            ['title' => 'Diyetim var diyordum, pizza beni kandırdı.', 'description' => 'Herkesin başına gelen komik diyet itirafı.'],
                            ['title' => 'Sabah 6\'da alarm kurdum, gece 12\'de neden bilmiyorum.', 'description' => 'Gece kuşlarının klasik sabah dramı.'],
                            ['title' => 'Para uçuyor derler, bende zaten uçtu bile.', 'description' => 'Ay sonunu güldürerek anlatan espri.'],
                            ['title' => 'Sosyal olmaya çalışıyorum ama yatağım çok ikna edici.', 'description' => 'İçe dönük insanların kendini ifade etme biçimi.'],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($categories as $categoryIndex => $categoryData) {
            $category = Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'name' => $categoryData['name'],
                    'icon' => $categoryData['icon'],
                    'color' => $categoryData['color'],
                    'sort_order' => $categoryIndex,
                    'is_active' => true,
                ]
            );

            foreach ($categoryData['subcategories'] as $subcategoryIndex => $subcategoryData) {
                $subcategory = Subcategory::updateOrCreate(
                    ['slug' => $subcategoryData['slug']],
                    [
                        'category_id' => $category->id,
                        'name' => $subcategoryData['name'],
                        'icon' => $subcategoryData['icon'],
                        'sort_order' => $subcategoryIndex,
                        'is_active' => true,
                    ]
                );

                if ($subcategory->suggestions()->exists()) {
                    continue;
                }

                foreach ($subcategoryData['suggestions'] as $index => $suggestionData) {
                    $user = $users->random();
                    $upvotes = rand(3, 80);
                    $downvotes = rand(0, 30);

                    Suggestion::create([
                        'subcategory_id' => $subcategory->id,
                        'user_id' => $user->id,
                        'title' => $suggestionData['title'],
                        'description' => $suggestionData['description'] ?? null,
                        'upvote_count' => $upvotes,
                        'downvote_count' => $downvotes,
                        'net_score' => $upvotes - $downvotes,
                        'status' => SuggestionStatus::Approved,
                        'is_featured' => $index === 0,
                        'created_at' => now()->subDays(rand(0, 20)),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
