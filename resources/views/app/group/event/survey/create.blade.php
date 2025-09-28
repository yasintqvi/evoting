@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">
                مدیریت نظرسنجی
            </h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript:void(0);">خانه</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">انتخابات</a></li>
                <li class="breadcrumb-item active">مدیریت</li>
            </ol>
        </div>
    </div>

    <div class="row">
        {{-- ستون سمت راست: همیشه یکی هست، فقط محتوایش تغییر می‌کند --}}
        <div class="col-xl-3">
            <div class="card">
                <div class="card-body">

                    {{-- فرم ویرایش نظرسنجی --}}
                    @if (request()->has('editSurvey') && $survey)
                        <h5 class="mb-3 fs-16 fw-semibold">ویرایش نظرسنجی</h5>
                        <form action="{{ route('surveys.update', [$group->slug, $event->id, $survey->id]) }}"
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

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('surveys.create', [$group->slug, $event->id, 'survey_id' => $survey->id]) }}"
                                    class="btn btn-sm btn-outline-secondary">↩ بازگشت</a>
                                <button type="submit" class="btn btn-primary">بروزرسانی</button>
                            </div>
                        </form>

                        {{-- فرم افزودن / ویرایش سؤال --}}
                    @elseif ($survey)
                        <h5 class="mb-3 fs-16 fw-semibold">
                            {{ isset($editQuestion) ? 'ویرایش سوال' : 'افزودن سوال' }}
                        </h5>
                        <form
                            action="{{ isset($editQuestion)
                                ? route('questions.update', [$group->slug, $event->id, $survey->id, $editQuestion->id])
                                : route('questions.store', [$group->slug, $event->id, $survey->id]) }}"
                            method="POST">
                            @csrf
                            @if (isset($editQuestion))
                                @method('PUT')
                            @endif

                            <div class="mb-3">
                                <label for="question_text" class="form-label">متن سوال</label>
                                <input type="text" name="question_text"
                                    value="{{ old('question_text', $editQuestion->question_text ?? '') }}"
                                    class="form-control" id="question_text">
                            </div>

                            <div class="mb-3">
                                <label for="election_type" class="form-label">نوع سوال</label>
                                <select name="type" id="election_type" class="form-select"
                                    onchange="checkElectionType(event)">
                                    <option value="">-- انتخاب کنید --</option>
                                    <option value="1"
                                        {{ isset($editQuestion) && $editQuestion->type == 1 ? 'selected' : '' }}>
                                        تک انتخابی
                                    </option>
                                    <option value="2"
                                        {{ isset($editQuestion) && $editQuestion->type == 2 ? 'selected' : '' }}>
                                        چند انتخابی
                                    </option>
                                </select>
                            </div>

                            <div id="options-wrapper" class="mt-3"
                                style="{{ isset($editQuestion) ? 'display:block;' : 'display:none;' }}">
                                <label class="form-label">گزینه‌ها</label>
                                <div id="options-list">
                                    @if (isset($editQuestion) && $editQuestion->options->count())
                                        @foreach ($editQuestion->options as $option)
                                            <div class="input-group mb-2">
                                                <input type="text" name="options[{{ $option->id }}]"
                                                    class="form-control" value="{{ $option->option_text }}">
                                                <button type="button" class="btn btn-danger"
                                                    onclick="this.parentElement.remove()">❌</button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <button type="button" onclick="addOption()" class="btn btn-sm btn-soft-info mt-2">
                                    ➕ افزودن گزینه
                                </button>
                            </div>

                            <div class="mt-2 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    {{ isset($editQuestion) ? 'بروزرسانی سوال' : 'ایجاد سوال' }}
                                </button>
                            </div>
                        </form>

                        {{-- حالت اولیه: هنوز نظرسنجی ساخته نشده --}}
                    @else
                        <h5 class="mb-3 fs-16 fw-semibold">ایجاد نظرسنجی جدید</h5>
                        <form action="{{ route('surveys.store', [$group->slug, $event->id]) }}" method="POST">
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

                            <button type="submit" class="btn btn-primary">ایجاد</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- ستون سمت چپ: نمایش جزئیات نظرسنجی و سوالات --}}
        @if ($survey)
            <div class="col-xl-9">
                <div class="card p-2">
                    <h4 class="header-title mt-2">{{ $survey->title }}</h4>
                    @if ($survey->description)
                        <p class="text-muted pt-1">{{ $survey->description }}</p>
                    @endif

                    <a href="{{ route('surveys.create', [$group->slug, $event->id, 'survey_id' => $survey->id]) }}"
                        class="btn btn-sm btn-outline-secondary">🔙 بازگشت به تنظیمات</a>

                    <a href="{{ route('surveys.create', [$group->slug, $event->id, 'survey_id' => $survey->id, 'editSurvey' => 1]) }}"
                        class="btn btn-sm btn-outline-primary">✏️ ویرایش نظرسنجی</a>
                </div>

                @if ($survey->questions->count())
                    @foreach ($survey->questions as $question)
                        <div class="card p-3 mt-3">
                            <div class="d-flex align-items-center mb-2 justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-title bg-secondary rounded-circle fw-bold">
                                            {{ $loop->iteration }}
                                        </span>
                                    </div>
                                    <h5 class="mb-0">{{ $question->question_text }}</h5>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('questions.edit', [
                                        'group' => $group->slug,
                                        'event' => $event->id,
                                        'survey' => $survey->id,
                                        'question' => $question->id,
                                    ]) }}"
                                        class="btn btn-sm btn-outline-primary">ویرایش</a>

                                    <form action="#" method="POST" onsubmit="return confirm('آیا مطمئن هستید؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">حذف</button>
                                    </form>
                                </div>
                            </div>

                            <span class="text-muted small">
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
                @endif
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
