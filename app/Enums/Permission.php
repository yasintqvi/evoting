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
}
