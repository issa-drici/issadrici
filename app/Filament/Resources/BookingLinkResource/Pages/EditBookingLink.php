<?php

namespace App\Filament\Resources\BookingLinkResource\Pages;

use App\Filament\Resources\BookingLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookingLink extends EditRecord
{
    protected static string $resource = BookingLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
