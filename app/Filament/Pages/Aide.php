<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Aide extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    
    protected static string $view = 'filament.pages.aide';
    
    protected static ?string $navigationLabel = 'Aide';
    
    protected static ?string $navigationGroup = 'Support';
    
    protected static ?int $navigationSort = 999;
    
    protected static ?string $title = 'Guide d\'utilisation du CRM';
    
    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}
