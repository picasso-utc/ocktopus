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

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Gestion des télés';

    protected static ?string $navigationLabel = 'Médias';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom')
                    ->required(),
                MediaTypeSelect::make('media_type'),
                Forms\Components\TextInput::make('times')
                    ->label(function (Forms\Get $get) {
                        $type = $get('media_type');
                        if ($type == MediaType::Video->value) {
                            return 'Nombre de répétitions de la vidéo';
                        } else if ($type == MediaType::Image->value) {
                            return 'Durée d\'affichage en secondes';
                        } else {
                            return '';
                        }
                    })
                    ->numeric()
                    ->default(1)
                    ->minValue(1),
                Forms\Components\FileUpload::make('media_path')
                    ->label('Média')
                    ->required()
                    ->acceptedFileTypes(function (Forms\Get $get) {
                        $type = $get('media_type');
                        if ($type == MediaType::Image->value) {
                            $files = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                        } else if ($type == MediaType::Video->value) {
                            $files = ['video/mp4', 'video/quicktime', 'video/ogg', 'video/webm'];
                        } else {
                            $files = [];
                        }
                        return $files;
                    }),
                Forms\Components\Checkbox::make('activated')
                    ->label('Activé')
                    ->inline(false)
                    ->default(true),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('media_type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('activated')
                    ->label('Activé')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\MediaResource\Pages\ManageMedia::route('/'),
        ];
    }
}
