<?php

namespace App\Http\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class UserManagement extends Component
{
    public $users = [];

    public function mount()
    {
        $this->users = User::query()->orderBy('name')->get()->toArray();
    }

    public function setRole($userId, $role)
    {
        $role = in_array($role, ['admin', 'user']) ? $role : 'user';
        $user = User::findOrFail($userId);
        $user->role = $role;
        $user->save();

        $this->users = User::query()->orderBy('name')->get()->toArray();
        session()->flash('status', 'Rol actualizado');
    }

    public function render()
    {
        return view('livewire.admin.user-management', [
            'list' => User::query()->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}

