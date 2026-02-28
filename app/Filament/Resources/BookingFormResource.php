<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingFormResource\Pages;
use App\Filament\Resources\BookingFormResource\RelationManagers;
use App\Models\BookingForm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingFormResource extends Resource
{
    protected static ?string $model = BookingForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'Formulaires de réservation';
    
    protected static ?string $navigationGroup = 'Outbound';
    
    protected static ?int $navigationSort = 5;
    
    protected static ?string $modelLabel = 'Formulaire de réservation';
    
    protected static ?string $pluralModelLabel = 'Formulaires de réservation';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\TextInput::make('nom')
                            ->label('Nom du formulaire')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nom interne pour identifier ce formulaire'),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->helperText('Description du formulaire (optionnel)')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('duree_call')
                            ->label('Durée du call (minutes)')
                            ->required()
                            ->numeric()
                            ->default(30)
                            ->minValue(15)
                            ->maxValue(120)
                            ->suffix('min')
                            ->helperText('Durée standard des calls réservés avec ce formulaire'),
                        Forms\Components\Toggle::make('actif')
                            ->label('Actif')
                            ->default(true)
                            ->helperText('Le formulaire est-il actif et utilisable ?'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Créneaux disponibles')
                    ->schema([
                        Forms\Components\Repeater::make('creneaux_disponibles')
                            ->label('Créneaux')
                            ->schema([
                                Forms\Components\Select::make('jour')
                                    ->label('Jour')
                                    ->options([
                                        'lundi' => 'Lundi',
                                        'mardi' => 'Mardi',
                                        'mercredi' => 'Mercredi',
                                        'jeudi' => 'Jeudi',
                                        'vendredi' => 'Vendredi',
                                        'samedi' => 'Samedi',
                                        'dimanche' => 'Dimanche',
                                    ])
                                    ->required(),
                                Forms\Components\TimePicker::make('heure_debut')
                                    ->label('Heure de début')
                                    ->required(),
                                Forms\Components\TimePicker::make('heure_fin')
                                    ->label('Heure de fin')
                                    ->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->helperText('Définissez les créneaux horaires disponibles pour la réservation')
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Champs du formulaire')
                    ->schema([
                        Forms\Components\Repeater::make('champs')
                            ->label('Champs personnalisés')
                            ->schema([
                                Forms\Components\TextInput::make('label')
                                    ->label('Label')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('type')
                                    ->label('Type')
                                    ->options([
                                        'text' => 'Texte',
                                        'email' => 'Email',
                                        'tel' => 'Téléphone',
                                        'textarea' => 'Zone de texte',
                                        'select' => 'Liste déroulante',
                                    ])
                                    ->required()
                                    ->default('text'),
                                Forms\Components\Toggle::make('required')
                                    ->label('Obligatoire')
                                    ->default(false),
                                Forms\Components\Textarea::make('options')
                                    ->label('Options (pour liste déroulante)')
                                    ->rows(2)
                                    ->helperText('Une option par ligne')
                                    ->visible(fn ($get) => $get('type') === 'select'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->helperText('Ajoutez des champs personnalisés au formulaire de réservation')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duree_call')
                    ->label('Durée')
                    ->suffix(' min')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bookingLinks_count')
                    ->label('Liens')
                    ->counts('bookingLinks')
                    ->sortable(),
                Tables\Columns\IconColumn::make('actif')
                    ->label('Actif')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookingForms::route('/'),
            'create' => Pages\CreateBookingForm::route('/create'),
            'view' => Pages\ViewBookingForm::route('/{record}'),
            'edit' => Pages\EditBookingForm::route('/{record}/edit'),
        ];
    }
}
