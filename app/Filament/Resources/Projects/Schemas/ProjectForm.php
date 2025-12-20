<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Project Name')
                ->required(),

            Textarea::make('description')
                ->label('Description')
                ->columnSpanFull(),

            TextInput::make('amount')
                ->numeric()
                ->label('Amount'),

            DatePicker::make('start_date')
                ->label('Start Date'),

            DatePicker::make('end_date')
                ->label('End Date'),
        ]);
    }
}
