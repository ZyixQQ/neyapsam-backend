<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Uygulama Durumu</x-slot>

        <div class="flex flex-wrap gap-3 items-center">

            {{-- Bakım Modu --}}
            <div class="flex items-center gap-2">
                @if($maintenanceMode)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        Bakım Modu AKTİF
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        Uygulama Çalışıyor
                    </span>
                @endif

                <button
                    wire:click="toggleMaintenance"
                    wire:loading.attr="disabled"
                    class="text-xs text-gray-500 underline hover:text-gray-800 dark:hover:text-gray-200 transition-colors"
                >
                    <span wire:loading.remove wire:target="toggleMaintenance">
                        {{ $maintenanceMode ? 'Kapat' : 'Aç' }}
                    </span>
                    <span wire:loading wire:target="toggleMaintenance">...</span>
                </button>
            </div>

            {{-- Güncelleme durumu --}}
            @if($forceUpdate)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                    ⚠️ Zorunlu Güncelleme Aktif — v{{ $latestVersion }}
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                    Güncel Sürüm: v{{ $latestVersion }} (min: v{{ $minimumVersion }})
                </span>
            @endif

            {{-- Duyuru --}}
            @if($showAnnouncement)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                    📢 Duyuru Aktif
                </span>
            @endif

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
