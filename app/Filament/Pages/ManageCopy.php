<?php

namespace App\Filament\Pages;

use App\Models\SiteText;
use App\Support\SiteCopy;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Every word on the invitation, built straight from the SiteCopy definition so
 * a new line of copy only has to be declared in one place to appear here.
 */
class ManageCopy extends Page
{
    protected string $view = 'filament.pages.manage-copy';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Wording';

    protected static ?int $navigationSort = 98;

    protected static ?string $title = 'Wording';

    protected ?string $subheading = 'Every heading, label and button on the invitation. Leave a field as it is to keep the standard wording.';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->currentState());
    }

    public function form(Schema $schema): Schema
    {
        $sections = SiteCopy::grouped()->map(function ($rows, string $group) {
            $fields = collect($rows)->map(function (array $row, string $key) {
                [, $label, $default, $lines] = $row;

                $field = $lines > 1
                    ? Textarea::make($this->fieldName($key))->rows($lines)
                    : TextInput::make($this->fieldName($key));

                return $field
                    ->label($label)
                    ->placeholder($default)
                    ->helperText($this->helperFor($default))
                    ->columnSpanFull();
            })->values()->all();

            return Section::make(SiteCopy::GROUPS[$group] ?? $group)
                ->collapsible()
                ->schema($fields);
        })->values()->all();

        return $schema->statePath('data')->components($sections);
    }

    /**
     * Filament state paths split on dots, so the copy keys are flattened for
     * the form and expanded again on save.
     */
    private function fieldName(string $key): string
    {
        return str_replace('.', '__', $key);
    }

    private function helperFor(string $default): ?string
    {
        $tokens = [];
        preg_match_all('/:([a-z]+)/', $default, $tokens);

        if ($tokens[0] === []) {
            return null;
        }

        return 'Keep '.implode(' and ', array_unique($tokens[0])).' — replaced with the real value.';
    }

    /** Stored value where there is one, otherwise the default. */
    private function currentState(): array
    {
        $state = [];

        foreach (SiteCopy::all() as $key => $value) {
            $state[$this->fieldName($key)] = $value;
        }

        return $state;
    }

    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save wording')
                ->submit('save'),

            Action::make('reset')
                ->label('Reset all to defaults')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Reset every line?')
                ->modalDescription('This puts all the wording back to the standard text. Your own wording will be lost.')
                ->action('resetToDefaults'),
        ];
    }

    public function save(): void
    {
        $defaults = SiteCopy::defaults();

        foreach ($this->form->getState() as $field => $value) {
            $key = str_replace('__', '.', $field);

            if (! array_key_exists($key, $defaults)) {
                continue;
            }

            $value = is_string($value) ? trim($value) : $value;

            // A field left empty or matching the default keeps no row, so the
            // default stays live and later wording changes flow through.
            if (blank($value) || $value === $defaults[$key]) {
                SiteText::where('key', $key)->delete();

                continue;
            }

            SiteText::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->form->fill($this->currentState());

        Notification::make()->title('Wording saved')->success()->send();
    }

    public function resetToDefaults(): void
    {
        SiteText::query()->delete();

        $this->form->fill($this->currentState());

        Notification::make()->title('Wording reset to defaults')->success()->send();
    }
}
