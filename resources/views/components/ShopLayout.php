<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class ShopLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        // This tells Laravel that <x-shop-layout> should use the
        // 'layouts.shop' Blade file as its template.
        return view('layouts.shop');
    }
}

