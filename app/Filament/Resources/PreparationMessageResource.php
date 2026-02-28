<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PreparationMessageResource\Pages;
use App\Filament\Resources\PreparationMessageResource\RelationManagers;
use App\Models\PreparationMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PreparationMessageResource extends Resource
{
    protected static ?string $model = PreparationMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    public static function shouldRegisterNavigation(): bool
    {
        return false; // Masquer de la navigation car accessible via RelationManager
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Préparation du message')
                    ->schema([
                        Forms\Components\Textarea::make('message')
                            ->label('Message complet')
                            ->rows(10)
                            ->columnSpanFull()
                            ->required()
                            ->helperText('Rédigez votre message ici. Vous pouvez le modifier et l\'améliorer au fur et à mesure de votre préparation.')
                            ->placeholder('Bonjour [Nom],\n\nJ\'ai remarqué que...'),
                        Forms\Components\TextInput::make('angle_choisi')
                            ->label('Angle choisi')
                            ->maxLength(255)
                            ->helperText('Angle d\'approche choisi pour votre message (ex: résoudre un problème spécifique, opportunité de croissance) - optionnel mais recommandé pour organiser vos messages'),
                        Forms\Components\Textarea::make('observation_utilisee')
                            ->label('Observation utilisée')
                            ->rows(2)
                            ->columnSpanFull()
                            ->helperText('Observation spécifique que vous allez utiliser dans votre message pour personnaliser'),
                        Forms\Components\Textarea::make('question_prevue')
                            ->label('Question prévue')
                            ->rows(2)
                            ->columnSpanFull()
                            ->helperText('Question que vous prévoyez de poser pour engager la conversation'),
                        Forms\Components\Textarea::make('objection_probable')
                            ->label('Objection probable')
                            ->rows(2)
                            ->columnSpanFull()
                            ->helperText('Objection probable que le prospect pourrait soulever, pour préparer votre réponse'),
                    ]),
                
                Forms\Components\Section::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('statut')
                            ->label('Statut')
                            ->options([
                                'en_preparation' => 'En préparation',
                                'utilise' => 'Utilisé',
                                'archive' => 'Archivé',
                            ])
                            ->default('en_preparation')
                            ->required()
                            ->helperText('Statut de cette préparation'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Notes complémentaires sur cette préparation'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('prospect.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('angle_choisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('statut')
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
                                        ->getStateUsing(fn ($record) => $record->prospect ? "{$record->prospect->prenom} {$record->prospect->nom} - {$record->prospect->societe}" : '-')
                                        ->url(fn ($record) => $record->prospect ? \App\Filament\Resources\ProspectResource::getUrl('view', ['record' => $record->prospect]) : null)
                                        ->openUrlInNewTab(),
                                    Infolists\Components\TextEntry::make('angle_choisi')
                                        ->label('Angle choisi')
                                        ->getStateUsing(fn ($record) => $record->angle_choisi ?: '-')
                                        ->badge()
                                        ->color('warning'),
                                    Infolists\Components\TextEntry::make('statut')
                                        ->label('Statut')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'en_preparation' => 'gray',
                                            'utilise' => 'success',
                                            'archive' => 'gray',
                                            default => 'gray',
                                        })
                                        ->formatStateUsing(fn (string $state): string => match ($state) {
                                            'en_preparation' => 'En préparation',
                                            'utilise' => 'Utilisé',
                                            'archive' => 'Archivé',
                                            default => $state,
                                        }),
                                ])
                                ->icon('heroicon-o-document-text'),
                            
                            Infolists\Components\Section::make('Préparation')
                                ->schema([
                                    Infolists\Components\TextEntry::make('observation_utilisee')
                                        ->label('Observation utilisée')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->observation_utilisee ?: '-')
                                        ->markdown(),
                                    Infolists\Components\TextEntry::make('question_prevue')
                                        ->label('Question prévue')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->question_prevue ?: '-')
                                        ->markdown(),
                                    Infolists\Components\TextEntry::make('objection_probable')
                                        ->label('Objection probable')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->objection_probable ?: '-')
                                        ->markdown(),
                                    Infolists\Components\TextEntry::make('message')
                                        ->label('Message complet')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->message ?: '-')
                                        ->markdown(),
                                ])
                                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                                ->collapsible(),
                            
                            Infolists\Components\Section::make('Notes')
                                ->schema([
                                    Infolists\Components\TextEntry::make('notes')
                                        ->label('Notes')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->notes ?: '-')
                                        ->markdown(),
                                ])
                                ->icon('heroicon-o-clipboard-document-list')
                                ->collapsible()
                                ->visible(fn ($record) => !empty($record->notes)),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPreparationMessages::route('/'),
            'create' => Pages\CreatePreparationMessage::route('/create'),
            'view' => Pages\ViewPreparationMessage::route('/{record}'),
            'edit' => Pages\EditPreparationMessage::route('/{record}/edit'),
        ];
    }
}
