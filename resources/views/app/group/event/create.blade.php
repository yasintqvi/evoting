    @extends('app.layouts.app')
    @section('head-tag')

    @section('content')
        <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold mb-0">شما در حال ایجاد رویداد جدیدی هستید</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">داشبورد</a></li>

                    <li class="breadcrumb-item active">ساخت رویداد جدید</li>
                </ol>

            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header border-bottom border-dashed">
                        <h4 class="card-title mb-0"> ساخت رویداد جدید</h4>
                    </div>


                    <form action="{{ route('events.store', $group->slug) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">نام رویداد</label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            placeholder="نام رویداد را وارد کنید" value="{{ old('name') }}">
                                        @error('name')
                                            <span class="text-danger font-weight-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div>
                                    <div class="mb-3">
                                        <label for="title" class="form-label">عنوان رویداد</label>
                                        <input type="text" class="form-control" name="title" id="title"
                                            placeholder="عنوان را وارد کنید" value="{{ old('title') }}">
                                        @error('title')
                                            <span class="text-danger font-weight-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="quorum_percent" class="form-label">حد نصاب مشارکت (درصد)</label>
                                    <input type="number" min="1" max="100" class="form-control" id="quorum_percent"
                                        name="quorum_percent" value="{{ old('quorum_percent') }}">
                                    @error('quorum_percent')
                                        <span class="text-danger font-weight-bold">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3 col-md-6">
                                    <label for="logo" class="form-label">لوگو (اختیاری)</label>
                                    <input type="file" class="form-control" id="logo" name="logo">
                                    @error('logo')
                                        <span class="text-danger font-weight-bold">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <div>
                                    <label for="description" class="form-label">توضیحات (اختیاری)</label>
                                    <textarea class="form-control mt-1" name="description" id="description" rows="3"
                                        placeholder="درباره ی رویداد بنویسید">{{ old('description') }}</textarea>
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
    @endsection
