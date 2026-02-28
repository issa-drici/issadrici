<?php

namespace App\Filament\Resources\BookingLinkResource\Pages;

use App\Filament\Resources\BookingLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBookingLink extends ViewRecord
{
    protected static string $resource = BookingLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
