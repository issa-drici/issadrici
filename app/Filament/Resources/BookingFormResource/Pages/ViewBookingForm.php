<?php

namespace App\Filament\Resources\BookingFormResource\Pages;

use App\Filament\Resources\BookingFormResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBookingForm extends ViewRecord
{
    protected static string $resource = BookingFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
