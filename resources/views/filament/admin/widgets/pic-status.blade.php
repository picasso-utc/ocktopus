<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Etat du Pic
                </p>
                <p @class([
                    'mt-1 text-xl font-bold',
                    'text-success-600 dark:text-success-400' => $open,
                    'text-danger-600 dark:text-danger-400' => !$open,
                ])>
                    {{ $open ? 'Pic ouvert !' : 'Pic fermé...' }}
                </p>
            </div>

            <x-filament::button
                wire:click="toggle"
                :color="$open ? 'danger' : 'success'"
            >
                {{ $open ? 'Fermer le pic' : 'Ouvrir le pic' }}
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
