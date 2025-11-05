<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    /**
     * Create a new policy instance.
     */
    public function before(User $user, string $ability, $model = null)
    {
        if ($model instanceof Group) {
            $groupId = $model->id;

            if ($user->hasPermissionTo("manage_group_{$groupId}")) {
                return true;
            }
        }

        return null;
    }

    public function dashboard(User $user, Group $group)
    {
        return $user->hasPermissionTo("group_dashboard_{$group->id}");
    }

    public function group_user(User $user, Group $group)
    {
        return $user->hasPermissionTo("group_user_{$group->id}");
    }
    public function group_events(User $user, Group $group){
        return $user->hasPermissionTo("group_events_{$group->id}");
    }
    public function group_add_user(User $user,Group $group){
        return $user->hasPermissionTo("group_add_user_{$group->id}");
    }
    public function group_delete_user(User $user,Group $group){
        return $user->hasPermissionTo("group_delete_user_{$group->id}");
    }
    public function group_edit_user(User $user,Group $group){
        return $user->hasPermissionTo("group_delete_user_{$group->id}");
    }
}
