<?php

namespace App\Filament\Resources\ProspectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InteractionsRelationManager extends RelationManager
{
    protected static string $relationship = 'interactions';
    
    protected static ?string $title = 'Interactions';
    
    protected static ?string $modelLabel = 'Interaction';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options([
                        'linkedin_invitation' => 'LinkedIn - Invitation',
                        'linkedin_message' => 'LinkedIn - Message',
                        'email' => 'Email',
                        'relance' => 'Relance',
                        'proposition_envoyee' => 'Proposition envoyée',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('date')
                    ->required()
                    ->default(now()),
                Forms\Components\Select::make('statut')
                    ->options([
                        'prevu' => 'Prévu',
                        'envoye' => 'Envoyé',
                        'repondu' => 'Répondu',
                        'termine' => 'Terminé',
                    ])
                    ->required()
                    ->default('envoye'),
                Forms\Components\TextInput::make('resume')
                    ->label('Résumé')
                    ->maxLength(255),
                Forms\Components\Select::make('resultat')
                    ->options([
                        'positif' => 'Positif',
                        'neutre' => 'Neutre',
                        'negatif' => 'Négatif',
                    ]),
                Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'linkedin_invitation' => 'LinkedIn - Invitation',
                        'linkedin_message' => 'LinkedIn - Message',
                        'email' => 'Email',
                        'relance' => 'Relance',
                        'proposition_envoyee' => 'Proposition envoyée',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'prevu' => 'gray',
                        'envoye' => 'info',
                        'repondu' => 'success',
                        'termine' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'prevu' => 'Prévu',
                        'envoye' => 'Envoyé',
                        'repondu' => 'Répondu',
                        'termine' => 'Terminé',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('resume')
                    ->label('Résumé')
                    ->limit(50),
                Tables\Columns\TextColumn::make('resultat')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'positif' => 'success',
                        'neutre' => 'gray',
                        'negatif' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'positif' => 'Positif',
                        'neutre' => 'Neutre',
                        'negatif' => 'Négatif',
                        default => '-',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('voir')
                        ->label('Voir')
                        ->icon('heroicon-o-eye')
                        ->url(fn ($record) => \App\Filament\Resources\InteractionResource::getUrl('view', ['record' => $record]))
                        ->openUrlInNewTab(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->icon('heroicon-o-ellipsis-vertical')
                ->label('Actions')
                ->color('gray')
                ->button(),
            ])
            ->actionsPosition(ActionsPosition::AfterCells)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc')
            ->recordUrl(fn () => null);
    }
}
