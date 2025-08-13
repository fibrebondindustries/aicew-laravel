<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BasicApplicationResource\Pages;
use App\Models\BasicApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class BasicApplicationResource extends Resource
{
    protected static ?string $model            = BasicApplication::class;
    protected static ?string $navigationIcon   = 'heroicon-o-user-group';
    protected static ?string $navigationGroup  = 'Candidate Management';
    protected static ?int    $navigationSort   = 1;
    protected static ?string $navigationLabel  = 'Applications';
    protected static ?string $modelLabel       = 'Applications';
    protected static ?string $pluralModelLabel = 'Applications';

    public static function form(Form $form): Form
    {
        // read-only details form
        return $form->schema([
            Forms\Components\TextInput::make('candidate_id')->disabled()->dehydrated(false),
            Forms\Components\TextInput::make('full_name')->disabled()->dehydrated(false),
            Forms\Components\TextInput::make('email')->disabled()->dehydrated(false),
            Forms\Components\TextInput::make('mobile')->disabled()->dehydrated(false),
            Forms\Components\TextInput::make('location')->disabled()->dehydrated(false),
            Forms\Components\TextInput::make('years_of_experience')->disabled()->dehydrated(false),
            Forms\Components\TextInput::make('current_salary')->disabled()->dehydrated(false),
            Forms\Components\TextInput::make('expected_salary')->disabled()->dehydrated(false),
            Forms\Components\TextInput::make('notice_period')->disabled()->dehydrated(false),
            Forms\Components\TextInput::make('portfolio_link')->disabled()->dehydrated(false),
            // Forms\Components\TextInput::make('resume_path')->disabled()->dehydrated(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('candidate_id')
                    ->label('Candidate ID')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Candidate ID copied'),

                Tables\Columns\TextColumn::make('job_id')
                    ->label('Job ID')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Job ID copied'),   
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')->sortable()->searchable()->limit(40),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()->toggleable()->limit(40),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('years_of_experience')
                    ->label('YoE')->sortable(),
                Tables\Columns\TextColumn::make('current_salary')
                    ->label('Current Salary')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('expected_salary')
                  ->label('Expected Salary')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('notice_period')
                  ->label('Notice Period')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('portfolio_link')
                  ->label('Portfolio Link')
                    ->searchable()->sortable(),
               Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied')
                    ->sortable()
                    ->dateTime('M j, Y H:i', timezone: 'Asia/Kolkata')

            ])
            ->filters([
                Tables\Filters\Filter::make('recent')
                    ->label('Last 7 days')
                    ->query(fn (Builder $query): Builder =>
                        $query->where('created_at', '>=', now()->subDays(7))
                    ),
                 Filter::make('job_id')
                    ->label('Job ID')
                    ->form([
                        Forms\Components\TextInput::make('value')->label('Job ID'),
                    ])
                    ->query(fn (Builder $query, array $data) =>
                        ! empty($data['value'])
                            ? $query->where('job_id', $data['value'])
                            : $query
                    ),

            ])
            ->actions([
                Tables\Actions\Action::make('download_resume')
                    ->label('Resume')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn (BasicApplication $r) => filled($r->resume_path))
                    ->url(fn (BasicApplication $r) => Storage::url($r->resume_path), shouldOpenInNewTab: true),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(false),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBasicApplications::route('/'),
            'view'  => Pages\ViewBasicApplication::route('/{record}'),
        ];
    }
}
