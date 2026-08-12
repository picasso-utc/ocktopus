<?php

namespace App\Filament\Admin\Resources;

use App\Models\MenuCategory;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuCategoryResource extends Resource
{
    protected static ?string $model = MenuCategory::class;
    protected static ?string $navigationGroup = 'Gestion de l\'application mobile';
    protected static ?string $label = 'Categories du menu';
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function form(Form $form): Form
    {
        $menuCategoriesByProductCategory = MenuCategory::query()
            ->get()
            ->flatMap(function (MenuCategory $menuCategory) {
                return collect($menuCategory->product_categories ?? [])
                    ->map(fn ($productCategory) => [
                        'product_category' => $productCategory,
                        'menu_category' => $menuCategory->name,
                    ]);
            })
            ->groupBy('product_category')
            ->map(fn ($items) => $items->pluck('menu_category')->unique()->values()->all());

        $existingCategories = Product::query()
            ->where('active', true)
            ->distinct()
            ->pluck('category')
            ->filter()
            ->mapWithKeys(function ($category) use ($menuCategoriesByProductCategory) {
                $menuCategories = $menuCategoriesByProductCategory[$category] ?? [];
                $label = $category;
                if (!empty($menuCategories)) {
                    $label .= ' [' . implode(', ', $menuCategories) . ']';
                }
                return [$category => $label];
            })
            ->toArray();

        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->label('Nom de la categorie'),
            Forms\Components\TextInput::make('icon')
                ->required()
                ->label('Icone Lucide React')
                ->helperText('Nom exact en CamelCase: CupSoda, UtensilsCrossed ...'),
            Forms\Components\CheckboxList::make('product_categories')
                ->required()
                ->label('Categories de produits')
                ->options($existingCategories)
                ->columns(2),
            Forms\Components\TextInput::make('sort_order')
                ->numeric()
                ->default(0)
                ->label('Ordre'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nom')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('icon')->label('Icone'),
                Tables\Columns\TextColumn::make('sort_order')->label('Ordre')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => MenuCategoryResource\Pages\ListMenuCategories::route('/'),
            'create' => MenuCategoryResource\Pages\CreateMenuCategory::route('/create'),
            'edit' => MenuCategoryResource\Pages\EditMenuCategory::route('/{record}/edit'),
        ];
    }
}
