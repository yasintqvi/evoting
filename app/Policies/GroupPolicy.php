<?php

namespace App\Policies;

use App\Enums\Permission;
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

            if ($user->hasPermissionTo(Permission::GROUP_OWNER_GROUP_ID->value.$groupId)) {
                return true;
            }
        }

        return null;
    }
//
//    public function dashboard(User $user, Group $group)
//    {
//        return $user->hasPermissionTo(Permission::VIEW_GROUP->value . ' ' . $group->id);
//    }
//
//    public function group_user(User $user, Group $group)
//    {
//        return $user->hasPermissionTo(Permission::VIEW_GROUP_USERS->value . ' ' . $group->id);
//    }
//
//    public function group_events(User $user, Group $group)
//    {
//        return $user->hasPermissionTo(Permission::VIEW_GROUP_EVENT->value . ' ' . $group->id);
//    }
//
//    public function group_create_events(User $user, Group $group)
//    {
//        return $user->hasPermissionTo(Permission::CREATE_GROUP_EVENT->value . ' ' . $group->id);
//    }
//
//    public function group_edit_events(User $user, Group $group)
//    {
//        return $user->hasPermissionTo(Permission::EDIT_GROUP_EVENT->value . ' ' . $group->id);
//    }
//
//    public function group_add_user(User $user, Group $group)
//    {
//        return $user->hasPermissionTo(Permission::CREATE_GROUP_USERS->value . ' ' . $group->id);
//    }
//
//    public function group_delete_user(User $user, Group $group)
//    {
//        return $user->hasPermissionTo(Permission::DELETE_GROUP_USERS->value . ' ' . $group->id);
//    }
//
//    public function group_edit_user(User $user, Group $group)
//    {
//        return $user->hasPermissionTo(Permission::EDIT_GROUP_USERS->value . ' ' . $group->id);
//    }
//
//    public function group_elections(User $user, Group $group)
//    {
//        return $user->hasPermissionTo(Permission::EDIT_GROUP_USERS->value . ' ' . $group->id);
//    }
}
