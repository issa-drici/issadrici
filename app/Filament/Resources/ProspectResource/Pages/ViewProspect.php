<?php

namespace App\Filament\Resources\ProspectResource\Pages;

use App\Filament\Resources\ProspectResource;
use App\Models\Prospect;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;

class ViewProspect extends ViewRecord
{
    protected static string $resource = ProspectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('nouvelle_preparation_message')
                    ->label('Préparer un message')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('warning')
                    ->form([
                        Forms\Components\Textarea::make('message')
                            ->label('Message complet')
                            ->rows(10)
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
                            ->helperText('Observation spécifique que vous allez utiliser dans votre message pour personnaliser'),
                        Forms\Components\Textarea::make('question_prevue')
                            ->label('Question prévue')
                            ->rows(2)
                            ->helperText('Question que vous prévoyez de poser pour engager la conversation'),
                        Forms\Components\Textarea::make('objection_probable')
                            ->label('Objection probable')
                            ->rows(2)
                            ->helperText('Objection probable que le prospect pourrait soulever, pour préparer votre réponse'),
                    ])
                    ->action(function (Prospect $record, array $data) {
                        $record->preparationsMessages()->create([
                            'angle_choisi' => $data['angle_choisi'],
                            'observation_utilisee' => $data['observation_utilisee'] ?? null,
                            'question_prevue' => $data['question_prevue'] ?? null,
                            'objection_probable' => $data['objection_probable'] ?? null,
                            'message' => $data['message'] ?? null,
                            'statut' => 'en_preparation',
                        ]);
                    })
                    ->successNotificationTitle('Préparation de message créée')
                    ->after(function () {
                        $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                    }),
                Actions\Action::make('marquer_message_envoye')
                    ->label('Message envoyé')
                    ->icon('heroicon-o-paper-airplane')
                    ->form([
                        Forms\Components\Select::make('preparation_message_id')
                            ->label('Message préparé utilisé')
                            ->relationship('preparationsMessages', 'angle_choisi', fn ($query) => $query->where('statut', 'en_preparation'))
                            ->getOptionLabelFromRecordUsing(fn ($record) => ($record->angle_choisi ?? 'Sans angle') . ($record->message ? ' (' . \Illuminate\Support\Str::limit($record->message, 30) . '...)' : ''))
                            ->searchable(['angle_choisi', 'message'])
                            ->helperText('Sélectionnez le message préparé que vous avez envoyé')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $preparation = \App\Models\PreparationMessage::find($state);
                                    if ($preparation) {
                                        $set('type', $preparation->prospect->canal_principal === 'linkedin' ? 'linkedin_message' : 'email');
                                        $set('resume', $preparation->angle_choisi);
                                    }
                                }
                            }),
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
                            ->helperText('Résumé court du message envoyé'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->helperText('Notes complémentaires sur le message envoyé (optionnel)')
                            ->columnSpanFull(),
                    ])
                    ->action(function (Prospect $record, array $data) {
                        // Créer l'interaction
                        $record->interactions()->create([
                            'type' => $data['type'],
                            'date' => now(),
                            'statut' => 'envoye',
                            'resume' => $data['resume'] ?? null,
                            'notes' => $data['notes'] ?? null,
                        ]);
                        
                        // Marquer le message préparé comme utilisé
                        if (isset($data['preparation_message_id'])) {
                            $preparation = $record->preparationsMessages()->find($data['preparation_message_id']);
                            if ($preparation) {
                                $preparation->update(['statut' => 'utilise']);
                            }
                        }
                        
                        // Mettre à jour le prospect
                        $record->update([
                            'statut' => 'contacte',
                            'prochaine_action' => 'Attendre réponse',
                            'date_prochaine_action' => now()->addDays(3),
                        ]);
                    })
                    ->visible(fn (Prospect $record) => $record->preparationsMessages()->where('statut', 'en_preparation')->exists()),
                Actions\Action::make('marquer_message_envoye_simple')
                    ->label('Message envoyé (sans préparation)')
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
                            ->helperText('Résumé court du message envoyé'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->helperText('Notes complémentaires sur le message envoyé (optionnel)')
                            ->columnSpanFull(),
                    ])
                    ->action(function (Prospect $record, array $data) {
                        $record->interactions()->create([
                            'type' => $data['type'],
                            'date' => now(),
                            'statut' => 'envoye',
                            'resume' => $data['resume'] ?? null,
                            'notes' => $data['notes'] ?? null,
                        ]);
                        $record->update([
                            'statut' => 'contacte',
                            'prochaine_action' => 'Attendre réponse',
                            'date_prochaine_action' => now()->addDays(3),
                        ]);
                    })
                    ->visible(fn (Prospect $record) => !$record->preparationsMessages()->where('statut', 'en_preparation')->exists()),
                Actions\Action::make('enregistrer_reponse')
                    ->label('Réponse reçue')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->form([
                        Forms\Components\Select::make('resultat')
                            ->label('Résultat de la réponse')
                            ->options([
                                'positif' => 'Positif',
                                'neutre' => 'Neutre',
                                'negatif' => 'Négatif',
                            ])
                            ->required()
                            ->helperText('Comment qualifieriez-vous la réponse reçue ?'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes sur la réponse')
                            ->rows(3)
                            ->helperText('Détails de la réponse reçue (optionnel)'),
                        Forms\Components\TextInput::make('prochaine_action')
                            ->label('Prochaine action')
                            ->required()
                            ->helperText('Quelle est la prochaine étape ?'),
                        Forms\Components\DatePicker::make('date_prochaine_action')
                            ->label('Date de la prochaine action')
                            ->required(),
                    ])
                    ->action(function (Prospect $record, array $data) {
                        // Créer une interaction pour enregistrer la réponse
                        $record->interactions()->create([
                            'type' => 'email', // Type générique pour une réponse
                            'date' => now(),
                            'statut' => 'repondu',
                            'resultat' => $data['resultat'],
                            'notes' => $data['notes'] ?? null,
                            'resume' => 'Réponse reçue - ' . ucfirst($data['resultat']),
                        ]);
                        
                        // Mettre à jour le prospect
                        $record->update([
                            'statut' => 'en_discussion',
                            'prochaine_action' => $data['prochaine_action'],
                            'date_prochaine_action' => $data['date_prochaine_action'],
                        ]);
                    })
                    ->successNotificationTitle('Réponse enregistrée')
                    ->after(function () {
                        $this->refreshFormData(['statut', 'prochaine_action', 'date_prochaine_action']);
                    }),
                Actions\Action::make('planifier_call')
                    ->label('Planifier call')
                    ->icon('heroicon-o-phone')
                    ->form([
                        Forms\Components\DateTimePicker::make('date_planifiee')
                            ->required()
                            ->label('Date planifiée')
                            ->helperText('Date et heure prévues pour le call'),
                        Forms\Components\Textarea::make('objectif_call')
                            ->required()
                            ->label('Objectif du call')
                            ->rows(3)
                            ->helperText('Objectif principal du call (ce que vous voulez accomplir)')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('points_a_verifier')
                            ->label('Points à vérifier')
                            ->rows(3)
                            ->helperText('Points spécifiques à vérifier ou questions à poser pendant le call (optionnel)')
                            ->columnSpanFull(),
                    ])
                    ->action(function (Prospect $record, array $data) {
                        $datePlanifiee = is_string($data['date_planifiee'])
                            ? \Carbon\Carbon::parse($data['date_planifiee'])
                            : $data['date_planifiee'];
                        
                        $record->calls()->create([
                            'date_planifiee' => $datePlanifiee,
                            'objectif_call' => $data['objectif_call'],
                            'points_a_verifier' => $data['points_a_verifier'] ?? null,
                            'statut' => 'planifie',
                        ]);
                        $record->update([
                            'statut' => 'call_planifie',
                            'prochaine_action' => 'Call planifié',
                            'date_prochaine_action' => $datePlanifiee->format('Y-m-d'),
                        ]);
                    })
                    ->successNotificationTitle('Call planifié')
                    ->after(function () {
                        $this->refreshFormData(['statut', 'prochaine_action', 'date_prochaine_action']);
                    }),
                Actions\Action::make('creer_opportunite')
                    ->label('Créer une opportunité')
                    ->icon('heroicon-o-briefcase')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('stade')
                            ->label('Stade de l\'opportunité')
                            ->options([
                                'decouverte' => 'Découverte',
                                'proposition' => 'Proposition',
                                'negociation' => 'Négociation',
                            ])
                            ->required()
                            ->default('decouverte')
                            ->helperText('À quel stade en êtes-vous avec ce prospect ?'),
                        Forms\Components\TextInput::make('montant_estime')
                            ->label('Montant estimé')
                            ->numeric()
                            ->prefix('€')
                            ->helperText('Montant estimé de cette opportunité (optionnel)'),
                        Forms\Components\TextInput::make('probabilite')
                            ->label('Probabilité (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0)
                            ->suffix('%')
                            ->helperText('Probabilité de conclure ce deal'),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->helperText('Description de l\'opportunité (optionnel)')
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('date_estimee_decision')
                            ->label('Date estimée de décision')
                            ->helperText('Date à laquelle vous estimez que la décision sera prise (optionnel)'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->helperText('Notes complémentaires sur l\'opportunité (optionnel)')
                            ->columnSpanFull(),
                    ])
                    ->action(function (Prospect $record, array $data) {
                        $record->opportunites()->create([
                            'stade' => $data['stade'],
                            'montant_estime' => $data['montant_estime'] ?? null,
                            'probabilite' => $data['probabilite'] ?? 0,
                            'description' => $data['description'] ?? null,
                            'date_estimee_decision' => $data['date_estimee_decision'] ?? null,
                            'notes' => $data['notes'] ?? null,
                        ]);
                    })
                    ->successNotificationTitle('Opportunité créée')
                    ->after(function () {
                        $this->redirect(static::getResource()::getUrl('view', ['record' => $this->record]));
                    }),
                Actions\Action::make('envoyer_proposition')
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
                        Forms\Components\Textarea::make('description')
                            ->label('Description de la proposition')
                            ->rows(3)
                            ->helperText('Description détaillée de la proposition (optionnel)')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->helperText('Notes complémentaires sur la proposition (optionnel)')
                            ->columnSpanFull(),
                    ])
                    ->action(function (Prospect $record, array $data) {
                        // Créer une nouvelle proposition
                        $record->propositions()->create([
                            'montant' => $data['montant_proposition'],
                            'duree' => $data['duree_proposition'],
                            'description' => $data['description'] ?? null,
                            'notes' => $data['notes'] ?? null,
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
                    ->after(function () {
                        $this->refreshFormData(['statut', 'montant_proposition', 'duree_proposition', 'prochaine_action', 'date_prochaine_action']);
                    }),
                Actions\Action::make('marquer_gagne')
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
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->helperText('Notes complémentaires sur le deal gagné (optionnel)')
                            ->columnSpanFull(),
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
                        $opportunite->notes = $data['notes'] ?? null;
                        $opportunite->save();
                    })
                    ->successNotificationTitle('Deal gagné enregistré')
                    ->after(function () {
                        $this->refreshFormData(['statut', 'montant_gagne']);
                    }),
                Actions\Action::make('marquer_perdu')
                    ->label('Perdu')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\TextInput::make('montant_perdu')
                            ->label('Montant perdu')
                            ->numeric()
                            ->prefix('€')
                            ->helperText('Montant estimé du deal perdu (optionnel)'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->helperText('Raison de la perte ou notes complémentaires (optionnel)')
                            ->columnSpanFull(),
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
                        $opportunite->notes = $data['notes'] ?? null;
                        $opportunite->save();
                    })
                    ->successNotificationTitle('Deal perdu enregistré')
                    ->after(function () {
                        $this->refreshFormData(['statut', 'montant_perdu']);
                    }),
                Actions\Action::make('creer_lien_reservation')
                    ->label('Créer un lien de réservation')
                    ->icon('heroicon-o-link')
                    ->color('info')
                    ->form([
                        Forms\Components\Select::make('booking_form_id')
                            ->label('Formulaire de réservation')
                            ->options(fn () => \App\Models\BookingForm::where('actif', true)->pluck('nom', 'id'))
                            ->required()
                            ->searchable()
                            ->helperText('Sélectionnez le formulaire à utiliser pour ce lien'),
                        Forms\Components\TextInput::make('nom_lien')
                            ->label('Nom du lien')
                            ->maxLength(255)
                            ->default(fn (Prospect $record) => "Lien pour {$record->prenom} {$record->nom}")
                            ->helperText('Nom interne pour identifier ce lien'),
                        Forms\Components\DateTimePicker::make('date_expiration')
                            ->label('Date d\'expiration')
                            ->helperText('Date après laquelle le lien ne sera plus valide (optionnel)'),
                    ])
                    ->action(function (Prospect $record, array $data) {
                        \App\Models\BookingLink::create([
                            'booking_form_id' => $data['booking_form_id'],
                            'nom' => $data['nom_lien'] ?? "Lien pour {$record->prenom} {$record->nom}",
                            'date_expiration' => $data['date_expiration'] ?? null,
                            'actif' => true,
                        ]);
                    })
                    ->successNotificationTitle('Lien de réservation créé'),
            ])
            ->label('Actions rapides')
            ->icon('heroicon-o-bolt')
            ->color('primary')
            ->button(),
            Actions\EditAction::make(),
        ];
    }
}
