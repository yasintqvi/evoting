<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum Permission: string
{
    use EnumValues;

    case CREATE_GROUP = 'create group';
    case VIEW_GROUP = 'view group';
    case EDIT_GROUP = 'edit group';
    case UPDATE_GROUP = 'update group';
    case DELETE_GROUP = 'delete group';
    case LIST_GROUPS = 'list groups';

    case VIEW_USERS = 'view users';
    case CREATE_USERS = 'create users';
    case EDIT_USERS = 'edit users';
    case UPDATE_USERS = 'update users';
    case DELETE_USERS = 'delete users';
    case IMPORT_USERS = 'import users';
    case CHANGE_ACCESS = 'changes access';

    case VIEW_GROUP_USERS = 'view group users';
    case CREATE_GROUP_USERS = 'create group users';
    case EDIT_GROUP_USERS = 'edit group users';
    case UPDATE_GROUP_USERS = 'update group users';
    case DELETE_GROUP_USERS = 'delete group users';
    case MANAGE_GROUP_USER_ACCESS = 'manage group user access';

    case VIEW_ELECTIONS = 'view elections';
    case CREATE_ELECTIONS = 'create elections';
    case EDIT_ELECTIONS = 'edit elections';
    case UPDATE_ELECTIONS = 'update elections';
    case DELETE_ELECTIONS = 'delete elections';
    case SHOW_ELECTION = 'show election';

    case VIEW_CANDIDATES = 'view candidates';
    case CREATE_CANDIDATES = 'create candidates';
    case EDIT_CANDIDATES = 'edit candidates';
    case UPDATE_CANDIDATES = 'update candidates';
    case DELETE_CANDIDATES = 'delete candidates';

    case VIEW_PARTICIPANTS = 'view participants';
    case CREATE_PARTICIPANTS = 'create participants';
    case EDIT_PARTICIPANTS = 'edit participants';
    case UPDATE_PARTICIPANTS = 'update participants';
    case DELETE_PARTICIPANTS = 'delete participants';
    case IMPORT_PARTICIPANTS = 'import participants';
    case STORE_TABLE_PARTICIPANT = 'store table participant';

    case VIEW_ELECTION_ROUNDS = 'view election rounds';
    case CREATE_ELECTION_ROUNDS = 'create election rounds';
    case EDIT_ELECTION_ROUNDS = 'edit election rounds';
    case UPDATE_ELECTION_ROUNDS = 'update election rounds';
    case DELETE_ELECTION_ROUNDS = 'delete election rounds';

    case CREATE_ATTENDANCE = 'create attendance';
    case STORE_ATTENDANCE = 'store attendance';

    case VIEW_DASHBOARD = 'view dashboard';

    case VIEW_ROLES = 'view roles';
    case CREATE_ROLES = 'create roles';
    case EDIT_ROLES = 'edit roles';
    case UPDATE_ROLES = 'update roles';
    case DELETE_ROLES = 'delete roles';

    case VIEW_PERMISSIONS = 'view permissions';

    case LOG_ACTIVITIES = 'view log activities';

    // Group Permissions
    case VIEW_GROUP_EVENT = 'view group event';
    case CREATE_GROUP_EVENT = 'create group event';
    case EDIT_GROUP_EVENT = 'edit group event';
    case DELETE_GROUP_EVENT = 'delete group event';

    /**
     * Get permissions based on role
     */
    public static function getPermissionsByRole(Role $role): array
    {
        return match ($role) {
            Role::Manager => [
                self::CREATE_GROUP,
                self::VIEW_GROUP,
                self::EDIT_GROUP,
                self::UPDATE_GROUP,
                self::LIST_GROUPS,
                self::VIEW_USERS,
                self::CREATE_USERS,
                self::EDIT_USERS,
                self::UPDATE_USERS,
                self::DELETE_USERS,
                self::VIEW_GROUP_USERS,
                self::CREATE_GROUP_USERS,
                self::EDIT_GROUP_USERS,
                self::UPDATE_GROUP_USERS,
                self::DELETE_GROUP_USERS,
                self::VIEW_ELECTIONS,
                self::CREATE_ELECTIONS,
                self::EDIT_ELECTIONS,
                self::UPDATE_ELECTIONS,
                self::DELETE_ELECTIONS,
                self::SHOW_ELECTION,
                self::VIEW_CANDIDATES,
                self::CREATE_CANDIDATES,
                self::EDIT_CANDIDATES,
                self::UPDATE_CANDIDATES,
                self::DELETE_CANDIDATES,
                self::VIEW_PARTICIPANTS,
                self::CREATE_PARTICIPANTS,
                self::EDIT_PARTICIPANTS,
                self::UPDATE_PARTICIPANTS,
                self::DELETE_PARTICIPANTS,
                self::IMPORT_PARTICIPANTS,
                self::STORE_TABLE_PARTICIPANT,
                self::VIEW_ELECTION_ROUNDS,
                self::CREATE_ELECTION_ROUNDS,
                self::EDIT_ELECTION_ROUNDS,
                self::UPDATE_ELECTION_ROUNDS,
                self::DELETE_ELECTION_ROUNDS,
                self::CREATE_ATTENDANCE,
                self::STORE_ATTENDANCE,
                self::VIEW_DASHBOARD,
                self::VIEW_ROLES,
                self::CREATE_ROLES,
                self::EDIT_ROLES,
                self::UPDATE_ROLES,
                self::DELETE_ROLES,
                self::VIEW_PERMISSIONS,
                self::CHANGE_ACCESS,
                self::LOG_ACTIVITIES,
                self::VIEW_GROUP_EVENT,
                self::CREATE_GROUP_EVENT,
                self::EDIT_GROUP_EVENT,
                self::DELETE_GROUP_EVENT,
            ],
            Role::Secretary => [
                self::VIEW_ELECTIONS,
                self::SHOW_ELECTION,
                self::VIEW_CANDIDATES,
                self::VIEW_PARTICIPANTS,
                self::CREATE_PARTICIPANTS,
                self::EDIT_PARTICIPANTS,
                self::UPDATE_PARTICIPANTS,
                self::DELETE_PARTICIPANTS,
                self::IMPORT_PARTICIPANTS,
                self::STORE_TABLE_PARTICIPANT,
                self::VIEW_ELECTION_ROUNDS,
                self::CREATE_ELECTION_ROUNDS,
                self::EDIT_ELECTION_ROUNDS,
                self::UPDATE_ELECTION_ROUNDS,
                self::DELETE_ELECTION_ROUNDS,
                self::CREATE_ATTENDANCE,
                self::STORE_ATTENDANCE,
                self::VIEW_DASHBOARD,
            ],
            Role::GroupManager => [
                self::VIEW_DASHBOARD,
                self::MANAGE_GROUP_USER_ACCESS,
            ],
            default => [],
        };
    }

    public function fa(): string
    {
        return match ($this) {
            self::CREATE_GROUP => 'ایجاد گروه',
            self::VIEW_GROUP => 'مشاهده گروه',
            self::EDIT_GROUP => 'ویرایش گروه',
            self::UPDATE_GROUP => 'بروزرسانی گروه',
            self::DELETE_GROUP => 'حذف گروه',
            self::LIST_GROUPS => 'لیست گروه‌ها',
            self::VIEW_USERS => 'مشاهده کاربران',
            self::CREATE_USERS => 'ایجاد کاربر',
            self::EDIT_USERS => 'ویرایش کاربر',
            self::UPDATE_USERS => 'بروزرسانی کاربر',
            self::DELETE_USERS => 'حذف کاربر',
            self::IMPORT_USERS => 'وارد کردن کاربران',
            self::CHANGE_ACCESS => 'تغییر دسترسی کاربران',
            self::VIEW_GROUP_USERS => 'مشاهده کاربران گروه',
            self::CREATE_GROUP_USERS => 'ایجاد کاربر گروه',
            self::EDIT_GROUP_USERS => 'ویرایش کاربر گروه',
            self::UPDATE_GROUP_USERS => 'بروزرسانی کاربر گروه',
            self::DELETE_GROUP_USERS => 'حذف کاربر گروه',
            self::MANAGE_GROUP_USER_ACCESS => 'مدیریت دسترسی کاربران گروه',
            self::VIEW_ELECTIONS => 'مشاهده انتخابات',
            self::CREATE_ELECTIONS => 'ایجاد انتخابات',
            self::EDIT_ELECTIONS => 'ویرایش انتخابات',
            self::UPDATE_ELECTIONS => 'بروزرسانی انتخابات',
            self::DELETE_ELECTIONS => 'حذف انتخابات',
            self::SHOW_ELECTION => 'نمایش انتخابات',
            self::VIEW_CANDIDATES => 'مشاهده کاندیداها',
            self::CREATE_CANDIDATES => 'ایجاد کاندیدا',
            self::EDIT_CANDIDATES => 'ویرایش کاندیدا',
            self::UPDATE_CANDIDATES => 'بروزرسانی کاندیدا',
            self::DELETE_CANDIDATES => 'حذف کاندیدا',
            self::VIEW_PARTICIPANTS => 'مشاهده گروه‌کنندگان',
            self::CREATE_PARTICIPANTS => 'ایجاد گروه‌کننده',
            self::EDIT_PARTICIPANTS => 'ویرایش گروه‌کننده',
            self::UPDATE_PARTICIPANTS => 'بروزرسانی گروه‌کننده',
            self::DELETE_PARTICIPANTS => 'حذف گروه‌کننده',
            self::IMPORT_PARTICIPANTS => 'وارد کردن گروه‌کنندگان',
            self::STORE_TABLE_PARTICIPANT => 'ذخیره جدول گروه‌کنندگان',
            self::VIEW_ELECTION_ROUNDS => 'مشاهده دوره‌های انتخابات',
            self::CREATE_ELECTION_ROUNDS => 'ایجاد دوره انتخابات',
            self::EDIT_ELECTION_ROUNDS => 'ویرایش دوره انتخابات',
            self::UPDATE_ELECTION_ROUNDS => 'بروزرسانی دوره انتخابات',
            self::DELETE_ELECTION_ROUNDS => 'حذف دوره انتخابات',
            self::CREATE_ATTENDANCE => 'ایجاد حضور و غیاب',
            self::STORE_ATTENDANCE => 'ذخیره حضور و غیاب',
            self::VIEW_DASHBOARD => 'مشاهده داشبورد',
            self::VIEW_ROLES => 'مشاهده نقش‌ها',
            self::CREATE_ROLES => 'ایجاد نقش',
            self::EDIT_ROLES => 'ویرایش نقش',
            self::UPDATE_ROLES => 'بروزرسانی نقش',
            self::DELETE_ROLES => 'حذف نقش',
            self::VIEW_PERMISSIONS => 'مشاهده دسترسی‌ها',
            self::LOG_ACTIVITIES => 'مشاهده لاگ فعالیت‌ها',
            self::VIEW_GROUP_EVENT => 'مشاهده رویداد گروه',
            self::CREATE_GROUP_EVENT => 'ایجاد رویداد گروه',
            self::EDIT_GROUP_EVENT => 'ویرایش رویداد گروه',
            self::DELETE_GROUP_EVENT => 'حذف رویداد گروه',
            default => $this->value
        };
    }
}
