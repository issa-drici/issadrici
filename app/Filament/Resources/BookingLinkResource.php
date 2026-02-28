<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingLinkResource\Pages;
use App\Filament\Resources\BookingLinkResource\RelationManagers;
use App\Models\BookingLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingLinkResource extends Resource
{
    protected static ?string $model = BookingLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';
    
    protected static ?string $navigationLabel = 'Liens de réservation';
    
    protected static ?string $navigationGroup = 'Outbound';
    
    protected static ?int $navigationSort = 6;
    
    protected static ?string $modelLabel = 'Lien de réservation';
    
    protected static ?string $pluralModelLabel = 'Liens de réservation';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informations générales')
                    ->schema([
                        Forms\Components\Select::make('booking_form_id')
                            ->label('Formulaire')
                            ->relationship('bookingForm', 'nom', fn ($query) => $query->where('actif', true))
                            ->required()
                            ->helperText('Formulaire de réservation à utiliser'),
                        Forms\Components\TextInput::make('nom')
                            ->label('Nom du lien')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nom interne pour identifier ce lien'),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->helperText('Description du lien (optionnel)')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('token')
                            ->label('Token (généré automatiquement)')
                            ->disabled()
                            ->dehydrated()
                            ->default(fn () => \Illuminate\Support\Str::random(32))
                            ->helperText('Token unique pour le lien de réservation'),
                        Forms\Components\DateTimePicker::make('date_expiration')
                            ->label('Date d\'expiration')
                            ->helperText('Date après laquelle le lien ne sera plus valide (optionnel)'),
                        Forms\Components\Toggle::make('actif')
                            ->label('Actif')
                            ->default(true)
                            ->helperText('Le lien est-il actif et utilisable ?'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Lien de réservation')
                    ->schema([
                        Forms\Components\TextInput::make('url')
                            ->label('URL du lien')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn ($record) => $record ? route('booking.show', ['token' => $record->token]) : null)
                            ->helperText('Copiez ce lien pour l\'envoyer au prospect')
                            ->columnSpanFull()
                            ->suffixAction(
                                Forms\Components\Actions\Action::make('copy')
                                    ->icon('heroicon-o-clipboard')
                                    ->action(function ($record) {
                                        if ($record) {
                                            return \Illuminate\Support\Facades\Session::flash('copied', route('booking.show', ['token' => $record->token]));
                                        }
                                    })
                            ),
                    ])
                    ->visible(fn ($record) => $record !== null),
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
                Tables\Columns\TextColumn::make('bookingForm.nom')
                    ->label('Formulaire')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Réservations')
                    ->counts('bookings')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_expiration')
                    ->label('Expiration')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color(fn ($record) => $record->isExpired() ? 'danger' : null),
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informations générales')
                    ->schema([
                        Infolists\Components\TextEntry::make('nom')
                            ->label('Nom du lien')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->weight('bold'),
                        Infolists\Components\TextEntry::make('bookingForm.nom')
                            ->label('Formulaire')
                            ->badge()
                            ->color('info'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Description')
                            ->getStateUsing(fn ($record) => $record->description ?: '-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Infolists\Components\Section::make('Lien de réservation')
                    ->schema([
                        Infolists\Components\TextEntry::make('url')
                            ->label('URL du lien')
                            ->getStateUsing(fn ($record) => route('booking.show', ['token' => $record->token]))
                            ->url(fn ($record) => route('booking.show', ['token' => $record->token]))
                            ->openUrlInNewTab()
                            ->copyable()
                            ->icon('heroicon-o-link')
                            ->color('primary')
                            ->weight('bold')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('token')
                            ->label('Token')
                            ->copyable()
                            ->getStateUsing(fn ($record) => $record->token)
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-link')
                    ->description('Copiez ce lien pour l\'envoyer au prospect'),
                
                Infolists\Components\Section::make('Configuration')
                    ->schema([
                        Infolists\Components\TextEntry::make('date_expiration')
                            ->label('Date d\'expiration')
                            ->getStateUsing(fn ($record) => $record->date_expiration ? $record->date_expiration->format('d/m/Y H:i') : 'Aucune expiration')
                            ->color(fn ($record) => $record->isExpired() ? 'danger' : 'gray')
                            ->icon(fn ($record) => $record->isExpired() ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-clock'),
                        Infolists\Components\IconEntry::make('actif')
                            ->label('Actif')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                        Infolists\Components\TextEntry::make('bookings_count')
                            ->label('Nombre de réservations')
                            ->getStateUsing(fn ($record) => $record->bookings()->count())
                            ->badge()
                            ->color('info'),
                    ])
                    ->columns(3),
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
            'index' => Pages\ListBookingLinks::route('/'),
            'create' => Pages\CreateBookingLink::route('/create'),
            'view' => Pages\ViewBookingLink::route('/{record}'),
            'edit' => Pages\EditBookingLink::route('/{record}/edit'),
        ];
    }
}
