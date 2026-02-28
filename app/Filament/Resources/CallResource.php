<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CallResource\Pages;
use App\Filament\Resources\CallResource\RelationManagers;
use App\Models\Call;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CallResource extends Resource
{
    protected static ?string $model = Call::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';
    
    protected static ?string $navigationLabel = 'Calls';
    
    protected static ?string $navigationGroup = 'Outbound';
    
    protected static ?int $navigationSort = 3;
    
    protected static ?string $modelLabel = 'Call';
    
    protected static ?string $pluralModelLabel = 'Calls';

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
                            ->helperText('Prospect avec qui le call est prévu ou réalisé'),
                        Forms\Components\Select::make('statut')
                            ->options([
                                'planifie' => 'Planifié',
                                'realise' => 'Réalisé',
                                'annule' => 'Annulé',
                            ])
                            ->required()
                            ->default('planifie')
                            ->helperText('Statut actuel du call'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Planification')
                    ->schema([
                        Forms\Components\DateTimePicker::make('date_planifiee')
                            ->required()
                            ->helperText('Date et heure prévues pour le call'),
                        Forms\Components\Textarea::make('objectif_call')
                            ->required()
                            ->rows(3)
                            ->helperText('Objectif principal du call (ce que vous voulez accomplir)')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('points_a_verifier')
                            ->rows(3)
                            ->label('Points à vérifier')
                            ->helperText('Points spécifiques à vérifier ou questions à poser pendant le call')
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Résultat')
                    ->schema([
                        Forms\Components\DateTimePicker::make('date_realisee')
                            ->label('Date réalisée')
                            ->helperText('Date et heure réelles du call si différent de la planification'),
                        Forms\Components\Textarea::make('resultat')
                            ->rows(4)
                            ->label('Résultat')
                            ->helperText('Résultat du call, points abordés, décisions prises')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('prochaine_etape')
                            ->rows(2)
                            ->label('Prochaine étape')
                            ->helperText('Action à réaliser suite au call (obligatoire après chaque call)')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->label('Notes')
                            ->helperText('Notes complémentaires sur le call')
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
                                        ->getStateUsing(fn (Call $record) => "{$record->prospect->prenom} {$record->prospect->nom} - {$record->prospect->societe}")
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                        ->weight('bold')
                                        ->icon('heroicon-o-user')
                                        ->url(fn (Call $record) => $record->prospect 
                                            ? \App\Filament\Resources\ProspectResource::getUrl('view', ['record' => $record->prospect])
                                            : null)
                                        ->openUrlInNewTab(),
                                    Infolists\Components\TextEntry::make('statut')
                                        ->label('Statut')
                                        ->badge()
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
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
                                ])
                                ->icon('heroicon-o-phone')
                                ->description('Informations du call'),
                            
                            Infolists\Components\Section::make('Planification')
                                ->schema([
                                    Infolists\Components\TextEntry::make('date_planifiee')
                                        ->label('Date planifiée')
                                        ->getStateUsing(fn ($record) => $record->date_planifiee ? $record->date_planifiee->format('d/m/Y H:i') : '-')
                                        ->icon('heroicon-o-calendar')
                                        ->color('warning')
                                        ->weight('bold'),
                                    Infolists\Components\TextEntry::make('objectif_call')
                                        ->label('Objectif du call')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->objectif_call ?: '-'),
                                    Infolists\Components\TextEntry::make('points_a_verifier')
                                        ->label('Points à vérifier')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->points_a_verifier ?: '-'),
                                ])
                                ->icon('heroicon-o-clipboard-document-list')
                                ->collapsible(),
                            
                            Infolists\Components\Section::make('Données du formulaire de réservation')
                                ->schema(function ($record) {
                                    $donnees = $record->booking?->donnees_formulaire ?? [];
                                    if (empty($donnees)) {
                                        return [];
                                    }
                                    
                                    $schema = [];
                                    foreach ($donnees as $key => $value) {
                                        $schema[] = Infolists\Components\TextEntry::make("donnees_formulaire_{$key}")
                                            ->label($key)
                                            ->getStateUsing(fn () => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : (string)$value)
                                            ->copyable()
                                            ->columnSpanFull();
                                    }
                                    
                                    return $schema;
                                })
                                ->icon('heroicon-o-document-text')
                                ->collapsible()
                                ->visible(fn ($record) => $record->booking && !empty($record->booking->donnees_formulaire)),
                            
                            Infolists\Components\Section::make('Résultat')
                                ->schema([
                                    Infolists\Components\TextEntry::make('date_realisee')
                                        ->label('Date réalisée')
                                        ->getStateUsing(fn ($record) => $record->date_realisee ? $record->date_realisee->format('d/m/Y H:i') : '-')
                                        ->icon('heroicon-o-check-circle')
                                        ->color('success'),
                                    Infolists\Components\TextEntry::make('resultat')
                                        ->label('Résultat')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->resultat ?: '-'),
                                    Infolists\Components\TextEntry::make('prochaine_etape')
                                        ->label('Prochaine étape')
                                        ->icon('heroicon-o-arrow-right')
                                        ->weight('bold')
                                        ->color('primary')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->prochaine_etape ?: '-'),
                                    Infolists\Components\TextEntry::make('notes')
                                        ->label('Notes')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->notes ?: '-'),
                                ])
                                ->icon('heroicon-o-check-circle')
                                ->collapsible(),
                        ])
                        ->columnSpan(2),
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
                    ->getStateUsing(fn (Call $record) => "{$record->prospect->prenom} {$record->prospect->nom} - {$record->prospect->societe}")
                    ->searchable(['prospect.prenom', 'prospect.nom', 'prospect.societe'])
                    ->sortable(),
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
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_realisee')
                    ->label('Date réalisée')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('prochaine_etape')
                    ->label('Prochaine étape')
                    ->limit(50),
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
                Tables\Filters\SelectFilter::make('statut')
                    ->options([
                        'planifie' => 'Planifié',
                        'realise' => 'Réalisé',
                        'annule' => 'Annulé',
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
            ->defaultSort('date_planifiee', 'desc');
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
            'index' => Pages\ListCalls::route('/'),
            'create' => Pages\CreateCall::route('/create'),
            'view' => Pages\ViewCall::route('/{record}'),
            'edit' => Pages\EditCall::route('/{record}/edit'),
        ];
    }
}
