<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContenuResource\Pages;
use App\Filament\Resources\ContenuResource\RelationManagers;
use App\Models\Contenu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContenuResource extends Resource
{
    protected static ?string $model = Contenu::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'Contenu';
    
    protected static ?string $navigationGroup = 'Inbound';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $modelLabel = 'Contenu';
    
    protected static ?string $pluralModelLabel = 'Contenus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Type de contenu')
                            ->options([
                                'post' => 'Post',
                                'story' => 'Story',
                                'reel' => 'Reel',
                                'article' => 'Article',
                                'video' => 'Vidéo',
                                'carousel' => 'Carousel',
                            ])
                            ->required()
                            ->default('post')
                            ->helperText('Type de contenu à créer'),
                        Forms\Components\Select::make('plateforme')
                            ->label('Plateforme')
                            ->options([
                                'linkedin' => 'LinkedIn',
                                'instagram' => 'Instagram',
                                'facebook' => 'Facebook',
                                'twitter' => 'Twitter/X',
                                'tiktok' => 'TikTok',
                                'youtube' => 'YouTube',
                            ])
                            ->required()
                            ->default('linkedin')
                            ->helperText('Plateforme sur laquelle publier'),
                        Forms\Components\Select::make('statut')
                            ->label('Statut')
                            ->options([
                                'brouillon' => 'Brouillon',
                                'planifie' => 'Planifié',
                                'publie' => 'Publié',
                                'archive' => 'Archivé',
                            ])
                            ->required()
                            ->default('brouillon')
                            ->helperText('Statut actuel du contenu'),
                    ])
                    ->columns(3),
                
                Forms\Components\Section::make('Stratégie et ciblage')
                    ->schema([
                        Forms\Components\TextInput::make('angle')
                            ->label('Angle choisi')
                            ->maxLength(255)
                            ->helperText('Angle d\'approche pour ce contenu (ex: résoudre un problème, opportunité de croissance, témoignage client...)')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('cible')
                            ->label('Cible')
                            ->maxLength(255)
                            ->helperText('Public cible de ce contenu (ex: startups, PME, dirigeants, responsables marketing...)')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('probleme_vise')
                            ->label('Problème visé')
                            ->rows(3)
                            ->helperText('Problème ou douleur que ce contenu adresse')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('solution_proposee')
                            ->label('Solution proposée')
                            ->rows(3)
                            ->helperText('Solution ou valeur apportée par ce contenu')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('objectif_contenu')
                            ->label('Objectif du contenu')
                            ->rows(2)
                            ->helperText('Objectif principal de ce contenu (ex: générer des leads, éduquer, construire l\'autorité...)')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('call_to_action')
                            ->label('Call to Action (CTA)')
                            ->maxLength(255)
                            ->helperText('Action souhaitée après la lecture (ex: "Réservez un appel", "Téléchargez le guide", "Commentez votre expérience"...')
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Contenu')
                    ->schema([
                        Forms\Components\TextInput::make('titre')
                            ->label('Titre')
                            ->maxLength(255)
                            ->helperText('Titre du contenu (optionnel)')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('contenu')
                            ->label('Contenu')
                            ->required()
                            ->rows(10)
                            ->helperText('Rédigez votre contenu ici')
                            ->columnSpanFull()
                            ->placeholder('Écrivez votre contenu...'),
                        Forms\Components\TextInput::make('image_url')
                            ->label('URL de l\'image')
                            ->url()
                            ->maxLength(255)
                            ->helperText('URL de l\'image à utiliser (optionnel)')
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Planification')
                    ->schema([
                        Forms\Components\DateTimePicker::make('date_publication_planifiee')
                            ->label('Date de publication planifiée')
                            ->helperText('Date et heure prévues pour la publication'),
                        Forms\Components\DateTimePicker::make('date_publication_reelle')
                            ->label('Date de publication réelle')
                            ->helperText('Date et heure réelles de publication (rempli automatiquement)'),
                        Forms\Components\TextInput::make('url_publication')
                            ->label('URL de publication')
                            ->url()
                            ->maxLength(255)
                            ->helperText('Lien vers le contenu publié (optionnel)')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Organisation')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('Tags')
                            ->helperText('Tags pour organiser vos contenus (ex: prospection, tips, cas client...)')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->helperText('Notes complémentaires sur ce contenu')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('engagement_estime')
                            ->label('Engagement estimé')
                            ->numeric()
                            ->helperText('Nombre d\'engagements estimés (likes, commentaires, partages...)'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'post' => 'info',
                        'story' => 'warning',
                        'reel' => 'success',
                        'article' => 'primary',
                        'video' => 'danger',
                        'carousel' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'post' => 'Post',
                        'story' => 'Story',
                        'reel' => 'Reel',
                        'article' => 'Article',
                        'video' => 'Vidéo',
                        'carousel' => 'Carousel',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('plateforme')
                    ->label('Plateforme')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'linkedin' => 'LinkedIn',
                        'instagram' => 'Instagram',
                        'facebook' => 'Facebook',
                        'twitter' => 'Twitter/X',
                        'tiktok' => 'TikTok',
                        'youtube' => 'YouTube',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('titre')
                    ->label('Titre')
                    ->limit(30)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contenu')
                    ->label('Contenu')
                    ->limit(50)
                    ->wrap()
                    ->searchable(),
                Tables\Columns\TextColumn::make('statut')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'brouillon' => 'gray',
                        'planifie' => 'warning',
                        'publie' => 'success',
                        'archive' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'brouillon' => 'Brouillon',
                        'planifie' => 'Planifié',
                        'publie' => 'Publié',
                        'archive' => 'Archivé',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_publication_planifiee')
                    ->label('Pub. planifiée')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('date_publication_reelle')
                    ->label('Pub. réelle')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tags')
                    ->label('Tags')
                    ->badge()
                    ->separator(',')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'post' => 'Post',
                        'story' => 'Story',
                        'reel' => 'Reel',
                        'article' => 'Article',
                        'video' => 'Vidéo',
                        'carousel' => 'Carousel',
                    ]),
                Tables\Filters\SelectFilter::make('plateforme')
                    ->label('Plateforme')
                    ->options([
                        'linkedin' => 'LinkedIn',
                        'instagram' => 'Instagram',
                        'facebook' => 'Facebook',
                        'twitter' => 'Twitter/X',
                        'tiktok' => 'TikTok',
                        'youtube' => 'YouTube',
                    ]),
                Tables\Filters\SelectFilter::make('statut')
                    ->label('Statut')
                    ->options([
                        'brouillon' => 'Brouillon',
                        'planifie' => 'Planifié',
                        'publie' => 'Publié',
                        'archive' => 'Archivé',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
                                    Infolists\Components\TextEntry::make('type')
                                        ->label('Type')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'post' => 'info',
                                            'story' => 'warning',
                                            'reel' => 'success',
                                            'article' => 'primary',
                                            'video' => 'danger',
                                            'carousel' => 'gray',
                                            default => 'gray',
                                        })
                                        ->formatStateUsing(fn (string $state): string => match ($state) {
                                            'post' => 'Post',
                                            'story' => 'Story',
                                            'reel' => 'Reel',
                                            'article' => 'Article',
                                            'video' => 'Vidéo',
                                            'carousel' => 'Carousel',
                                            default => $state,
                                        }),
                                    Infolists\Components\TextEntry::make('plateforme')
                                        ->label('Plateforme')
                                        ->badge()
                                        ->color('gray')
                                        ->formatStateUsing(fn (string $state): string => match ($state) {
                                            'linkedin' => 'LinkedIn',
                                            'instagram' => 'Instagram',
                                            'facebook' => 'Facebook',
                                            'twitter' => 'Twitter/X',
                                            'tiktok' => 'TikTok',
                                            'youtube' => 'YouTube',
                                            default => $state,
                                        }),
                                    Infolists\Components\TextEntry::make('statut')
                                        ->label('Statut')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'brouillon' => 'gray',
                                            'planifie' => 'warning',
                                            'publie' => 'success',
                                            'archive' => 'gray',
                                            default => 'gray',
                                        })
                                        ->formatStateUsing(fn (string $state): string => match ($state) {
                                            'brouillon' => 'Brouillon',
                                            'planifie' => 'Planifié',
                                            'publie' => 'Publié',
                                            'archive' => 'Archivé',
                                            default => $state,
                                        }),
                                ])
                                ->icon('heroicon-o-document-text')
                                ->description('Informations du contenu'),
                            
                            Infolists\Components\Section::make('Stratégie et ciblage')
                                ->schema([
                                    Infolists\Components\TextEntry::make('angle')
                                        ->label('Angle choisi')
                                        ->getStateUsing(fn ($record) => $record->angle ?: '-')
                                        ->icon('heroicon-o-light-bulb')
                                        ->color('primary'),
                                    Infolists\Components\TextEntry::make('cible')
                                        ->label('Cible')
                                        ->getStateUsing(fn ($record) => $record->cible ?: '-')
                                        ->icon('heroicon-o-user-group')
                                        ->color('info'),
                                    Infolists\Components\TextEntry::make('probleme_vise')
                                        ->label('Problème visé')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->probleme_vise ?: '-')
                                        ->wrap(),
                                    Infolists\Components\TextEntry::make('solution_proposee')
                                        ->label('Solution proposée')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->solution_proposee ?: '-')
                                        ->wrap(),
                                    Infolists\Components\TextEntry::make('objectif_contenu')
                                        ->label('Objectif du contenu')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->objectif_contenu ?: '-')
                                        ->wrap(),
                                    Infolists\Components\TextEntry::make('call_to_action')
                                        ->label('Call to Action')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->call_to_action ?: '-')
                                        ->icon('heroicon-o-arrow-right')
                                        ->color('success')
                                        ->weight('bold'),
                                ])
                                ->icon('heroicon-o-light-bulb')
                                ->collapsible(),
                            
                            Infolists\Components\Section::make('Contenu')
                                ->schema([
                                    Infolists\Components\TextEntry::make('titre')
                                        ->label('Titre')
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                                        ->weight('bold')
                                        ->getStateUsing(fn ($record) => $record->titre ?: '-'),
                                    Infolists\Components\TextEntry::make('contenu')
                                        ->label('Contenu')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->contenu ?: '-')
                                        ->wrap(),
                                    Infolists\Components\TextEntry::make('image_url')
                                        ->label('Image')
                                        ->url(fn ($record) => $record->image_url)
                                        ->openUrlInNewTab()
                                        ->getStateUsing(fn ($record) => $record->image_url ? 'Voir l\'image' : '-')
                                        ->visible(fn ($record) => !empty($record->image_url)),
                                ])
                                ->icon('heroicon-o-pencil-square')
                                ->collapsible(),
                            
                            Infolists\Components\Section::make('Planification')
                                ->schema([
                                    Infolists\Components\TextEntry::make('date_publication_planifiee')
                                        ->label('Date de publication planifiée')
                                        ->getStateUsing(fn ($record) => $record->date_publication_planifiee ? $record->date_publication_planifiee->format('d/m/Y H:i') : '-')
                                        ->icon('heroicon-o-calendar')
                                        ->color('warning')
                                        ->weight('bold'),
                                    Infolists\Components\TextEntry::make('date_publication_reelle')
                                        ->label('Date de publication réelle')
                                        ->getStateUsing(fn ($record) => $record->date_publication_reelle ? $record->date_publication_reelle->format('d/m/Y H:i') : '-')
                                        ->icon('heroicon-o-check-circle')
                                        ->color('success')
                                        ->visible(fn ($record) => !empty($record->date_publication_reelle)),
                                    Infolists\Components\TextEntry::make('url_publication')
                                        ->label('URL de publication')
                                        ->url(fn ($record) => $record->url_publication)
                                        ->openUrlInNewTab()
                                        ->getStateUsing(fn ($record) => $record->url_publication ? 'Voir le contenu publié' : '-')
                                        ->visible(fn ($record) => !empty($record->url_publication)),
                                ])
                                ->icon('heroicon-o-clock')
                                ->collapsible(),
                            
                            Infolists\Components\Section::make('Organisation')
                                ->schema([
                                    Infolists\Components\TextEntry::make('tags')
                                        ->label('Tags')
                                        ->badge()
                                        ->separator(',')
                                        ->getStateUsing(fn ($record) => $record->tags ?? []),
                                    Infolists\Components\TextEntry::make('notes')
                                        ->label('Notes')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->notes ?: '-')
                                        ->wrap(),
                                    Infolists\Components\TextEntry::make('engagement_estime')
                                        ->label('Engagement estimé')
                                        ->getStateUsing(fn ($record) => $record->engagement_estime ? number_format($record->engagement_estime, 0, ',', ' ') : '-')
                                        ->icon('heroicon-o-heart')
                                        ->color('primary'),
                                ])
                                ->icon('heroicon-o-tag')
                                ->collapsible(),
                        ])
                        ->columnSpan(2),
                    ]),
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
            'index' => Pages\ListContenus::route('/'),
            'create' => Pages\CreateContenu::route('/create'),
            'view' => Pages\ViewContenu::route('/{record}'),
            'edit' => Pages\EditContenu::route('/{record}/edit'),
        ];
    }
}
