<?php

namespace App\Livewire;

use App\Models\Media;
use App\Models\Setting;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function render()
    {
        return view('livewire.admin-dashboard', [
            'totalMedia' => Media::count(),
            'activeMedia' => Media::where('is_active', true)->count(),
            'setting' => Setting::first(),
        ])->layout('layouts.app');
    }
}
