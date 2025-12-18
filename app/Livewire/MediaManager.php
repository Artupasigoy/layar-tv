<?php

namespace App\Livewire;

use App\Models\Media;
use App\Models\Setting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class MediaManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $file;
    public $type = 'image'; // Default

    public function save()
    {
        // Get max file size from settings (in MB), convert to KB for validation
        $settings = Setting::first();
        $maxFileSizeKB = ($settings->max_file_size ?? 50) * 1024;

        $this->validate([
            'file' => "required|file|mimes:jpg,jpeg,png,webp,mp4|max:{$maxFileSizeKB}",
        ], [
            'file.max' => 'Ukuran file tidak boleh lebih dari ' . ($settings->max_file_size ?? 50) . ' MB.',
        ]);

        $path = $this->file->store('media', 'public');
        $mime = $this->file->getMimeType();
        $type = str_contains($mime, 'video') ? 'video' : 'image';

        // Auto-generate title from filename
        $originalTitle = pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
        $title = $originalTitle;
        $counter = 1;

        // Check for duplicates and append increment
        while (Media::where('title', $title)->exists()) {
            $title = $originalTitle . '_' . $counter;
            $counter++;
        }

        Media::create([
            'title' => $title,
            'file_path' => $path,
            'type' => $type,
            'file_size' => $this->file->getSize(),
            'order' => Media::max('order') + 1,
            'is_active' => true,
        ]);

        $this->reset(['file', 'type']);
        $this->reset(['file', 'type']);
        $this->dispatch('notify', message: 'Media berhasil diupload!', type: 'success');
    }

    public $showDeleteModal = false;
    public $mediaIdToDelete = null;

    public function confirmDelete($id)
    {
        $this->mediaIdToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->mediaIdToDelete = null;
    }

    public $showPreviewModal = false;
    public $previewMedia = null;

    public function openPreview($id)
    {
        $this->previewMedia = Media::find($id);
        if ($this->previewMedia) {
            $this->showPreviewModal = true;
        }
    }

    public function closePreview()
    {
        $this->showPreviewModal = false;
        $this->previewMedia = null;
    }

    public function destroy()
    {
        if ($this->mediaIdToDelete) {
            $media = Media::find($this->mediaIdToDelete);
            if ($media) {
                Storage::disk('public')->delete($media->file_path);
                $media->delete();
                $this->dispatch('notify', message: 'Media berhasil dihapus.', type: 'success');
            }
        }

        // Reset modal state
        $this->showDeleteModal = false;
        $this->mediaIdToDelete = null;
    }

    public function toggleStatus($id)
    {
        $media = Media::find($id);
        if ($media) {
            $media->update(['is_active' => !$media->is_active]);
            $status = $media->is_active ? 'diaktifkan' : 'dinonaktifkan';
            $this->dispatch('notify', message: "Media berhasil $status.", type: 'success');
        }
    }

    public function moveUp($id)
    {
        $current = Media::find($id);
        $previous = Media::where('order', '<', $current->order)->orderBy('order', 'desc')->first();

        if ($previous) {
            $tempOrder = $current->order;
            $current->update(['order' => $previous->order]);
            $previous->update(['order' => $tempOrder]);
            $this->dispatch('notify', message: 'Urutan berhasil diubah.', type: 'success');
        }
    }

    public function moveDown($id)
    {
        $current = Media::find($id);
        $next = Media::where('order', '>', $current->order)->orderBy('order', 'asc')->first();

        if ($next) {
            $tempOrder = $current->order;
            $current->update(['order' => $next->order]);
            $next->update(['order' => $tempOrder]);
            $this->dispatch('notify', message: 'Urutan berhasil diubah.', type: 'success');
        }
    }

    public $filterStatus = 'all';

    public function setFilter($status)
    {
        $this->filterStatus = $status;
        $this->resetPage(); // Reset pagination when filter changes
    }

    public function render()
    {
        $query = Media::orderBy('order');

        if ($this->filterStatus === 'active') {
            $query->where('is_active', true);
        } elseif ($this->filterStatus === 'inactive') {
            $query->where('is_active', false);
        }

        // Get max file size from settings for client-side validation
        $settings = Setting::first();
        $maxFileSizeMB = $settings->max_file_size ?? 50;

        return view('livewire.media-manager', [
            'medias' => $query->paginate(6),
            'maxFileSizeMB' => $maxFileSizeMB,
        ])->layout('layouts.app');
    }
}
