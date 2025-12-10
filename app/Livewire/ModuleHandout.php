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
use Illuminate\Support\Facades\Storage;

class ModuleHandout extends Component
{
    use WithFileUploads;

    public $folder;
    public $level_id;
    public Handout $handout;
    public $handoutScore;

    const LOCAL_STORAGE_FOLDER = 'handouts/';
    

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

        // Update timestamp
        $this->folder->update(['updated_at' => now()]);
        $this->folder->project->update(['updated_at' => now()]);

        $this->dispatch('sortable:refresh');
        $this->dispatch('suneditor:refresh');
    }

    public function addComponentFromPalette($pageId, $type)
    {
        HandoutComponent::create([
            'page_id'    => $pageId,
            'type'       => $type,
            'data'       => null,
            'sort_order' => $this->nextSortOrder($pageId),
        ]);

        // Update timestamp
        $this->folder->update(['updated_at' => now()]);
        $this->folder->project->update(['updated_at' => now()]);

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

        // Update timestamp
        $this->folder->update(['updated_at' => now()]);
        $this->folder->project->update(['updated_at' => now()]);

        $this->dispatch('sortable:refresh');
        $this->dispatch('suneditor:refresh');
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

        // Update timestamp
        $this->folder->update(['updated_at' => now()]);
        $this->folder->project->update(['updated_at' => now()]);

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

        // Update timestamp
        $this->folder->update(['updated_at' => now()]);
        $this->folder->project->update(['updated_at' => now()]);

        $this->dispatch('sortable:refresh');
        $this->dispatch('suneditor:refresh');
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

        // Update timestamp
        $this->folder->update(['updated_at' => now()]);
        $this->folder->project->update(['updated_at' => now()]);

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

        // Update timestamp
        $this->folder->update(['updated_at' => now()]);
        $this->folder->project->update(['updated_at' => now()]);

        $message = 'Score updated successfully!';
        $this->dispatch('flashMessage', type: 'success', message: $message);
        $this->dispatch('suneditor:refresh');
    }

    // --------- STORE JSON ----------
    public function saveTextComponent($component_id, $content)
    {
        // // Dump & die to inspect the data
        // dd([
        //     'component_id' => $component_id,
        //     'content' => $content,
        // ]);

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
        $this->dispatch('suneditor:refresh');
    }

    // public function saveTextComponent($component_id, $content)
    // {
    //     // Step 1: Find all Base64 <img> tags
    //     preg_match_all('/<img[^>]+src="data:(image\/[a-zA-Z]+);base64,([^"]+)"/', $content, $matches, PREG_SET_ORDER);

    //     $imagesData = [];

    //     foreach ($matches as $match) {
    //         $mime = $match[1];         // e.g., image/png
    //         $base64Data = $match[2];   // the actual Base64 string

    //         // Decode Base64
    //         $imageBinary = base64_decode($base64Data);

    //         // Optional: create a unique filename
    //         $extension = explode('/', $mime)[1]; // png, jpeg, etc
    //         $fileName = time() . "." . $extension;

    //         // Store info in array for inspection
    //         $imagesData[] = [
    //             'filename' => $fileName,
    //             'mime' => $mime,
    //             'size' => strlen($imageBinary), // size in bytes
    //             'binary_sample' => substr($imageBinary, 0, 50), // first 50 bytes for preview
    //         ];

    //         // Here you could save the file if needed:
    //         // Storage::disk('public')->put('handouts/' . $fileName, $imageBinary);
    //     }

    //     // Step 2: Dump & die to inspect
    //     dd([
    //         'component_id' => $component_id,
    //         'original_content' => $content,
    //         'images_extracted' => $imagesData,
    //     ]);

    //     // Step 3: Continue normal save (unreachable now because of dd)
    //     $component = HandoutComponent::find($component_id);
    //     if (! $component) return;

    //     $payload = [
    //         'type' => 'doc',
    //         'content' => $content,
    //     ];

    //     $component->update([
    //         'data' => json_encode($payload),
    //     ]);
    // }

    # WORKING EXCEPT AUDIO LINK
    // public function saveTextComponent($component_id, $content)
    // {
    //     // Step 1: Find all Base64 <img> tags
    //     preg_match_all('/<img[^>]+src="data:(image\/[a-zA-Z]+);base64,([^"]+)"/', $content, $matches, PREG_SET_ORDER);

    //     $imagesData = [];

    //     foreach ($matches as $match) {
    //         $mime = $match[1];       // e.g., image/png
    //         $base64Data = $match[2]; // the actual Base64 string

    //         // Decode Base64
    //         $imageBinary = base64_decode($base64Data);

    //         // Step 1a: Determine extension
    //         $extension = explode('/', $mime)[1];

    //         // Step 1b: Use saveImage logic (adapted for binary)
    //         $fileName = time() . '.' . $extension;
    //         Storage::disk('public')->put(self::LOCAL_STORAGE_FOLDER . $fileName, $imageBinary);

    //         // Step 1c: Store info for debugging
    //         $imagesData[] = [
    //             'filename' => $fileName,
    //             'mime' => $mime,
    //             'size' => strlen($imageBinary),
    //         ];

    //         // Step 1d: Replace Base64 src with storage URL in content
    //         $storageUrl = asset('storage/' . self::LOCAL_STORAGE_FOLDER . $fileName);
    //         $content = str_replace($match[0], '<img src="' . $storageUrl . '"', $content);
    //     }

    //     // Step 2: Dump & die to inspect
    //     // dd([
    //     //     'component_id' => $component_id,
    //     //     'updated_content' => $content,
    //     //     'images_saved' => $imagesData,
    //     // ]);

    //     // Step 3: Save
    //     $component = HandoutComponent::find($component_id);
    //     if (! $component) return;

    //     $payload = [
    //         'type' => 'doc',
    //         'content' => $content,
    //     ];

    //     $component->update([
    //         'data' => json_encode($payload),
    //     ]);

    //     $this->dispatch('flashMessage', type: 'success', message: 'Content saved successfully!');
    //     $this->dispatch('suneditor:refresh');
    // }

    // public function saveTextComponent($component_id, $content)
    // {
    //     // Step 1: Handle Base64 <img> tags
    //     preg_match_all('/<img[^>]+src="data:(image\/[a-zA-Z]+);base64,([^"]+)"/', $content, $matches, PREG_SET_ORDER);

    //     $imagesData = [];

    //     foreach ($matches as $match) {
    //         $mime = $match[1];       // e.g., image/png
    //         $base64Data = $match[2]; // actual Base64 string

    //         // Decode Base64
    //         $imageBinary = base64_decode($base64Data);

    //         // Validate size (max 5MB)
    //         if (strlen($imageBinary) > 5 * 1024 * 1024) {
    //             $this->dispatch('flashMessage', type: 'error', message: 'One of the images exceeds the 5MB limit.');
    //             $this->dispatch('suneditor:refresh');
    //             return; // stop saving
    //         }

    //         // Determine extension
    //         $extension = explode('/', $mime)[1];

    //         // Save image to storage/app/public
    //         $fileName = time() . '.' . $extension;
    //         Storage::disk('public')->put(self::LOCAL_STORAGE_FOLDER . $fileName, $imageBinary);

    //         // Store info
    //         $imagesData[] = [
    //             'filename' => $fileName,
    //             'mime' => $mime,
    //             'size' => strlen($imageBinary),
    //         ];

    //         // Replace Base64 src with storage URL
    //         $storageUrl = asset('storage/' . self::LOCAL_STORAGE_FOLDER . $fileName);
    //         $content = str_replace($match[0], '<img src="' . $storageUrl . '"', $content);
    //     }

    //     // Step 2: Add target="_blank" to all <a> tags
    //     $content = preg_replace('/<a\s+([^>]*?)href=/', '<a $1 target="_blank" href=', $content);

    //     // Step 3: Save content
    //     $component = HandoutComponent::find($component_id);
    //     if (! $component) return;

    //     $payload = [
    //         'type' => 'doc',
    //         'content' => $content,
    //     ];

    //     $component->update([
    //         'data' => json_encode($payload),
    //     ]);

    //     $this->dispatch('flashMessage', type: 'success', message: 'Content saved successfully!');
    //     $this->dispatch('suneditor:refresh');
    // }

    // public function saveTextComponent($component_id, $content)
    // {
    //     // Step 0: Get old images from previously saved content
    //     $component = HandoutComponent::find($component_id);
    //     if (! $component) return;

    //     $oldData = json_decode($component->data, true);
    //     $oldContent = $oldData['content'] ?? '';

    //     preg_match_all(
    //         '/<img[^>]+src="[^"]*\/storage\/' . preg_quote(self::LOCAL_STORAGE_FOLDER, '/') . '([^"]+)"/',
    //         $oldContent,
    //         $oldMatches
    //     );

    //     $oldImages = $oldMatches[1] ?? [];

    //     // Step 1: Handle Base64 <img> tags (your original logic)
    //     preg_match_all('/<img[^>]+src="data:(image\/[a-zA-Z]+);base64,([^"]+)"/', $content, $matches, PREG_SET_ORDER);

    //     $imagesData = [];
    //     $counter = 0;

    //     foreach ($matches as $match) {
    //         $mime = $match[1];
    //         $base64Data = $match[2];

    //         $imageBinary = base64_decode($base64Data);

    //         // Validate size (max 5MB)
    //         if (strlen($imageBinary) > 5 * 1024 * 1024) {
    //             $this->dispatch('flashMessage', type: 'error', message: 'One of the images exceeds the 5MB limit.');
    //             $this->dispatch('suneditor:refresh');
    //             return;
    //         }

    //         // Determine extension
    //         $extension = explode('/', $mime)[1];

    //         // Create filename using time()
    //         $fileName = time() . '_' . $counter++ . '.' . $extension;

    //         Storage::disk('public')->put(self::LOCAL_STORAGE_FOLDER . $fileName, $imageBinary);

    //         $imagesData[] = [
    //             'filename' => $fileName,
    //             'mime' => $mime,
    //             'size' => strlen($imageBinary),
    //         ];

    //         $storageUrl = asset('storage/' . self::LOCAL_STORAGE_FOLDER . $fileName);

    //         // Replace Base64 <img> tag with URL
    //         $content = str_replace($match[0], '<img src="' . $storageUrl . '"', $content);
    //     }

    //     // Step 2: Add target="_blank" to <a> tags (your logic)
    //     $content = preg_replace('/<a\s+([^>]*?)href=/', '<a $1 target="_blank" href=', $content);

    //     // Step 3: Find all final images in updated content
    //     preg_match_all(
    //         '/<img[^>]+src="[^"]*\/storage\/' . preg_quote(self::LOCAL_STORAGE_FOLDER, '/') . '([^"]+)"/',
    //         $content,
    //         $finalMatches
    //     );

    //     $finalImages = $finalMatches[1] ?? [];

    //     // Step 4: Delete removed images
    //     $imagesToDelete = array_diff($oldImages, $finalImages);

    //     foreach ($imagesToDelete as $img) {
    //         $path = self::LOCAL_STORAGE_FOLDER . $img;

    //         if (Storage::disk('public')->exists($path)) {
    //             Storage::disk('public')->delete($path);
    //         }
    //     }

    //     // Step 5: Save updated content (your logic)
    //     $payload = [
    //         'type' => 'doc',
    //         'content' => $content,
    //     ];

    //     $component->update([
    //         'data' => json_encode($payload),
    //     ]);

    //     // Update timestamp
    //     $this->folder->update(['updated_at' => now()]);
    //     $this->folder->project->update(['updated_at' => now()]);

    //     $this->dispatch('flashMessage', type: 'success', message: 'Content saved successfully!');
    //     $this->dispatch('suneditor:refresh');
    // }

    public function render()
    {
        return view('livewire.module-handout');
    }
}