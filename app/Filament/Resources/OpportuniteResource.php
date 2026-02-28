<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpportuniteResource\Pages;
use App\Filament\Resources\OpportuniteResource\RelationManagers;
use App\Models\Opportunite;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OpportuniteResource extends Resource
{
    protected static ?string $model = Opportunite::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    
    protected static ?string $navigationLabel = 'Opportunités';
    
    protected static ?string $navigationGroup = 'Outbound';
    
    protected static ?int $navigationSort = 4;
    
    protected static ?string $modelLabel = 'Opportunité';
    
    protected static ?string $pluralModelLabel = 'Opportunités';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\Select::make('prospect_id')
                            ->relationship('prospect', 'societe', fn ($query) => $query->orderBy('societe'))
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->prenom} {$record->nom} - {$record->societe}")
                            ->searchable(['prenom', 'nom', 'societe'])
                            ->required()
                            ->label('Prospect')
                            ->helperText('Prospect concerné par cette opportunité'),
                        Forms\Components\Select::make('stade')
                            ->options([
                                'decouverte' => 'Découverte',
                                'proposition' => 'Proposition',
                                'negociation' => 'Négociation',
                                'gagne' => 'Gagné',
                                'perdu' => 'Perdu',
                            ])
                            ->required()
                            ->default('decouverte')
                            ->helperText('Stade actuel de l\'opportunité dans le processus de vente'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Estimation')
                    ->schema([
                        Forms\Components\TextInput::make('montant_estime')
                            ->label('Montant estimé')
                            ->numeric()
                            ->prefix('€')
                            ->step(0.01)
                            ->helperText('Montant estimé de l\'opportunité en euros'),
                        Forms\Components\TextInput::make('probabilite')
                            ->label('Probabilité (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required()
                            ->default(0)
                            ->suffix('%')
                            ->helperText('Probabilité de clôture de l\'opportunité (0-100%)'),
                        Forms\Components\DatePicker::make('date_estimee_decision')
                            ->label('Date estimée de décision')
                            ->helperText('Date à laquelle vous estimez que la décision sera prise'),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Détails')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(4)
                            ->label('Description')
                            ->helperText('Description détaillée de l\'opportunité, contexte, besoins identifiés')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->label('Notes')
                            ->helperText('Notes complémentaires sur l\'opportunité')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(2)
                    ->schema([
                        Infolists\Components\Group::make([
                            Infolists\Components\Section::make('Informations générales')
                                ->schema([
                                    Infolists\Components\TextEntry::make('prospect.nom_complet')
                                        ->label('Prospect')
                                        ->getStateUsing(fn (Opportunite $record) => "{$record->prospect->prenom} {$record->prospect->nom} - {$record->prospect->societe}")
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                        ->weight('bold')
                                        ->icon('heroicon-o-user'),
                                    Infolists\Components\TextEntry::make('stade')
                                        ->label('Stade')
                                        ->badge()
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
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
                                        }),
                                ])
                                ->icon('heroicon-o-briefcase')
                                ->description('Détails de l\'opportunité'),
                            
                            Infolists\Components\Section::make('Détails')
                                ->schema([
                                    Infolists\Components\TextEntry::make('description')
                                        ->label('Description')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->description ?: '-')
                                        ->markdown(),
                                    Infolists\Components\TextEntry::make('notes')
                                        ->label('Notes')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->notes ?: '-')
                                        ->markdown(),
                                ])
                                ->icon('heroicon-o-document-text')
                                ->collapsible(),
                        ])
                        ->columnSpan(2),
                        
                        Infolists\Components\Group::make([
                            Infolists\Components\Section::make('Estimation')
                                ->schema([
                                    Infolists\Components\TextEntry::make('montant_estime')
                                        ->label('Montant estimé')
                                        ->money('EUR')
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                        ->weight('bold')
                                        ->color('success')
                                        ->icon('heroicon-o-currency-euro'),
                                    Infolists\Components\TextEntry::make('probabilite')
                                        ->label('Probabilité')
                                        ->suffix('%')
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                        ->weight('bold')
                                        ->color('info')
                                        ->icon('heroicon-o-chart-bar'),
                                    Infolists\Components\TextEntry::make('date_estimee_decision')
                                        ->label('Date estimée de décision')
                                        ->date('d/m/Y')
                                        ->icon('heroicon-o-calendar')
                                        ->color('warning')
                                        ->weight('bold'),
                                ])
                                ->icon('heroicon-o-calculator')
                                ->description('Valeur et probabilité'),
                        ])
                        ->columnSpan(1),
                    ]),
                
                // Historique des modifications
                Infolists\Components\Section::make('Historique des modifications')
                    ->schema([
                        Infolists\Components\ViewEntry::make('activity_log')
                            ->label('')
                            ->view('filament.infolists.components.activity-log')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-clock')
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('prospect.nom_complet')
                    ->label('Prospect')
                    ->getStateUsing(fn (Opportunite $record) => "{$record->prospect->prenom} {$record->prospect->nom} - {$record->prospect->societe}")
                    ->searchable(['prospect.prenom', 'prospect.nom', 'prospect.societe'])
                    ->sortable(),
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
                    ->searchable()
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
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->recordUrl(fn ($record) => static::getUrl('view', ['record' => $record]))
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOpportunites::route('/'),
            'create' => Pages\CreateOpportunite::route('/create'),
            'view' => Pages\ViewOpportunite::route('/{record}'),
            'edit' => Pages\EditOpportunite::route('/{record}/edit'),
        ];
    }
}
