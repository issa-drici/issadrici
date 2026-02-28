<?php

namespace App\Filament\Resources\BookingFormResource\Pages;

use App\Filament\Resources\BookingFormResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookingForm extends EditRecord
{
    protected static string $resource = BookingFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
