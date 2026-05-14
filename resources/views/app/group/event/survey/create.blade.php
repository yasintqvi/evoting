@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">مدیریت نظرسنجی</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript:void(0);">خانه</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">نظرسنجی</a></li>
                <li class="breadcrumb-item active">{{ $survey ? 'ویرایش نظرسنجی' : 'ایجاد نظرسنجی' }}</li>
            </ol>
        </div>
    </div>

    <div class="row">
        {{-- ستون سمت چپ: فرم ایجاد یا ویرایش نظرسنجی / سوال --}}
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
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

                    {{-- ویرایش نظرسنجی --}}
                    @if ($survey && request()->has('editSurvey') && ! $survey->isLockedForEditing())
                        <h5 class="mb-3 fs-16 fw-semibold">ویرایش نظرسنجی</h5>
                        <form action="{{ route('surveys.update', [$group->slug, $event->slug, $survey->slug]) }}"
                            method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">عنوان</label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ old('title', $survey->title) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">توضیحات</label>
                                <textarea name="description" class="form-control">{{ old('description', $survey->description) }}</textarea>
                            </div>

                            <label for="is_anonymous" class="form-label d-block mt-2">نمایش ناشناس</label>
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="is_anonymous" value="0">
                                <input class="form-check-input" type="checkbox" id="is_anonymous" name="is_anonymous"
                                    value="1" @checked(old('is_anonymous', $survey->is_anonymous))>
                                <label class="form-check-label" for="is_anonymous">فعال / غیر فعال</label>
                            </div>

                            @if ($group->type === App\Enums\GroupType::SPECIAL)
                                <label for="weight_by_stock" class="form-label d-block mt-2">وزن‌دهی پاسخ‌ها بر اساس
                                    سهام</label>
                                <div class="form-check form-switch mb-2">
                                    <input type="hidden" name="weight_by_stock" value="0">
                                    <input class="form-check-input" type="checkbox" id="weight_by_stock"
                                        name="weight_by_stock" value="1" @checked(old('weight_by_stock', $survey->weight_by_stock))>
                                    <label class="form-check-label" for="weight_by_stock">فعال / غیر فعال</label>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">عدم اجازه شرکت در نظردهی</label>
                                <select name="blocked_user_ids[]" class="form-select" multiple data-toggle="select2">
                                    @foreach ($participants as $p)
                                        <option value="{{ $p->user->id }}" @selected(in_array($p->user->id, old('blocked_user_ids', [])))>
                                            {{ $p->user->first_name }} {{ $p->user->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block">کاربرانی که انتخاب شوند، امکان پاسخ‌دادن به این نظرسنجی را
                                    نخواهند داشت.</small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('surveys.create', [$group->slug, $event->slug, 'survey_id' => $survey->slug]) }}"
                                    class="btn btn-sm btn-outline-secondary">↩ بازگشت</a>
                                <button type="submit" class="btn btn-primary">بروزرسانی</button>
                            </div>
                        </form>

                        {{-- اضافه یا ویرایش سوال --}}
                    @elseif ($survey && $survey->isLockedForEditing())
                        <div class="alert alert-warning mb-0">
                            این نظرسنجی شروع شده است؛ امکان ویرایش عنوان، سوال‌ها یا حذف ساختار وجود ندارد. می‌توانید از
                            <a href="{{ route('surveys.index', [$group, $event]) }}">لیست نظرسنجی‌ها</a>
                            آمار و نتایج را ببینید.
                        </div>
                    @elseif ($survey && ! $survey->isLockedForEditing())
                        <h5 class="mb-3 fs-16 fw-semibold">{{ isset($editQuestion) ? 'ویرایش سوال' : 'افزودن سوال' }}</h5>
                        <form
                            action="{{ isset($editQuestion)
                                ? route('questions.update', [$group->slug, $event->slug, $survey->slug, $editQuestion->id])
                                : route('questions.store', [$group->slug, $event->slug, $survey->slug]) }}"
                            method="POST">
                            @csrf
                            @if (isset($editQuestion))
                                @method('PUT')
                            @endif

                            <div class="mb-3">
                                <label class="form-label">متن سوال</label>
                                <input type="text" name="question_text" class="form-control"
                                    value="{{ old('question_text', $editQuestion->question_text ?? '') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">نوع سوال</label>
                                <select name="type" class="form-control" onchange="checkElectionType(event)" required>
                                    <option value="">انتخاب کنید</option>
                                    <option value="1" @selected(old('type', $editQuestion->type ?? '') == 1)>تک انتخابی</option>
                                    <option value="2" @selected(old('type', $editQuestion->type ?? '') == 2)>چند انتخابی</option>
                                </select>
                            </div>

                            <div id="options-wrapper"
                                style="display: {{ old('type', $editQuestion->type ?? '') == 1 || old('type', $editQuestion->type ?? '') == 2 ? 'block' : 'none' }}">
                                <label class="form-label">گزینه‌ها</label>
                                <div id="options-list">
                                    @if (!empty(old('options')))
                                        @foreach (old('options') as $key => $option)
                                            <div class="input-group mb-2">
                                                <input type="text" name="options[]" class="form-control"
                                                    value="{{ $option }}"
                                                    placeholder="گزینه {{ $loop->iteration }}">
                                                <button type="button" class="btn btn-danger"
                                                    onclick="this.parentElement.remove()">❌</button>
                                            </div>
                                        @endforeach
                                    @elseif(isset($editQuestion) && $editQuestion->options->count())
                                        @foreach ($editQuestion->options as $key => $option)
                                            <div class="input-group mb-2">
                                                <input type="text" name="options[]" class="form-control"
                                                    value="{{ $option->option_text }}"
                                                    placeholder="گزینه {{ $loop->iteration }}">
                                                <button type="button" class="btn btn-danger"
                                                    onclick="this.parentElement.remove()">❌</button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <button type="button" class="btn btn-sm btn-success mt-2" onclick="addOption()">افزودن
                                    گزینه</button>
                            </div>

                            <div class="form-check mt-2">
                                <input type="hidden" name="is_required" value="0">
                                <input type="checkbox" name="is_required" class="form-check-input" value="1"
                                    @checked(old('is_required', $editQuestion->is_required ?? false))>
                                <label class="form-check-label">الزامی</label>
                            </div>

                            <button type="submit"
                                class="btn btn-primary mt-3">{{ isset($editQuestion) ? 'بروزرسانی سوال' : 'افزودن سوال' }}</button>
                        </form>

                        {{-- ایجاد نظرسنجی جدید --}}
                    @else
                        <h5 class="mb-3 fs-16 fw-semibold">ایجاد نظرسنجی جدید</h5>
                        <form action="{{ route('surveys.store', [$group->slug, $event->slug]) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">عنوان</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">توضیحات</label>
                                <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                            </div>

                            <label for="is_anonymous_create" class="form-label d-block mt-2">نمایش ناشناس</label>
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="is_anonymous" value="0">
                                <input class="form-check-input" type="checkbox" id="is_anonymous_create" name="is_anonymous"
                                    value="1" @checked(old('is_anonymous'))>
                                <label class="form-check-label" for="is_anonymous_create">فعال / غیر فعال</label>
                            </div>

                            @if ($group->type === App\Enums\GroupType::SPECIAL)
                                <label for="weight_by_stock" class="form-label d-block mt-2">وزن‌دهی پاسخ‌ها بر اساس
                                    سهام</label>
                                <div class="form-check form-switch mb-2">
                                    <input type="hidden" name="weight_by_stock" value="0">
                                    <input class="form-check-input" type="checkbox" id="weight_by_stock"
                                        name="weight_by_stock" value="1" @checked(old('weight_by_stock'))>
                                    <label class="form-check-label" for="weight_by_stock">فعال / غیر فعال</label>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">عدم اجازه شرکت در نظردهی</label>
                                <select name="blocked_user_ids[]" class="form-select" multiple data-toggle="select2">
                                    @foreach ($participants as $p)
                                        <option value="{{ $p->user->id }}" @selected(in_array($p->user->id, old('blocked_user_ids', [])))>
                                            {{ $p->user->first_name }} {{ $p->user->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block">کاربرانی که انتخاب شوند، امکان پاسخ‌دادن به این نظرسنجی
                                    را نخواهند داشت.</small>
                            </div>

                            @php
                                $isSpecialGroup = $group->type === App\Enums\GroupType::SPECIAL;
                            @endphp
                            <div class="alert {{ $isSpecialGroup ? 'alert-info' : 'alert-secondary' }}">
                                @if ($isSpecialGroup)
                                    در این رویداد (سهامی خاص)، پاسخ‌ها می‌توانند بر اساس میزان سهام کاربران وزن‌دهی شوند.
                                @else
                                    در رویداد تعاونی، سهام کاربران نمایش داده نمی‌شود و پاسخ‌ها بدون وزن محاسبه می‌شوند.
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary">ایجاد</button>
                        </form>
                    @endif

                </div>
            </div>
        </div>

        {{-- ستون سمت راست: نمایش جزئیات نظرسنجی و لیست سوالات --}}
        @if ($survey)
            <div class="col-xl-8">
                <div class="card p-2 mb-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="header-title mt-2">{{ $survey->title }}</h4>
                        @if (! $survey->isLockedForEditing())
                            <a href="{{ route('surveys.create', [$group->slug, $event->slug, 'survey_id' => $survey->slug, 'editSurvey' => 1]) }}"
                                class="btn btn-sm btn-outline-primary">ویرایش نظرسنجی</a>
                        @endif
                    </div>
                    @if ($survey->description)
                        <p class="text-muted pt-1">{{ $survey->description }}</p>
                    @endif
                </div>

                @foreach ($survey->questions as $question)
                    <div class="card p-3 mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs me-2">
                                    <span
                                        class="avatar-title bg-secondary rounded-circle fw-bold">{{ $loop->iteration }}</span>
                                </div>
                                <h5 class="mb-0">{{ $question->question_text }}</h5>
                            </div>
                            <div class="d-flex gap-2">
                                @if (! $survey->isLockedForEditing())
                                    <a href="{{ route('questions.edit', [$group->slug, $event->slug, $survey->slug, $question->id]) }}"
                                        class="btn btn-sm btn-outline-primary">ویرایش</a>
                                    <form
                                        action="{{ route('questions.destroy', [$group->slug, $event->slug, $survey->slug, $question->id]) }}"
                                        method="POST"
                                        onsubmit="return confirm('آیا مطمئن هستید که می‌خواهید این سوال را حذف کنید؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <span class="text-muted small">
                            <span class="text-danger">{{ $question->is_required ? ' *' : '' }}</span>
                            ({{ $question->type == 1 ? 'تک انتخابی' : 'چند انتخابی' }})
                        </span>

                        @if ($question->options->count())
                            <div class="d-flex flex-wrap gap-3 mt-3">
                                @foreach ($question->options as $option)
                                    <label class="d-flex align-items-center gap-2 border p-2 rounded">
                                        @if ($question->type == 1)
                                            <input type="radio" disabled>
                                        @else
                                            <input type="checkbox" disabled>
                                        @endif
                                        {{ $option->option_text }}
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        let optionCount = 0;

        function checkElectionType(event) {
            const value = event.target.value;
            const optionsWrapper = document.getElementById('options-wrapper');
            const optionsList = document.getElementById('options-list');

            if (value === "1" || value === "2") {
                optionsWrapper.style.display = "block";
                if (!optionsList.hasChildNodes()) {
                    addOption();
                }
            } else {
                optionsWrapper.style.display = "none";
                optionsList.innerHTML = "";
                optionCount = 0;
            }
        }

        function addOption() {
            optionCount++;
            const optionsList = document.getElementById('options-list');
            const div = document.createElement('div');
            div.classList.add("input-group", "mb-2");
            div.innerHTML = `
        <input type="text" name="options[]" class="form-control" placeholder="گزینه ${optionCount}">
        <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()">❌</button>
    `;
            optionsList.appendChild(div);
        }
    </script>
@endsection
