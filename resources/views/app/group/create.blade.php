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
            <h4 class="fs-18 fw-semibold mb-0">ایجاد گروه جدید</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">داشبورد</a></li>

                <li class="breadcrumb-item active">ساخت گروه جدید</li>
            </ol>

        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header border-bottom border-dashed">
                    <h4 class="card-title mb-0"> ساخت گروه جدید</h4>
                </div>
                <form action="{{ route('groups.store') }}" method="post" enctype="multipart/form-data">
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
                            <h6 class="fs-13 ">انتخاب نوع شرکت</h6>
                            <div class="mt-2">
                                <div class="form-check form-check-inline d-inline-block me-3">
                                    <input type="radio" id="customRadio3" name="company_type"
                                        value="{{ App\Enums\ElectionType::COOPERTAIVE->value }}" class="form-check-input">
                                    <label class="form-check-label" for="customRadio3">نوع شرکت تعاونی</label>
                                </div>
                                <div class="form-check form-check-inline d-inline-block me-3">
                                    <input type="radio" id="customRadio4" name="company_type"
                                        value="{{ App\Enums\ElectionType::SPECIAL->value }}" class="form-check-input">
                                    <label class="form-check-label" for="customRadio4">نوع شرکت سهامی خاص</label>
                                </div>
                                @error('company_type')
                                    <span class="text-danger font-weight-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="d-flex gap-2 mt-3 hidden" id="shareInput">
                                <div class="flex-grow-1">
                                    <label for="title" class="form-label">تعداد سهام کل شرکت</label>
                                    <input type="number" id="example-input-normal-1" name="sum_stock"
                                        class="form-control input-sm" placeholder=" تعداد سهام کل شرکت را وارد کنید">
                                </div>
                                @error('company_type')
                                    <span class="text-danger font-weight-bold">{{ $message }}</span>
                                @enderror   
                                <div class="flex-grow-1">
                                    <label for="title" class="form-label">وزن سهام شرکت </label>
                                    <input type="number" id="example-input-normal-2" name="prefered_stock_weight"
                                        class="form-control input-sm" placeholder="وزن سهام شرکت وارد کنید">
                                </div>
                            </div>
                            <div>
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
                                        placeholder="درباره ی گروه بنویسید"></textarea>
                                    @error('description')
                                        <span class="text-danger font-weight-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="text-end mb-3">
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
        document.getElementById('customRadio3').addEventListener('change', function() {
            document.getElementById('shareInput').classList.add('hidden');
        });

        document.getElementById('customRadio4').addEventListener('change', function() {
            document.getElementById('shareInput').classList.remove('hidden');
        });
    </script>
    @include('app.alerts.toastr.success')
@endsection
