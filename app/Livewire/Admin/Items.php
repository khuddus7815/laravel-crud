<?php

namespace App\Livewire\Admin; // <-- UPDATED NAMESPACE

use App\Models\Category;
use Livewire\Component;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Items extends Component
{
    use WithFileUploads;
    use WithPagination;

    // Datatable properties
    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Modal properties
    public $showModal = false;
    public $showDeleteModal = false;
    public $showViewModal = false;

    // Form properties
    public $itemId;
    public $name;
    public $description;
    public $price;
    public $image;
    public $existingImage;
    public $viewingItem;

    // Properties for managing categories
    public $selectedCategoryId;
    public $selectedCategoryName;

    #[Computed]
    public function categories()
    {
        return Category::where('user_id', auth()->id())->orderBy('name')->get();
    }

    // Validation rules
    protected function rules()
    {
        return [
            'name' => 'required|min:3',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:1024', // 1MB Max
        ];
    }

    #[On('categorySelected')]
    public function filterByCategory($categoryId)
    {
        $this->selectedCategoryId = $categoryId;
        $category = $this->categories()->firstWhere('id', $categoryId);
        $this->selectedCategoryName = $category ? $category->name : 'Items';
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if (!in_array($field, ['name', 'price', 'created_at'])) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        if (!$this->selectedCategoryId) {
            session()->flash('message', 'Please select a category before adding an item.');
            session()->flash('message_type', 'error');
            return;
        }
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);
        $this->itemId = $item->id;
        $this->name = $item->name;
        $this->description = $item->description;
        $this->price = $item->price;
        $this->existingImage = $item->image_path;
        $this->showModal = true;
    }

    public function view($id)
    {
        $this->viewingItem = Item::findOrFail($id);
        $this->showViewModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingItem = null;
    }

    private function resetForm()
    {
        $this->itemId = null;
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->image = null;
        $this->existingImage = null;
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate(array_merge($this->rules(), [
            'selectedCategoryId' => 'required|exists:categories,id'
        ]));

        $data = [
            'user_id' => auth()->id(),
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category_id' => $this->selectedCategoryId,
        ];

        $imagePathToUpdate = $this->existingImage;

        if ($this->image) {
            if ($this->existingImage) {
                Storage::disk('public')->delete($this->existingImage);
            }
            $imagePathToUpdate = $this->image->store('items', 'public');
        }

        $data['image_path'] = $imagePathToUpdate;

        if ($this->itemId) {
            $item = Item::findOrFail($this->itemId);
            $item->update($data);
        } else {
            Item::create($data);
        }

        session()->flash('message', 'Item successfully ' . ($this->itemId ? 'updated.' : 'created.'));
        $this->closeModal();
    }

    public function delete($id)
    {
        $this->itemId = $id;
        $this->showDeleteModal = true;
    }

    public function confirmDelete()
    {
        $item = Item::findOrFail($this->itemId);

        if ($item->image_path) {
            Storage::disk('public')->delete($item->image_path);
        }

        $item->delete();
        session()->flash('message', 'Item successfully deleted.');
        $this->showDeleteModal = false;
        $this->itemId = null;
    }

    public function render()
    {
        $query = Item::where('user_id', auth()->id());

        if ($this->selectedCategoryId) {
            $query->where('category_id', $this->selectedCategoryId);
        } else {
            $query->whereRaw('1 = 0');
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $items = $query->orderBy($this->sortField, $this->sortDirection)
                       ->paginate(10);
        
        // Make sure this points to the correct view file
        return view('livewire.admin.items', [
            'items' => $items
        ]);
    }
}
