<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobResource\Pages;
use App\Models\Job;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Carbon\Carbon;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Job Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Jobs';

    protected static ?string $modelLabel = 'Job';

    protected static ?string $pluralModelLabel = 'Jobs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Job Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $state, callable $set) => $set('slug', Str::slug($state))),
                        
                        Forms\Components\TextInput::make('slug')
                            ->disabled()
                            ->dehydrated(false) // prevent it from being sent in the form data
                            ->label('Slug (Auto-generated)')
                            ->helperText('This will be auto-generated when the job is created.')
                            ->visible(fn ($record) => $record !== null), // show only on edit/view
                        
                        Forms\Components\Select::make('type')
                            ->options([
                                'Full-time' => 'Full-time',
                                'Part-time' => 'Part-time',
                                'Contract' => 'Contract',
                                'Internship' => 'Internship',
                                'Freelance' => 'Freelance',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('experience_level')
                            ->options([
                                'Entry' => 'Entry Level',
                                'Mid' => 'Mid Level',
                                'Senior' => 'Senior Level',
                                'Lead' => 'Lead Level',
                                'Manager' => 'Manager Level',
                            ])
                            ->required(),
                        
                        Forms\Components\TextInput::make('location')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Salary Information')
                    ->schema([
                        Forms\Components\TextInput::make('salary_min')
                            ->numeric()
                            ->prefix('₹')
                            ->maxValue(999999),
                        
                        Forms\Components\TextInput::make('salary_max')
                            ->numeric()
                            ->prefix('₹')
                            ->maxValue(999999),
                        
                        Forms\Components\Select::make('salary_currency')
                            ->options([
                                'USD' => 'USD ($)',
                                'EUR' => 'EUR (€)',
                                'GBP' => 'GBP (£)',
                                'INR' => 'INR (₹)',
                            ])
                            ->default('INR'),
                    ])->columns(3),

                Forms\Components\Section::make('Job Details')
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                        
                        Forms\Components\RichEditor::make('requirements')
                            ->required()
                            ->columnSpanFull(),
                        
                        Forms\Components\RichEditor::make('responsibilities')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\TextInput::make('indeed_apply_url')
                    ->url()
                    ->maxLength(255)
                    ->helperText('Auto-generated from slug')
                    ->afterStateHydrated(function ($component, $state, $record) {
                        if ($record && $record->slug) {
                            $baseUrl = app()->environment('local')
                                ? 'http://127.0.0.1:8000/career/'
                                : 'https://careers.fibrebondindustries.com/career/'; // <-- add /career/
                            $component->state($baseUrl . $record->slug);
                        }
                    })
                    ->disabled(), // prevents manual editing


                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active Job Posting')
                            ->default(true)
                            ->helperText('Inactive jobs won\'t be visible to candidates'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('job_id')
                    ->label('Job ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Full-time' => 'success',
                        'Part-time' => 'info',
                        'Contract' => 'warning',
                        'Internship' => 'primary',
                        'Freelance' => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('experience_level')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Entry' => 'gray',
                        'Mid' => 'info',
                        'Senior' => 'warning',
                        'Lead' => 'success',
                        'Manager' => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('formatted_salary')
                    ->label('Salary')
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('salary_min', $direction)),
                
                Tables\Columns\TextColumn::make('candidates_count')
                    ->label('Applications')
                    ->counts('candidates')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Job Posted On')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->timezone('Asia/Kolkata')->format('M j, Y H:i:s'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
      
                
              
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'Full-time' => 'Full-time',
                        'Part-time' => 'Part-time',
                        'Contract' => 'Contract',
                        'Internship' => 'Internship',
                        'Freelance' => 'Freelance',
                    ]),
                
                Tables\Filters\SelectFilter::make('experience_level')
                    ->options([
                        'Entry' => 'Entry Level',
                        'Mid' => 'Mid Level',
                        'Senior' => 'Senior Level',
                        'Lead' => 'Lead Level',
                        'Manager' => 'Manager Level',
                    ]),
                
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Jobs Only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_applications')
                    ->label('Applications')
                    ->icon('heroicon-o-users')
                    ->url(fn (Job $record): string => route('filament.admin.resources.candidates.index', ['tableFilters[job_id][value]' => $record->id]))
                    ->color('info'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobs::route('/'),
            'create' => Pages\CreateJob::route('/create'),
            'edit' => Pages\EditJob::route('/{record}/edit'),
        ];
    }
} 