@extends('app.layouts.app')

@section('content')
<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0">ویرایش همه پرسی</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>

            <li class="breadcrumb-item"><a href="javascript: void(0);">انتخابات</a></li>

            <li class="breadcrumb-item active">ویرایش</li>
        </ol>
    </div>
</div>

<form action="{{ $election['operations']['update'] }}" method="post">
    @csrf
    @method('PUT')
    <div class="card col-lg-6">
        <div class="card-header border-bottom border-dashed">
            <h4 class="card-title">اطلاعات مربوط به همه پرسی</h4>
            <p class="text-muted mb-0">شما در حال ویرایش همه پرسی جدید هستید</p>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label for="title" class="form-label">عنوان همه پرسی</label>
                        <input type="text" name="title" value="{{ old('title' , $election['title']) }}" class="form-control" id="title">
                        @error('title')
                        <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div id="prefered_stock_weight" class="row m-0 p-0 d-none">
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="normal_stock_count" class="form-label">تعداد سهام عادی</label>
                            <input type="number" class="form-control" name="normal_stock_count" value="{{ old('normal_stock_count' , $election['normal_stock_count']) }}" id="normal_stock_count">
                            @error('normal_stock_count')
                            <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="prefered_stock_count" class="form-label">تعداد سهام ممتاز</label>
                            <input type="number" class="form-control" name="prefered_stock_count" value="{{ old('prefered_stock_count' , $election['prefered_stock_count']) }}" id="prefered_stock_count">
                            @error('prefered_stock_count')
                            <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label for="prefered_stock_weight" class="form-label">وزن سهام ممتاز</label>
                            <input type="number" class="form-control" name="prefered_stock_weight" value="{{ old('prefered_stock_weight' , $election['prefered_stock_weight']) }}" id="prefered_stock_weight">
                            @error('prefered_stock_weight')
                            <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="main_member_count" class="form-label">تعداد عضو اصلی هیت مدیره</label>
                        <input type="number" class="form-control" id="main_member_count" name="main_member_count" value="{{ old('main_member_count' , $election['main_member_count']) }}">
                        @error('main_member_count')
                        <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="substitute_member_count" class="form-label">تعداد عضو علی البدل هیت مدیره</label>
                        <input type="number" class="form-control" name="substitute_member_count" value="{{ old('substitute_member_count' , $election['substitute_member_count']) }}" id="substitute_member_count">
                        @error('substitute_member_count')
                        <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="text-end mb-3 d-flex">
                <button type="submit" class="btn btn-primary">ویرایش </button>
            </div>
        </div>
    </div>
</form>

@endsection

@section('scripts')

<script>
    function checkElectionType(event) {
        const privateJoint = "{{ App\Enums\ElectionType::PRIVATE_JOINT->value }}";
        const privateJointWith88 = "{{ App\Enums\ElectionType::PRIVATE_JOINT_WITH_88->value }}";

        const selectedValue = event.target.value;

        const preferredStockWeightField = document.getElementById('prefered_stock_weight');

        if (selectedValue === privateJoint || selectedValue === privateJointWith88) {
            preferredStockWeightField.classList.remove('d-none');
        } else {
            preferredStockWeightField.classList.add('d-none');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const electionTypeElement = document.getElementById('election_type');
        checkElectionType({
            target: electionTypeElement
        });
    });
</script>

@include('app.alerts.toastr.error')

@endsection