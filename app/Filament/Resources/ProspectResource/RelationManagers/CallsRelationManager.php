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

class CallsRelationManager extends RelationManager
{
    protected static string $relationship = 'calls';
    
    protected static ?string $title = 'Calls';
    
    protected static ?string $modelLabel = 'Call';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('date_planifiee')
                    ->required()
                    ->label('Date planifiée'),
                Forms\Components\Textarea::make('objectif_call')
                    ->required()
                    ->rows(3)
                    ->label('Objectif du call')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('points_a_verifier')
                    ->rows(3)
                    ->label('Points à vérifier')
                    ->columnSpanFull(),
                Forms\Components\Select::make('statut')
                    ->options([
                        'planifie' => 'Planifié',
                        'realise' => 'Réalisé',
                        'annule' => 'Annulé',
                    ])
                    ->required()
                    ->default('planifie'),
                Forms\Components\DateTimePicker::make('date_realisee')
                    ->label('Date réalisée'),
                Forms\Components\Textarea::make('resultat')
                    ->rows(3)
                    ->label('Résultat')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('prochaine_etape')
                    ->rows(2)
                    ->label('Prochaine étape')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->label('Notes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('objectif_call')
            ->columns([
                Tables\Columns\TextColumn::make('date_planifiee')
                    ->label('Date planifiée')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('objectif_call')
                    ->label('Objectif')
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'planifie' => 'warning',
                        'realise' => 'success',
                        'annule' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'planifie' => 'Planifié',
                        'realise' => 'Réalisé',
                        'annule' => 'Annulé',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('date_realisee')
                    ->label('Date réalisée')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('prochaine_etape')
                    ->label('Prochaine étape')
                    ->limit(50),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statut')
                    ->options([
                        'planifie' => 'Planifié',
                        'realise' => 'Réalisé',
                        'annule' => 'Annulé',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('voir')
                        ->label('Voir')
                        ->icon('heroicon-o-eye')
                        ->url(fn ($record) => \App\Filament\Resources\CallResource::getUrl('view', ['record' => $record]))
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
            ->defaultSort('date_planifiee', 'desc')
            ->recordUrl(fn () => null);
    }
}
