<?php

namespace App\Filament\Resources;

use App\Enums\MemberRole;
use App\Filament\Fields\UserRoleSelect;
use App\Filament\Resources\MembersResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MembersResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'General';

    protected static ?string $navigationLabel = 'Gestion des membres';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                UserRoleSelect::make('role')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => MemberRole::tryFrom($state->value)->title())
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match ($state->value) {
                        MemberRole::Administrator->value => 'danger',
                        MemberRole::Member->value => 'warning',
                        MemberRole::None->value => 'gray',
                    })
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options(enum_pluck(MemberRole::class))
                    ->label('Rôle')
                    ->placeholder('Tous les rôles')
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
            'index' => Pages\ManageMembers::route('/'),
        ];
    }
}
