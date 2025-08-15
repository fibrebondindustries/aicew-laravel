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

use App\Http\Controllers\TaskMailController;
use Filament\Notifications\Notification;


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
    return $form->schema([

        // Personal Information
        Forms\Components\Section::make('Personal Information')
            ->schema([
                Forms\Components\TextInput::make('full_name')->label('Name')->disabled()->dehydrated(false),
                Forms\Components\TextInput::make('email')->label('Email')->disabled()->dehydrated(false),
                Forms\Components\TextInput::make('mobile')->label('Phone')->disabled()->dehydrated(false),
                Forms\Components\TextInput::make('gender')->label('Gender')->disabled()->dehydrated(false),
                Forms\Components\TextInput::make('location')->label('Location')->disabled()->dehydrated(false),
            ])
            ->columns(3),

        // Application Details
        Forms\Components\Section::make('Application Details')
            ->schema([
                Forms\Components\TextInput::make('job_id')->label('Job ID')->disabled()->dehydrated(false),
                Forms\Components\TextInput::make('job_role')->label('Job Role')->disabled()->dehydrated(false),
                Forms\Components\TextInput::make('years_of_experience')->label('Years of experience')->disabled()->dehydrated(false),
                Forms\Components\TextInput::make('current_salary')->label('Current salary')->disabled()->dehydrated(false),
                Forms\Components\TextInput::make('expected_salary')->label('Expected salary')->disabled()->dehydrated(false),
                Forms\Components\TextInput::make('notice_period')->label('Notice period')->disabled()->dehydrated(false),
                Forms\Components\TextInput::make('portfolio_link')->label('Portfolio link')->disabled()->dehydrated(false),
            ])
            ->columns(3),

        // Resume Evaluation Results
        Forms\Components\Section::make('Resume Evaluation Results')
            ->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('candidate_id')
                        ->label('Candidate Id')
                        ->disabled()->dehydrated(false),

                    Forms\Components\TextInput::make('ai_score')
                        ->label('Score')
                        ->formatStateUsing(fn ($state) => is_null($state) ? null : number_format((float) $state, 2))
                        ->disabled()->dehydrated(false),

                    
                ]),

                Forms\Components\Textarea::make('ai_summary')
                    ->label('Summary')
                    ->rows(6)
                    ->disabled()->dehydrated(false),
            ])
            ->collapsible()
            ->columns(1),

    

    ])->columns(1);
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
                Tables\Columns\TextColumn::make('job_role')
                    ->label('Job Role')
                    ->sortable()
                    ->searchable()
                    ->limit(40),
    
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
                    // app/Filament/Resources/BasicApplicationResource.php (columns section)
Tables\Columns\BadgeColumn::make('evaluation_status')
    ->label('Status')
    ->getStateUsing(function (BasicApplication $record) {
        if (is_null($record->ai_score)) {
            return 'Not shortlisted';
        }
        return $record->ai_score >= 90
            ? 'Highly recommended'
            : ($record->ai_score >= 80 ? 'Shortlisted' : 'Not shortlisted');
    })
    ->colors([
        'success' => fn ($state): bool => $state === 'Highly recommended',
        'warning' => fn ($state): bool => $state === 'Shortlisted',
        'danger'  => fn ($state): bool => $state === 'Not shortlisted',
    ])
    ->icons([
        'heroicon-m-star'         => fn ($state): bool => $state === 'Highly recommended',
        'heroicon-m-check-circle' => fn ($state): bool => $state === 'Shortlisted',
        'heroicon-m-x-circle'     => fn ($state): bool => $state === 'Not shortlisted',
    ]),


Tables\Columns\TextColumn::make('ai_summary')
    ->label('AI Summary')
    ->limit(80)
    ->tooltip(fn ($record) => $record->ai_summary),

               Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied On')
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

                    Tables\Actions\Action::make('send_task_mail')
    ->label('Send Mail')
    ->icon('heroicon-o-paper-airplane')
    ->color('success')
    ->requiresConfirmation()
    ->visible(fn ($record) => filled($record->email) && filled($record->job_id))
    ->action(function ($record) {
        try {
            $controller = app(TaskMailController::class);
            $controller->sendByApplication($record); // direct controller call (no extra service)

            Notification::make()
                ->title('Email sent successfully.')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Failed to send email.')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }),
    
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
