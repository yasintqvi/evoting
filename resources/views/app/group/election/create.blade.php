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

<form action="" method="post">
    <div class="card col-lg-6">
        <div class="card-header border-bottom border-dashed">
            <h4 class="card-title">اطلاعات مربوط به همه پرسی</h4>
            <p class="text-muted mb-0">شما در حال ایجاد همه پرسی جدید هستید</p>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label for="productName" class="form-label">عنوان همه پرسی</label>
                        <input type="text" class="form-control" id="productName" required="">
                    </div>
                </div>
                <div class="col-12">
                    <div class="mb-3">
                        <label for="productName" class="form-label">نوع همه پرسی</label>
                        <select name="" id="" class="form-control">
                            <option value="">یک نوع همه پرسی را انتخاب نمایید</option>
                            <option value=""> تعاونی (هر عضو یک رای)</option>
                            <option value="">سهامی خاص با ماده ۸۸</option>
                            <option value="">سهامی خاص بدون ماده ۸۸</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="productName" class="form-label">تعداد سهام عادی</label>
                        <input type="number" class="form-control" id="productName" required="">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="productName" class="form-label">تعداد سهام ممتاز</label>
                        <input type="number" class="form-control" id="productName" required="">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="productName" class="form-label">وزن سهام ممتاز</label>
                        <input type="number" class="form-control" id="productName" required="">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="productName" class="form-label">تعداد عضو اصلی هیت مدیره</label>
                        <input type="number" class="form-control" id="productName" required="">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="productName" class="form-label">تعداد عضو علی البدل هیت مدیره</label>
                        <input type="number" class="form-control" id="productName" required="">
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="productName" class="form-label">تعداد عضو اصلی بازرس </label>
                        <input type="number" class="form-control" id="productName" required="">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="productName" class="form-label">تعداد عضو علی البدل بازرس</label>
                        <input type="number" class="form-control" id="productName" required="">
                    </div>
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