<x-filament-widgets::widget class="fi-filament-info-widget">
    <x-filament::section>
        <div class="flex items-center gap-x-3">
            <div class="flex-1">

                <p class="mt-2 text-xl font-bold text-white-500 dark:text-white-400">
                    Pic'Asso
                </p>
            </div>

            <div class="flex flex-col items-end gap-y-1">
                <x-filament::link
                    color="gray"
                    href="{{ url('/admin') }}"
                    icon="heroicon-m-command-line"
                    rel="noopener noreferrer"
                    target="_blank"
                >
                    Panel Admin
                </x-filament::link>

                <x-filament::link
                    color="gray"
                    href="{{ url('/treso') }}"
                    icon="heroicon-m-currency-euro"
                    rel="noopener noreferrer"
                    target="_blank"
                >
                    Picsous
                </x-filament::link>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
