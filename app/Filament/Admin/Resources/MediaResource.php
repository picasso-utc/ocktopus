<?php

namespace App\Filament\Admin\Resources;

use App\Enums\MediaType;
use App\Filament\Fields\MediaTypeSelect;
use App\Filament\Admin\Resources\MediaResource\Pages;
use App\Filament\Admin\Resources\MediaResource\RelationManagers;
use App\Models\Media;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Class MediaResource
 *
 * A Filament resource for managing media items.
 *
 * @package App\Filament\Admin\Resources
 */
class MediaResource extends Resource
{
    /**
     * The model associated with this resource.
     *
     * @var string|null
     */
    protected static ?string $model = Media::class;

    /**
     * The icon to be displayed in the navigation menu.
     *
     * @var string|null
     */
    protected static ?string $navigationIcon = 'heroicon-o-photo';

    /**
     * The navigation group to which this resource belongs.
     *
     * @var string|null
     */
    protected static ?string $navigationGroup = 'Gestion des télés';

    /**
     * The label to be displayed in the navigation menu.
     *
     * @var string|null
     */
    protected static ?string $navigationLabel = 'Médias';

    /**
     * Define the form for creating and updating media items.
     *
     * @param Form $form The Filament form instance.
     *
     * @return Form The modified form.
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                Forms\Components\TextInput::make('name')
                    ->label('Nom')
                    ->required(),
                MediaTypeSelect::make('media_type'),
                Forms\Components\TextInput::make('times')
                    ->label(
                        function (Forms\Get $get) {
                            $type = $get('media_type');
                            if ($type == MediaType::Video->value) {
                                return 'Nombre de répétitions de la vidéo';
                            } elseif ($type == MediaType::Image->value) {
                                return 'Durée d\'affichage en secondes';
                            } else {
                                return '';
                            }
                        }
                    )
                    ->numeric()
                    ->default(1)
                    ->minValue(1),
                Forms\Components\FileUpload::make('media_path')
                    ->label('Média (10Mo max)')
                    ->required()
                    ->acceptedFileTypes(
                        function (Forms\Get $get) {
                            $type = $get('media_type');
                            if ($type == MediaType::Image->value) {
                                $files = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                            } elseif ($type == MediaType::Video->value) {
                                $files = ['video/mp4', 'video/quicktime', 'video/ogg', 'video/webm'];
                            } else {
                                $files = [];
                            }
                            return $files;
                        }
                    ),
                Forms\Components\Checkbox::make('activated')
                    ->label('Activé')
                    ->inline(false)
                    ->default(true),
                ]
            );
    }

    /**
     * Define the table columns and configuration for displaying media items.
     *
     * @param Table $table The Filament table instance.
     *
     * @return Table The modified table.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('media_type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('times')
                        ->label('durée/nombre de repétitions')
                        ->sortable(),
                Tables\Columns\ToggleColumn::make('activated')
                    ->label('Activé')
                    ->sortable(),
                ]
            )
            ->filters(
                [
                //
                ]
            )
            ->actions(
                [
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                ]
            )
            ->bulkActions(
                [
                Tables\Actions\BulkActionGroup::make(
                    [
                    Tables\Actions\DeleteBulkAction::make(),
                    ]
                ),
                ]
            )
            ->emptyStateHeading('Aucun média');
    }

    /**
     * Get the pages associated with this resource.
     *
     * @return array An array of page configurations.
     */
    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\MediaResource\Pages\ManageMedia::route('/'),
        ];
    }
}
