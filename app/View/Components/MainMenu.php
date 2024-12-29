<?php

namespace App\View\Components;

use App\Models\Menu;
use Illuminate\View\Component;

class MainMenu extends Component
{
    public function render()
    {
        $menus = Menu::whereNull('parent_id')->orderBy('order')->with(['children' => function ($query) {
            $query->orderBy('order');
        }])->get();

        return view('components.main-menu', compact('menus'));
    }
}