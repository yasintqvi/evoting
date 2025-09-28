@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">ایجاد نظرسنجی</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>
                <li class="breadcrumb-item"><a href="javascript: void(0);">انتخابات</a></li>
                <li class="breadcrumb-item active">ایجاد</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between border-bottom border-light">
            <h4 class="header-title">طراحی فرم</h4>
            <div>
                <a href="http://127.0.0.1:8000/Evoting-Test-Group/events/2/surveys/create" class="btn btn-primary"><i
                        class="ti ti-plus me-1"></i>ایجاد نظرسنجی</a>
            </div>
        </div>
    </div>

    <div class="row">
        @php
            $showSurveyForm = is_null($survey) || request()->get('back') === 'settings';
            $showQuestionForm = isset($survey) && request()->get('back') !== 'settings';
        @endphp

        @if ($showQuestionForm)
            <form class="col-xl-3"
                action="{{ isset($editQuestion)
                    ? route('questions.update', [$group->slug, $event->id, $survey->id, $editQuestion->id])
                    : route('questions.store', [$group->slug, $event->id, $survey->id]) }}"
                method="post">

                @csrf
                @if (isset($editQuestion))
                    @method('PUT')
                @endif

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                            <h5 class="mb-1 fs-16 fw-semibold">
                                {{ isset($editQuestion) ? 'ویرایش سوال' : 'افزودن سوال' }}
                            </h5>
                            <a href="{{ route('surveys.create', [$group->slug, $event->id, 'survey_id' => $survey->id]) }}"
                                class="btn btn-sm btn-outline-secondary">
                                🔙 بازگشت
                            </a>
                        </div>

                        {{-- متن سوال --}}
                        <div class="mb-3">
                            <label for="question_text" class="form-label">متن سوال</label>
                            <input type="text" name="question_text"
                                value="{{ old('question_text', $editQuestion->question_text ?? '') }}" class="form-control"
                                id="question_text">
                        </div>

                        {{-- نوع سوال --}}
                        <div class="mb-3">
                            <label for="election_type" class="form-label">نوع همه پرسی</label>
                            <select name="type" id="election_type" class="form-select"
                                onchange="checkElectionType(event)">
                                <option value="">-- انتخاب کنید --</option>
                                <option value="1"
                                    {{ isset($editQuestion) && $editQuestion->type == 1 ? 'selected' : '' }}>تک انتخابی
                                </option>
                                <option value="2"
                                    {{ isset($editQuestion) && $editQuestion->type == 2 ? 'selected' : '' }}>چند انتخابی
                                </option>
                            </select>
                        </div>

                        {{-- گزینه‌ها --}}
                        <div id="options-wrapper" class="mt-3"
                            style="{{ isset($editQuestion) ? 'display:block;' : 'display:none;' }}">
                            <label class="form-label">گزینه‌ها</label>
                            <div id="options-list">
                                @if (isset($editQuestion) && $editQuestion->options->count())
                                    @foreach ($editQuestion->options as $i => $option)
                                        <div class="input-group mb-2">
                                            <input type="text" name="options[{{ $option->id }}]" class="form-control"
                                                value="{{ $option->option_text }}">
                                            <button type="button" class="btn btn-danger"
                                                onclick="this.parentElement.remove()">❌</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" onclick="addOption()" class="btn btn-sm btn-soft-info mt-2">➕ افزودن
                                گزینه</button>
                        </div>

                        <div class="mt-2 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                {{ isset($editQuestion) ? 'بروزرسانی سوال' : 'ایجاد سوال' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @endif


        @if (isset($survey))
            <div class="col-xl-9">
                <div class="card p-2">
                    <h4 class="header-title mt-2">{{ $survey->title }}</h4>
                    @if ($survey->description)
                        <p class="text-muted pt-1">{{ $survey->description }}</p>
                    @endif
                </div>

                @if ($survey->questions->count())
                    @foreach ($survey->questions as $question)
                        <div class="card p-3 mt-3">
                            <div class="d-flex align-items-center mb-2 justify-content-between">
                                {{-- شماره و متن سوال --}}
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-title bg-secondary rounded-circle fw-bold">
                                            {{ $loop->iteration }}
                                        </span>
                                    </div>
                                    <h5 class="mb-0">{{ $question->question_text }}</h5>
                                </div>

                                {{-- دکمه‌های عملیات --}}
                                <div class="d-flex gap-2">
                                    {{-- دکمه ویرایش --}}
                                    <a href="{{ route('questions.edit', [
                                        'group' => $group->slug,
                                        'event' => $event->id,
                                        'survey' => $survey->id,
                                        'question' => $question->id,
                                    ]) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        ✏️ ویرایش
                                    </a>


                                    {{-- دکمه حذف (اختیاری) --}}
                                    <form action="#" method="POST"
                                        onsubmit="return confirm('آیا مطمئن هستید که می‌خواهید این سوال را حذف کنید؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">🗑️ حذف</button>
                                    </form>
                                </div>
                            </div>

                            {{-- نوع سوال --}}
                            <span class="text-muted small">
                                ({{ $question->type == 1 ? 'تک انتخابی' : 'چند انتخابی' }})
                            </span>

                            {{-- نمایش گزینه‌ها --}}
                            @if ($question->options->count())
                                <div class="d-flex flex-wrap gap-3 mt-3">
                                    @foreach ($question->options as $option)
                                        <label class="d-flex align-items-center gap-2 border p-2 rounded">
                                            @if ($question->type == 1)
                                                <input type="radio" disabled name="question_{{ $question->id }}"
                                                    value="{{ $option->id }}">
                                            @else
                                                <input type="checkbox" disabled name="question_{{ $question->id }}[]"
                                                    value="{{ $option->id }}">
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




    </div>
    </div>
@endsection

@section('scripts')
    @include('app.alerts.toastr.success')
    @include('app.alerts.toastr.error')

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
