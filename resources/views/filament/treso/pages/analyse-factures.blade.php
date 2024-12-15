<x-filament::page>
    <!-- Formulaire pour les dates -->
    <form wire:submit.prevent="calculerTotal">
        {{ $this->form }}
        <div style="display: flex; justify-content: center">
            <button style="margin: 15px; padding: 8px; background-color: #1e40af" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 dark:bg-custom-500 dark:hover:bg-custom-400 focus-visible:ring-custom-500/50 dark:focus-visible:ring-custom-400/50 fi-ac-btn-action">
                Calculer Total Global
            </button></div>
        <!-- Bouton pour calculer le total global -->

    </form>

    <!-- Affichage du total global -->
    @if ($total !== null)
        <div class="mt-6 p-4 bg-center shadow rounded">
            <h2 class="text-lg font-semibold">Total des factures entre les dates sélectionnées :</h2>
            <p class="text-2xl font-bold">{{ number_format($total, 2) }} €</p>
        </div>
    @endif

    <!-- Bouton pour calculer le total par catégorie -->
    <button wire:click="calculerTotals" style="margin: 15px; padding: 8px; background-color: #1e40af" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 dark:bg-custom-500 dark:hover:bg-custom-400 focus-visible:ring-custom-500/50 dark:focus-visible:ring-custom-400/50 fi-ac-btn-action">
        Calculer Total par Catégorie
    </button>

    <!-- Affichage des totaux par catégorie -->
    @if ($totalsByCategory)
        <div class="mt-6">
            <h3 class="text-lg font-semibold">Total par catégorie</h3>
            <table style="border-radius: 5px" class="w-full roun border border-gray-200 mt-2">
                <thead>
                <tr>
                    <th style="background-color: #008855;" class="px-4 py-2 border-b w-1/2">Catégorie</th>
                    <th style="background-color: #008855;" class="px-4 py-2 border-b w-1/2">Total</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($totalsByCategory as $total)
                    <tr>
                        <td style="background-color: #119e69;" class="px-4 py-2 border-b text-center">{{ $total->categorie->nom }}</td>
                        <td style="background-color: #119e69;" class="px-4 py-2 border-b text-center">{{ number_format($total->total_prix, 2) }} €</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <style>
        table {
            border-collapse: separate; /* Permet d'appliquer le border-radius */
            border-spacing: 0; /* Supprime les espaces entre les cellules */
            border: 1px solid #ccc; /* Ajoute une bordure autour du tableau */
            border-radius: 10px; /* Arrondit les coins du tableau */
            overflow: hidden; /* Cache les débordements pour un rendu net */
        }

        th, td {
            border: 1px solid #ccc; /* Bordures internes des cellules */
            padding: 10px; /* Ajoute de l'espace interne */
            text-align: center; /* Centre le contenu */
        }

    </style>
</x-filament::page>


