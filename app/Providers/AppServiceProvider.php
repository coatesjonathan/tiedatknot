<?php

namespace App\Providers;

use Filament\Forms\Components\RichEditor;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // The invitation's typography is opinionated, so the editor offers only
        // what it can carry: emphasis, links and lists. Headings, alignment,
        // tables and attachments would all fight the design.
        RichEditor::configureUsing(fn (RichEditor $editor) => $editor
            ->toolbarButtons([
                ['bold', 'italic', 'link'],
                ['bulletList', 'orderedList'],
                ['undo', 'redo'],
            ])
            ->columnSpanFull());
    }
}
