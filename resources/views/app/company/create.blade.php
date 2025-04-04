@extends('app.layouts.app')
@section('head-tag')
<style>
    .hidden {
        display: none !important;
    }
</style>
@endsection

@section('content')
<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0">ایجاد شرکت جدید</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">داشبورد</a></li>

            <li class="breadcrumb-item active">ساخت شرکت جدید</li>
        </ol>

    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header border-bottom border-dashed">
                <h4 class="card-title mb-0"> ساخت شرکت جدید</h4>
            </div>


            <form action="{{ route('companies.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div>
                            <div class="mb-3">
                                <label for="title" class="form-label">عنوان</label>
                                <input type="text" class="form-control" name="title" id="title"
                                    placeholder="عنوان را وارد کنید" required="">
                                @error('title')
                                <span class="text-danger font-weight-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <h6 class="fs-13">انتخاب نوع شرکت</h6>
                        <div class="mt-2">
                            <div class="form-check form-check-inline d-inline-block me-3">
                                <input type="radio" id="customRadio3" name="type"
                                    value="{{ App\Enums\CompanyType::COOPERTAIVE->value }}" class="form-check-input">
                                <label class="form-check-label" for="customRadio3">نوع شرکت تعاونی</label>
                            </div>
                            <div class="form-check form-check-inline d-inline-block me-3">
                                <input type="radio" id="customRadio4" name="type"
                                    value="{{ App\Enums\CompanyType::SPECIAL->value }}"
                                    {{ old('type', request()->input('type')) == App\Enums\CompanyType::SPECIAL->value ? 'checked' : '' }}
                                    class="form-check-input">
                                <label class="form-check-label" for="customRadio4">نوع شرکت سهامی خاص</label>
                            </div>

                        </div>
                        @error('type')
                        <span class="text-danger font-weight-bold mt-1">{{ $message }}</span>
                        @enderror

                        <div class="d-flex gap-2 mt-3 {{ old('type') == App\Enums\CompanyType::SPECIAL->value ? '' : 'hidden' }}"
                            id="shareInput">
                            <div class="flex-grow-1">
                                <label for="normal_stock_count" class="form-label">تعداد سهام عادی کل شرکت</label>
                                <input type="number" id="normal_stock_count" name="normal_stock_count"
                                    class="form-control input-sm" placeholder="تعداد سهام عادی کل شرکت"
                                    value="{{ old('normal_stock_count') }}">
                                @error('normal_stock_count')
                                <span class="text-danger font-weight-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex-grow-1">
                                <label for="prefered_stock_count" class="form-label">تعداد سهام ممتاز کل شرکت</label>
                                <input type="number" id="prefered_stock_count" name="prefered_stock_count"
                                    class="form-control input-sm" placeholder="تعداد سهام ممتاز کل شرکت"
                                    value="{{ old('prefered_stock_count') }}">
                                @error('prefered_stock_count')
                                <span class="text-danger font-weight-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex-grow-1">
                                <label for="prefered_stock_weight" class="form-label">وزن سهام شرکت</label>
                                <input type="number" id="prefered_stock_weight" name="prefered_stock_weight"
                                    class="form-control input-sm" placeholder="وزن سهام شرکت وارد کنید"
                                    value="{{ old('prefered_stock_weight') }}">
                                @error('prefered_stock_weight')
                                <span class="text-danger font-weight-bold">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                        <div class="mt-3">
                            <label for="logo" class="form-label">لوگو (اختیاری)</label>
                            <input type="file" class="form-control" id="logo" name="logo">
                            @error('logo')
                            <span class="text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <div>
                            <label for="description" class="form-labe mt-3"> توضیحات (اختیاری)</label>
                            <textarea class="form-control mt-1" name="description" id="description" rows="3"
                                placeholder="درباره ی شرکت بنویسید"></textarea>
                            @error('description')
                            <span class="text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">ایجاد</ذ>
                </div>
        </div>
        </form>
    </div>
</div>
</div>
@endsection

@section('scripts')
{{-- include alerts --}}
<script>
    function toggleShareInput() {
        const isSpecial = document.getElementById('customRadio4').checked;
        const shareInput = document.getElementById('shareInput');
        const inputs = shareInput.querySelectorAll('input');

        if (isSpecial) {
            shareInput.classList.remove('hidden');
            inputs.forEach(input => input.removeAttribute('disabled'));
        } else {
            shareInput.classList.add('hidden');
            inputs.forEach(input => input.setAttribute('disabled', 'true'));
        }
    }

    document.getElementById('customRadio3').addEventListener('change', toggleShareInput);
    document.getElementById('customRadio4').addEventListener('change', toggleShareInput);

    toggleShareInput();
</script>

<script>
    function calculateTotal() {
        const normalStock = parseFloat(document.getElementById('normal_stock_count').value) || 0;
        const preferedStock = parseFloat(document.getElementById('prefered_stock_count').value) || 0;
        const stockWeight  = parseFloat(document.getElementById('prefered_stock_weight').value) || 0;
        const total = (preferedStock * stockWeight) + normalStock;

        document.getElementById('total_stock_display').textContent = total;
    }

    document.getElementById('normal_stock_count').addEventListener('input', calculateTotal);
    document.getElementById('prefered_stock_count').addEventListener('input', calculateTotal);
    document.getElementById('prefered_stock_weight').addEventListener('input', calculateTotal);

    calculateTotal();
</script>

@include('app.alerts.toastr.success')
@endsection