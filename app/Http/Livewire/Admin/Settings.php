<?php

namespace App\Http\Livewire\Admin;

use App\Models\Setting;
use App\Models\User;
use Livewire\Component;

class Settings extends Component
{

    public $users;
    public $selectedUserId = ""; // Default to Global (empty string)
    
    // Unified properties for the form
    public $allow_registration;
    public $allow_send_messages;
    public $allow_send_images;
    public $allow_send_audio;
    public $is_active = true;

    public function mount()
    {
        $this->users = User::all();
        $this->loadSettings();
    }

    public function updatedSelectedUserId()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        if ($this->selectedUserId) {
            // Load User Settings
            $user = User::find($this->selectedUserId);
            if ($user) {
                $this->allow_send_messages = (bool) $user->allow_send_messages;
                $this->allow_send_images = (bool) $user->allow_send_images;
                $this->allow_send_audio = (bool) $user->allow_send_audio;
                $this->is_active = (bool) $user->is_active;
                // Registration is not a user setting, so we can ignore or nullify it
                $this->allow_registration = false; 
            }
        } else {
            // Load Global Settings
            $s = Setting::instance();
            $this->allow_registration = (bool) $s->allow_registration;
            $this->allow_send_messages = (bool) $s->allow_send_messages;
            $this->allow_send_images = (bool) $s->allow_send_images;
            $this->allow_send_audio = (bool) $s->allow_send_audio;
            $this->is_active = true; // Not used for global
        }
    }

    public function save()
    {
        if ($this->selectedUserId) {
            // Save User Settings
            $user = User::find($this->selectedUserId);
            if ($user) {
                $user->update([
                    'allow_send_messages' => (bool) $this->allow_send_messages,
                    'allow_send_images' => (bool) $this->allow_send_images,
                    'allow_send_audio' => (bool) $this->allow_send_audio,
                    'is_active' => (bool) $this->is_active,
                ]);
                session()->flash('status', 'Permisos de usuario actualizados correctamente');
            }
        } else {
            // Save Global Settings
            $s = Setting::instance();
            $s->update([
                'allow_registration' => (bool) $this->allow_registration,
                'allow_send_messages' => (bool) $this->allow_send_messages,
                'allow_send_images' => (bool) $this->allow_send_images,
                'allow_send_audio' => (bool) $this->allow_send_audio,
            ]);
            session()->flash('status', 'Ajustes globales guardados correctamente');
        }
    }

    public function render()
    {
        return view('livewire.admin.settings')->layout('layouts.app');
    }
}

