<?php

namespace App\Livewire;

use App\Jobs\GeneratePlaylistJob;
use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class DisplaySettings extends Component
{
    use WithFileUploads;

    public $logo;
    public $existingLogo;
    public $show_clock;
    public $enable_animation;
    public $image_duration;
    public $max_file_size;
    public $auto_reload_interval;
    public $delete_logo_pending = false;

    public function mount()
    {
        $settings = Setting::first();
        $this->existingLogo = $settings->logo_path;
        $this->show_clock = (bool) $settings->show_clock;
        $this->enable_animation = (bool) $settings->enable_animation;
        $this->image_duration = $settings->image_duration;
        $this->max_file_size = $settings->max_file_size ?? 50;
        $this->auto_reload_interval = $settings->auto_reload_interval ?? 6;
    }

    public function deleteLogo()
    {
        // Mark for deletion but do not save yet
        $this->delete_logo_pending = true;
        $this->existingLogo = null; // Hide from UI
    }

    /**
     * Resize logo to max 200px width to reduce file size
     */
    protected function resizeLogo($uploadedFile): string
    {
        $tempPath = $uploadedFile->getRealPath();
        
        // Get image info
        $imageInfo = getimagesize($tempPath);
        if (!$imageInfo) {
            // If can't process, just store original
            return $uploadedFile->store('logos', 'public');
        }

        [$origWidth, $origHeight, $imageType] = $imageInfo;

        // Create source image (suppress warnings about incorrect sRGB profiles)
        $sourceImage = match ($imageType) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($tempPath),
            IMAGETYPE_PNG => @imagecreatefrompng($tempPath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($tempPath),
            IMAGETYPE_GIF => @imagecreatefromgif($tempPath),
            default => null,
        };

        if (!$sourceImage) {
            return $uploadedFile->store('logos', 'public');
        }

        // Calculate new dimensions (max 200px width for logo)
        $maxWidth = 200;
        if ($origWidth <= $maxWidth) {
            imagedestroy($sourceImage);
            return $uploadedFile->store('logos', 'public');
        }

        $newWidth = $maxWidth;
        $newHeight = (int) ($origHeight * ($newWidth / $origWidth));

        // Create resized image
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG
        if ($imageType === IMAGETYPE_PNG) {
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
            imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        // Generate filename and save
        $filename = 'logos/' . uniqid() . '_' . time() . '.png';
        $outputPath = Storage::disk('public')->path($filename);
        
        Storage::disk('public')->makeDirectory('logos');
        
        // Save as PNG to preserve quality and transparency
        imagepng($resizedImage, $outputPath, 8); // Quality 8 (0-9, 0 = best)

        // Cleanup
        imagedestroy($sourceImage);
        imagedestroy($resizedImage);

        return $filename;
    }

    public function save()
    {
        $this->validate([
            'logo' => 'nullable|image|max:1024', // 1MB
            'show_clock' => 'boolean',
            'enable_animation' => 'boolean',
            'image_duration' => 'required|integer|min:3',
            'max_file_size' => 'required|integer|min:1|max:500',
            'auto_reload_interval' => 'required|integer|min:1|max:24',
        ], [
            'logo.max' => 'Ukuran file tidak boleh lebih dari 1 MB.',
            'logo.image' => 'File harus berupa gambar.',
            'max_file_size.max' => 'Maksimal ukuran file tidak boleh lebih dari 500 MB.',
            'auto_reload_interval.max' => 'Interval reload maksimal 24 jam.',
        ]);

        $settings = Setting::first();

        $data = [
            'show_clock' => $this->show_clock,
            'enable_animation' => $this->enable_animation,
            'image_duration' => $this->image_duration,
            'max_file_size' => $this->max_file_size,
            'auto_reload_interval' => $this->auto_reload_interval,
        ];

        if ($this->logo) {
            // Delete old logo if exists
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            // Resize and store new logo
            $data['logo_path'] = $this->resizeLogo($this->logo);
        } elseif ($this->delete_logo_pending) {
            // Only remove if pending delete is true AND no new logo uploaded
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $data['logo_path'] = null;
        }

        $settings->update($data);

        // Regenerate playlist with new settings
        GeneratePlaylistJob::dispatch();

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
