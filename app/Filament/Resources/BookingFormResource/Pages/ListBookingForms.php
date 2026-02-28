<?php

namespace App\Filament\Resources\BookingFormResource\Pages;

use App\Filament\Resources\BookingFormResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookingForms extends ListRecords
{
    protected static string $resource = BookingFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
