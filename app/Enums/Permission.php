<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum Permission: string
{

    use EnumValues;

    case CREATE_COMPANY = 'create company';
    case VIEW_COMPANY = 'view company';
    case EDIT_COMPANY = 'edit company';
    case UPDATE_COMPANY = 'update company';
    case DELETE_COMPANY = 'delete company';
    case LIST_COMPANIES = 'list companies';

    case VIEW_USERS = 'view users';
    case CREATE_USERS = 'create users';
    case EDIT_USERS = 'edit users';
    case UPDATE_USERS = 'update users';
    case DELETE_USERS = 'delete users';
    case IMPORT_USERS = 'import users';
    case CHANGE_ACCESS = 'changes access';

    case VIEW_COMPANY_USERS = 'view company users';
    case CREATE_COMPANY_USERS = 'create company users';
    case EDIT_COMPANY_USERS = 'edit company users';
    case UPDATE_COMPANY_USERS = 'update company users';
    case DELETE_COMPANY_USERS = 'delete company users';

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

    /**
     * Get permissions based on role
     *
     * @param Role $role
     * @return array
     */
    public static function getPermissionsByRole(Role $role): array
    {
        return match ($role) {
            Role::Manager => [
                self::CREATE_COMPANY,
                self::VIEW_COMPANY,
                self::EDIT_COMPANY,
                self::UPDATE_COMPANY,
                self::LIST_COMPANIES,
                self::VIEW_USERS,
                self::CREATE_USERS,
                self::EDIT_USERS,
                self::UPDATE_USERS,
                self::DELETE_USERS,
                self::VIEW_COMPANY_USERS,
                self::CREATE_COMPANY_USERS,
                self::EDIT_COMPANY_USERS,
                self::UPDATE_COMPANY_USERS,
                self::DELETE_COMPANY_USERS,
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
            self::CREATE_COMPANY => 'ایجاد شرکت',
            self::VIEW_COMPANY => 'مشاهده شرکت',
            self::EDIT_COMPANY => 'ویرایش شرکت',
            self::UPDATE_COMPANY => 'بروزرسانی شرکت',
            self::DELETE_COMPANY => 'حذف شرکت',
            self::LIST_COMPANIES => 'لیست شرکت‌ها',
            self::VIEW_USERS => 'مشاهده کاربران',
            self::CREATE_USERS => 'ایجاد کاربر',
            self::EDIT_USERS => 'ویرایش کاربر',
            self::UPDATE_USERS => 'بروزرسانی کاربر',
            self::DELETE_USERS => 'حذف کاربر',
            self::IMPORT_USERS => 'وارد کردن کاربران',
            self::CHANGE_ACCESS => 'تغییر دسترسی کاربران',
            self::VIEW_COMPANY_USERS => 'مشاهده کاربران شرکت',
            self::CREATE_COMPANY_USERS => 'ایجاد کاربر شرکت',
            self::EDIT_COMPANY_USERS => 'ویرایش کاربر شرکت',
            self::UPDATE_COMPANY_USERS => 'بروزرسانی کاربر شرکت',
            self::DELETE_COMPANY_USERS => 'حذف کاربر شرکت',
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
            self::VIEW_PARTICIPANTS => 'مشاهده شرکت‌کنندگان',
            self::CREATE_PARTICIPANTS => 'ایجاد شرکت‌کننده',
            self::EDIT_PARTICIPANTS => 'ویرایش شرکت‌کننده',
            self::UPDATE_PARTICIPANTS => 'بروزرسانی شرکت‌کننده',
            self::DELETE_PARTICIPANTS => 'حذف شرکت‌کننده',
            self::IMPORT_PARTICIPANTS => 'وارد کردن شرکت‌کنندگان',
            self::STORE_TABLE_PARTICIPANT => 'ذخیره جدول شرکت‌کنندگان',
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
            default => $this->value
        };
    }
}
