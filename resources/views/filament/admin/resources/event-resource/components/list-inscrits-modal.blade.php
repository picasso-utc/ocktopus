<div class="space-y-4">
    <table class="w-full text-sm border-collapse">
        <thead>
            <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Email</th>
                <th class="px-4 py-2 text-center font-medium text-gray-500 dark:text-gray-400">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($this->event->shotguns as $inscrit)
                <tr>
                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $inscrit->email }}</td>
                    <td class="px-4 py-2">
                        <div class="flex items-center justify-center gap-2">
                            <x-filament::icon-button
                                icon="heroicon-o-envelope"
                                color="primary"
                                size="sm"
                                wire:click="envoyerMailPerso('{{ $inscrit->email }}')"
                            />
                            <x-filament::icon-button
                                icon="heroicon-o-trash"
                                color="danger"
                                size="sm"
                                wire:click="supprimerInscrit('{{ $inscrit->email }}')"
                            />
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="px-4 py-2 text-center text-gray-500 dark:text-gray-400">Aucun inscrit.e</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>