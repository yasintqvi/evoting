@extends('app.layouts.app')

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
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header border-bottom border-dashed">
                <h4 class="card-title mb-0"> ساخت گروه جدید</h4>
            </div>
            <form action="{{route('groups.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div>
                            <div class="mb-3">
                                <label for="title" class="form-label">عنوان</label>
                                <input type="text" class="form-control" name="title" id="title" placeholder="عنوان را وارد کنید" required="">
                                @error('title')
                                <span class="text-danger font-weight-bold">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <div class="mb-3">
                                <label for="logo" class="form-label">لوگو (اختیاری)</label>
                                <input type="file" class="form-control" id="logo" name="logo">
                                @error('logo')
                                <span class="text-danger font-weight-bold">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <div>
                                <label for="description" class="form-label"> توضیحات (اختیاری)</label>
                                <textarea class="form-control" name="description" id="description" rows="3" placeholder="درباره ی گروه بنویسید"></textarea>
                                @error('description')
                                <span class="text-danger font-weight-bold">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="text-end mb-3">
                        <button type="submit" class="btn btn-primary">ایجاد گروه</ذ>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection