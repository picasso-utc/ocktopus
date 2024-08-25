<?php

namespace App\Filament\Admin\Resources;

use App\Enums\MemberRole;
use App\Filament\Fields\UserRoleSelect;
use App\Filament\Admin\Resources\MembersResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MembersResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Admin';
    protected static ?string $navigationLabel = 'Gestion des membres';
    public static ?string $pluralLabel = "Gestion des membres";

    protected static ?int $navigationSort = -3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(
                [
                UserRoleSelect::make('role')
                ]
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(
                [
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => MemberRole::tryFrom($state->value)->title())
                    ->sortable()
                    ->badge()
                    ->color(
                        fn ($state) => match ($state->value) {
                        MemberRole::Administrator->value => 'danger',
                        MemberRole::Member->value => 'warning',
                        MemberRole::None->value => 'gray',
                        }
                    )
                ]
            )
            ->filters(
                [
                Tables\Filters\SelectFilter::make('role')
                    ->options(enum_pluck(MemberRole::class))
                    ->label('Rôle')
                    ->placeholder('Tous les rôles')
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
            ->emptyStateHeading('Aucun membre');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\MembersResource\Pages\ManageMembers::route('/'),
        ];
    }
}
