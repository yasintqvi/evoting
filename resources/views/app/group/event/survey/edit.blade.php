@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">ویرایش نظرسنجی</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript:void(0);">خانه</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">انتخابات</a></li>
                <li class="breadcrumb-item active">ویرایش</li>
            </ol>
        </div>
    </div>

    <form action="{{ route('surveys.update', [$group->slug, $event->id, $survey->id]) }}" method="post">
        @csrf
        @method('PUT')
        <div class="card col-lg-6">
            <div class="card-header border-bottom border-dashed">
                <h4 class="card-title">اطلاعات نظرسنجی</h4>
                <p class="text-muted mb-0">ویرایش اطلاعات نظرسنجی</p>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="title" class="form-label">عنوان نظر سنجی</label>
                    <input type="text" name="title" class="form-control" id="title"
                        value="{{ old('title', $survey->title) }}">
                    @error('title')
                        <span class="text-danger font-weight-bold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">توضیحات</label>
                    <textarea class="form-control" name="description" id="description" rows="3">{{ old('description', $survey->description) }}</textarea>
                    @error('description')
                        <span class="text-danger font-weight-bold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous" value="1"
                        @checked(old('is_anonymous', $survey->is_anonymous))>
                    <label class="form-check-label" for="is_anonymous">ناشناس</label>
                </div>
            </div>
            <div class="card-footer">
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">بروزرسانی</button>
                </div>
            </div>
        </div>
    </form>
@endsection


{{-- //create --}}

{{-- <form action="{{ route('surveys.store', [$group->slug, $event->id]) }}" method="post">
        @csrf
        <div class="card col-lg-6">
            <div class="card-header border-bottom border-dashed">
                <h4 class="card-title">اطلاعات مربوط به نظر سنجی</h4>
                <p class="text-muted mb-0">شما در حال ایجاد نظر سنجی جدید هستید</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="title" class="form-label">عنوان نظر سنجی</label>
                            <input type="text" name="title" value="{{ old('title') }}" class="form-control"
                                id="title">
                            @error('title')
                                <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <div>
                            <label for="description" class="form-label">توضیحات (اختیاری)</label>
                            <textarea class="form-control mt-1" name="description" id="description" rows="3" placeholder="درباره ی نظرسنجی">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-6 mt-3">
                        <div class="mt-2">
                            <label for="is_anonymous" class="form-label d-block">ناشناس</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous"
                                    value="1" @checked(old('is_anonymous'))>
                                <label class="form-check-label" for="is_anonymous">بدون نام / با نام </label>
                            </div>
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
    </form> --}}
