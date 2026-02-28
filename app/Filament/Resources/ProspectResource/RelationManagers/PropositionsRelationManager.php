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

class PropositionsRelationManager extends RelationManager
{
    protected static string $relationship = 'propositions';
    
    protected static ?string $title = 'Propositions';
    
    protected static ?string $modelLabel = 'Proposition';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Détails de la proposition')
                    ->schema([
                        Forms\Components\TextInput::make('montant')
                            ->label('Montant')
                            ->numeric()
                            ->prefix('€')
                            ->required()
                            ->helperText('Montant proposé dans la proposition'),
                        Forms\Components\TextInput::make('duree')
                            ->label('Durée promise')
                            ->placeholder('Ex: 12 mois, 6 mois, 1 an...')
                            ->maxLength(255)
                            ->required()
                            ->helperText('Durée promise dans la proposition (ex: "12 mois", "6 mois")'),
                        Forms\Components\DatePicker::make('date_envoi')
                            ->label('Date d\'envoi')
                            ->default(now())
                            ->helperText('Date à laquelle la proposition a été envoyée'),
                        Forms\Components\Select::make('statut')
                            ->options([
                                'envoyee' => 'Envoyée',
                                'acceptee' => 'Acceptée',
                                'refusee' => 'Refusée',
                                'en_negociation' => 'En négociation',
                            ])
                            ->default('envoyee')
                            ->required()
                            ->helperText('Statut actuel de la proposition'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Détails')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Description de la proposition'),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Notes complémentaires sur cette proposition'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('montant')
            ->columns([
                Tables\Columns\TextColumn::make('montant')
                    ->label('Montant')
                    ->money('EUR', locale: 'fr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('duree')
                    ->label('Durée')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('date_envoi')
                    ->label('Date d\'envoi')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'envoyee' => 'info',
                        'acceptee' => 'success',
                        'refusee' => 'danger',
                        'en_negociation' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'envoyee' => 'Envoyée',
                        'acceptee' => 'Acceptée',
                        'refusee' => 'Refusée',
                        'en_negociation' => 'En négociation',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statut')
                    ->options([
                        'envoyee' => 'Envoyée',
                        'acceptee' => 'Acceptée',
                        'refusee' => 'Refusée',
                        'en_negociation' => 'En négociation',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nouvelle proposition'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
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
            ->defaultSort('date_envoi', 'desc');
    }
}
