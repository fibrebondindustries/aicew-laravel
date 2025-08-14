<?php

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
            Forms\Components\TextInput::make('title')->disabled()->dehydrated(false),
            
            Forms\Components\Textarea::make('prompt')->disabled()->dehydrated(false)->rows(10),
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
                Forms\Components\Toggle::make('is_active')
                ->label('Active')
                ->helperText('Inactive records will be hidden from use.')
                ->disabled()
                ->dehydrated(false),

    ])->columns(2);
        
    }

    // app/Filament/Resources/ResumePromptResource.php
public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('job_id')
                ->label('Job ID')
                ->sortable()
                ->searchable()
                ->copyable()
                ->copyMessage('Job ID copied'),

            Tables\Columns\TextColumn::make('title')
               ->label('Job Role')
                ->sortable()
                ->searchable()
                ->limit(60),

            Tables\Columns\TextColumn::make('prompt')
                ->label('Prompt (preview)')
                ->toggleable()
                ->limit(80),

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
            // 'edit'   => Pages\EditResumePrompt::route('/{record}/edit'),
        ];
    }
}
