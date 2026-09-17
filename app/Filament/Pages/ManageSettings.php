<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * The one-record settings page: everything the couple can change that isn't a list.
 */
class ManageSettings extends Page
{
    protected string $view = 'filament.pages.manage-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?int $navigationSort = 99;

    protected static ?string $title = 'Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::current()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('The wedding')
                    ->columns(2)
                    ->schema([
                        TextInput::make('couple_names')->label('Couple')->required(),
                        TextInput::make('monogram')->label('Monogram')->maxLength(8)->required(),
                        TextInput::make('first_name')->label('First name shown')->required(),
                        TextInput::make('second_name')->label('Second name shown')->required(),
                        DatePicker::make('wedding_date')->required(),
                        TextInput::make('wedding_time')->label('Ceremony time')->placeholder('18:45'),
                        TextInput::make('location_label')->label('Location')->placeholder('Granada, Spain'),
                        TextInput::make('contact_email')->label('Contact address')->email()->required(),
                    ]),

                Section::make('The venue')
                    ->columns(2)
                    ->schema([
                        TextInput::make('venue_name')->required(),
                        TextInput::make('maps_url')->label('Maps link')->url(),
                        static::copy('venue_description')->columnSpanFull(),
                        Textarea::make('venue_address')->rows(2),
                        static::copy('venue_travel_note')->label('Getting there note')->columnSpanFull(),
                        FileUpload::make('venue_image_path')
                            ->label('Venue photo')
                            ->image()
                            ->imageEditor()
                            ->directory('wedding'),
                        FileUpload::make('hero_image_path')
                            ->label('Hero photo')
                            ->image()
                            ->imageEditor()
                            ->directory('wedding'),
                    ]),

                Section::make('Copy')
                    ->schema([
                        static::copy('pull_quote')
                            ->label('Welcome / pull quote')
                            ->helperText('Set a line to Heading 1 to render it as the big cursive title.'),
                        static::copy('schedule_footnote'),
                        static::copy('travel_intro'),
                        static::copy('travel_footnote'),
                        static::copy('hotels_intro')
                            ->helperText('Write :code where the hotel block code should appear.'),
                        static::copy('rsvp_body')->label('RSVP explainer'),
                    ]),

                Section::make('Replies and extras')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('rsvp_deadline')->label('Reply by'),
                        TextInput::make('block_code')->label('Hotel block code'),
                        TextInput::make('honeymoon_url')->label('Honeymoon fund link')->url(),
                    ]),

                Section::make('The envelope')
                    ->columns(2)
                    ->schema([
                        Select::make('animation_speed')
                            ->label('Animation tempo')
                            ->options([
                                'quick' => 'Quick (×0.75)',
                                'full' => 'Full (×1)',
                                'cinematic' => 'Cinematic (×1.35)',
                            ])
                            ->required(),
                        Toggle::make('countdown_enabled')->label('Show the countdown'),
                    ]),
            ]);
    }

    /**
     * A rich editor with the full toolbar behind it.
     *
     * Headings map onto the invitation's type scale when rendered: `h1` is the
     * big script flourish, `h2`–`h6` step down through the serif. Whatever the
     * toolbar can write, `App\\Support\\RichText` lets through and the `rich`
     * stylesheet styles.
     */
    protected static function copy(string $name): RichEditor
    {
        return RichEditor::make($name)
            ->toolbarButtons([
                [ToolbarButtonGroup::make('Style', ['paragraph', 'lead', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'])->textualButtons()],
                ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                ['textColor', 'highlight', 'small', 'code', 'clearFormatting'],
                [ToolbarButtonGroup::make('Alignment', ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'])],
                ['bulletList', 'orderedList', 'blockquote', 'horizontalRule', 'details'],
                ['table'],
                ['undo', 'redo'],
            ])
            ->floatingToolbars([
                'paragraph' => ['bold', 'italic', 'underline', 'strike', 'link', 'highlight'],
                'heading' => ['h1', 'h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                'table' => [
                    'tableAddColumnBefore', 'tableAddColumnAfter', 'tableDeleteColumn',
                    'tableAddRowBefore', 'tableAddRowAfter', 'tableDeleteRow',
                    'tableMergeCells', 'tableSplitCell',
                    'tableToggleHeaderRow', 'tableToggleHeaderCell',
                    'tableDelete',
                ],
            ]);
    }

    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save changes')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        Setting::current()->update($this->form->getState());

        Notification::make()->title('Settings saved')->success()->send();
    }
}
