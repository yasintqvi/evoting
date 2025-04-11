@extends('app.layouts.app')

@section('content')
<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0">ایجاد همه پرسی</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>

            <li class="breadcrumb-item"><a href="javascript: void(0);">انتخابات</a></li>

            <li class="breadcrumb-item active">ایجاد</li>
        </ol>
    </div>
</div>

<form action="{{ route('elections.store', $company->slug) }}" method="post">
    @csrf
    <div class="card col-lg-6">
        <div class="card-header border-bottom border-dashed">
            <h4 class="card-title">اطلاعات مربوط به همه پرسی</h4>
            <p class="text-muted mb-0">شما در حال ایجاد همه پرسی جدید هستید</p>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label for="title" class="form-label">عنوان همه پرسی</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="form-control" id="title">
                        @error('title')
                        <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="mb-3">
                        <label for="election_type" class="form-label">نوع همه پرسی</label>
                        <select name="type" onchange="checkElectionType(event)" id="election_type" data-toggle="select2" class="form-select">
                            <option value="">یک نوع همه پرسی را انتخاب نمایید</option>
                            @if ($company->type === App\Enums\CompanyType::SPECIAL)
                            <option @selected(old('type')==App\Enums\ElectionType::PRIVATE_JOINT_WITH_88->value) value="{{ App\Enums\ElectionType::PRIVATE_JOINT_WITH_88->value }}">سهامی خاص با ماده ۸۸</option>
                            <option @selected(old('type')==App\Enums\ElectionType::PRIVATE_JOINT->value) value="{{ App\Enums\ElectionType::PRIVATE_JOINT->value }}">سهامی خاص بدون ماده ۸۸</option>
                            @else
                            <option @selected(old('type')==App\Enums\ElectionType::PUBLIC_JOINT->value) selected value="{{ App\Enums\ElectionType::PUBLIC_JOINT->value }}">انتخابات تعاونی</option>
                            @endif
                        </select>
                        @error('type')
                        <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="main_member_count" class="form-label">تعداد عضو اصلی هیت مدیره</label>
                        <input type="number" class="form-control" id="main_member_count" name="main_member_count" value="{{ old('main_member_count') }}">
                        @error('main_member_count')
                        <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="substitute_member_count" class="form-label">تعداد عضو علی البدل هیت مدیره</label>
                        <input type="number" class="form-control" name="substitute_member_count" value="{{ old('substitute_member_count') }}" id="substitute_member_count">
                        @error('substitute_member_count')
                        <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="incpector_main_member_count" class="form-label">تعداد عضو اصلی بازرس </label>
                        <input type="number" class="form-control" name="incpector_main_member_count" value="{{ old('incpector_main_member_count') }}" id="incpector_main_member_count">
                        @error('incpector_main_member_count')
                        <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="incpector_substitute_member_count" class="form-label">تعداد عضو علی البدل بازرس </label>
                        <input type="number" class="form-control" name="incpector_substitute_member_count" value="{{ old('incpector_substitute_member_count') }}" id="incpector_substitute_member_count">
                        @error('incpector_substitute_member_count')
                        <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="election_type" class="form-label">منشی</label>
                        <select name="supervisor_id" id="supervisor_id" data-toggle="select2" class="form-select">
                            <option value="">منشی مورد نظر را انتخاب کنید</option>
                            @foreach ($users as $user)
                            <option @selected(old('supervisor_id')==$user->id) value="{{ $user->id }}">{{ $user->full_name }}</option>
                            @endforeach
                        </select>
                        @error('supervisor_id')
                        <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-check">
                        <label for="quorum_required" class="form-label">فعال سازی قانون حدنصاب اعضا</label>
                        <input type="checkbox" class="form-check-input" value="1" name="quorum_required" value="{{ old('quorum_required') }}" id="quorum_required">
                        @error('quorum_required')
                        <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>
                    <small class="text-muted">
                        برای برگزاری انتخابات، حضور حداقل نصف به علاوه یک اعضا الزامی است. در صورتی که این تعداد محقق نشود، انتخابات نمی‌تواند برگزار شود.
                    </small>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="text-end mb-3 d-flex">
                <button type="submit" class="btn btn-primary">ایجاد </button>
            </div>
        </div>
    </div>
</form>

@endsection

@section('scripts')

@include('app.alerts.toastr.success')
@include('app.alerts.toastr.error')

@endsection