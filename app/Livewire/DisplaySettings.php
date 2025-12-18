<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;

class DisplaySettings extends Component
{
    use WithFileUploads;

    public $logo;
    public $existingLogo;
    public $show_clock;
    public $enable_animation;
    public $image_duration;
    public $max_file_size;
    public $delete_logo_pending = false;

    public function mount()
    {
        $settings = Setting::first();
        $this->existingLogo = $settings->logo_path;
        $this->show_clock = (bool) $settings->show_clock;
        $this->enable_animation = (bool) $settings->enable_animation;
        $this->image_duration = $settings->image_duration;
        $this->max_file_size = $settings->max_file_size ?? 50;
    }

    public function deleteLogo()
    {
        // Mark for deletion but do not save yet
        $this->delete_logo_pending = true;
        $this->existingLogo = null; // Hide from UI
    }

    public function save()
    {
        $this->validate([
            'logo' => 'nullable|image|max:1024', // 1MB
            'show_clock' => 'boolean',
            'enable_animation' => 'boolean',
            'image_duration' => 'required|integer|min:3',
            'max_file_size' => 'required|integer|min:1|max:500',
        ], [
            'logo.max' => 'Ukuran file tidak boleh lebih dari 1 MB.',
            'logo.image' => 'File harus berupa gambar.',
            'max_file_size.max' => 'Maksimal ukuran file tidak boleh lebih dari 500 MB.',
        ]);

        $settings = Setting::first();

        $data = [
            'show_clock' => $this->show_clock,
            'enable_animation' => $this->enable_animation,
            'image_duration' => $this->image_duration,
            'max_file_size' => $this->max_file_size,
        ];

        if ($this->logo) {
            $data['logo_path'] = $this->logo->store('logos', 'public');
        } elseif ($this->delete_logo_pending) {
            // Only remove if pending delete is true AND no new logo uploaded
            $data['logo_path'] = null;
        }

        $settings->update($data);

        // Reset state
        $this->existingLogo = $settings->logo_path;
        $this->logo = null;
        $this->delete_logo_pending = false;

        $this->dispatch('notify', message: 'Pengaturan berhasil disimpan.', type: 'success');
    }

    public function render()
    {
        return view('livewire.display-settings')->layout('layouts.app');
    }
}
