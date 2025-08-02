<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = null;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Reports & Analytics';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Reports';

    protected static ?string $modelLabel = 'Report';

    protected static ?string $pluralModelLabel = 'Reports';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options([
                        'candidate_performance' => 'Candidate Performance',
                        'evaluation_summary' => 'Evaluation Summary',
                        'system_analytics' => 'System Analytics',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('date_range')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'candidate_performance' => 'success',
                        'evaluation_summary' => 'info',
                        'system_analytics' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('date_range')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'candidate_performance' => 'Candidate Performance',
                        'evaluation_summary' => 'Evaluation Summary',
                        'system_analytics' => 'System Analytics',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
} 