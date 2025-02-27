<x-filament::page>
    <!-- Formulaire pour les dates -->
    <form wire:submit.prevent="calculerTotal">
        {{ $this->form }}
        <div style="display: flex; justify-content: center">
            <button style="margin: 15px; padding: 8px; background-color: #1e40af" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 dark:bg-custom-500 dark:hover:bg-custom-400 focus-visible:ring-custom-500/50 dark:focus-visible:ring-custom-400/50 fi-ac-btn-action">
                Calculer Total Global
            </button>
        </div>
    </form>

    <!-- Affichage des totaux globaux -->
    @if ($totalRecettes !== null || $totalDepenses !== null)
        <div class="p-8 bg-center shadow rounded">
            <h2 class="text-lg font-semibold">Totaux entre les dates sélectionnées :</h2>
            <p class="text-2xl font-bold my-8">Recettes : {{ number_format($totalRecettes, 2) }} € | Dépenses : {{ number_format($totalDepenses, 2) }} €</p>
            <p class="text-m italic">* Les calculs se réfèrent à la date de fin des recettes</p>
        </div>
    @endif

    <!-- Bouton pour calculer le total par catégorie -->
    <button wire:click="calculerTotaux" style="margin: 15px; padding: 8px; background-color: #1e40af" class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 dark:bg-custom-500 dark:hover:bg-custom-400 focus-visible:ring-custom-500/50 dark:focus-visible:ring-custom-400/50 fi-ac-btn-action">
        Calculer Total par Catégorie
    </button>

    <!-- Affichage des totaux par catégorie -->
    @if ($totalsByCategory)
        <div class="mt-6">
            <h3 class="text-lg font-semibold">Total par catégorie</h3>
            <table style="border-radius: 5px" class="w-full roun border border-white mt-2">
                <thead>
                <tr>
                    <th class="px-4 py-2 border-b w-1/3">Catégories</th>
                    <th style="background-color: #FF7F7F; color:black;" class="px-4 py-2 border-b w-1/3">Dépenses</th>
                    <th style="background-color: #FF7F7F; color:black;" class="px-4 py-2 border-b w-1/3">TVA</th>
                    <th style="background-color: #89E894; color:black;" class="px-4 py-2 border-b w-1/3">Recettes</th>
                    <th style="background-color: #89E894; color:black;" class="px-4 py-2 border-b w-1/3">TVA</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($totalsByCategory as $total)
                    <tr>
                        <td class="px-4 py-2 border-b text-center">{{ $total['categorie'] }}</td>
                        <td style="background-color: #FF7F7F; color:black;" class="px-4 py-2 border-b text-center">{{ number_format($total['totalDepenses'], 2) }} €</td>
                        <td style="background-color: #FF7F7F; color:black;" class="px-4 py-2 border-b text-center">{{ number_format($total['totalTvaDepenses'], 2) }} €</td>
                        <td style="background-color: #89E894; color:black;" class="px-4 py-2 border-b text-center">{{ number_format($total['totalRecettes'], 2) }} €</td>
                        <td style="background-color: #89E894; color:black;" class="px-4 py-2 border-b text-center">{{ number_format($total['totalTvaRecettes'], 2) }} €</td>
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
            border: 1px solid #ffffff; /* Bordures internes des cellules */
            padding: 10px; /* Ajoute de l'espace interne */
            text-align: center; /* Centre le contenu */
        }
    </style>
</x-filament::page>
