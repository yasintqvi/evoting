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

    <div class="row">
        @php
            $showSurveyForm = is_null($survey) || request()->get('back') === 'settings';
            $showQuestionForm = isset($survey) && request()->get('back') !== 'settings';
        @endphp

        @if ($showSurveyForm)
            <form class="col-xl-3"
                action="{{ $survey
                    ? route('surveys.update', [$group->slug, $event->id, $survey->id])
                    : route('surveys.store', [$group->slug, $event->id]) }}"
                method="post">
                @csrf
                @if ($survey)
                    @method('PUT')
                @endif
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-1 fs-16 fw-semibold border-bottom pb-2">
                            {{ $survey ? 'ویرایش نظرسنجی' : 'ایجاد نظرسنجی جدید' }}
                        </h5>
                        <div class="mb-3">
                            <label for="title" class="form-label">عنوان نظر سنجی</label>
                            <input type="text" name="title" value="{{ old('title', $survey->title ?? '') }}"
                                class="form-control" id="title">
                            @error('title')
                                <span class="text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">توضیحات (اختیاری)</label>
                            <textarea class="form-control mt-1" name="description" id="description" rows="3">{{ old('description', $survey->description ?? '') }}</textarea>
                            @error('description')
                                <span class="text-danger font-weight-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mt-2 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                {{ $survey ? 'بروزرسانی' : 'ایجاد' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @endif

        @if ($showQuestionForm)
            <form class="col-xl-3" action="{{ route('questions.store', [$group->slug, $event->id, $survey->id]) }}" @csrf
                <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h5 class="mb-1 fs-16 fw-semibold">افزودن سوال</h5>
                        <a href="{{ route('surveys.create', [$group->slug, $event->id, 'survey_id' => $survey->id, 'back' => 'settings']) }}"
                            class="btn btn-sm btn-outline-secondary">
                            🔙 بازگشت به تنظیمات
                        </a>
                    </div>

                    <div class="mb-3">
                        <label for="question_text" class="form-label">متن سوال</label>
                        <input type="text" name="question_text" class="form-control" id="question_text">
                        @error('question_text')
                            <span class="text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="election_type" class="form-label">نوع همه پرسی</label>
                        <select name="type" onchange="checkElectionType(event)" id="election_type" class="form-select">
                            <option value="">-- انتخاب کنید --</option>
                            <option value="single">تک انتخابی</option>
                            <option value="multiple">چند انتخابی</option>
                        </select>
                        @error('type')
                            <span class="text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>

                    <div id="options-wrapper" class="mt-3" style="display:none;">
                        <label class="form-label">گزینه‌ها</label>
                        <div id="options-list"></div>
                        <button type="button" onclick="addOption()" class="btn btn-sm btn-soft-info mt-2">
                            ➕ افزودن گزینه
                        </button>
                    </div>

                    <div class="mt-2 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">ایجاد سوال</button>
                    </div>
                </div>
    </div>
    </form>
    @endif

    @if (isset($survey))
        <div class="col-xl-6">
            <div class="card p-2">
                <h4 class="header-title">{{ $survey->title }}</h4>
                @if ($survey->description)
                    <p class="text-muted">{{ $survey->description }}</p>
                @endif
                <a href="{{ route('surveys.edit', [$group->slug, $event->id, $survey->id]) }}"
                    class="btn btn-warning mt-2">ویرایش نظرسنجی</a>
            </div>
        </div>
    @endif
    </div>

    <div class="col-xl-9">
        <div class="card p-2">
            @if ($survey && $survey->questions->count())
                <h4 class="header-title">{{ $survey->title }}</h4>
                @foreach ($survey->questions as $question)
                    <div class="mt-3 p-2 border rounded">
                        <strong>{{ $question->text }}</strong>
                        <span class="text-muted">({{ $question->type == 1 ? 'تک انتخابی' : 'چند انتخابی' }})</span>
                        @if ($question->options->count())
                            <ul class="mt-2">
                                @foreach ($question->options as $option)
                                    <li>{{ $option->text }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            @endif
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

            if (value === "single" || value === "multiple") {
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
