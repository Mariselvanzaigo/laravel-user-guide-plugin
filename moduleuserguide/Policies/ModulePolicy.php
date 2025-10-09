<?php
namespace ModuleUserGuide\Policies;

use App\Models\User;
use ModuleUserGuide\Models\Module;

class ModulePolicy
{
    public function index(User $user) {
        return $user->can(config('moduleuserguide.permissions.index_module'));
    }

    public function create(User $user) {
        return $user->can(config('moduleuserguide.permissions.create_module'));
    }

    public function store(User $user) {
        return $user->can(config('moduleuserguide.permissions.store_module'));
    }

    public function edit(User $user) {
        return $user->can(config('moduleuserguide.permissions.edit_module'));
    }

    public function update(User $user, Module $module) {
        return $user->can(config('moduleuserguide.permissions.update_module'));
    }

    public function delete(User $user, Module $module) {
        return $user->can(config('moduleuserguide.permissions.delete_module'));
    }

    public function view(User $user, Module $module) {
        return true; // everyone can view
    }
}
