<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Shared\MassMessage as Message;
use Exception;

class MassMessage extends Component
{
    /**
     * @var string Message of the mass message
     */
    public string $message = '';
    public bool $status = true;
    public bool $isUpdate = false;

    /**
     * @var bool Whether to show the success message
     */
    public bool $showSuccess = false;

    /**
     * Get the validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'message' => ['required', 'max:500'],
        ];

    }

    /**
     * Get the messages for the validation rules.
     *
     * @return array
     */
    protected function messages()
    {
        return [
            'message.required' => __('admin.validation.message_required'),
            'message.max' => __('admin.validation.message_max'),
        ];
    }


    /**
     * Send mass message to selected user groups
     *
     * @return void
     */
    public function sendMessage()
    {
        $this->validate();

        try {
            if ($this->isUpdate) {
                $message = Message::first();
                $message->update([
                    'message' => $this->message,
                    'status' => $this->status,
                ]);

                $alert_message = $this->status ? __('admin.message.mass_message_updated') : __('admin.message.status_updated');

            } else {
                Message::create([
                    'message' => $this->message,
                    'status' => $this->status,
                ]);
                $alert_message = $this->status ? __('admin.message.mass_message_saved') : __('admin.message.status_updated');
            }
        } catch (Exception $e) {
            $alert_message = __('admin.message.mass_message_failed');
        }

        session()->flash('success', $alert_message);
        return redirect()->route('admin.mass-message');
    }

    /**
     * Render the component
     *
     * @return \Illuminate\View\View
     */
    /**
     * Initialize the component state.
     *
     * @return void
     */
    public function mount(): void
    {
        $existingMessage = Message::first();

        if ($existingMessage) {
            $this->message = $existingMessage->message;
            $this->status = $existingMessage->status;
            $this->isUpdate = true;
        }
    }

    public function render()
    {
        return view('livewire.admin.mass-message');
    }
}
