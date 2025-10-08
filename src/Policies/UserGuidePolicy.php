<?php
namespace ModuleUserGuide\Policies;

use App\Models\User;
use ModuleUserGuide\Models\UserGuide;

class UserGuidePolicy
{
    public function viewAny(User $user)
    {
        return $user->can(config('moduleuserguide.permissions.index_user_guide'));
    }

    public function view(User $user, UserGuide $userGuide)
    {
        return true; // everyone can view details
    }

    public function create(User $user)
    {
        return $user->can(config('moduleuserguide.permissions.create_user_guide'));
    }

    public function update(User $user, UserGuide $userGuide)
    {
        return $user->can(config('moduleuserguide.permissions.update_user_guide'));
    }

    public function delete(User $user, UserGuide $userGuide)
    {
        return $user->can(config('moduleuserguide.permissions.delete_user_guide'));
    }
}
