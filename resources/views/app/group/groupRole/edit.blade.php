@extends('app.layouts.app')

@section('title', 'ویرایش نقش')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom border-light">
                <h4 class="card-title">فرم ویرایش نقش</h4>
                <p class="text-muted mb-0">لطفا اطلاعات نقش را ویرایش کنید</p>
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

                <form action="{{ route('group.permissions.update', [$group,$role]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">نام نقش</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>دسترسی‌ها</h5>
                            <div class="row">
                                @foreach($permissions as $permission)
                                <div class="col-lg-3 col-md-4 col-sm-6">
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input @error('permissions') is-invalid @enderror" id="permission_{{ $permission->id }}"
                                            name="permissions[]" value="{{ $permission->id }}"
                                            {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) ||
                                               (!old() && in_array($permission->id, $rolePermissions)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                            {{\App\Enums\Permission::withIdFa($permission->name) }}
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
                    <div class="card mt-4">
                        <h5 class="card-header">مدیریت دسترسی رکوردی</h5>
                        <div class="card-body">
                            <div class="mb-3">
                                <label>انتخاب ماژول</label>
                                <select id="module-select" class="form-select">
                                    <option value="">انتخاب کنید</option>
                                    <option value="events">رویدادها</option>
                                    <option value="elections">انتخابات</option>
                                    <option value="surveys">نظرسنجی‌ها</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>انتخاب رکورد</label>
                                <select id="record-select" class="form-select select2" disabled></select>
                            </div>

                            <div id="permissions-area" class="mt-3" style="display:none;">
                                <h6>نوع دسترسی</h6>
                                <div class="form-check">
                                    <input type="checkbox" id="show" value="show" class="form-check-input">
                                    <label for="show" class="form-check-label">مشاهده</label>
                                </div>
{{--                                <div class="form-check">--}}
{{--                                    <input type="checkbox" id="create" value="create" class="form-check-input">--}}
{{--                                    <label for="create" class="form-check-label">ایجاد</label>--}}
{{--                                </div>--}}

                                <div class="form-check">
                                    <input type="checkbox" id="edit" value="edit" class="form-check-input">
                                    <label for="edit" class="form-check-label">ویرایش</label>
                                </div>
{{--                                <div class="form-check">--}}
{{--                                    <input type="checkbox" id="delete" value="delete" class="form-check-input">--}}
{{--                                    <label for="delete" class="form-check-label">حذف</label>--}}
{{--                                </div>--}}
                                <div class="form-check">
                                    <input type="checkbox" id="attendance" value="attendance"
                                           class="form-check-input event-permission">
                                    <label for="attendance" class="form-check-label event-permission">حضور
                                        غیاب</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" id="electionevent" value="electionevent"
                                           class="form-check-input event-permission">
                                    <label for="electionevent" class="form-check-label event-permission">انتخابات</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" id="surveyevent" value="surveyevent"
                                           class="form-check-input event-permission">
                                    <label for="surveyevent" class="form-check-label event-permission">نظرسنجی</label>
                                </div>
                                <button id="add-permission" type="button" class="btn btn-outline-primary mt-3">
                                    افزودن دسترسی
                                </button>
                            </div>

                            <div id="selected-permissions" class="mt-2">

                                @foreach($otherPermissions as $permission)
                                    <div class="badge bg-primary me-2 mb-2 d-inline-flex align-items-center">

                                        {{-- Text --}}
                                        <span>{{ \App\Enums\Permission::withIdFa($permission->name) }}</span>


                                        {{-- Hidden input for form submit --}}
                                        <input type="hidden" name="permissionsRecord[]" value="{{ $permission->name }}">

                                        {{-- Delete button --}}
                                        <button type="button"
                                                class="btn-close btn-close-white ms-2"
                                                style="font-size: 0.6rem;"
                                                onclick="this.parentElement.remove()">
                                        </button>
                                    </div>
                                @endforeach

                            </div>

                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">ذخیره</button>
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">انصراف</a>
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
        const moduleSelect = document.getElementById('module-select');
        const recordSelect = document.getElementById('record-select');
        const permissionsArea = document.getElementById('permissions-area');
        const selectedPermissions = document.getElementById('selected-permissions');

        moduleSelect.addEventListener('change', async () => {
            document.querySelectorAll('.event-permission').forEach(el => {
                el.style.display = 'none';
            });
            const module = moduleSelect.value;
            recordSelect.innerHTML = '';
            permissionsArea.style.display = 'none';
            if (module === 'events') {
                document.querySelectorAll('.event-permission').forEach(el => {
                    el.style.display = 'block';
                });
                permissionsArea.style.display = 'block';
            }
            if (!module) return;

            // با API رکوردهای اون ماژول رو بیار
            const res = await fetch(`/api/get/${module}/group/{{$group->slug}}`);
            const data = await res.json();

            data.forEach(record => {
                const opt = document.createElement('option');
                opt.value = record.id;
                opt.textContent = record.title || `#${record.id}`;
                recordSelect.appendChild(opt);
            });
            permissionsArea.style.display = recordSelect.value ? 'block' : 'none';

            recordSelect.disabled = false;
        });

        recordSelect.addEventListener('change', () => {
            permissionsArea.style.display = recordSelect.value ? 'block' : 'none';
        });

        document.getElementById('add-permission').addEventListener('click', () => {
            const module = moduleSelect.value;
            const recordId = recordSelect.value;

            const translations = {
                // Modules
                surveys: "نظرسنجی",
                elections: "انتخابات",
                events: "رویداد",
                electionevent: "انتخابات کامل",
                surveyevent: "رویداد کامل",

                // Actions
                show: "مشاهده",
                create: "ایجاد",
                edit: "ویرایش",
                delete: "حذف",

                // Special action for event
                attendance: "حضور و غیاب"
            };

            const actions = Array.from(
                document.querySelectorAll('#permissions-area input[type=checkbox]:checked')
            ).map(i => i.value);

            actions.forEach(action => {
                const value = `${module}_${action}_${recordId}`;

                const div = document.createElement('div');
                div.className = 'badge bg-primary me-2 mb-2 d-inline-flex align-items-center';

                // Text
                const span = document.createElement('span');
                span.textContent = translatePermission(value);
                div.appendChild(span);

                // Hidden input (IMPORTANT)
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'permissionsRecord[]';
                input.value = value;
                div.appendChild(input);

                // Delete button
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn-close btn-close-white ms-2';
                btn.style.fontSize = '0.6rem';
                btn.addEventListener('click', () => {
                    div.remove();
                });
                div.appendChild(btn);

                selectedPermissions.appendChild(div);
            });

            function translatePermission(value) {
                // مثال value:  "event_attendance_44"
                const parts = value.split("_");

                const module = translations[parts[0]] || parts[0];
                const action = translations[parts[1]] || parts[1];
                const recordId = parts[2];

                return `${action} ${module} (شناسه ${recordId})`;
            }
        });


    </script>
    @include('app.alerts.toastr.error')
@endsection
