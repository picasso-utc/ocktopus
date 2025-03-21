<div class="p-4 mx-auto">
    <table class="bg-white border border-gray-300 shadow-lg rounded-lg w-full">
        <thead>
            <tr class="bg-gray-200 text-gray-700 text-left">
                <th class="py-3 px-4 border">Email</th>
                <th class="py-3 px-4 border">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($event->shotguns as $inscrit)
                <tr class="text-center bg-white hover:bg-gray-100 transition">
                    <td class="py-3 px-4 border">{{ $inscrit->email }}</td>
                    <td class="py-3 px-4 border flex justify-center gap-2 space-x-2">
                        <x-filament::button 
                            wire:click="envoyerMailPerso('{{ $inscrit->email }}')"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg shadow">
                            ✉️ Envoyer un mail
                        </x-filament::button>
                        <x-filament::button 
                            wire:click="supprimerInscrit('{{ $inscrit->email }}')"
                            class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg shadow">
                            ❌ Supprimer
                        </x-filament::button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="py-3 px-4 border text-center text-gray-500">Aucun inscrit.e</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
