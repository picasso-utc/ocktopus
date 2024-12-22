<x-filament::page>
    <form wire:submit.prevent="calculerExonerations" class="space-y-4">
        {{ $this->form }}
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Calculer
        </button>
    </form>

    @if ($exonerationsCount)
        <div class="mt-6">
            <h3 class="text-lg font-semibold">Nombre d'exonérations par article</h3>
            <table class="min-w-full border border-gray-200 mt-2">
                <thead>
                <tr>
                    <th class="px-4 py-2 border-b">Nom de l'article</th>
                    <th class="px-4 py-2 border-b">Nombre d'exonérations</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($exonerationsCount as $exoneration)
                    <tr>
                        <td class="px-4 py-2 border-b">{{ $exoneration['name'] }}</td>
                        <td class="px-4 py-2 border-b">{{ $exoneration['total'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif



<style>
        table {
            background-color: #1e40af;
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
