# Picsous

## Le service

Picsous est un outil interne au Pic'Asso visant à permettre la gestion de la trésorerie. Pour les membres de l'équipe du Pic'Asso, il permet de 
remplir les données financières pour chaque perm réalisée en astreinte. Pour l'équipe de trésorerie, il permet de visualiser de façon efficace ces données.

___

## Models

- Les factures reçues
- Les catégories de facture
- Le montant des catégories
- Les factures émises
- Les note de frais
- Les éléments d'une facture (et note de frais)
- Les chèques
- Les modifications

### Factures reçues :

- Contient le prix total et la tva
- Les infos de facturation (Date, Destinataire, Immobilisation, etc..)

- Méthode : `categoriePrix()` -> HasMany MontantCatégorie (Il faut utiliser la méthode `categorie()` de MontantCatégorie pour récuperer la catégorie en question cf. `Montant des catégories`)

### Les catégories de facture :

- Le nom de la catégorie
- Id d'une catgéorie parent pour créer des sous catégories

- Méthode : 
  - `parent()` -> belongsTo CategorieFacture
  - `children()` -> HasMany CategorieFacture

### Montant des catégories :

- Contient le montant relatif à une catégorie pour une facture
- L'id de la facture
- L'id de la catégorie

- Méthode :
    - `categorie()` -> belongsTo CategorieFacture

### Facture émises & Note de frais:

- Contient les infos de facturation

- Méthode :
    - `elementFacture()` -> belongsTo ElementFacture
    - Contient les informations relatives au prix et description 

*Pas encore opérationel pour Facture émises*

### Éléments d'une facture :

- Une descritpion 
- Le prix unitaire TTC
- Une quantitée
- Le taux de TVA
- L'id de la facture ou note 

### Les chèques:

- Infos de facturation
- Peut-être relier à une facture 

*Pas de création implémenté*

___

## Gestions des PDF

Génération des pdf pour les note de frais et les facture émises

- Utilisation de la librairie `laravel-dompdf` ([Page Github](https://github.com/barryvdh/laravel-dompdf))
- Attributs `->actions([])` de la `$table` NoteDeFrais (ligne 171) / Facture emise :

```php
    Tables\Actions\Action::make('pdf')
        ->label('PDF')
        ->color('success')
        ->icon('heroicon-o-arrow-down-tray')
        ->action(function (NoteDeFrais $record) {
            return response()->streamDownload(function () use ($record) {
                echo Pdf::loadHtml(
                Blade::render('pdf', ['record' => $record])
            )->stream();
        }, $record->id . '.pdf');
```

___

## Services:
- Rentrer des factures reçues
- Rentrer et générer en PDF des factures émises et note de frais
