<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CandidateResource\Pages;
use App\Models\Candidate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Candidate Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Candidates';
    protected static ?string $modelLabel = 'Candidate';
    protected static ?string $pluralModelLabel = 'Candidates';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')->required()->maxLength(255),
                        Forms\Components\TextInput::make('email')->email()->required()->maxLength(255),
                        Forms\Components\TextInput::make('phone')->tel()->required()->maxLength(20),
                      
                    ])->columns(3),

                Forms\Components\Section::make('Application Details')
                    ->schema([
                        Forms\Components\Select::make('job_id')
                            ->relationship('job', 'title')
                            ->required()
                            ->searchable()
                            ->preload(),

                    //     Forms\Components\Textarea::make('cover_letter')
                    //         ->rows(4)
                    //         ->columnSpanFull(),
                    ]),

                // Forms\Components\Section::make('Documents')
                //     ->schema([
                //         Forms\Components\FileUpload::make('resume_path')
                //             ->label('Resume')
                //             ->directory('resumes')
                //             ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                //             ->maxSize(2048)
                //             ->downloadable()
                //             ->openable(),
                //     ]),

                Forms\Components\Section::make('Resume Evaluation Results')
                    ->schema([
                        Forms\Components\TextInput::make('candidate_id')
                            ->label('Candidate Id')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('score')
                            ->numeric()
                            ->step(0.1)
                            ->minValue(0)
                            ->maxValue(10),

                        Forms\Components\Textarea::make('summary')
                            ->rows(4)
                            ->columnSpanFull(),
  
                      
                    ])->collapsible(),
                Forms\Components\Section::make('Task Evaluation Results')
                    ->schema([

                        Forms\Components\TextInput::make('task_score')
                            ->numeric()
                            ->step(0.1)
                            ->minValue(0)
                            ->maxValue(10),

                        Forms\Components\Textarea::make('task_summary')
                            ->rows(4)
                            ->columnSpanFull(),
  
                      
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('candidate_id')
                    ->label('Candidate ID')
                    ->sortable()
                    ->searchable(),
           Tables\Columns\TextColumn::make('job_id')
                    ->label('Job ID')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($state) => 'JOB' . $state),


                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('phone')->sortable()->searchable(),

                Tables\Columns\TextColumn::make('job.title')
                    ->label('Applied For')
                    ->sortable()
                    ->searchable()
                    ->limit(30),


               Tables\Columns\TextColumn::make('score')
                    ->label('Score')
                    ->sortable()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        $state >= 40 => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): ?string => match (true) {
                        $state >= 80 => 'heroicon-o-check-circle',
                        $state >= 60 => 'heroicon-o-exclamation-circle',
                        $state >= 40 => 'heroicon-o-x-circle',
                        default => null,
                    }),
                    
               Tables\Columns\TextColumn::make('task_score')
                    ->label('Task Score')
                    ->sortable()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        $state >= 40 => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): ?string => match (true) {
                        $state >= 80 => 'heroicon-o-check-circle',
                        $state >= 60 => 'heroicon-o-exclamation-circle',
                        $state >= 40 => 'heroicon-o-x-circle',
                        default => null,
                    }),


            Tables\Columns\TextColumn::make('experience')
                ->label('Experience')
                ->sortable()
                ->searchable(),

             // inside your ->columns([]) array:
            Tables\Columns\TextColumn::make('created_at')
                ->label('Applied On')
                ->sortable()
                ->formatStateUsing(fn ($state) => Carbon::parse($state)->timezone('Asia/Kolkata')->format('M j, Y H:i:s')),

           

                 ])
                 
            ->filters([
                Tables\Filters\SelectFilter::make('job_id')
                    ->label('Job Role')
                    ->relationship('job', 'title')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('has_score')
                    ->label('Evaluated Candidates')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('score')),

                Tables\Filters\Filter::make('high_score')
                    ->label('High Score (80+)')
                    ->query(fn (Builder $query): Builder => $query->where('score', '>=', 80)),

                Tables\Filters\Filter::make('recent_applications')
                    ->label('Recent Applications (Last 7 days)')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7))),
            ])
      ->actions([
            Tables\Actions\ViewAction::make(),

            Action::make('view_resume')
                ->label('View Resume')
                ->icon('heroicon-o-document-text')
                ->url(fn (Candidate $record) => Storage::url($record->resume))
                ->openUrlInNewTab()
                ->visible(fn (Candidate $record) => filled($record->resume)),

           Action::make('download_resume')
                ->label('Download Resume')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (Candidate $record) {
                    $path = storage_path("app/public/{$record->resume}");
                    abort_unless(file_exists($path), 404, "File not found.");
                    return response()->download($path);
                })
                ->requiresConfirmation()
                ->visible(fn (Candidate $record) => filled($record->resume)),

        ])

           ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('export_selected')
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $first = $records->first();
                            $name = preg_replace('/[^A-Za-z0-9]/', '', $first->name);
                            $candidateId = $first->candidate_id ?? 'Candidate';
                            $timestamp = now()->format('Ymd-His');

                            $fileName = "{$candidateId}_{$name}_{$timestamp}.xlsx";

                            return \Maatwebsite\Excel\Facades\Excel::download(
                                new \App\Exports\SelectedCandidatesExport($records),
                                $fileName
                            );
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');

    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCandidates::route('/'),
        ];
    }
}
