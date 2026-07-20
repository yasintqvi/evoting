@extends('app.layouts.app')

@section('head-tag')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@0.9.12/dist/jalalidatepicker.min.css">
    <style>
        /* تقویم (jdp-container یک تگ سفارشی است نه کلاس) باید بالاتر از مودال بوت‌استرپ (z-index: 1055) نمایش داده شود. */
        jdp-container {
            z-index: 1100 !important;
        }
    </style>
@endsection

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">نظرسنجی</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('groups.index', $group) }}">داشبورد</a></li>
                <li class="breadcrumb-item">
                    <a href="{{ route('surveys.index', ['group' => $group, 'event' => $event]) }}">نظرسنجی</a>
                </li>
                <li class="breadcrumb-item active">همه</li>
            </ol>
        </div>
    </div>

    @if ($errors->has('end_at'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first('end_at') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between border-bottom border-light">
                    <form class="col-lg-9 gap-2 d-flex" method="get" action="">
                        <h4 class="header-title">لیست نظرسنجی</h4>
                        <div class="position-relative">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control ps-4"
                                placeholder="جستجو...">
                        </div>
                        <div class="position-relative">
                            <select class="form-control" name="status">
                                <option value="" disabled selected>وضعیت</option>
                                <option value="">همه</option>
                                <option value="1" @selected(request('status') == 1)>فعال</option>
                                <option value="0" @selected(request('status') == 0)>غیر فعال</option>
                            </select>
                        </div>
                        <div class="position-relative">
                            <select class="form-control" name="is_anonymous">
                                <option value="" disabled selected>ناشناس</option>
                                <option value="">همه</option>
                                <option value="1" @selected(request('is_anonymous') == 1)>بله</option>
                                <option value="0" @selected(request('is_anonymous') == 0)>خیر</option>
                            </select>
                        </div>

                        <button class="btn btn-primary bg-gradient">جست و جو</button>
                        <a href="{{ route('surveys.index', [$group, $event]) }}" class="btn btn-danger bg-gradient">حذف
                            فیلتر</a>
                    </form>

                    <a href="{{ route('surveys.create', [$group, $event]) }}" class="btn btn-success bg-gradient">
                        <i class="ti ti-plus me-1"></i>ایجاد نظرسنجی
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th class="ps-3" style="width: 50px;"></th>
                                <th>عنوان</th>
                                <th>زمان شروع</th>
                                <th>زمان پایان</th>
                                <th>ناشناس</th>
                                <th>وضعیت</th>
                                <th class="text-center" style="width: 120px;">اقدامات</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($surveys as $survey)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $survey->title }}</td>
                                    <td class="text-muted small">
                                        {{ $survey->start_at ? verta($survey->start_at)->format('Y/m/d H:i') : '---' }}
                                    </td>
                                    <td class="text-muted small">
                                        {{ $survey->end_at ? verta($survey->end_at)->format('Y/m/d H:i') : '---' }}
                                    </td>
                                    <td>
                                        @if ($survey->is_anonymous)
                                            <span class="badge bg-info">بله</span>
                                        @else
                                            <span class="badge bg-secondary">خیر</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($survey->status == 1)
                                            <span class="badge bg-success">فعال</span>
                                        @else
                                            <span class="badge bg-danger">غیرفعال</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $surveyIsActive = (int) $survey->status === 1;
                                            $surveyHasEnded = ! $surveyIsActive && $survey->start_at !== null;
                                        @endphp

                                        <div class="hstack gap-1 justify-content-end flex-nowrap">
                                            @if ($surveyIsActive)
                                                <form id="endSurveyForm-{{ $survey->id }}"
                                                    action="{{ route('surveys.end', [$group, $event, $survey]) }}"
                                                    method="POST" class="d-none">@csrf</form>
                                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#endSurveyModal"
                                                    onclick="setEndSurveyForm({{ $survey->id }})">
                                                    پایان نظرسنجی
                                                </button>
                                            @elseif ($surveyHasEnded)
                                                <span class="badge bg-secondary">پایان یافته</span>
                                            @else
                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#startSurveyModal"
                                                    data-start-url="{{ route('surveys.start', [$group, $event, $survey]) }}"
                                                    onclick="openStartSurveyModal(this.dataset.startUrl)">
                                                    شروع نظرسنجی
                                                </button>
                                            @endif

                                            @if (! $survey->isLockedForEditing())
                                                <a href="{{ route('surveys.edit', ['group' => $group, 'event' => $event, 'survey' => $survey]) }}"
                                                    class="btn btn-secondary btn-sm" title="ویرایش نظرسنجی">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                <form action="{{ route('surveys.destroy', [$group, $event, $survey]) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('حذف نظرسنجی و همهٔ سوال‌ها و پاسخ‌های ثبت‌شده (در صورت وجود) غیرقابل بازگشت است. ادامه می‌دهید؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="حذف نظرسنجی">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('surveys.answer', [$group, $event, $survey]) }}"
                                                class="btn btn-success btn-sm" title="نمایش نظرسنجی">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            <a href="{{ route('surveys.statistics', [$group, $event, $survey]) }}"
                                                class="btn btn-warning btn-sm" title="آمار نظرسنجی">
                                                <i class="ti ti-align-box-bottom-center"></i>
                                            </a>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">
                                        هنوز هیچ نظرسنجی برگزار نشده است.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال تایید شروع نظرسنجی (زمان پایان اختیاری) --}}
    <div class="modal fade" id="startSurveyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="startSurveyModalForm" method="POST" action="#">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">شروع نظرسنجی</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">آیا از شروع این نظرسنجی مطمئن هستید؟</p>
                        <div class="mb-2">
                            <label for="startSurveyEndAt" class="form-label">زمان پایان (اختیاری)</label>
                            <input type="text" name="end_at" id="startSurveyEndAt" autocomplete="off"
                                class="form-control @error('end_at') is-invalid @enderror" data-jdp
                                data-jdp-time-picker="true" placeholder="۱۴۰۳/۰۱/۰۱ ۱۴:۳۰"
                                value="{{ old('end_at') }}">
                            @error('end_at')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-1">می‌توانید خالی بگذارید؛ در آن صورت محدودیت زمانی خودکار برای پایان اعمال نمی‌شود (می‌توانید بعداً دستی پایان دهید).</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                        <button type="submit" class="btn btn-success">بله، شروع کن</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- مودال تایید پایان نظرسنجی --}}
    <div class="modal fade" id="endSurveyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تایید پایان نظرسنجی</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">آیا از پایان دادن به این نظرسنجی مطمئن هستید؟</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="button" class="btn btn-danger" id="confirmEndBtn">بله، پایان بده</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/assets/samples/assets/irregular-data-series.js"></script>
    <script src="/assets/js/pages/chart-apex-bar.js"></script>

    <script>
        let currentEndFormId = null;

        function openStartSurveyModal(actionUrl) {
            const form = document.getElementById('startSurveyModalForm');
            if (form) {
                form.action = actionUrl;
            }
        }

        function setEndSurveyForm(surveyId) {
            currentEndFormId = 'endSurveyForm-' + surveyId;
        }

        document.getElementById('confirmEndBtn').addEventListener('click', function() {
            if (currentEndFormId) {
                document.getElementById(currentEndFormId).submit();
            }
        });

        if (typeof jalaliDatepicker !== 'undefined') {
            jalaliDatepicker.startWatch();
        }
    </script>
@endsection
