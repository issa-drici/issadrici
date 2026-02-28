<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InteractionResource\Pages;
use App\Filament\Resources\InteractionResource\RelationManagers;
use App\Models\Interaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InteractionResource extends Resource
{
    protected static ?string $model = Interaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationLabel = 'Interactions';
    
    protected static ?string $navigationGroup = 'Outbound';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $modelLabel = 'Interaction';
    
    protected static ?string $pluralModelLabel = 'Interactions';

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
                            ->helperText('Prospect concerné par cette interaction'),
                        Forms\Components\Select::make('type')
                            ->options([
                                'linkedin_invitation' => 'LinkedIn - Invitation',
                                'linkedin_message' => 'LinkedIn - Message',
                                'email' => 'Email',
                                'relance' => 'Relance',
                                'proposition_envoyee' => 'Proposition envoyée',
                            ])
                            ->required()
                            ->helperText('Type d\'interaction réalisée avec le prospect'),
                        Forms\Components\DateTimePicker::make('date')
                            ->required()
                            ->default(now())
                            ->helperText('Date et heure de l\'interaction'),
                        Forms\Components\Select::make('statut')
                            ->options([
                                'prevu' => 'Prévu',
                                'envoye' => 'Envoyé',
                                'repondu' => 'Répondu',
                                'termine' => 'Terminé',
                            ])
                            ->required()
                            ->default('envoye')
                            ->helperText('Statut actuel de l\'interaction'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Résultat')
                    ->schema([
                        Forms\Components\TextInput::make('resume')
                            ->label('Résumé')
                            ->maxLength(255)
                            ->helperText('Résumé court de l\'interaction (une phrase)')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('resultat')
                            ->options([
                                'positif' => 'Positif',
                                'neutre' => 'Neutre',
                                'negatif' => 'Négatif',
                            ])
                            ->helperText('Résultat de l\'interaction du point de vue du prospect'),
                        Forms\Components\Textarea::make('notes')
                            ->rows(4)
                            ->helperText('Notes détaillées sur l\'interaction, contexte, points importants')
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
                                        ->getStateUsing(fn (Interaction $record) => "{$record->prospect->prenom} {$record->prospect->nom} - {$record->prospect->societe}")
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                        ->weight('bold')
                                        ->icon('heroicon-o-user'),
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('type')
                                                ->label('Type')
                                                ->badge()
                                                ->color('info')
                                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                                    'linkedin_invitation' => 'LinkedIn - Invitation',
                                                    'linkedin_message' => 'LinkedIn - Message',
                                                    'email' => 'Email',
                                                    'relance' => 'Relance',
                                                    'proposition_envoyee' => 'Proposition envoyée',
                                                    default => $state,
                                                }),
                                            Infolists\Components\TextEntry::make('statut')
                                                ->label('Statut')
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
                                        ]),
                                    Infolists\Components\TextEntry::make('date')
                                        ->label('Date')
                                        ->dateTime('d/m/Y H:i')
                                        ->icon('heroicon-o-calendar')
                                        ->color('primary'),
                                ])
                                ->icon('heroicon-o-information-circle')
                                ->description('Détails de l\'interaction'),
                            
                            Infolists\Components\Section::make('Résultat')
                                ->schema([
                                    Infolists\Components\TextEntry::make('resume')
                                        ->label('Résumé')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->resume ?: '-')
                                        ->markdown(),
                                    Infolists\Components\TextEntry::make('resultat')
                                        ->label('Résultat')
                                        ->badge()
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                        ->color(fn ($record) => match ($record->resultat) {
                                            'positif' => 'success',
                                            'neutre' => 'gray',
                                            'negatif' => 'danger',
                                            default => 'gray',
                                        })
                                        ->getStateUsing(fn ($record) => match ($record->resultat) {
                                            'positif' => 'Positif',
                                            'neutre' => 'Neutre',
                                            'negatif' => 'Négatif',
                                            null => '-',
                                            default => '-',
                                        }),
                                    Infolists\Components\TextEntry::make('notes')
                                        ->label('Notes')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->notes ?: '-')
                                        ->markdown(),
                                ])
                                ->icon('heroicon-o-clipboard-document-check')
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
                    ->getStateUsing(fn (Interaction $record) => "{$record->prospect->prenom} {$record->prospect->nom} - {$record->prospect->societe}")
                    ->searchable(['prospect.prenom', 'prospect.nom', 'prospect.societe'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'linkedin_invitation' => 'LinkedIn - Invitation',
                        'linkedin_message' => 'LinkedIn - Message',
                        'email' => 'Email',
                        'relance' => 'Relance',
                        'proposition_envoyee' => 'Proposition envoyée',
                        default => $state,
                    })
                    ->searchable(),
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
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('resume')
                    ->label('Résumé')
                    ->limit(50)
                    ->searchable(),
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
                    })
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'linkedin_invitation' => 'LinkedIn - Invitation',
                        'linkedin_message' => 'LinkedIn - Message',
                        'email' => 'Email',
                        'relance' => 'Relance',
                        'proposition_envoyee' => 'Proposition envoyée',
                    ]),
                Tables\Filters\SelectFilter::make('statut')
                    ->options([
                        'prevu' => 'Prévu',
                        'envoye' => 'Envoyé',
                        'repondu' => 'Répondu',
                        'termine' => 'Terminé',
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
            ->defaultSort('date', 'desc');
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
            'index' => Pages\ListInteractions::route('/'),
            'create' => Pages\CreateInteraction::route('/create'),
            'view' => Pages\ViewInteraction::route('/{record}'),
            'edit' => Pages\EditInteraction::route('/{record}/edit'),
        ];
    }
}
