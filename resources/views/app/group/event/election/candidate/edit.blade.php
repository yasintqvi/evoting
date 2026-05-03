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

@php
    $requiredCandidatesCount = (int) ($election->candidate_count ?? 0);
@endphp

<form action="{{ route('candidates.update', [$group,$event, $election]) }}" method="post">
    @csrf
    @method('put')
    <div class="card col-lg-5">
        <div class="card-header border-bottom border-dashed">
            <h4 class="card-title">انتخاب نامزد های همه پرسی</h4>
            <p class="text-muted mb-0">شما در حال انتخاب نامزد های همه پرسی هستید</p>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="main_candidates" class="form-label">انتخاب نامزد</label>
                        <small class="text-muted">
                            ({{ $requiredCandidatesCount > 0 ? "دقیقاً {$requiredCandidatesCount} نامزد را انتخاب کنید" : 'تعداد نامزدها هنوز مشخص نشده است' }})
                        </small>
                        <select
                            class="form-select my-1 my-md-0 me-sm-3"
                            name="main_candidates_ids[]"
                            id="main_candidates"
                            data-toggle="select2"
                            data-required-count="{{ $requiredCandidatesCount }}"
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
                    <div class="alert alert-info" id="candidate-count-info" style="display: none;">
                        <span id="candidate-count-info-text"></span>
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

@section('scripts')
    <script>
        function showToast(type, message) {
            if (typeof Toast !== 'undefined') {
                Toast.create({
                    title: type === 'success' ? 'موفق' : 'خطا',
                    message: message,
                    type: type,
                    duration: 3000,
                });
            } else {
                alert(message);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('main_candidates');
            const requiredCount = parseInt(select?.getAttribute('data-required-count') || '0', 10);
            const info = document.getElementById('candidate-count-info');
            const infoText = document.getElementById('candidate-count-info-text');
            const submitButton = document.querySelector('button[type="submit"]');

            let lastValidSelection = Array.from(select?.selectedOptions || []).map(o => o.value);

            function updateInfo() {
                const selectedCount = Array.from(select?.selectedOptions || []).length;

                if (!requiredCount) {
                    info.style.display = 'block';
                    info.className = 'alert alert-warning';
                    infoText.textContent = 'ابتدا تعداد کل نامزدها را در مرحله قبل مشخص کنید.';
                    if (submitButton) submitButton.disabled = true;
                    return;
                }

                const remaining = requiredCount - selectedCount;
                info.style.display = 'block';

                if (remaining === 0) {
                    info.className = 'alert alert-success';
                    infoText.textContent = `تعداد نامزدها کامل است (${requiredCount} نفر).`;
                    if (submitButton) submitButton.disabled = false;
                } else if (remaining > 0) {
                    info.className = 'alert alert-info';
                    infoText.textContent = `تا تکمیل لیست، ${remaining} نفر دیگر انتخاب کنید.`;
                    if (submitButton) submitButton.disabled = true;
                } else {
                    info.className = 'alert alert-danger';
                    infoText.textContent = `بیش از حد مجاز انتخاب شده است. باید دقیقاً ${requiredCount} نفر باشد.`;
                    if (submitButton) submitButton.disabled = true;
                }
            }

            function restoreSelection(values) {
                Array.from(select.options).forEach(opt => {
                    opt.selected = values.includes(opt.value);
                });

                if (window.jQuery && jQuery(select).data('select2')) {
                    jQuery(select).trigger('change.select2');
                } else {
                    select.dispatchEvent(new Event('change'));
                }
            }

            if (window.jQuery) {
                const $select = jQuery(select);
                $select.on('change', function() {
                    const selectedValues = $select.val() || [];

                    if (requiredCount && selectedValues.length > requiredCount) {
                        restoreSelection(lastValidSelection);
                        showToast('error', `حداکثر تعداد انتخاب ${requiredCount} نفر است.`);
                        return;
                    }

                    lastValidSelection = selectedValues.slice();
                    updateInfo();
                });
            } else {
                select.addEventListener('change', function() {
                    const selectedValues = Array.from(select.selectedOptions).map(o => o.value);

                    if (requiredCount && selectedValues.length > requiredCount) {
                        restoreSelection(lastValidSelection);
                        showToast('error', `حداکثر تعداد انتخاب ${requiredCount} نفر است.`);
                        return;
                    }

                    lastValidSelection = selectedValues.slice();
                    updateInfo();
                });
            }

            updateInfo();
        });
    </script>
@endsection
