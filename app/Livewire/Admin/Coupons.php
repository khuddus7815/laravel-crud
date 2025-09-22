<?php

namespace App\Livewire\Admin; // <-- UPDATED NAMESPACE

use Livewire\Component;
use App\Models\Coupon;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Coupons extends Component
{
    use WithPagination;

    public $showModal = false;
    public $couponId;
    public $code, $type = 'percent', $value, $expires_at;

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        $this->couponId = $id;
        $this->code = $coupon->code;
        $this->type = $coupon->type;
        $this->value = $coupon->value;
        $this->expires_at = $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : null;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'code' => 'required|string|unique:coupons,code,' . $this->couponId,
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0',
            'expires_at' => 'nullable|date',
        ]);

        Coupon::updateOrCreate(['id' => $this->couponId], [
            'user_id' => auth()->id(),
            'code' => strtoupper($this->code),
            'type' => $this->type,
            'value' => $this->value,
            'expires_at' => $this->expires_at,
        ]);

        session()->flash('message', 'Coupon saved successfully.');
        $this->closeModal();
    }
    
    public function delete($id)
    {
        Coupon::findOrFail($id)->delete();
        session()->flash('message', 'Coupon deleted successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->couponId = null;
        $this->code = '';
        $this->type = 'percent';
        $this->value = '';
        $this->expires_at = null;
    }

    public function render()
    {
        return view('livewire.admin.coupons', [ // <-- Point to the correct view
            'coupons' => Coupon::where('user_id', auth()->id())->latest()->paginate(10),
        ]);
    }
}
