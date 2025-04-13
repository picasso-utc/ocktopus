<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FactureRecueExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected $factures;

    public function __construct($factures)
    {
        $this->factures = $factures;
    }

    public function collection()
    {
        return $this->factures;
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Entreprise',
            'Date',
            'Date de paiement',
            'Prix TTC',
            'TVA',
        ];
    }

    public function map($facture): array
    {
        return [
            $facture->facture_number,
            $facture->destinataire,
            $facture->date ? date('d/m/Y', strtotime($facture->date)) : '',
            $facture->date_paiement ? date('d/m/Y', strtotime($facture->date_paiement)) : '',
            number_format($facture->prix, 2, ',', ' ') . ' €',
            number_format($facture->tva, 2, ',', ' ') . ' €',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                foreach (range('A', 'F') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}