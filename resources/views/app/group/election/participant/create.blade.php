@extends('app.layouts.app')

@section('content')
<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0">نامزد های {{ $election->title }}</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>
            <li class="breadcrumb-item"><a href="javascript: void(0);">انتخابات</a></li>
            <li class="breadcrumb-item"><a href="javascript: void(0);">{{$election->title}}</a></li>
            <li class="breadcrumb-item active">تعیین شرکت کنندگان</li>
        </ol>
    </div>
</div>

<form class="card col-lg-9" action="{{ route('participants.store', [$group->slug, $election->id]) }}" method="post">
    @csrf
    <div >
        <div class="card-header border-bottom border-dashed">
            <h4 class="card-title">اطلاعات مربوط به همه پرسی</h4>
            <p class="text-muted mb-0">شما در حال ایجاد همه پرسی جدید هستید</p>
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
            <ul class="alert alert-danger">
                {!! implode('', $errors->all('<div>:message</div>')) !!}
            </ul>
            @endif
            <div class="row" id="participants-container">
                <div class="col-md-3 participant-form" id="participant-form-0">
                    <div class="mb-3">
                        تعداد کل سهام عادی: {{ $election->normal_stock_count }}
                    </div>
                </div>
                <div class="col-md-3 participant-form" id="participant-form-0">
                    <div class="mb-3">
                        تعداد کل سهام ممتاز: {{ $election->prefered_stock_count }}
                    </div>
                </div>
                <div class="col-md-3 participant-form" id="participant-form-0">
                    <div class="mb-3">
                        تعداد کل سهم ها: {{ ($election->prefered_stock_count * $election->prefered_stock_weight) + $election->normal_stock_count }}
                    </div>
                </div>
                <div class="col-md-3 participant-form" id="participant-form-0">
                    <div class="mb-3">
                        حداقل تعداد مشارکت کنندگان: {{ $election->min_participants }}
                    </div>
                </div>
                <div class="col-md-4 participant-form" id="participant-form-0">
                    <div class="mb-3">
                        <label for="participants.0.user_id" class="form-label">مشارکت کننده</label>
                        <select class="form-select my-1 my-md-0 me-sm-3" name="participants[0][user_id]" id="participants.0.user_id" data-toggle="select2">
                            <option value="">یک کاربر را انتخاب نمایید</option>
                            @foreach ($group->users as $user)
                            <option value="{{ $user->id }}" @selected(old('participants.0.user_id') == $user->id) >
                                {{ $user->fullName }}
                            </option>
                            @endforeach
                        </select>
                        @error('participants.0.user_id')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
                <div class="col-md-4 participant-form" id="participant-form-0">
                    <div class="mb-3">
                        <label for="participants.0.normal_stock_count" class="form-label">تعداد سهام عادی</label>
                        <input type="number" class="form-control" name="participants[0][normal_stock_count]" id="participants.0.normal_stock_count" placeholder="تعداد سهام عادی" required value="{{ old('participants.0.normal_stock_count') }}">
                        @error('participants.0.normal_stock_count')
                        <span class="text-danger font-weight-bold">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4 participant-form" id="participant-form-0">
                    <div class="mb-3">
                        <label for="participants.0.prefered_stock_count" class="form-label">تعداد سهام ممتاز</label>
                        <input type="number" class="form-control" name="participants[0][prefered_stock_count]" id="participants.0.prefered_stock_count" placeholder="تعداد سهام ممتاز" required value="{{ old('participants.0.prefered_stock_count') }}">
                        @error('participants.0.prefered_stock_count')
                        <span class="text-danger font-weight-bold">{{$message}}</span>
                        @enderror
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div class="card-footer">
            <div class="text-end mb-3 d-flex justify-content-between align-items-center">
                <button type="submit" class="btn btn-primary">ایجاد</button>
                <button type="button" id="add-participant-btn" class="btn btn-success">افزودن سهامدار</button>
            </div>
        </div>
    </div>
</form>

    @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
    <form class="card col-lg-5" action="{{ route('participants.import' , [$group->slug, $election->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf   
            <div >
                <div class="card-header col-lg-12 border-bottom border-dashed d-flex align-items-center">
                    <div class="col-lg-6">
                        <h4 class="header-title">آپلود فایل اکسل کاربران</h4>
                    </div>
                    <div class="col-lg-6 text-end">
                        <a href="{{ asset('assets/excel/نمونه اکسل شرکت کنندگان.xlsx') }}" download="" class="header-title" style="cursor: pointer">فایل نمونه اکسل</a>
                    </div>
                </div>
        
                <div class="card-body">
                
                    <div class="mb-3">
                        <label for="formFileMultiple" class="form-label">قایل مورد نظر را انتخاب کنید</label>
                        <input class="form-control" name="file" type="file" id="formFileMultiple">
                    </div>
                        
                    <!-- Preview -->
                </div>
                <div class="card-footer">
                    <div class="text-end mb-3">
                        <button type="submit" class="btn btn-primary">  ایجاد مشارکت کننده با اکسل</button>
                    </div>
                </div>
                <!-- end card-body -->
            </div>
    </form>
    @endif

@endsection

@section('scripts')

<script>
    let participantIndex = 1;

    document.getElementById('add-participant-btn').addEventListener('click', function() {
        const participantsContainer = document.getElementById('participants-container');

        const newParticipantForm = `
            <div class="col-md-4 participant-form" id="participant-form-${participantIndex}">
                <div class="mb-3">
                    <label for="participants.${participantIndex}.user_id" class="form-label">مشارکت کننده</label>
                    <select class="form-select my-1 my-md-0 me-sm-3" name="participants[${participantIndex}][user_id]" id="participants.${participantIndex}.user_id" data-toggle="select2">
                        <option value="">یک کاربر را انتخاب نمایید</option>
                        @foreach ($group->users as $user)
                            <option value="{{ $user->id }}" @selected(old('participants.${participantIndex}.user_id') == $user->id) >{{ $user->fullName }}</option>
                        @endforeach
                    </select>
                    @error('participants.${participantIndex}.user_id')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
            <div class="col-md-4 participant-form" id="participant-form-${participantIndex}">
                <div class="mb-3">
                    <label for="participants.${participantIndex}.normal_stock_count" class="form-label">تعداد سهام عادی:</label>
                    <input type="number" class="form-control" name="participants[${participantIndex}][normal_stock_count]" id="participants.${participantIndex}.normal_stock_count" placeholder="تعداد سهام عادی" required value="{{ old('participants.${participantIndex}.normal_stock_count') }}">
                    @error('participants.${participantIndex}.normal_stock_count')
                    <span class="text-danger font-weight-bold">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-md-4 participant-form" id="participant-form-${participantIndex}">
                <div class="mb-3">
                    <label for="participants.${participantIndex}.prefered_stock_count" class="form-label">تعداد سهام ممتاز</label>
                    <input type="number" class="form-control" name="participants[${participantIndex}][prefered_stock_count]" id="participants.${participantIndex}.prefered_stock_count" placeholder="تعداد سهام ممتاز" required value="{{ old('participants.${participantIndex}.prefered_stock_count') }}">
                    @error('participants.${participantIndex}.prefered_stock_count')
                    <span class="text-danger font-weight-bold">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            @endif
        `;

        participantsContainer.insertAdjacentHTML('beforeend', newParticipantForm);
        participantIndex++;
    });
</script>
@include('app.alerts.toastr.success')

@endsection