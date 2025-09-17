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

            <li class="breadcrumb-item active">تعیین نامزد ها</li>
        </ol>
    </div>
</div>

<form action="{{ route('candidates.update', [$group->slug,$event->id, $election->id]) }}" method="post">
    @csrf
    @method('put')
    <div class="card col-lg-6">
        <div class="card-header border-bottom border-dashed">
            <h4 class="card-title">انتخاب نامزد های همه پرسی</h4>
            <p class="text-muted mb-0">شما در حال انتخاب نامزد های همه پرسی هستید</p>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="main_candidates" class="form-label">انتخاب نامزد های هیت میره</label>
                        <small class="text-muted">(حداقل {{ $election->main_member_count }} نامزد را انتخاب کنید)</small>
                        <select
                            class="form-select my-1 my-md-0 me-sm-3"
                            name="main_candidates_ids[]"
                            id="main_candidates"
                            data-toggle="select2"
                            multiple>
                            @foreach ($group->users as $user)
                            <option
                                value="{{ $user->id }}"
                                {{ collect(old('main_candidates_ids', $election->candidates()->where('candidate_type', App\Enums\CandidateType::DIRECTOR)->get()->pluck('user_id')->toArray()))->contains($user->id) ? 'selected' : '' }}>
                                {{ $user->fullName }}
                            </option>
                            @endforeach
                        </select>
                        @error('main_candidates_ids')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="incpector_candidates" class="form-label">انتخاب نامزد های بازرس </label>
                        <small class="text-muted">(حداقل {{ $election->incpector_main_member_count }} نامزد را انتخاب کنید)</small>
                        <select
                            class="form-select my-1 my-md-0 me-sm-3"
                            name="incpector_candidates_ids[]"
                            id="incpector_candidates"
                            data-toggle="select2"
                            multiple>
                            @foreach ($group->users as $user)
                            <option
                                value="{{ $user->id }}"
                                {{ collect(old('incpector_candidates_ids', $election->candidates()->where('candidate_type', App\Enums\CandidateType::INSPECTOR)->get()->pluck('user_id')->toArray()))->contains($user->id) ? 'selected' : '' }}>
                                {{ $user->fullName }}
                            </option>
                            @endforeach
                        </select>
                        @error('incpector_candidates_ids')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
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