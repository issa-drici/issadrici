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

class PreparationsMessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'preparationsMessages';
    
    protected static ?string $title = 'Préparations de messages';
    
    protected static ?string $modelLabel = 'Préparation de message';

    protected static bool $shouldCheckPolicyExistence = false;
    
    protected static bool $shouldSkipAuthorization = true;

    protected function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        \Log::info('PreparationsMessagesRelationManager::canEdit', [
            'record_id' => $record->id,
            'should_skip' => static::shouldSkipAuthorization(),
        ]);
        return true;
    }

    protected function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        \Log::info('PreparationsMessagesRelationManager::canDelete', [
            'record_id' => $record->id,
            'should_skip' => static::shouldSkipAuthorization(),
        ]);
        return true;
    }

    public function form(Form $form): Form
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
                            ->rows(2)
                            ->columnSpanFull()
                            ->helperText('Observation spécifique que vous allez utiliser dans votre message pour personnaliser'),
                        Forms\Components\Textarea::make('question_prevue')
                            ->rows(2)
                            ->columnSpanFull()
                            ->helperText('Question que vous prévoyez de poser pour engager la conversation'),
                        Forms\Components\Textarea::make('objection_probable')
                            ->rows(2)
                            ->columnSpanFull()
                            ->helperText('Objection probable que le prospect pourrait soulever, pour préparer votre réponse'),
                    ]),
                
                Forms\Components\Section::make('Statut')
                    ->schema([
                        Forms\Components\Select::make('statut')
                            ->options([
                                'en_preparation' => 'En préparation',
                                'utilise' => 'Utilisé',
                                'archive' => 'Archivé',
                            ])
                            ->default('en_preparation')
                            ->helperText('Statut de cette préparation'),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Notes complémentaires sur cette préparation'),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        \Log::info('PreparationsMessagesRelationManager::table called', [
            'should_skip_auth' => static::shouldSkipAuthorization(),
            'should_check_policy' => static::shouldCheckPolicyExistence(),
        ]);
        
        return $table
            ->recordTitleAttribute('message')
            ->columns([
                Tables\Columns\TextColumn::make('angle_choisi')
                    ->label('Angle choisi')
                    ->formatStateUsing(fn (?string $state): string => $state ?? '—')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->weight('bold')
                    ->size('lg'),
                Tables\Columns\TextColumn::make('statut')
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
                Tables\Columns\TextColumn::make('observation_utilisee')
                    ->label('Observation utilisée')
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('question_prevue')
                    ->label('Question prévue')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->limit(150)
                    ->wrap()
                    ->searchable()
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statut')
                    ->options([
                        'en_preparation' => 'En préparation',
                        'utilise' => 'Utilisé',
                        'archive' => 'Archivé',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Nouvelle préparation')
                    ->icon('heroicon-o-plus')
                    ->button(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('voir')
                        ->label('Voir')
                        ->icon('heroicon-o-eye')
                        ->url(fn ($record) => \App\Filament\Resources\PreparationMessageResource::getUrl('view', ['record' => $record]))
                        ->openUrlInNewTab(),
                    Tables\Actions\EditAction::make()
                        ->authorize(fn ($record) => true),
                    Tables\Actions\DeleteAction::make()
                        ->authorize(fn ($record) => true),
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
            ->emptyStateHeading('Aucune préparation de message')
            ->emptyStateDescription('Commencez par créer votre première préparation de message pour ce prospect.')
            ->emptyStateIcon('heroicon-o-chat-bubble-left-ellipsis')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Créer une préparation')
                    ->icon('heroicon-o-plus'),
            ]);
    }
}
