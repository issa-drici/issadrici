<?php

namespace App\Filament\Resources\BookingFormResource\Pages;

use App\Filament\Resources\BookingFormResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBookingForm extends CreateRecord
{
    protected static string $resource = BookingFormResource::class;
}
