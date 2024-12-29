<?php

namespace App\View\Components;

use App\Models\Menu;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MobileMenu extends Component
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $menus = Menu::whereNull('parent_id')->orderBy('order')->with(['children' => function ($query) {
            $query->orderBy('order');
        }])->get();

        return view('components.mobile-menu', compact('menus'));
    }
}
