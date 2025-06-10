<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\CustomersTable;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';

    public function getColumns(): int | string | array
    {
        return [
            'md' => 1,
            'xl' => 1,
        ];
    }

}
