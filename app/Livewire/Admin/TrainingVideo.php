<?php

namespace App\Livewire\Admin;

use App\Http\Requests\Admin\TrainingVideoRequest;
use App\Models\Shared\Setting;
use App\Services\Admin\AdminService;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class TrainingVideo extends Component
{
    use WithFileUploads;

    public $video;

    public $settings;

    public $videoPath = '/training-video';

    public $uploadProgress = 0;

    public $isUploading = false;

    public int $maxUploadSize = 30;

    public $error = false;

    public string $message;

    public $showToast = false;

    public function mount()
    {
        $this->settings = Setting::first();
        $this->videoPath = $this->getVideoUrl();
        $this->message = __('admin.validation.upload_failed');
    }

    public function getVideoUrl(): ?string
    {
        if ($this->settings?->training_video) {
            return Storage::disk(config('filesystems.default'))->url($this->settings->training_video);
        }

        return null;
    }

    public function updatedVideo()
    {
        // Reset previous errors and state
        $this->resetValidation();
        $this->showToast = false;
        $this->error = false;

        $request = new TrainingVideoRequest();
        $validator = Validator::make(
            ['video' => $this->video],
            $request->rules(),
            $request->messages(),
            $request->attributes()
        );

        if ($validator->fails()) {
            $this->message = $validator->errors()->first('video');
            $this->addError('video', $this->message);
            $this->video = null;
            $this->showToast = true;
            $this->error = true;
            $this->dispatch('hide-toast');
            return;
        }

        // Start upload immediately
        $this->uploadVideo();
    }

    public function uploadVideo()
    {
        if ($this->video) {
            $adminService = app(AdminService::class);
            $result = $adminService->uploadTrainingVideo($this->video);
            $this->video = null;

            if ($result['success']) {
                $this->uploadProgress = 100;
                $this->videoPath = $result['path'];
                session()->flash('success', $result['message']);
            } else {
                session()->flash('error', __('admin.validation.upload_failed'));
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.training-video');
    }
}
