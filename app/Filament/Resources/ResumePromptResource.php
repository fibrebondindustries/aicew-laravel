<?php

// namespace App\Filament\Resources;

// use App\Filament\Resources\ResumePromptResource\Pages;
// use App\Models\ResumePrompt;
// use Filament\Forms;
// use Filament\Forms\Form;
// use Filament\Resources\Resource;
// use Filament\Tables;
// use Filament\Tables\Table;
// use Carbon\Carbon;
// use Illuminate\Database\Eloquent\Builder;
// use Filament\Forms\Components\View as ViewComponent;
// use App\Models\BasicApplication;


// class ResumePromptResource extends Resource
// {
//     protected static ?string $model = ResumePrompt::class;

//     protected static ?string $navigationIcon   = 'heroicon-o-briefcase';
//     protected static ?string $navigationGroup  = 'Job Management';
//     protected static ?int    $navigationSort   = 2;
//     protected static ?string $navigationLabel  = 'Jobs';
//     protected static ?string $modelLabel       = 'Jobs';
//     protected static ?string $pluralModelLabel = 'Jobs';

//     public static function form(Form $form): Form
//     {
//         // view-only for now; you can enable create/edit later
//         return $form->schema([
//             Forms\Components\TextInput::make('job_id')->disabled()->dehydrated(false),
//             // Forms\Components\TextInput::make('title')->disabled()->dehydrated(false),
//             Forms\Components\TextInput::make('title')
//             ->label('Job Role')
//             ->required()
//             ->maxLength(120),
//             // Forms\Components\Textarea::make('prompt')->disabled()->dehydrated(false)->rows(10),
//             Forms\Components\Textarea::make('prompt')
//             ->label('Resume Prompt')
//             ->rows(10)
//             ->required(),
//               Forms\Components\Select::make('task_mode')
//             ->label('Task Mode')
//             ->options([
//                 'ai'     => 'AI evaluate',
//                 'manual' => 'Manual evaluate',
//             ])
//             ->native(false),
//             // ->disabled()
//             // ->dehydrated(false),

//         // ▼ NEW: Task Prompt (read-only)
//         Forms\Components\Textarea::make('task_prompt')
//             ->label('Task Prompt')
//             ->rows(8)
//             ->placeholder('—'),
//             // ->disabled()
//             // ->dehydrated(false),
//               // ▼ Read-only Apply URL (local → 127.0.0.1:8000, prod → FRONTEND_URL)
//         Forms\Components\TextInput::make('apply_url')
//             ->label('Apply URL')
//             ->disabled()
//             ->dehydrated(false)
//             ->formatStateUsing(function ($record) {
//                 if (! $record) return '';
//                 $base = app()->environment('local')
//                     ? 'http://127.0.0.1:8000'
//                     : rtrim(env('FRONTEND_URL', config('app.url')), '/');

//                 return $base . '/job-apply?job_id=' . $record->job_id;
//             })
//             ->helperText('Share this link with candidates to apply directly for this Job ID.'),
               
            

//     ])->columns(2);
        
//     }

//     // app/Filament/Resources/ResumePromptResource.php
// public static function table(Table $table): Table
// {
//     return $table
//      ->modifyQueryUsing(function (Builder $query) {
//             $resumeTbl = (new \App\Models\ResumePrompt)->getTable();
//             $appsTbl   = (new \App\Models\BasicApplication)->getTable();

//             $query->addSelect([
//                 // Total applications per job_id
//                 'total_apps' => BasicApplication::query()
//                     ->selectRaw('COUNT(*)')
//                     ->whereColumn("{$appsTbl}.job_id", "{$resumeTbl}.job_id"),

//                 // Shortlisted 80–89.9999 per job_id
//                 'shortlisted_count' => BasicApplication::query()
//                     ->selectRaw('COUNT(*)')
//                     ->whereColumn("{$appsTbl}.job_id", "{$resumeTbl}.job_id")
//                     ->whereNotNull("{$appsTbl}.ai_score")
//                     ->whereBetween("{$appsTbl}.ai_score", [80, 89.9999]),

//                 // Highly recommended >= 90 per job_id
//                 'highly_count' => BasicApplication::query()
//                     ->selectRaw('COUNT(*)')
//                     ->whereColumn("{$appsTbl}.job_id", "{$resumeTbl}.job_id")
//                     ->where("{$appsTbl}.ai_score", '>=', 90),
//             ]);
//         })
//         ->columns([
//             Tables\Columns\TextColumn::make('job_id')
//                 ->label('Job ID')
//                 ->sortable()
//                 ->searchable()
//                 ->copyable()
//                 ->copyMessage('Job ID copied'),
//               // ▼ NEW: counts per job_id
//          Tables\Columns\TextColumn::make('total_apps')
//                 ->label('Total Apps')
//                 ->badge()
//                 ->color(fn ($state) => $state > 0 ? 'info' : 'gray')
//                 ->url(fn ($record) => route(
//                     'filament.admin.resources.basic-applications.index',
//                     ['tableFilters[job_id][value]' => $record->job_id]
//                 ))
//                 // ->openUrlInNewTab()
//                 ->alignCenter(),


//             Tables\Columns\TextColumn::make('shortlisted_count')
//                 ->label('Shortlisted')
//                 ->badge()
//                 ->color(fn ($state) => $state > 0 ? 'warning' : 'gray')
//                   ->url(fn ($record) => route(
//                     'filament.admin.resources.basic-applications.index',
//                     ['tableFilters[job_id][value]' => $record->job_id]
//                 ))
//                 ->alignCenter(),

//             Tables\Columns\TextColumn::make('highly_count')
//                 ->label('Highly Rec.')
//                 ->badge()
//                 ->color(fn ($state) => $state > 0 ? 'success' : 'gray')
//                   ->url(fn ($record) => route(
//                     'filament.admin.resources.basic-applications.index',
//                     ['tableFilters[job_id][value]' => $record->job_id]
//                 ))
//                 ->alignCenter(),    

//             Tables\Columns\TextColumn::make('title')
//                ->label('Job Role')
//                 ->sortable()
//                 ->searchable()
//                 ->limit(60),

//             Tables\Columns\TextColumn::make('prompt')
//                 ->label('Prompt (preview)')
//                 ->toggleable()
//                 ->limit(80),

//                   Tables\Columns\BadgeColumn::make('task_mode')
//         ->label('Task Mode')
//         ->colors([
//             'info'  => fn ($state) => $state === 'ai',
//             'gray'  => fn ($state) => $state === 'manual',
//         ])
//         ->icons([
//             'heroicon-m-cpu-chip'    => fn ($state) => $state === 'ai',
//             'heroicon-m-hand-raised' => fn ($state) => $state === 'manual',
//         ])
//         ->formatStateUsing(function ($state) {
//             return $state === 'ai' ? 'AI evaluate' : 'Manual evaluate';
//         })
//         ->sortable(),

//     // ▼ NEW: Task Prompt preview
//     Tables\Columns\TextColumn::make('task_prompt')
//         ->label('Task Prompt')
//         ->toggleable()
//         ->wrap()
//         ->limit(80),

//                 // Files count badge
//             // Tables\Columns\TextColumn::make('filesCount')
//             //     ->label('Files')
//             //     ->state(fn (ResumePrompt $record) => $record->filesCount())
//             //     ->badge()
//             //     ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),

//                 // Tables\Columns\TextColumn::make('task_indicator')
//                 //     ->label('Files')
//                 //     ->state(function (ResumePrompt $r) {
//                 //         return $r->filesCount() > 0
//                 //             ? $r->filesCount()
//                 //             : (filled($r->task_link) ? 'Link' : 0);
//                 //     })
//                 //     ->badge()
//                 //     ->color(function (ResumePrompt $r) {
//                 //         return $r->filesCount() > 0
//                 //             ? 'success'
//                 //             : (filled($r->task_link) ? 'info' : 'gray');
//                 //     }),

//                  // ▼ NEW: public apply URL built from job_id
//             Tables\Columns\TextColumn::make('apply_url')
//                 ->label('Apply URL')
//                 ->getStateUsing(function ($record) {
//                     // pick your base host
//                     $base = app()->environment('local')
//                         ? env('APP_URL', 'http://127.0.0.1:8000')
//                         : (env('FRONTEND_URL', config('app.url')) ?: config('app.url'));

//                     // ensure no trailing slash
//                     $base = rtrim($base, '/');

//                     return $base . '/job-apply?job_id=' . $record->job_id;
//                 })
//                 ->url(fn ($record) => (app()->environment('local')
//                         ? rtrim(env('APP_URL', 'http://127.0.0.1:8000'), '/')
//                         : rtrim((env('FRONTEND_URL', config('app.url')) ?: config('app.url')), '/')
//                     ) . '/job-apply?job_id=' . $record->job_id,
//                     shouldOpenInNewTab: true
//                 )
//                 ->copyable()
//                 ->copyMessage('Apply link copied')
//                 ->limit(60)
//                 ->extraAttributes(['class' => 'font-mono text-xs']),

//             Tables\Columns\IconColumn::make('is_active')
//                 ->label('Active')
//                 ->boolean(),

//             Tables\Columns\TextColumn::make('created_at')
//                 ->label('Created')
//                 ->sortable()
//                 ->dateTime()
//                 ->formatStateUsing(fn ($state) =>
//                     \Carbon\Carbon::parse($state)
//                         ->timezone('Asia/Kolkata')
//                         ->format('M j, Y H:i')
//                 ),
//         ])
//         ->filters([
//             Tables\Filters\TernaryFilter::make('is_active')
//                     ->label('Active')
//                     ->boolean()
//                     ->queries(
//                         true:  fn (Builder $q) => $q->where('is_active', true),
//                         false: fn (Builder $q) => $q->where('is_active', false),
//                         blank: fn (Builder $q) => $q
//                     ),
//             ])
//         ->actions([
//             Tables\Actions\ViewAction::make(),
//              Tables\Actions\EditAction::make(), 
//               // Open a modal listing the files with links
//             // Tables\Actions\Action::make('view_files')
//             //     ->label('View Files')
//             //     ->icon('heroicon-o-paper-clip')
//             //     ->color('gray')
//             //      ->visible(fn (ResumePrompt $record) => $record->filesCount() > 0 || filled($record->task_link))
//             //     ->modalHeading(fn (ResumePrompt $record) => 'Files for ' . $record->job_id)
//             //     ->modalSubmitAction(false) // view-only
//             //    ->modalContent(fn (ResumePrompt $record) => view(
//             //         'components.resume-files-list',    
//             //         [
//             //             'files' => $record->fileItems(),
//             //             'link'  => $record->task_link, // Pass link to blade
//             //         ]
//             //     )),
//         Tables\Actions\Action::make('view_task')
//             ->label('View Task Link')
//             ->icon('heroicon-o-paper-clip')
//             ->color('gray')
//             // show when there are files OR a link
//             ->visible(fn (ResumePrompt $r) => $r->filesCount() > 0 || filled($r->task_link))
//             ->modalHeading(fn (ResumePrompt $r) => 'Task for ' . $r->job_id)
//             ->modalSubmitAction(false)
//             ->modalContent(fn (ResumePrompt $r) => view(
//                  'components.task-view',
//                 [
//                     'files' => $r->fileItems(),   // may be []
//                     'link'  => $r->task_link,     // ← pass the link to the view
//                 ]
//                 )),

//         Tables\Actions\Action::make('applications')
//             ->label('Applications')
//             ->icon('heroicon-o-users')
//             ->color('info')
//             ->url(fn ($record) => route(
//                 'filament.admin.resources.basic-applications.index',
//                 // Pre-apply the Job ID filter on the Applications list
//                 ['tableFilters[job_id][value]' => $record->job_id]
//         )),
//            // Per-row toggle Active/Inactive
//                 Tables\Actions\Action::make('toggle_active')
//                     ->label(fn (ResumePrompt $record) => $record->is_active ? 'Deactivate' : 'Activate')
//                     ->icon(fn (ResumePrompt $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
//                     ->color(fn (ResumePrompt $record) => $record->is_active ? 'danger' : 'success')
//                     ->requiresConfirmation()
//                     ->action(function (ResumePrompt $record) {
//                         $record->update(['is_active' => ! $record->is_active]);
//                     }),
//         ])
//         //  ->headerActions([
//         //     Tables\Actions\CreateAction::make(),    // ← enable create
//         // ])
//         ->bulkActions([
//                 Tables\Actions\BulkActionGroup::make([
//                     Tables\Actions\DeleteBulkAction::make(),   // ✔ shows checkbox + delete

//                     Tables\Actions\BulkAction::make('activate')
//                         ->label('Activate Selected')
//                         ->icon('heroicon-o-check-circle')
//                         ->color('success')
//                         ->requiresConfirmation()
//                         ->action(fn ($records) => $records->each->update(['is_active' => true])),

//                     Tables\Actions\BulkAction::make('deactivate')
//                         ->label('Deactivate Selected')
//                         ->icon('heroicon-o-x-circle')
//                         ->color('danger')
//                         ->requiresConfirmation()
//                         ->action(fn ($records) => $records->each->update(['is_active' => false])),
//                 ]),
//             ])
//         ->defaultSort('created_at', 'desc');
// }

//     public static function getPages(): array
//     {
//         return [
//             'index'  => Pages\ListResumePrompts::route('/'),
//             'view'   => Pages\ViewResumePrompt::route('/{record}'),
//             // 'create' => Pages\CreateResumePrompt::route('/create'),
//             'edit'   => Pages\EditResumePrompt::route('/{record}/edit'),
//         ];
//     }
// }


namespace App\Filament\Resources;

use App\Filament\Resources\ResumePromptResource\Pages;
use App\Models\ResumePrompt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\View as ViewComponent;
use App\Models\BasicApplication;


class ResumePromptResource extends Resource
{
    protected static ?string $model = ResumePrompt::class;

    protected static ?string $navigationIcon   = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup  = 'Job Management';
    protected static ?int    $navigationSort   = 2;
    protected static ?string $navigationLabel  = 'Jobs';
    protected static ?string $modelLabel       = 'Jobs';
    protected static ?string $pluralModelLabel = 'Jobs';

    public static function form(Form $form): Form
    {
        // view-only for now; you can enable create/edit later
        return $form->schema([
            Forms\Components\TextInput::make('job_id')->disabled()->dehydrated(false),
            Forms\Components\TextInput::make('title')
            ->label('Job Role')
            ->required()
            ->maxLength(120),
            
              Forms\Components\Textarea::make('prompt')
            ->label('Resume Prompt')
            ->rows(10)
            ->required(),

              Forms\Components\Select::make('task_mode')
            ->label('Task Mode')
            ->options([
                'ai'     => 'AI evaluate',
                'manual' => 'Manual evaluate',
            ])
            ->native(false),
           

        // ▼ NEW: Task Prompt (read-only)
        Forms\Components\Textarea::make('task_prompt')
            ->label('Task Prompt')
            ->rows(8)
            ->placeholder('—'),

        Forms\Components\Textarea::make('task_link')
            ->label('Task Link')
            ->rows(8)
            ->placeholder('—'), 
              // ▼ Read-only Apply URL (local → 127.0.0.1:8000, prod → FRONTEND_URL)
        Forms\Components\TextInput::make('apply_url')
            ->label('Apply URL')
            ->disabled()
            ->dehydrated(false)
            ->formatStateUsing(function ($record) {
                if (! $record) return '';
                $base = app()->environment('local')
                    ? 'http://127.0.0.1:8000'
                    : rtrim(env('FRONTEND_URL', config('app.url')), '/');

                return $base . '/job-apply?job_id=' . $record->job_id;
            })
            ->helperText('Share this link with candidates to apply directly for this Job ID.'),
               
            

    ])->columns(2);
        
    }

    // app/Filament/Resources/ResumePromptResource.php
public static function table(Table $table): Table
{
    return $table
     ->modifyQueryUsing(function (Builder $query) {
            $resumeTbl = (new \App\Models\ResumePrompt)->getTable();
            $appsTbl   = (new \App\Models\BasicApplication)->getTable();

            $query->addSelect([
                // Total applications per job_id
                'total_apps' => BasicApplication::query()
                    ->selectRaw('COUNT(*)')
                    ->whereColumn("{$appsTbl}.job_id", "{$resumeTbl}.job_id"),

                // Shortlisted 80–89.9999 per job_id
                'shortlisted_count' => BasicApplication::query()
                    ->selectRaw('COUNT(*)')
                    ->whereColumn("{$appsTbl}.job_id", "{$resumeTbl}.job_id")
                    ->whereNotNull("{$appsTbl}.ai_score")
                    ->whereBetween("{$appsTbl}.ai_score", [80, 89.9999]),

                // Highly recommended >= 90 per job_id
                'highly_count' => BasicApplication::query()
                    ->selectRaw('COUNT(*)')
                    ->whereColumn("{$appsTbl}.job_id", "{$resumeTbl}.job_id")
                    ->where("{$appsTbl}.ai_score", '>=', 90),
            ]);
        })
        ->columns([
            Tables\Columns\TextColumn::make('job_id')
                ->label('Job ID')
                ->sortable()
                ->searchable()
                ->copyable()
                ->copyMessage('Job ID copied'),
              // ▼ NEW: counts per job_id
         Tables\Columns\TextColumn::make('total_apps')
                ->label('Total Apps')
                ->badge()
                ->color(fn ($state) => $state > 0 ? 'info' : 'gray')
                ->url(fn ($record) => route(
                    'filament.admin.resources.basic-applications.index',
                    ['tableFilters[job_id][value]' => $record->job_id]
                ))
                // ->openUrlInNewTab()
                ->alignCenter(),


            Tables\Columns\TextColumn::make('shortlisted_count')
                ->label('Shortlisted')
                ->badge()
                ->color(fn ($state) => $state > 0 ? 'warning' : 'gray')
                  ->url(fn ($record) => route(
                    'filament.admin.resources.basic-applications.index',
                    ['tableFilters[job_id][value]' => $record->job_id]
                ))
                ->alignCenter(),

            Tables\Columns\TextColumn::make('highly_count')
                ->label('Highly Rec.')
                ->badge()
                ->color(fn ($state) => $state > 0 ? 'success' : 'gray')
                  ->url(fn ($record) => route(
                    'filament.admin.resources.basic-applications.index',
                    ['tableFilters[job_id][value]' => $record->job_id]
                ))
                ->alignCenter(),    

            Tables\Columns\TextColumn::make('title')
               ->label('Job Role')
                ->sortable()
                ->searchable()
                ->limit(60),

            Tables\Columns\TextColumn::make('prompt')
                ->label('Prompt (preview)')
                ->toggleable()
                ->limit(80),

                  Tables\Columns\BadgeColumn::make('task_mode')
        ->label('Task Mode')
        ->colors([
            'info'  => fn ($state) => $state === 'ai',
            'gray'  => fn ($state) => $state === 'manual',
        ])
        ->icons([
            'heroicon-m-cpu-chip'    => fn ($state) => $state === 'ai',
            'heroicon-m-hand-raised' => fn ($state) => $state === 'manual',
        ])
        ->formatStateUsing(function ($state) {
            return $state === 'ai' ? 'AI evaluate' : 'Manual evaluate';
        })
        ->sortable(),

    // ▼ NEW: Task Prompt preview
    Tables\Columns\TextColumn::make('task_prompt')
        ->label('Task Prompt')
        ->toggleable()
        ->wrap()
        ->limit(80),

                // Files count badge
            // Tables\Columns\TextColumn::make('filesCount')
            //     ->label('Files')
            //     ->state(fn (ResumePrompt $record) => $record->filesCount())
            //     ->badge()
            //     ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),

                // Tables\Columns\TextColumn::make('task_indicator')
                //     ->label('Files')
                //     ->state(function (ResumePrompt $r) {
                //         return $r->filesCount() > 0
                //             ? $r->filesCount()
                //             : (filled($r->task_link) ? 'Link' : 0);
                //     })
                //     ->badge()
                //     ->color(function (ResumePrompt $r) {
                //         return $r->filesCount() > 0
                //             ? 'success'
                //             : (filled($r->task_link) ? 'info' : 'gray');
                //     }),

                 // ▼ NEW: public apply URL built from job_id
            Tables\Columns\TextColumn::make('apply_url')
                ->label('Apply URL')
                ->getStateUsing(function ($record) {
                    // pick your base host
                    $base = app()->environment('local')
                        ? env('APP_URL', 'http://127.0.0.1:8000')
                        : (env('FRONTEND_URL', config('app.url')) ?: config('app.url'));

                    // ensure no trailing slash
                    $base = rtrim($base, '/');

                    return $base . '/job-apply?job_id=' . $record->job_id;
                })
                ->url(fn ($record) => (app()->environment('local')
                        ? rtrim(env('APP_URL', 'http://127.0.0.1:8000'), '/')
                        : rtrim((env('FRONTEND_URL', config('app.url')) ?: config('app.url')), '/')
                    ) . '/job-apply?job_id=' . $record->job_id,
                    shouldOpenInNewTab: true
                )
                ->copyable()
                ->copyMessage('Apply link copied')
                ->limit(60)
                ->extraAttributes(['class' => 'font-mono text-xs']),

            Tables\Columns\IconColumn::make('is_active')
                ->label('Active')
                ->boolean(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Created')
                ->sortable()
                ->dateTime()
                ->formatStateUsing(fn ($state) =>
                    \Carbon\Carbon::parse($state)
                        ->timezone('Asia/Kolkata')
                        ->format('M j, Y H:i')
                ),
        ])
        ->filters([
            Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->queries(
                        true:  fn (Builder $q) => $q->where('is_active', true),
                        false: fn (Builder $q) => $q->where('is_active', false),
                        blank: fn (Builder $q) => $q
                    ),
            ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(), 
              // Open a modal listing the files with links
            // Tables\Actions\Action::make('view_files')
            //     ->label('View Files')
            //     ->icon('heroicon-o-paper-clip')
            //     ->color('gray')
            //      ->visible(fn (ResumePrompt $record) => $record->filesCount() > 0 || filled($record->task_link))
            //     ->modalHeading(fn (ResumePrompt $record) => 'Files for ' . $record->job_id)
            //     ->modalSubmitAction(false) // view-only
            //    ->modalContent(fn (ResumePrompt $record) => view(
            //         'components.resume-files-list',    
            //         [
            //             'files' => $record->fileItems(),
            //             'link'  => $record->task_link, // Pass link to blade
            //         ]
            //     )),
        Tables\Actions\Action::make('view_task')
            ->label('View Task Link')
            ->icon('heroicon-o-paper-clip')
            ->color('gray')
            // show when there are files OR a link
            ->visible(fn (ResumePrompt $r) => $r->filesCount() > 0 || filled($r->task_link))
            ->modalHeading(fn (ResumePrompt $r) => 'Task for ' . $r->job_id)
            ->modalSubmitAction(false)
            ->modalContent(fn (ResumePrompt $r) => view(
                 'components.task-view',
                [
                    'files' => $r->fileItems(),   // may be []
                    'link'  => $r->task_link,     // ← pass the link to the view
                ]
                )),

        Tables\Actions\Action::make('applications')
            ->label('Applications')
            ->icon('heroicon-o-users')
            ->color('info')
            ->url(fn ($record) => route(
                'filament.admin.resources.basic-applications.index',
                // Pre-apply the Job ID filter on the Applications list
                ['tableFilters[job_id][value]' => $record->job_id]
        )),
           // Per-row toggle Active/Inactive
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn (ResumePrompt $record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn (ResumePrompt $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (ResumePrompt $record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (ResumePrompt $record) {
                        $record->update(['is_active' => ! $record->is_active]);
                    }),
        ])
        ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),   // ✔ shows checkbox + delete

                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                ]),
            ])
        ->defaultSort('created_at', 'desc');
}

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListResumePrompts::route('/'),
            'view'   => Pages\ViewResumePrompt::route('/{record}'),
            // 'create' => Pages\CreateResumePrompt::route('/create'),
            'edit'   => Pages\EditResumePrompt::route('/{record}/edit'),
        ];
    }
}
