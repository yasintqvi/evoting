@extends('app.layouts.app')

@section('title', 'ویرایش نقش')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom border-light">
                    <h4 class="card-title"> اعطای دسترسی به کاربر {{ $user->full_name }}</h4>
                    <p class="text-muted mb-0">شما در حال تغییر دسترسی های کاربر {{ $user->full_name }} هستید</p>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('users.change-access.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>دسترسی‌ها</h5>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="toggleAllPermissions(this)">انتخاب همه</button>
                                </div>
                                <div class="row">
                                    @php
                                        $isGroupManager = auth()
                                            ->user()
                                            ?->hasRole(\App\Enums\Role::GroupManager->value);
                                        $groupManagerPermissions = [
                                            \App\Enums\Permission::VIEW_GROUP->value,
                                            \App\Enums\Permission::VIEW_GROUP_USERS->value,
                                            \App\Enums\Permission::CREATE_GROUP_USERS->value,
                                            \App\Enums\Permission::EDIT_GROUP_USERS->value,
                                            \App\Enums\Permission::UPDATE_GROUP_USERS->value,
                                            \App\Enums\Permission::DELETE_GROUP_USERS->value,
                                            \App\Enums\Permission::MANAGE_GROUP_USER_ACCESS->value,
                                            \App\Enums\Permission::VIEW_GROUP_EVENT->value,
                                            \App\Enums\Permission::CREATE_GROUP_EVENT->value,
                                            \App\Enums\Permission::EDIT_GROUP_EVENT->value,
                                            \App\Enums\Permission::DELETE_GROUP_EVENT->value,
                                            \App\Enums\Permission::VIEW_ELECTIONS->value,
                                            \App\Enums\Permission::CREATE_ELECTIONS->value,
                                            \App\Enums\Permission::EDIT_ELECTIONS->value,
                                            \App\Enums\Permission::UPDATE_ELECTIONS->value,
                                            \App\Enums\Permission::DELETE_ELECTIONS->value,
                                            \App\Enums\Permission::SHOW_ELECTION->value,
                                            \App\Enums\Permission::VIEW_CANDIDATES->value,
                                            \App\Enums\Permission::CREATE_CANDIDATES->value,
                                            \App\Enums\Permission::EDIT_CANDIDATES->value,
                                            \App\Enums\Permission::UPDATE_CANDIDATES->value,
                                            \App\Enums\Permission::DELETE_CANDIDATES->value,
                                            \App\Enums\Permission::VIEW_PARTICIPANTS->value,
                                            \App\Enums\Permission::CREATE_PARTICIPANTS->value,
                                            \App\Enums\Permission::EDIT_PARTICIPANTS->value,
                                            \App\Enums\Permission::UPDATE_PARTICIPANTS->value,
                                            \App\Enums\Permission::DELETE_PARTICIPANTS->value,
                                            \App\Enums\Permission::IMPORT_PARTICIPANTS->value,
                                            \App\Enums\Permission::STORE_TABLE_PARTICIPANT->value,
                                            \App\Enums\Permission::VIEW_ELECTION_ROUNDS->value,
                                            \App\Enums\Permission::CREATE_ELECTION_ROUNDS->value,
                                            \App\Enums\Permission::EDIT_ELECTION_ROUNDS->value,
                                            \App\Enums\Permission::UPDATE_ELECTION_ROUNDS->value,
                                            \App\Enums\Permission::DELETE_ELECTION_ROUNDS->value,
                                            \App\Enums\Permission::CREATE_ATTENDANCE->value,
                                            \App\Enums\Permission::STORE_ATTENDANCE->value,
                                            \App\Enums\Permission::VIEW_DASHBOARD->value,
                                        ];
                                    @endphp
                                    @foreach ($permissions as $permission)
                                        @if ($isGroupManager && !in_array($permission->name, $groupManagerPermissions))
                                            @continue
                                        @endif
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <div class="form-check mb-2">
                                                <input type="checkbox"
                                                    class="form-check-input @error('permissions') is-invalid @enderror"
                                                    id="permission_{{ $permission->id }}" name="permissions[]"
                                                    value="{{ $permission->id }}"
                                                    {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) ||
                                                    (!old() && in_array($permission->id, $user->getAllPermissions()->pluck('id')->toArray()))
                                                        ? 'checked'
                                                        : '' }}>
                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                    {{ \App\Enums\Permission::from($permission->name)->fa() }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('permissions')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>نقش ها</h5>
                                <div class="row">
                                    @php
                                        $userRoles = $user->roles;
                                        $allowedRolesForGroupManager = [
                                            \App\Enums\Role::Secretary->value,
                                            \App\Enums\Role::GroupManager->value,
                                        ];
                                    @endphp
                                    @foreach ($roles as $role)
                                        @if ($isGroupManager && !in_array($role->name, $allowedRolesForGroupManager))
                                            @continue
                                        @endif
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <div class="form-check mb-2">
                                                <input type="checkbox"
                                                    class="form-check-input @error('roles') is-invalid @enderror"
                                                    id="role_{{ $role->id }}" name="roles[]"
                                                    value="{{ $role->id }}"
                                                    {{ (is_array(old('roles')) && in_array($role->id, haystack: old('roles'))) ||
                                                    (!old() && in_array($role->id, $userRoles->pluck('id')->toArray()))
                                                        ? 'checked'
                                                        : '' }}>
                                                <label class="form-check-label" for="role_{{ $role->id }}">
                                                    {{ $role->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('permissions')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">بروزرسانی</button>
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">انصراف</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function toggleAllPermissions(button) {
            const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);

            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });

            button.textContent = allChecked ? 'انتخاب همه' : 'حذف انتخاب همه';
        }
    </script>
@endsection
