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

class OpportunitesRelationManager extends RelationManager
{
    protected static string $relationship = 'opportunites';
    
    protected static ?string $title = 'Opportunités';
    
    protected static ?string $modelLabel = 'Opportunité';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('stade')
                    ->options([
                        'decouverte' => 'Découverte',
                        'proposition' => 'Proposition',
                        'negociation' => 'Négociation',
                        'gagne' => 'Gagné',
                        'perdu' => 'Perdu',
                    ])
                    ->required()
                    ->default('decouverte'),
                Forms\Components\TextInput::make('montant_estime')
                    ->label('Montant estimé')
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01),
                Forms\Components\TextInput::make('probabilite')
                    ->label('Probabilité (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0)
                    ->suffix('%'),
                Forms\Components\DatePicker::make('date_estimee_decision')
                    ->label('Date estimée de décision'),
                Forms\Components\Textarea::make('description')
                    ->rows(3)
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
            ->recordTitleAttribute('stade')
            ->columns([
                Tables\Columns\TextColumn::make('stade')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'decouverte' => 'info',
                        'proposition' => 'warning',
                        'negociation' => 'warning',
                        'gagne' => 'success',
                        'perdu' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'decouverte' => 'Découverte',
                        'proposition' => 'Proposition',
                        'negociation' => 'Négociation',
                        'gagne' => 'Gagné',
                        'perdu' => 'Perdu',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('montant_estime')
                    ->label('Montant estimé')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('probabilite')
                    ->label('Probabilité')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_estimee_decision')
                    ->label('Date décision')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->wrap(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('stade')
                    ->options([
                        'decouverte' => 'Découverte',
                        'proposition' => 'Proposition',
                        'negociation' => 'Négociation',
                        'gagne' => 'Gagné',
                        'perdu' => 'Perdu',
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
                        ->url(fn ($record) => \App\Filament\Resources\OpportuniteResource::getUrl('view', ['record' => $record]))
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
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn () => null);
    }
}
