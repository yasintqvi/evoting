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
            <h4 class="fs-18 fw-semibold mb-0">ویرایش گروه</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">داشبورد</a></li>
                <li class="breadcrumb-item active">ویرایش گروه</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header border-bottom border-dashed">
                    <h4 class="card-title mb-0">ویرایش گروه</h4>
                </div>
                <form action="{{ route('groups.update', $group) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="mb-3">
                                <label for="title" class="form-label">عنوان</label>
                                <input type="text" class="form-control" name="title" id="title"
                                    placeholder="عنوان را وارد کنید" value="{{ old('title', $group->title) }}"
                                    required="">
                                @error('title')
                                    <span class="text-danger font-weight-bold">{{ $message }}</span>
                                @enderror
                            </div>

                            <h6 class="fs-13">نوع گروه</h6>
                            <div class="mt-2 mb-3">
                                <span class="badge bg-secondary fs-13 px-3 py-2">
                                    {{ $group->type == App\Enums\GroupType::SPECIAL ? 'سهامی خاص' : 'تعاونی' }}
                                </span>
                                <small class="text-muted ms-2">
                                    <i class="ti ti-lock me-1"></i>نوع گروه پس از ایجاد قابل تغییر نیست.
                                </small>
                            </div>

                            <div class="d-flex gap-2 mt-3" id="shareInput">
                                <div class="flex-grow-1">
                                    <label for="normal_stock_count" class="form-label">تعداد سهام عادی کل گروه</label>
                                    <input type="number" id="normal_stock_count" name="normal_stock_count"
                                        class="form-control input-sm" placeholder="تعداد سهام عادی کل گروه"
                                        value="{{ old('normal_stock_count', $group->normal_stock_count) }}">
                                    @error('normal_stock_count')
                                        <span class="text-danger font-weight-bold">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="flex-grow-1">
                                    <label for="prefered_stock_count" class="form-label">تعداد سهام ممتاز کل گروه</label>
                                    <input type="number" id="prefered_stock_count" name="prefered_stock_count"
                                        class="form-control input-sm" placeholder="تعداد سهام ممتاز کل گروه"
                                        value="{{ old('prefered_stock_count', $group->prefered_stock_count) }}">
                                    @error('prefered_stock_count')
                                        <span class="text-danger font-weight-bold">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="flex-grow-1">
                                    <label for="prefered_stock_weight" class="form-label">وزن سهام گروه</label>
                                    <input type="number" id="prefered_stock_weight" name="prefered_stock_weight"
                                        class="form-control input-sm" placeholder="وزن سهام گروه وارد کنید"
                                        value="{{ old('prefered_stock_weight', $group->prefered_stock_weight) }}">
                                    @error('prefered_stock_weight')
                                        <span class="text-danger font-weight-bold">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="flex-grow-1">
                                    <label class="form-label">جمع کل سهام</label>
                                    <div class="form-control" style="background-color: #f8f9fa; border: 1px solid #dee2e6;">
                                        <span id="total_stock_display"
                                            class="fw-bold">{{ ($group->normal_stock_count ?? 0) + ($group->prefered_stock_count ?? 0) }}</span>
                                    </div>
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
                                <label for="description" class="form-label mt-3"> توضیحات (اختیاری)</label>
                                <textarea class="form-control mt-1" name="description" id="description" rows="3"
                                    placeholder="درباره ی گروه بنویسید">{{ old('description', $group->description) }}</textarea>
                                @error('description')
                                    <span class="text-danger font-weight-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="text-end mb-3">
                            <button type="submit" class="btn btn-primary">ویرایش</button>
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
        // فیلدهای سهام برای همه گروه‌ها قابل مشاهده هستن
        // چون نوع گروه قابل تغییر نیست، نیازی به toggle نداریم
        // function toggleShareInput() {
        //     const isSpecial = document.getElementById('customRadio4').checked;
        //     const shareInput = document.getElementById('shareInput');
        //     const inputs = shareInput.querySelectorAll('input');

        //     if (isSpecial) {
        //         shareInput.classList.remove('hidden');
        //         inputs.forEach(input => input.removeAttribute('disabled'));
        //     } else {
        //         shareInput.classList.add('hidden');
        //         inputs.forEach(input => input.setAttribute('disabled', 'true'));
        //     }
        // }

        // document.getElementById('customRadio3').addEventListener('change', toggleShareInput);
        // document.getElementById('customRadio4').addEventListener('change', toggleShareInput);

        // toggleShareInput();
    </script>

    <script>
        function calculateTotal() {
            const normalStock = parseFloat(document.getElementById('normal_stock_count').value) || 0;
            const preferedStock = parseFloat(document.getElementById('prefered_stock_count').value) || 0;
            const total = normalStock + preferedStock;

            document.getElementById('total_stock_display').textContent = total;
        }

        document.getElementById('normal_stock_count').addEventListener('input', calculateTotal);
        document.getElementById('prefered_stock_count').addEventListener('input', calculateTotal);

        calculateTotal();
    </script>

    @include('app.alerts.toastr.success')
@endsection
