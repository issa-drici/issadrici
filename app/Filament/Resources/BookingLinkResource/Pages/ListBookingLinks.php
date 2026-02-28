<?php

namespace App\Filament\Resources\BookingLinkResource\Pages;

use App\Filament\Resources\BookingLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookingLinks extends ListRecords
{
    protected static string $resource = BookingLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
