<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Folder; // you pass Folder in mount
use App\Models\Handout;
use App\Models\HandoutPage;
use App\Models\HandoutComponent;
use App\Models\HandoutScore;
use Illuminate\Support\Facades\Auth;

class ModuleHandout extends Component
{
    use WithFileUploads;

    public $folder;
    public $level_id;
    public Handout $handout;
    public $handoutScore;
    

    protected $listeners = [
        'reorderPages',
        'addComponentFromPalette',
        'reorderComponents',
        'saveTextComponent',
    ];

    public function mount(Folder $folder, $level_id)
    {
        $this->folder = $folder;
        $this->level_id = $level_id;

        // Ensure a Handout exists for this folder + user (adjust business logic as needed)
        $this->handout = Handout::firstOrCreate(
            [
                'folder_id' => $folder->id,
                'level_id' => $level_id,
                'user_id' => Auth::user()->id
            ],
            [
                'title' => null
            ]
        );

        // Load existing score
        $this->handoutScore = HandoutScore::where('handout_id', $this->handout->id)
            ->value('score');
    }

    // computed property to fetch pages + components
    public function getPagesProperty()
    {
        return HandoutPage::where('handout_id', $this->handout->id)
            ->orderBy('page_number')
            ->get();
    }

    public function addPage()
    {
        $max = HandoutPage::where('handout_id', $this->handout->id)->max('page_number') ?? 0;
        HandoutPage::create([
            'handout_id' => $this->handout->id,
            'page_number' => $max + 1,
        ]);

        $this->dispatch('sortable:refresh');
    }

    public function addComponentFromPalette($pageId, $type)
    {
        HandoutComponent::create([
            'page_id'    => $pageId,
            'type'       => $type,
            'data'       => null,
            'sort_order' => $this->nextSortOrder($pageId),
        ]);

        $this->dispatch('sortable:refresh');
        $this->dispatch('suneditor:refresh');
    }

    public function reorderPages(array $orderedPageIds)
    {
        // Fetch all pages in handout
        $pages = HandoutPage::where('handout_id', $this->handout->id)
                            ->whereIn('id', $orderedPageIds)
                            ->get()
                            ->keyBy('id'); // key by ID for quick access

        foreach ($orderedPageIds as $index => $pageId) {
            if (isset($pages[$pageId])) {
                $pages[$pageId]->update(['page_number' => $index + 1]);
            }
        }

        $this->dispatch('sortable:refresh');
    }

    public function reorderComponents($pageId, array $orderedComponentIds)
    {
        $components = HandoutComponent::whereIn('id', $orderedComponentIds)
            ->get()
            ->keyBy('id');

        foreach ($orderedComponentIds as $index => $componentId) {
            if (isset($components[$componentId])) {
                $components[$componentId]->update([
                    'page_id' => $pageId,
                    'sort_order' => $index
                ]);
            }
        }

        $this->dispatch('sortable:refresh');
        $this->dispatch('suneditor:refresh');
    }

    public function removePage($pageId)
    {
        $page = HandoutPage::find($pageId);
        if ($page) {
            $page->delete();

            // Reorder remaining pages
            $pages = HandoutPage::where('handout_id', $this->handout->id)
                        ->orderBy('page_number')
                        ->get();

            foreach ($pages as $index => $p) {
                $p->update(['page_number' => $index + 1]);
            }
        }
        $this->dispatch('sortable:refresh');
    }

    public function removeComponent($componentId)
    {
        $component = HandoutComponent::find($componentId);

        if (! $component) {
            return;
        }

        $pageId = $component->page_id;

        // Delete the component
        $component->delete();

        // Reorder remaining components on the same page
        $components = HandoutComponent::where('page_id', $pageId)
            ->orderBy('sort_order')
            ->get();

        foreach ($components as $index => $comp) {
            $comp->update(['sort_order' => $index]);
        }

        // Refresh sortable + editor instances
        $this->dispatch('sortable:refresh');
        $this->dispatch('suneditor:refresh');
    }


    protected function nextSortOrder($pageId)
    {
        $max = HandoutComponent::where('page_id', $pageId)->max('sort_order');
        return is_null($max) ? 0 : ($max + 1);
    }

    protected function defaultDataForType($type)
    {
        return match($type) {
            'text' => ['type' => 'doc', 'content' => []],
            'image' => ['url' => null, 'caption' => null],
            'video' => ['url' => null],
            'audio' => ['url' => null],
            'link' => ['url' => null, 'label' => null],
            default => [],
        };
    }

    public function store()
    {
        // You can later implement final validation/transform.
        $this->dispatchBrowserEvent('handout-saved');
    }

    public function saveHandoutScore()
    {
        $this->validate([
            'handoutScore' => 'required|integer|min:0'
        ]);

        // Store or update score for this handout
        HandoutScore::updateOrCreate(
            [
                'handout_id' => $this->handout->id
            ],
            [
                'score' => $this->handoutScore
            ]
        );

        $message = 'Score updated successfully!';
        $this->dispatch('flashMessage', type: 'success', message: $message);
    }

    // --------- STORE JSON ----------
    public function saveTextComponent($component_id, $content)
    {
        // Fetch the component
        $component = HandoutComponent::find($component_id);

        if (! $component) {
            $this->dispatch('flashMessage', type: 'error', message: 'Component not found.');
            return;
        }

        // Build JSON structure
        $payload = [
            'type'    => 'doc',
            'content' => $content,
        ];

        // Save into database
        $component->update([
            'data' => json_encode($payload),
        ]);

        $this->dispatch('flashMessage', type: 'success', message: 'Text saved successfully!');
    }


    public function render()
    {
        return view('livewire.module-handout');
    }
}
