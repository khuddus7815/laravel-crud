<?php

namespace App\Livewire\Admin; // <-- UPDATED NAMESPACE

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class Categories extends Component
{
    public $categories;
    public $newCategoryName = '';
    public $showAddCategoryModal = false;
    public $selectedCategoryId;

    public $showDeleteConfirmModal = false;
    public $categoryToDeleteId;

    public function mount()
    {
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $user = Auth::guard('web')->user();

        if ($user) {
            $this->categories = $user->categories()->orderBy('name')->get();
        } else {
            $this->categories = collect();
        }
    }

    public function openAddCategoryModal()
    {
        $this->reset('newCategoryName');
        $this->showAddCategoryModal = true;
    }

    public function saveCategory()
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:255|unique:categories,name,NULL,id,user_id,' . Auth::id(),
        ]);

        Auth::guard('web')->user()->categories()->create([
            'name' => $this->newCategoryName,
        ]);

        $this->showAddCategoryModal = false;
        $this->loadCategories();
        session()->flash('category_message', 'Category added successfully.');
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategoryId = $categoryId;
        $this->dispatch('categorySelected', categoryId: $categoryId);
    }

    #[On('categorySelected')]
    public function updateSelectedCategory($categoryId)
    {
        $this->selectedCategoryId = $categoryId;
    }

    public function confirmCategoryDeletion($id)
    {
        $this->categoryToDeleteId = $id;
        $this->showDeleteConfirmModal = true;
    }

    public function deleteCategory()
    {
        $category = Auth::guard('web')->user()->categories()->find($this->categoryToDeleteId);

        if ($category) {
            $category->delete();
            session()->flash('category_message', 'Category deleted successfully.');

            if ($this->selectedCategoryId == $this->categoryToDeleteId) {
                $this->selectedCategoryId = null;
                $this->dispatch('categorySelected', categoryId: null);
            }
        }

        $this->showDeleteConfirmModal = false;
        $this->loadCategories();
        $this->reset('categoryToDeleteId');
    }

    public function render()
    {
        return view('livewire.admin.categories'); // <-- Point to the correct view
    }
}
