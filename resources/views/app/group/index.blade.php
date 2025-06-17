@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">{{ $group->title }}</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">داشبورد</a></li>

                <li class="breadcrumb-item active">{{ $group->title }}</li>
            </ol>
        </div>
    </div>

    <div class="d-flex flex-column gap-3">

        <div
            class="d-flex align-items-center justify-content-between p-3 rounded shadow-sm border-start border-4 border-success bg-light">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-success text-white rounded px-3 py-2 fw-bold">✓</div>
                <div class="text-success fw-semibold">ایجاد گروه</div>
            </div>
            <small class="text-success">تکمیل شده</small>
        </div>

        <div
            class="d-flex align-items-center justify-content-between p-3 rounded shadow-sm border-start border-4 border-primary bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary text-white rounded px-3 py-2 fw-bold">2</div>
                <div class="text-primary fw-semibold">ایجاد اعضا</div>
            </div>
            <small class="text-primary">در حال انجام</small>
        </div>

        <div
            class="d-flex align-items-center justify-content-between p-3 rounded shadow-sm border-start border-4 border-secondary bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-secondary text-white rounded px-3 py-2 fw-bold">3</div>
                <div class="text-muted fw-semibold">ایجاد سهام‌داران</div>
            </div>
            <small class="text-muted">در انتظار</small>
        </div>
    </div>

    <div class="alert alert-info mt-4">
        <strong>نمونه نمایشی:</strong> این یک طرح بصری است و وضعیت مراحل به‌صورت ثابت تنظیم شده‌اند:
        <ul class="mt-2 mb-0">
            <li>مرحله 1: <span class="text-success fw-semibold">تکمیل شده</span></li>
            <li>مرحله 2: <span class="text-primary fw-semibold">فعال</span></li>
            <li>مرحله 3: <span class="text-muted fw-semibold">غیرفعال</span></li>
        </ul>
    </div>
@endsection
