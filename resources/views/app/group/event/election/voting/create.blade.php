@extends('app.layouts.app')

@section('content')
    @php
        // محاسبه سهام موثر بر اساس تنظیمات انتخابات
        if ($election->ignore_stock_weight) {
            // وزن سهام ممتاز بی‌تأثیر است — هر سهم = ۱ رأی
            $effectiveStock = $participant->normal_stock_count + $participant->prefered_stock_count;
        } else {
            $effectiveStock = $participant->total_stock;
        }
    @endphp
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2 mb-3">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">انتخابات {{ $election->title }}</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('groups.index', $group) }}">خانه</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('elections.index', [$group->slug, $event->slug]) }}">انتخابات</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('elections.show', [$group->slug, $event->slug, $election->slug]) }}">{{ $election->title }}</a>
                </li>
                <li class="breadcrumb-item active">رای گیری</li>
            </ol>
        </div>
    </div>

    @if ($election->ends_at)
        <div class="alert {{ $election->isExpired() ? 'alert-danger' : 'alert-info' }} d-flex align-items-center gap-2">
            <i class="ti ti-clock fs-18"></i>
            <div>
                @if ($election->isExpired())
                    <strong>این انتخابات منقضی شده است.</strong>
                    زمان پایان:
                    {{ verta($election->ends_at)->format('Y/m/d H:i') }}
                @else
                    <strong>زمان پایان انتخابات:</strong>
                    {{ verta($election->ends_at)->format('Y/m/d H:i') }}
                    <div>
                        <small class="text-primary election-countdown"
                            data-end="{{ $election->ends_at?->toDateTimeString() }}">
                            زمان باقی‌مانده: <span class="value"></span>
                        </small>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <form id="votingForm" action="{{ route('voting.store', [$group->slug, $event->slug, $election->slug]) }}"
        method="post">
        @csrf
        <input type="hidden" name="election_slug" value="{{ $election->slug }}">
        <input type="hidden" name="group_slug" value="{{ $group->slug }}">
        <input type="hidden" name="event_slug" value="{{ $event->slug }}">
        <div class="row">
            <div class="col-xl-4 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset(user()->profile_image) }}" alt=""
                                class="avatar-xl rounded-circle border border-light border-2">
                            <div>
                                <h4 class="text-dark fw-medium">{{ user()->full_name }}</h4>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="mt-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="bg-opacity-75 d-flex align-items-center justify-content-center rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="m17.578 4.432l-2-1.05C13.822 2.461 12.944 2 12 2s-1.822.46-3.578 1.382l-.321.169l8.923 5.099l4.016-2.01c-.646-.732-1.688-1.279-3.462-2.21m4.17 3.534l-3.998 2V13a.75.75 0 0 1-1.5 0v-2.286l-3.5 1.75v9.44c.718-.179 1.535-.607 2.828-1.286l2-1.05c2.151-1.129 3.227-1.693 3.825-2.708c.597-1.014.597-2.277.597-4.8v-.117c0-1.893 0-3.076-.252-3.978M11.25 21.904v-9.44l-8.998-4.5C2 8.866 2 10.05 2 11.941v.117c0 2.525 0 3.788.597 4.802c.598 1.015 1.674 1.58 3.825 2.709l2 1.049c1.293.679 2.11 1.107 2.828 1.286M2.96 6.641l9.04 4.52l3.411-1.705l-8.886-5.078l-.103.054c-1.773.93-2.816 1.477-3.462 2.21" />
                                        </svg>
                                    </div>
                                    <p class="mb-0 text-dark">{{ $election->title }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <h4 class="fs-15">اطلاعات انتخابات:</h4>
                            <div class="row mt-1 g-2">
                                <div class="col-lg-4 col-6">
                                    <h4 class="fw-medium mb-0">
                                        {{ $election->candidates()->where('candidate_type', App\Enums\CandidateType::DIRECTOR)->count() }}
                                    </h4>
                                    <p class="mb-0 text-muted lh-lg"> کاندیدای هیت مدیره </p>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <h4 class="fw-medium mb-0">
                                        {{ $event->participants()->where('is_present', 1)->count() }}
                                    </h4>
                                    <p class="mb-0 text-muted lh-lg">تعداد مشارکت کنندگان</p>
                                </div>
                                <div class="col-lg-4 col-6">
                                    <h4 class="fw-medium mb-0">
                                        {{ $election->type == App\Enums\ElectionType::PUBLIC_JOINT ? $event->participants()->count() : $election->normal_stock_count + $election->prefered_stock_count * $election->prefered_stock_weight }}
                                    </h4>
                                    <p class="mb-0 text-muted lh-lg">تعداد کل سهم ها</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <h4 class="fs-15">اطلاعات رای دهنده:</h4>
                            <div class="row mt-1 g-2">
                                @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
                                    <div class="col-lg-4 col-6">
                                        <h4 class="fw-medium mb-0">{{ $participant->normal_stock_count }}</h4>
                                        <p class="mb-0 text-muted lh-lg">میزان سهم عادی</p>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <h4 class="fw-medium mb-0">{{ $participant->prefered_stock_count }}</h4>
                                        <p class="mb-0 text-muted lh-lg">میزان سهم ممتاز</p>
                                    </div>
                                    <div class="col-lg-4 col-6">
                                        <h4 class="fw-medium mb-0">{{ $effectiveStock }}</h4>
                                        <p class="mb-0 text-muted lh-lg">کل سهم</p>
                                        @if ($election->ignore_stock_weight)
                                            <small class="text-info">بدون وزن</small>
                                        @endif
                                    </div>
                                @else
                                    <div class="col-lg-4 col-6">
                                        <h4 class="fw-medium mb-0">1</h4>
                                        <p class="mb-0 text-muted lh-lg">کل سهم</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom border-dashed">
                        <h4 class="card-title mb-0">انتخاب کاندیدای هیت مدیره</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($election->candidates->where('candidate_type', App\Enums\CandidateType::DIRECTOR) as $candidate)
                                <div class="col-xl-3 col-lg-6">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <div class="row justify-content-between mb-3">
                                                <div class="col-12">
                                                    <img src="{{ asset($candidate->user->profile_image) }}" alt=""
                                                        class="avatar-xl rounded">
                                                </div>
                                            </div>
                                            <h5>{{ $candidate->user->full_name }}</h5>
                                            <div class="mt-3 d-flex gap-2 justify-content-center">

                                                {{-- تعاونی: PUBLIC_JOINT --}}
                                                @if ($election->type == App\Enums\ElectionType::PUBLIC_JOINT)
                                                    <div class="form-check form-checkbox-secondary mb-2">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="director_candidates[{{ $candidate->id }}]" value="1"
                                                            id="candidate_{{ $candidate->id }}">
                                                        <label for="candidate_{{ $candidate->id }}"
                                                            class="form-check-label">انتخاب</label>
                                                    </div>

                                                    {{-- خصوصی: PRIVATE_JOINT --}}
                                                @elseif ($election->type == App\Enums\ElectionType::PRIVATE_JOINT)
                                                    <div class="form-check form-checkbox-secondary mb-2">
                                                        <input type="checkbox" class="form-check-input"
                                                            name="director_candidates[{{ $candidate->id }}]"
                                                            value="{{ $effectiveStock }}"
                                                            id="candidate_{{ $candidate->id }}">
                                                        <label for="candidate_{{ $candidate->id }}"
                                                            class="form-check-label">انتخاب</label>
                                                    </div>

                                                    {{-- ماده ۸۸: PRIVATE_JOINT_WITH_88 --}}
                                                @elseif ($election->type == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88)
                                                    <div class="mt-2">
                                                        <label for="director-candidate-input-{{ $candidate->id }}"
                                                            class="form-label">
                                                            مقدار سهم قابل تخصیص:
                                                            {{ $effectiveStock }}
                                                        </label>

                                                        <input type="number"
                                                            name="director_candidates[{{ $candidate->id }}]"
                                                            id="director-candidate-input-{{ $candidate->id }}"
                                                            class="form-control" min="0"
                                                            max="{{ $effectiveStock }}" value="0"
                                                            oninput="updateVoteValue('director-candidate-input-{{ $candidate->id }}', 'director-candidate-value-{{ $candidate->id }}')">

                                                        <div class="d-flex justify-content-center mt-1">
                                                            <h4 id="director-candidate-value-{{ $candidate->id }}">0</h4>
                                                            <span>&nbsp; سهم</span>
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="card">
                    <button type="button" class="btn btn-primary" id="showVotePreviewBtn">
                        ثبت نهایی
                    </button>
                </div>

                {{-- مودال پیش‌نمایش رای قبل از ثبت --}}
                <div class="modal fade" id="votePreviewModal" tabindex="-1" aria-labelledby="votePreviewModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="votePreviewModalLabel">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                    </svg>
                                    پیش‌نمایش رای شما
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <div class="card border border-success border-2 bg-light">
                                            <div class="card-body text-center">
                                                <h3 class="text-success fw-bold mb-2" id="preview-voted-count">0</h3>
                                                <p class="mb-0 text-muted">کاندیدایی که رای داده‌اید</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border border-warning border-2 bg-light">
                                            <div class="card-body text-center">
                                                <h3 class="text-warning fw-bold mb-2" id="preview-not-voted-count">0</h3>
                                                <p class="mb-0 text-muted">کاندیدایی که رای نداده‌اید</p>
                                            </div>
                                        </div>
                                    </div>
                                    @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
                                        <div class="col-md-6">
                                            <div class="card border border-primary border-2 bg-light">
                                                <div class="card-body text-center">
                                                    <h3 class="text-primary fw-bold mb-2" id="preview-total-votes">0</h3>
                                                    <p class="mb-0 text-muted">تعداد کل رای داده شده</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border border-info border-2 bg-light">
                                                <div class="card-body text-center">
                                                    <h3 class="text-info fw-bold mb-2" id="preview-remaining-votes">0</h3>
                                                    <p class="mb-0 text-muted">رای باقی‌مانده</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="alert alert-warning mb-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path
                                                d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z">
                                            </path>
                                            <line x1="12" y1="9" x2="12" y2="13"></line>
                                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                        </svg>
                                        <p class="mb-0">بعد از ثبت امکان ویرایش وجود ندارد. لطفاً اطلاعات را بررسی کنید.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                    انصراف
                                </button>
                                <button type="button" id="confirmSubmitBtn" class="btn btn-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    بله، ثبت شود
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- مودال نمایش نتیجه رای‌گیری --}}
                @if (session('vote_summary'))
                    @php
                        $summary = session('vote_summary');
                    @endphp
                    <div class="modal fade" id="voteResultModal" tabindex="-1" aria-labelledby="voteResultModalLabel"
                        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="voteResultModalLabel">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        رای شما با موفقیت ثبت شد
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="card border border-success border-2 bg-light">
                                                <div class="card-body text-center">
                                                    <h3 class="text-success fw-bold mb-2">
                                                        {{ $summary['voted_candidates_count'] }}
                                                    </h3>
                                                    <p class="mb-0 text-muted">کاندیدایی که رای داده‌اید</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border border-warning border-2 bg-light">
                                                <div class="card-body text-center">
                                                    <h3 class="text-warning fw-bold mb-2">
                                                        {{ $summary['not_voted_candidates_count'] }}
                                                    </h3>
                                                    <p class="mb-0 text-muted">کاندیدایی که رای نداده‌اید</p>
                                                </div>
                                            </div>
                                        </div>
                                        @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
                                            <div class="col-md-6">
                                                <div class="card border border-primary border-2 bg-light">
                                                    <div class="card-body text-center">
                                                        <h3 class="text-primary fw-bold mb-2">
                                                            {{ number_format($summary['total_votes_given']) }}
                                                        </h3>
                                                        <p class="mb-0 text-muted">تعداد کل رای داده شده</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card border border-info border-2 bg-light">
                                                    <div class="card-body text-center">
                                                        <h3 class="text-info fw-bold mb-2">
                                                            {{ number_format($summary['remaining_stock']) }}
                                                        </h3>
                                                        <p class="mb-0 text-muted">رای باقی‌مانده</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="alert alert-info mt-3 mb-0">
                                        <div class="d-flex align-items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="16" x2="12" y2="12">
                                                </line>
                                                <line x1="12" y1="8" x2="12.01" y2="8">
                                                </line>
                                            </svg>
                                            <p class="mb-0">رای شما با موفقیت ثبت شد و امکان ویرایش وجود ندارد.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-center">
                                    <a href="{{ route('app.index') }}" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                            <polyline points="15 18 9 12 15 6"></polyline>
                                        </svg>
                                        بازگشت به داشبورد
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const showPreviewBtn = document.getElementById('showVotePreviewBtn');
            const confirmBtn = document.getElementById('confirmSubmitBtn');
            const form = document.getElementById('votingForm');
            const previewModalEl = document.getElementById('votePreviewModal');

            if (!showPreviewBtn || !confirmBtn || !form || !previewModalEl) {
                console.error('Voting elements not found');
                return;
            }

            const previewModal = new bootstrap.Modal(previewModalEl);

            const totalCandidates =
                {{ $election->candidates()->where('candidate_type', App\Enums\CandidateType::DIRECTOR)->count() }};

            const totalAvailableStock = {{ $effectiveStock ?? 1 }};

            function calculateVotePreview() {

                const inputs = form.querySelectorAll('input[name^="director_candidates"]');

                let votedCount = 0;
                let totalVotesGiven = 0;

                inputs.forEach(input => {

                    if (input.type === 'checkbox' && input.checked) {
                        votedCount++;
                        totalVotesGiven += parseFloat(input.value) || 1;
                    }

                    if (input.type === 'number') {
                        const value = parseFloat(input.value) || 0;
                        if (value > 0) {
                            votedCount++;
                            totalVotesGiven += value;
                        }
                    }
                });

                const notVotedCount = totalCandidates - votedCount;
                const remainingVotes = totalAvailableStock - totalVotesGiven;

                document.getElementById('preview-voted-count').textContent = votedCount;
                document.getElementById('preview-not-voted-count').textContent = notVotedCount;

                @if (in_array($election->type, [App\Enums\ElectionType::PRIVATE_JOINT, App\Enums\ElectionType::PRIVATE_JOINT_WITH_88]))
                    document.getElementById('preview-total-votes').textContent =
                        totalVotesGiven.toLocaleString('fa-IR');

                    document.getElementById('preview-remaining-votes').textContent =
                        Math.max(0, remainingVotes).toLocaleString('fa-IR');
                @endif
            }

            showPreviewBtn.addEventListener('click', function(e) {
                e.preventDefault();
                calculateVotePreview();
                previewModal.show();
            });

            confirmBtn.addEventListener('click', function(e) {
                e.preventDefault();

                console.log('Submitting form to:', form.action);

                previewModal.hide();

                form.submit();
            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const maxDirectorCandidates = "{{ $election->main_member_count }}";
            // const maxInspectorCandidates = "{{ $election->incpector_main_member_count }}";

            const directorCheckboxes = document.querySelectorAll('input[name^="director_candidates"]');
            const inspectorCheckboxes = document.querySelectorAll('input[name^="inspector_candidates"]');

            function enforceLimit(checkboxes, max) {
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
                        if (checkedCount > max) {
                            Swal.fire({
                                icon: "error",
                                title: "خطا...",
                                text: `شما نمی‌توانید بیشتر از ${max} کاندیدای اصلی انتخاب کنید.`,
                                confirmButtonText: "باشه"
                            });
                            this.checked = false;
                        }
                    });
                });
            }

            enforceLimit(directorCheckboxes, maxDirectorCandidates);
            enforceLimit(inspectorCheckboxes, maxInspectorCandidates);

        });
    </script>

    <script>
        function updateRangeValue(rangeId, valueId) {
            var rangeElement = document.getElementById(rangeId);
            var valueElement = document.getElementById(valueId);

            if (rangeElement && valueElement) {
                valueElement.textContent = rangeElement.value;
            }
        }

        function updateVoteValue(inputId, valueId) {
            var inputElement = document.getElementById(inputId);
            var valueElement = document.getElementById(valueId);

            if (inputElement && valueElement) {
                valueElement.textContent = inputElement.value || 0;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // به‌روزرسانی مقادیر اولیه برای input های number
            @if ($election->type == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88)
                @foreach ($election->candidates->where('candidate_type', App\Enums\CandidateType::DIRECTOR) as $candidate)
                    var inputId = 'director-candidate-input-{{ $candidate->id }}';
                    var valueId = 'director-candidate-value-{{ $candidate->id }}';
                    updateVoteValue(inputId, valueId);
                @endforeach
            @endif
        });
    </script>

    @if (session('vote_summary'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // نمایش مودال نتیجه رای‌گیری
                var voteResultModal = new bootstrap.Modal(document.getElementById('voteResultModal'));
                voteResultModal.show();
            });
        </script>
    @endif
    <script>
        (function() {
            function formatDuration(ms) {
                if (ms <= 0) return '0:00:00';
                var totalSeconds = Math.floor(ms / 1000);
                var days = Math.floor(totalSeconds / 86400);
                totalSeconds %= 86400;
                var hours = Math.floor(totalSeconds / 3600);
                var minutes = Math.floor((totalSeconds % 3600) / 60);
                var seconds = totalSeconds % 60;
                if (days > 0) {
                    return days + ' روز ' + String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') +
                        ':' + String(seconds).padStart(2, '0');
                }
                return String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') + ':' + String(seconds)
                    .padStart(2, '0');
            }

            function tick() {
                var items = document.querySelectorAll('.election-countdown');
                var now = new Date().getTime();
                items.forEach(function(el) {
                    var endAttr = el.getAttribute('data-end');
                    if (!endAttr) return;
                    var endTime = new Date(endAttr.replace(' ', 'T')).getTime();
                    var remaining = endTime - now;
                    var valueEl = el.querySelector('.value');
                    if (valueEl) {
                        valueEl.textContent = formatDuration(remaining);
                    }
                    if (remaining <= 0) {
                        el.classList.remove('text-primary');
                        el.classList.add('text-danger');
                    }
                });
            }
            tick();
            setInterval(tick, 1000);
        })();
    </script>
@endsection
