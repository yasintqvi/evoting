@extends('app.layouts.app')


@section('content')
<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0">ایجاد کاربران</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>
            <li class="breadcrumb-item"><a href="javascript: void(0);">کاربران</a></li>
            <li class="breadcrumb-item active">ایجاد</li>
        </ol>
    </div>
</div>

<div class="row">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <div class="card col-lg-6">
        <!-- بخش بالای فرم -->
        <div class="d-flex col-lg-12 align-items-center">
            <div class="card-header border-bottom border-dashed col-lg-6">
                <h4 class="card-title">اطلاعات مربوط به کاربر</h4>
                <p class="text-muted mb-0">شما در حال ایجاد کاربر جدید هستید</p>
            </div>
            <div class="col-lg-6 text-start p-2">
                <div class="form-check form-checkbox-success mb-2">
                    <input 
                        type="checkbox" 
                        class="form-check-input" 
                        id="customCheckcolor2" 
                        onchange="toggleFormFields()"
                    >
                    <label class="form-check-label" for="customCheckcolor2">کاربران موجود</label>
                </div>
            </div>
        </div>
    
        <!-- فرم ایجاد کاربر -->
        <form id="new-user-form" action="{{ route('users.store')}}" method="post" style="display: block;">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="productName" class="form-label">نام </label>
                            <input type="text" name="first_name" class="form-control" id="productName" value="{{ old('first_name') }}">
                            @error('first_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="lastName" class="form-label">نام خانوادگی</label>
                            <input type="text" name="last_name" class="form-control" id="lastName" value="{{ old('last_name') }}">
                            @error('last_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label for="phone" class="form-label">تلفن همراه کاربر</label>
                            <input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone') }}">
                            @error('phone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <label for="is_active" class="form-label">وضعیت</label>
                        <div class="mt-1">
                            <input type="checkbox" value="1" @checked(old('is_active')) name="is_active" id="is_active" data-switch="primary" />
                            <label for="is_active" data-on-label="فعال" data-off-label="غیر فعال"></label>
                        </div>
                    </div>
                     <!-- انتخاب گروه -->
                <div class="col-lg-12">
                    <div class="mb-3">
                        <label for="group1" class="form-label">افزودن به گروه</label>
                        <select 
                            class="form-select my-1 my-md-0 me-sm-3" 
                            name="group_ids[]" 
                            id="group1"
                            data-toggle="select2"
                            multiple>
                            @foreach ($groups as $group)
                                <option 
                                    value="{{ $group->id }}" 
                                    {{ collect(old('group_ids'))->contains($group->id) ? 'selected' : '' }}>
                                    {{ $group->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('group_ids')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="text-end mb-3">
                    <button type="submit" class="btn btn-primary">ایجاد کاربر جدید</button>
                </div>
            </div>
        </form>
    
        <!-- فرم کاربران فعال -->
        <form id="active-users-form" action="{{ route('users.store') }}" method="post" style="display: none;">
            @csrf
            <div class="card-body">
                <!-- انتخاب کاربران -->
                <div class="col-lg-12">
                    <div class="mb-3">
                        <label for="users" class="form-label">انتخاب کاربران</label>
                        <select 
                            class="form-select my-1 my-md-0 me-sm-3" 
                            name="user_ids[]" 
                            id="users" 
                            data-toggle="select2" 
                            multiple>
                            @foreach ($users as $user)
                                <option 
                                    value="{{ $user->id }}" 
                                    {{ collect(old('user_ids'))->contains($user->id) ? 'selected' : '' }}>
                                    {{ $user->fullName }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_ids')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <!-- انتخاب گروه -->
                <div class="col-lg-12">
                    <div class="mb-3">
                        <label for="group" class="form-label">افزودن به گروه</label>
                        <select 
                            class="form-select my-1 my-md-0 me-sm-3" 
                            name="group_ids[]" 
                            id="group"
                            data-toggle="select2"
                            multiple>
                            @foreach ($groups as $group)
                                <option 
                                    value="{{ $group->id }}" 
                                    {{ collect(old('group_ids'))->contains($group->id) ? 'selected' : '' }}>
                                    {{ $group->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('group_ids')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                {{-- <div class="col-lg-6">
                    <label for="is_active1" class="form-label">وضعیت</label>
                    <div class="mt-1">
                        <input type="hidden" name="is_active" value="0"> 
                        <input type="checkbox" value="1" @checked(old('is_active')) name="is_active" id="is_active1" data-switch="primary" />
                        <label for="is_active1" data-on-label="فعال" data-off-label="غیر فعال"></label>
                    </div>
                </div> --}}
                
            </div>
        
            <div class="card-footer">
                <div class="text-end mb-3">
                    <button type="submit" class="btn btn-success">افزودن کاربران به گروه</button>
                </div>
            </div>
        </form>
        
        
    </div>

    <form action="{{ route('uplode-users') }}" method="post" enctype="multipart/form-data" class="col-lg-6">
        @csrf
        <div class="page-container">
            <div class="card">
                <div class="card-header col-lg-12 border-bottom border-dashed d-flex align-items-center">
                    <div class="col-lg-6">
                        <h4 class="header-title">آپلود فایل اکسل کاربران</h4>
                    </div>
                    <div class="col-lg-6 text-end">
                        <a href="{{ asset('assets/excel/sample.xlsx') }}" download class="header-title" style="cursor: pointer">فایل نمونه اکسل</a>
                    </div>
                </div>

                <div class="card-body">
                   
                    <div class="mb-3">
                        <label for="formFileMultiple" class="form-label">قایل مورد نظر را انتخاب کنید</label>
                        <input class="form-control" name="file"  type="file" id="formFileMultiple" >
                      </div>
                      @if ($errors->has('file'))
                      <span class="text-danger">{{ $errors->first('file') }}</span>
                      @endif    
                    <!-- Preview -->
                </div>
                <div class="card-footer">
                    <div class="text-end mb-3">
                        <button type="submit" class="btn btn-primary">  ایجاد کاربر جدید با اکسل</button>
                    </div>
                </div>
                <!-- end card-body -->
            </div>
            <!-- end card-->

        </div>
    </form>
    
   
</div>

   

<script>
    window.onload = function () {
        const hasErrors = @json($errors->any()); 
        const errorKeys = @json($errors->keys()); 
        const activeUsersForm = document.getElementById('active-users-form');
        const newUserForm = document.getElementById('new-user-form');
        const checkbox = document.getElementById('customCheckcolor2');

        if (hasErrors) {
            if (errorKeys.some(key => key.startsWith('user_ids') || key === 'group_id')) {
                activeUsersForm.style.display = 'block';
                newUserForm.style.display = 'none';
                checkbox.checked = true; 
            } else if (errorKeys.some(key => key === 'first_name' || key === 'last_name' || key === 'phone')) {
                newUserForm.style.display = 'block';
                activeUsersForm.style.display = 'none';
                checkbox.checked = false; 
            }
        }

        checkbox.addEventListener('change', function () {
            toggleFormFields(checkbox.checked);
        });
    };

    function toggleFormFields(isChecked) {
        const newUserForm = document.getElementById('new-user-form');
        const activeUsersForm = document.getElementById('active-users-form');

        if (isChecked) {
            newUserForm.style.display = 'none';
            activeUsersForm.style.display = 'block';
        } else {
            newUserForm.style.display = 'block';
            activeUsersForm.style.display = 'none';
        }
    }
</script>





@endsection

@section('scripts')
    {{-- include alerts --}}
    @include('app.alerts.toastr.success')
@endsection