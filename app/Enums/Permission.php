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


    // Group owner & user permissions
    case GROUP_OWNER_GROUPID = 'group_owner_group_';
    case DELETE_GROUP_USERS_GROUPID = 'delete_group_users_group_';
    case CREATE_GROUP_USERS_GROUPID = 'create_group_users_group_';
    case VIEW_GROUP_USERS_GROUPID = 'view_group_users_group_';
    case UPDATE_GROUP_USERS_GROUPID = 'update_group_users_group_';


    // Group event permissions
    case CREATE_GROUP_EVENT_GROUPID = 'create_group_event_group_';
    case VIEW_GROUP_EVENT_GROUPID = 'view_group_event_group_';
    case EDIT_GROUP_EVENT_GROUPID = 'edit_group_event_group_';
    case CLONE_GROUP_EVENT_GROUPID = 'clone_group_event_group_';
    case GROUP_EVENT_ELECTION_GROUPID = 'group_event_elections_group_';

    case GROUP_EVENT_SURVEY_GROUPID = 'group_event_survey_group_';
    case GROUP_EVENT_EDIT_SURVEY_GROUPID = 'group_event_edit_survey_group_';
    case GROUP_EVENT_CREATE_SURVEY_GROUPID = 'group_event_create_survey_group_'; //    case DELETE_GROUP_EVENT_GROUPID = 'delete_group_event_group_';

    // Attendance permissions
    case CREATE_ATTENDANCE_GROUPID = 'create_attendance_group_';
//    case VIEW_ATTENDANCE_GROUPID = 'view_attendance_group_';
//    case EDIT_ATTENDANCE_GROUPID = 'edit_attendance_group_';
//    case DELETE_ATTENDANCE_GROUPID = 'delete_attendance_group_';

    case CREATE_EVENT_EVENTID = 'events_create_';
    case UPDATE_EVENT_EVENTID = 'events_update_';
    case DELETE_EVENT_EVENTID = 'events_delete_';
    case SHOW_EVENT_EVENTID = 'events_show_';
    case ATTENDANCE_EVENT_EVENTID = 'events_attendance_';


    case CREATE_ELECTION_ELECTIONID = 'elections_create_';
    case UPDATE_ELECTION_ELECTIONID = 'elections_update_';
    case DELETE_ELECTION_ELECTIONID = 'elections_delete_';
    case SHOW_ELECTION_ELECTIONID = 'elections_show_';

    case CREATE_SURVEY_SURVEYID = 'survey_create_';
    case UPDATE_SURVEY_SURVEYID = 'survey_update_';
    case SHOW_SURVEY_SURVEYID = 'survey_show_';
    case DELETE_SURVEY_SURVEYID = 'survey_delete_';

    case EVENT_ELECTION_EVENTID = 'events_electionevent_';
    case EVENT_SURVEY_EVENTID = 'events_surveyevent_';


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
            default => 'Unknown permission',

        };
    }

    public static function withIdFa($permission)
    {
//        if (!preg_match("/\d+$/", $permission)) {
//            return $permission->fa(); // return normal label
//        }
        preg_match("/(\d+)$/", $permission, $matches);
        $id = $matches[1] ?? null;

        // Remove the trailing number to get the base string
        $base = preg_replace("/\d+$/", '', $permission);

        $label = match ($base) {
            self::GROUP_OWNER_GROUPID->value => 'مدیر گروه',
            self::DELETE_GROUP_USERS_GROUPID->value => 'حذف کاربران گروه',
            self::CREATE_GROUP_USERS_GROUPID->value => 'ایجاد کاربران گروه',
            self::VIEW_GROUP_USERS_GROUPID->value => 'مشاهده کاربران گروه',
            self::UPDATE_GROUP_USERS_GROUPID->value => 'ویرایش کاربران گروه',

            self::CREATE_GROUP_EVENT_GROUPID->value => 'ایجاد رویداد گروه',
            self::VIEW_GROUP_EVENT_GROUPID->value => 'مشاهده رویداد گروه',
            self::EDIT_GROUP_EVENT_GROUPID->value => 'ویرایش رویداد گروه',

            self::CREATE_ATTENDANCE_GROUPID->value => 'ایجاد حضور و غیاب',
            self::SHOW_EVENT_EVENTID->value => "نمایش رویداد",

            self::EVENT_SURVEY_EVENTID->value=>'نظر سنجی های رویداد',
            self::EVENT_ELECTION_EVENTID->value=>"انتخابات رویداد",

            default => 'نامشخص',
        };

        return $id ? "$label (شناسه: $id)" : $label;
    }
}
