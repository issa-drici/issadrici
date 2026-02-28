<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProspectResource\Pages;
use App\Filament\Resources\ProspectResource\RelationManagers;
use App\Models\Prospect;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProspectResource extends Resource
{
    protected static ?string $model = Prospect::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = 'Prospects';
    
    protected static ?string $navigationGroup = 'Outbound';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $modelLabel = 'Prospect';
    
    protected static ?string $pluralModelLabel = 'Prospects';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identité')
                    ->schema([
                        Forms\Components\TextInput::make('prenom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nom')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('fonction')
                            ->maxLength(255)
                            ->helperText('Fonction ou poste occupé par le prospect'),
                        Forms\Components\TextInput::make('societe')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nom de l\'entreprise du prospect'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Segmentation')
                    ->schema([
                        Forms\Components\TextInput::make('secteur')
                            ->maxLength(255)
                            ->helperText('Secteur d\'activité de l\'entreprise (ex: Tech, Finance, Retail...)'),
                        Forms\Components\TextInput::make('localisation')
                            ->maxLength(255)
                            ->helperText('Ville ou région de l\'entreprise'),
                        Forms\Components\Select::make('taille_estimee')
                            ->options([
                                'startup' => 'Startup',
                                'pme' => 'PME',
                                'eti' => 'ETI',
                                'grand_groupe' => 'Grand Groupe',
                            ])
                            ->helperText('Taille estimée de l\'entreprise pour adapter votre approche'),
                        Forms\Components\TextInput::make('type_entreprise')
                            ->maxLength(255)
                            ->helperText('Type d\'entreprise (ex: SaaS, E-commerce, Agence...)'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Canaux de contact')
                    ->schema([
                        Forms\Components\TextInput::make('linkedin')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('telephone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Select::make('canal_principal')
                            ->options([
                                'linkedin' => 'LinkedIn',
                                'email' => 'Email',
                                'telephone' => 'Téléphone',
                            ])
                            ->helperText('Canal de communication principal utilisé pour ce prospect'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Recherche stratégique')
                    ->schema([
                        Forms\Components\Textarea::make('observations')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Informations clés trouvées lors de votre recherche (actualités, posts LinkedIn, etc.)'),
                        Forms\Components\TextInput::make('signal_declencheur')
                            ->placeholder('Croissance, recrutement, projet...')
                            ->maxLength(255)
                            ->helperText('Signal qui indique que c\'est le bon moment pour contacter (ex: levée de fonds, recrutement, nouveau projet)'),
                        Forms\Components\Textarea::make('hypotheses_organisationnelles')
                            ->rows(2)
                            ->columnSpanFull()
                            ->helperText('Hypothèses sur l\'organisation, les processus, les besoins probables'),
                        Forms\Components\Textarea::make('points_friction_probables')
                            ->rows(2)
                            ->columnSpanFull()
                            ->helperText('Points de friction ou problèmes probables que votre solution pourrait résoudre'),
                    ]),
                
                Forms\Components\Section::make('Pilotage')
                    ->schema([
                        Forms\Components\Select::make('statut')
                            ->options([
                                'a_contacter' => 'À contacter',
                                'contacte' => 'Contacté',
                                'en_discussion' => 'En discussion',
                                'call_planifie' => 'Call planifié',
                                'call_realise' => 'Call réalisé',
                                'proposition_envoyee' => 'Proposition envoyée',
                                'gagne' => 'Gagné',
                                'perdu' => 'Perdu',
                                'en_attente' => 'En attente',
                            ])
                            ->required()
                            ->default('a_contacter')
                            ->helperText('Position du prospect dans votre pipeline de prospection'),
                        Forms\Components\Select::make('niveau_interet')
                            ->options([
                                'neutre' => 'Neutre',
                                'friction_detectee' => 'Friction détectée',
                                'chaud' => 'Chaud',
                            ])
                            ->required()
                            ->default('neutre')
                            ->helperText('Niveau d\'intérêt estimé du prospect basé sur les interactions'),
                        Forms\Components\TextInput::make('prochaine_action')
                            ->maxLength(255)
                            ->helperText('Action concrète à réaliser ensuite (ex: "Relancer dans 3 jours", "Envoyer proposition", "Planifier call")'),
                        Forms\Components\DatePicker::make('date_prochaine_action')
                            ->helperText('Date à laquelle vous devez réaliser la prochaine action'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Budget et valeur')
                    ->schema([
                        Forms\Components\TextInput::make('budget_estime')
                            ->label('Budget estimé')
                            ->numeric()
                            ->prefix('€')
                            ->helperText('Budget estimé du prospect pour ce type de solution'),
                        Forms\Components\Textarea::make('douleur')
                            ->label('Douleur')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Douleur principale identifiée du prospect (problème qu\'il rencontre)'),
                        Forms\Components\TextInput::make('valeur_perdue_actuelle')
                            ->label('Valeur perdue actuellement')
                            ->numeric()
                            ->prefix('€')
                            ->helperText('Valeur de ce que le prospect perd aujourd\'hui sans résoudre son problème (coût de l\'inaction)'),
                        Forms\Components\TextInput::make('valeur_deal')
                            ->label('Valeur du deal')
                            ->numeric()
                            ->prefix('€')
                            ->helperText('Valeur estimée du deal si gagné'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Résultat du deal')
                    ->schema([
                        Forms\Components\TextInput::make('montant_gagne')
                            ->label('Montant gagné')
                            ->numeric()
                            ->prefix('€')
                            ->helperText('Montant réel du deal si c\'est gagné'),
                        Forms\Components\TextInput::make('montant_perdu')
                            ->label('Montant perdu')
                            ->numeric()
                            ->prefix('€')
                            ->helperText('Montant estimé du deal si c\'est perdu'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Grid::make(3)
                    ->schema([
                        // Colonne principale (2/3)
                        Infolists\Components\Group::make([
                            Infolists\Components\Section::make('Identité')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('prenom')
                                                ->label('Prénom')
                                                ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                                            Infolists\Components\TextEntry::make('nom')
                                                ->label('Nom')
                                                ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                                            Infolists\Components\TextEntry::make('fonction')
                                                ->label('Fonction')
                                                ->getStateUsing(fn ($record) => $record->fonction ?: '-')
                                                ->icon('heroicon-o-briefcase'),
                                            Infolists\Components\TextEntry::make('societe')
                                                ->label('Société')
                                                ->getStateUsing(fn ($record) => $record->societe ?: '-')
                                                ->icon('heroicon-o-building-office')
                                                ->weight('bold'),
                                        ]),
                                ])
                                ->icon('heroicon-o-user')
                                ->collapsible(),
                            
                            Infolists\Components\Section::make('Canaux de contact')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('linkedin')
                                                ->label('LinkedIn')
                                                ->getStateUsing(fn ($record) => $record->linkedin ?: '-')
                                                ->url(fn ($record) => $record->linkedin ? $record->linkedin : null, shouldOpenInNewTab: true)
                                                ->icon('heroicon-o-link')
                                                ->color('info'),
                                            Infolists\Components\TextEntry::make('email')
                                                ->label('Email')
                                                ->getStateUsing(fn ($record) => $record->email ?: '-')
                                                ->icon('heroicon-o-envelope')
                                                ->copyable()
                                                ->color('primary'),
                                            Infolists\Components\TextEntry::make('telephone')
                                                ->label('Téléphone')
                                                ->getStateUsing(fn ($record) => $record->telephone ?: '-')
                                                ->icon('heroicon-o-phone')
                                                ->copyable()
                                                ->color('success'),
                                            Infolists\Components\TextEntry::make('canal_principal')
                                                ->label('Canal principal')
                                                ->badge()
                                                ->color('warning')
                                                ->getStateUsing(fn ($record) => match ($record->canal_principal) {
                                                    'linkedin' => 'LinkedIn',
                                                    'email' => 'Email',
                                                    'telephone' => 'Téléphone',
                                                    null => '-',
                                                    default => '-',
                                                }),
                                        ]),
                                ])
                                ->icon('heroicon-o-phone')
                                ->collapsible(),
                            
                            Infolists\Components\Section::make('Recherche stratégique')
                                ->schema([
                                    Infolists\Components\TextEntry::make('observations')
                                        ->label('Observations')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->observations ?: '-')
                                        ->markdown(),
                                    Infolists\Components\TextEntry::make('signal_declencheur')
                                        ->label('Signal déclencheur')
                                        ->badge()
                                        ->color('info')
                                        ->icon('heroicon-o-bolt')
                                        ->getStateUsing(fn ($record) => $record->signal_declencheur ?: '-'),
                                    Infolists\Components\TextEntry::make('hypotheses_organisationnelles')
                                        ->label('Hypothèses organisationnelles')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->hypotheses_organisationnelles ?: '-')
                                        ->markdown(),
                                    Infolists\Components\TextEntry::make('points_friction_probables')
                                        ->label('Points de friction probables')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->points_friction_probables ?: '-')
                                        ->markdown(),
                                ])
                                ->icon('heroicon-o-magnifying-glass')
                                ->collapsible()
                                ->collapsed(),
                            
                            Infolists\Components\Section::make('Budget et valeur')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('budget_estime')
                                                ->label('Budget estimé')
                                                ->getStateUsing(fn ($record) => $record->budget_estime ? number_format($record->budget_estime, 2, ',', ' ') . ' €' : '-')
                                                ->icon('heroicon-o-currency-euro')
                                                ->color('info'),
                                            Infolists\Components\TextEntry::make('valeur_perdue_actuelle')
                                                ->label('Valeur perdue actuellement')
                                                ->getStateUsing(fn ($record) => $record->valeur_perdue_actuelle ? number_format($record->valeur_perdue_actuelle, 2, ',', ' ') . ' €' : '-')
                                                ->icon('heroicon-o-arrow-trending-down')
                                                ->color('danger'),
                                            Infolists\Components\TextEntry::make('valeur_deal')
                                                ->label('Valeur du deal')
                                                ->getStateUsing(fn ($record) => $record->valeur_deal ? number_format($record->valeur_deal, 2, ',', ' ') . ' €' : '-')
                                                ->icon('heroicon-o-arrow-trending-up')
                                                ->color('success')
                                                ->weight('bold'),
                                        ]),
                                    Infolists\Components\TextEntry::make('douleur')
                                        ->label('Douleur')
                                        ->columnSpanFull()
                                        ->getStateUsing(fn ($record) => $record->douleur ?: '-')
                                        ->markdown(),
                                ])
                                ->icon('heroicon-o-banknotes')
                                ->description('Informations financières et valeur'),
                            
                            Infolists\Components\Section::make('Résultat du deal')
                                ->schema([
                                    Infolists\Components\Grid::make(2)
                                        ->schema([
                                            Infolists\Components\TextEntry::make('montant_gagne')
                                                ->label('Montant gagné')
                                                ->getStateUsing(fn ($record) => $record->montant_gagne ? number_format($record->montant_gagne, 2, ',', ' ') . ' €' : '-')
                                                ->icon('heroicon-o-check-circle')
                                                ->color('success')
                                                ->weight('bold'),
                                            Infolists\Components\TextEntry::make('montant_perdu')
                                                ->label('Montant perdu')
                                                ->getStateUsing(fn ($record) => $record->montant_perdu ? number_format($record->montant_perdu, 2, ',', ' ') . ' €' : '-')
                                                ->icon('heroicon-o-x-circle')
                                                ->color('danger'),
                                        ]),
                                ])
                                ->icon('heroicon-o-trophy')
                                ->description('Résultat final du deal'),
                        ])
                        ->columnSpan(2),
                        
                        // Colonne latérale (1/3) - Informations importantes
                        Infolists\Components\Group::make([
                            Infolists\Components\Section::make('Statut & Pilotage')
                                ->schema([
                                    Infolists\Components\TextEntry::make('statut')
                                        ->label('Statut')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'a_contacter' => 'gray',
                                            'contacte' => 'info',
                                            'en_discussion' => 'warning',
                                            'call_planifie' => 'warning',
                                            'call_realise' => 'info',
                                            'proposition_envoyee' => 'success',
                                            'gagne' => 'success',
                                            'perdu' => 'danger',
                                            'en_attente' => 'gray',
                                            default => 'gray',
                                        })
                                        ->formatStateUsing(fn (string $state): string => match ($state) {
                                            'a_contacter' => 'À contacter',
                                            'contacte' => 'Contacté',
                                            'en_discussion' => 'En discussion',
                                            'call_planifie' => 'Call planifié',
                                            'call_realise' => 'Call réalisé',
                                            'proposition_envoyee' => 'Proposition envoyée',
                                            'gagne' => 'Gagné',
                                            'perdu' => 'Perdu',
                                            'en_attente' => 'En attente',
                                            default => $state,
                                        })
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                                    Infolists\Components\TextEntry::make('niveau_interet')
                                        ->label('Niveau d\'intérêt')
                                        ->badge()
                                        ->color(fn (string $state): string => match ($state) {
                                            'neutre' => 'gray',
                                            'friction_detectee' => 'warning',
                                            'chaud' => 'success',
                                            default => 'gray',
                                        })
                                        ->formatStateUsing(fn (string $state): string => match ($state) {
                                            'neutre' => 'Neutre',
                                            'friction_detectee' => 'Friction détectée',
                                            'chaud' => 'Chaud',
                                            default => $state,
                                        })
                                        ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                                    Infolists\Components\TextEntry::make('prochaine_action')
                                        ->label('Prochaine action')
                                        ->icon('heroicon-o-arrow-right')
                                        ->weight('bold')
                                        ->color('primary')
                                        ->getStateUsing(fn ($record) => $record->prochaine_action ?: '-'),
                                    Infolists\Components\TextEntry::make('date_prochaine_action')
                                        ->label('Date prochaine action')
                                        ->getStateUsing(fn ($record) => $record->date_prochaine_action ? $record->date_prochaine_action->format('d/m/Y') : '-')
                                        ->icon('heroicon-o-calendar')
                                        ->color(fn ($record) => $record->date_prochaine_action && $record->date_prochaine_action->isPast() ? 'danger' : ($record->date_prochaine_action ? 'success' : 'gray'))
                                        ->weight('bold'),
                                ])
                                ->icon('heroicon-o-chart-bar')
                                ->description('État actuel du prospect'),
                            
                            Infolists\Components\Section::make('Segmentation')
                                ->schema([
                                    Infolists\Components\TextEntry::make('secteur')
                                        ->label('Secteur')
                                        ->badge()
                                        ->color('info')
                                        ->getStateUsing(fn ($record) => $record->secteur ?: '-'),
                                    Infolists\Components\TextEntry::make('localisation')
                                        ->label('Localisation')
                                        ->icon('heroicon-o-map-pin')
                                        ->getStateUsing(fn ($record) => $record->localisation ?: '-'),
                                    Infolists\Components\TextEntry::make('taille_estimee')
                                        ->label('Taille estimée')
                                        ->badge()
                                        ->color('warning')
                                        ->getStateUsing(fn ($record) => match ($record->taille_estimee) {
                                            'startup' => 'Startup',
                                            'pme' => 'PME',
                                            'eti' => 'ETI',
                                            'grand_groupe' => 'Grand Groupe',
                                            null => '-',
                                            default => $record->taille_estimee ?? '-',
                                        }),
                                    Infolists\Components\TextEntry::make('type_entreprise')
                                        ->label('Type d\'entreprise')
                                        ->badge()
                                        ->color('gray')
                                        ->getStateUsing(fn ($record) => $record->type_entreprise ?: '-'),
                                ])
                                ->icon('heroicon-o-tag')
                                ->collapsible(),
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
                Tables\Columns\TextColumn::make('nom_complet')
                    ->label('Nom')
                    ->getStateUsing(fn (Prospect $record) => "{$record->prenom} {$record->nom}")
                    ->searchable(['prenom', 'nom'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('societe')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'a_contacter' => 'gray',
                        'contacte' => 'info',
                        'en_discussion' => 'warning',
                        'call_planifie' => 'warning',
                        'call_realise' => 'info',
                        'proposition_envoyee' => 'success',
                        'gagne' => 'success',
                        'perdu' => 'danger',
                        'en_attente' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'a_contacter' => 'À contacter',
                        'contacte' => 'Contacté',
                        'en_discussion' => 'En discussion',
                        'call_planifie' => 'Call planifié',
                        'call_realise' => 'Call réalisé',
                        'proposition_envoyee' => 'Proposition envoyée',
                        'gagne' => 'Gagné',
                        'perdu' => 'Perdu',
                        'en_attente' => 'En attente',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('canal_principal')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'linkedin' => 'LinkedIn',
                        'email' => 'Email',
                        'telephone' => 'Téléphone',
                        default => '-',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('derniere_interaction')
                    ->label('Dernière interaction')
                    ->getStateUsing(function (Prospect $record) {
                        $interaction = $record->interactions()->latest('date')->first();
                        return $interaction ? $interaction->date->format('d/m/Y') : '-';
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(
                            \App\Models\Interaction::select('date')
                                ->whereColumn('interactions.prospect_id', 'prospects.id')
                                ->latest('date')
                                ->limit(1),
                            $direction
                        );
                    }),
                Tables\Columns\TextColumn::make('prochaine_action')
                    ->label('Prochaine action')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_prochaine_action')
                    ->label('Date prochaine action')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : null),
                Tables\Columns\TextColumn::make('niveau_interet')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'neutre' => 'gray',
                        'friction_detectee' => 'warning',
                        'chaud' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'neutre' => 'Neutre',
                        'friction_detectee' => 'Friction détectée',
                        'chaud' => 'Chaud',
                        default => $state,
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statut')
                    ->options([
                        'a_contacter' => 'À contacter',
                        'contacte' => 'Contacté',
                        'en_discussion' => 'En discussion',
                        'call_planifie' => 'Call planifié',
                        'call_realise' => 'Call réalisé',
                        'proposition_envoyee' => 'Proposition envoyée',
                        'gagne' => 'Gagné',
                        'perdu' => 'Perdu',
                        'en_attente' => 'En attente',
                    ])
                    ->label('Statut'),
                Tables\Filters\SelectFilter::make('niveau_interet')
                    ->options([
                        'neutre' => 'Neutre',
                        'friction_detectee' => 'Friction détectée',
                        'chaud' => 'Chaud',
                    ])
                    ->label('Niveau d\'intérêt'),
                Tables\Filters\Filter::make('a_traiter')
                    ->label('À traiter')
                    ->query(fn (Builder $query): Builder => $query->whereIn('statut', ['a_contacter', 'contacte', 'en_discussion'])),
                Tables\Filters\Filter::make('en_discussion')
                    ->label('En discussion')
                    ->query(fn (Builder $query): Builder => $query->where('statut', 'en_discussion')),
                Tables\Filters\Filter::make('calls_a_venir')
                    ->label('Calls à venir')
                    ->query(fn (Builder $query): Builder => $query->where('statut', 'call_planifie')),
                Tables\Filters\Filter::make('sans_reponse')
                    ->label('Sans réponse')
                    ->query(fn (Builder $query): Builder => $query->whereIn('statut', ['contacte', 'en_discussion'])
                        ->where('date_prochaine_action', '<', now())),
                Tables\Filters\Filter::make('propositions_en_cours')
                    ->label('Propositions en cours')
                    ->query(fn (Builder $query): Builder => $query->where('statut', 'proposition_envoyee')),
            ])
            ->recordUrl(fn ($record) => static::getUrl('view', ['record' => $record]))
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('marquer_message_envoye')
                        ->label('Message envoyé')
                        ->icon('heroicon-o-paper-airplane')
                        ->form([
                            Forms\Components\Select::make('type')
                                ->label('Type de message')
                                ->options([
                                    'linkedin_message' => 'LinkedIn - Message',
                                    'email' => 'Email',
                                ])
                                ->required()
                                ->default(fn (Prospect $record) => $record->canal_principal === 'linkedin' ? 'linkedin_message' : 'email'),
                            Forms\Components\TextInput::make('resume')
                                ->label('Résumé')
                                ->maxLength(255)
                                ->helperText('Résumé court du message envoyé (optionnel)'),
                        ])
                        ->action(function (Prospect $record, array $data) {
                            $record->interactions()->create([
                                'type' => $data['type'],
                                'date' => now(),
                                'statut' => 'envoye',
                                'resume' => $data['resume'] ?? null,
                            ]);
                            $record->update([
                                'statut' => 'contacte',
                                'prochaine_action' => 'Attendre réponse',
                                'date_prochaine_action' => now()->addDays(3),
                            ]);
                        })
                        ->successNotificationTitle('Message envoyé enregistré')
                        ->after(function (Prospect $record) {
                            return redirect(static::getUrl('view', ['record' => $record]));
                        }),
                    Tables\Actions\Action::make('enregistrer_reponse')
                        ->label('Réponse reçue')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->form([
                            Forms\Components\Select::make('resultat')
                                ->options([
                                    'positif' => 'Positif',
                                    'neutre' => 'Neutre',
                                    'negatif' => 'Négatif',
                                ])
                                ->required(),
                            Forms\Components\TextInput::make('prochaine_action')
                                ->required(),
                            Forms\Components\DatePicker::make('date_prochaine_action')
                                ->required(),
                        ])
                        ->action(function (Prospect $record, array $data) {
                            $record->update([
                                'statut' => 'en_discussion',
                                'prochaine_action' => $data['prochaine_action'],
                                'date_prochaine_action' => $data['date_prochaine_action'],
                            ]);
                        })
                        ->successNotificationTitle('Réponse enregistrée')
                        ->after(function (Prospect $record) {
                            return redirect(static::getUrl('view', ['record' => $record]));
                        }),
                    Tables\Actions\Action::make('planifier_call')
                        ->label('Planifier call')
                        ->icon('heroicon-o-phone')
                        ->form([
                            Forms\Components\DateTimePicker::make('date_planifiee')
                                ->required(),
                            Forms\Components\Textarea::make('objectif_call')
                                ->required(),
                        ])
                        ->action(function (Prospect $record, array $data) {
                            $datePlanifiee = is_string($data['date_planifiee']) 
                                ? \Carbon\Carbon::parse($data['date_planifiee']) 
                                : $data['date_planifiee'];
                            
                            $record->calls()->create([
                                'date_planifiee' => $datePlanifiee,
                                'objectif_call' => $data['objectif_call'],
                                'statut' => 'planifie',
                            ]);
                            $record->update([
                                'statut' => 'call_planifie',
                                'prochaine_action' => 'Call planifié',
                                'date_prochaine_action' => $datePlanifiee->format('Y-m-d'),
                            ]);
                        })
                        ->successNotificationTitle('Call planifié')
                        ->after(function (Prospect $record) {
                            return redirect(static::getUrl('view', ['record' => $record]));
                        }),
                    Tables\Actions\Action::make('envoyer_proposition')
                        ->label('Envoyer proposition')
                        ->icon('heroicon-o-document-text')
                        ->form([
                            Forms\Components\TextInput::make('montant_proposition')
                                ->label('Montant de la proposition')
                                ->numeric()
                                ->prefix('€')
                                ->required()
                                ->helperText('Montant proposé dans la proposition'),
                            Forms\Components\TextInput::make('duree_proposition')
                                ->label('Durée promise')
                                ->placeholder('Ex: 12 mois, 6 mois, 1 an...')
                                ->maxLength(255)
                                ->required()
                                ->helperText('Durée promise dans la proposition (ex: "12 mois", "6 mois")'),
                        ])
                        ->action(function (Prospect $record, array $data) {
                            // Créer une nouvelle proposition
                            $proposition = $record->propositions()->create([
                                'montant' => $data['montant_proposition'],
                                'duree' => $data['duree_proposition'],
                                'date_envoi' => now(),
                                'statut' => 'envoyee',
                            ]);
                            
                            // Mettre à jour le prospect
                            $record->update([
                                'statut' => 'proposition_envoyee',
                                'montant_proposition' => $data['montant_proposition'], // Garder pour référence rapide
                                'duree_proposition' => $data['duree_proposition'], // Garder pour référence rapide
                                'prochaine_action' => 'Suivre proposition',
                                'date_prochaine_action' => now()->addDays(5),
                            ]);
                            
                            // Créer ou mettre à jour une opportunité
                            $opportunite = $record->opportunites()->firstOrNew();
                            $opportunite->stade = 'proposition';
                            $opportunite->montant_estime = $data['montant_proposition'];
                            $opportunite->save();
                        })
                        ->successNotificationTitle('Proposition enregistrée')
                        ->after(function (Prospect $record) {
                            return redirect(static::getUrl('view', ['record' => $record]));
                        }),
                    Tables\Actions\Action::make('marquer_gagne')
                        ->label('Gagné')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->form([
                            Forms\Components\TextInput::make('montant_gagne')
                                ->label('Montant gagné')
                                ->numeric()
                                ->prefix('€')
                                ->required()
                                ->helperText('Montant réel du deal gagné'),
                        ])
                        ->action(function (Prospect $record, array $data) {
                            $record->update([
                                'statut' => 'gagne',
                                'montant_gagne' => $data['montant_gagne'],
                            ]);
                            
                            // Créer ou mettre à jour une opportunité
                            $opportunite = $record->opportunites()->firstOrNew();
                            $opportunite->stade = 'gagne';
                            $opportunite->montant_estime = $data['montant_gagne'];
                            $opportunite->probabilite = 100;
                            $opportunite->save();
                        })
                        ->successNotificationTitle('Deal gagné enregistré')
                        ->after(function (Prospect $record) {
                            return redirect(static::getUrl('view', ['record' => $record]));
                        }),
                    Tables\Actions\Action::make('marquer_perdu')
                        ->label('Perdu')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\TextInput::make('montant_perdu')
                                ->label('Montant perdu')
                                ->numeric()
                                ->prefix('€')
                                ->helperText('Montant estimé du deal perdu (optionnel)'),
                        ])
                        ->action(function (Prospect $record, array $data) {
                            $record->update([
                                'statut' => 'perdu',
                                'montant_perdu' => $data['montant_perdu'] ?? null,
                            ]);
                            
                            // Créer ou mettre à jour une opportunité
                            $opportunite = $record->opportunites()->firstOrNew();
                            $opportunite->stade = 'perdu';
                            if (isset($data['montant_perdu'])) {
                                $opportunite->montant_estime = $data['montant_perdu'];
                            }
                        $opportunite->probabilite = 0;
                        $opportunite->save();
                        })
                        ->successNotificationTitle('Deal perdu enregistré')
                        ->after(function (Prospect $record) {
                            return redirect(static::getUrl('view', ['record' => $record]));
                        }),
                    Tables\Actions\EditAction::make(),
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
            ->defaultSort('date_prochaine_action', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PreparationsMessagesRelationManager::class,
            RelationManagers\InteractionsRelationManager::class,
            RelationManagers\CallsRelationManager::class,
            RelationManagers\PropositionsRelationManager::class,
            RelationManagers\OpportunitesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProspects::route('/'),
            'create' => Pages\CreateProspect::route('/create'),
            'view' => Pages\ViewProspect::route('/{record}'),
            'edit' => Pages\EditProspect::route('/{record}/edit'),
        ];
    }
}
