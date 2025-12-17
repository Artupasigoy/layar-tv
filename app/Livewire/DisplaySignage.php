<?php

namespace App\Livewire;

use App\Models\Media;
use App\Models\Setting;
use Livewire\Component;

class DisplaySignage extends Component
{
    public function getMediaAndSettings()
    {
        return [
            'media' => Media::where('is_active', true)->orderBy('order')->get()->map(function ($m) {
                return [
                    'id' => $m->id,
                    'type' => $m->type,
                    'url' => asset('storage/' . $m->file_path),
                ];
            }),
            'settings' => Setting::first(),
        ];
    }

    public function checkForUpdates($currentHash)
    {
        $data = $this->getMediaAndSettings();
        $newHash = md5(json_encode($data));

        if ($currentHash !== $newHash) {
            $this->dispatch('content-updated', [
                'data' => $data,
                'hash' => $newHash
            ]);
        }
    }

    public function render()
    {
        // Initial load
        $data = $this->getMediaAndSettings();
        return view('livewire.display-signage', [
            'initialData' => $data,
            'initialHash' => md5(json_encode($data)),
        ])->layout('layouts.guest'); // Use guest layout or empty layout
    }
}
