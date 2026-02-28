<?php

namespace App\Filament\Resources\PreparationMessageResource\Pages;

use App\Filament\Resources\PreparationMessageResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;

class EditPreparationMessage extends EditRecord
{
    protected static string $resource = PreparationMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
    
    public function form(Forms\Form $form): Forms\Form
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
}
