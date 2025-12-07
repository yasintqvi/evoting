@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">انتخابات {{ $election->title }}</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>
                <li class="breadcrumb-item"><a href="javascript: void(0);">انتخابات</a></li>
                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $election->title }}</a></li>
                <li class="breadcrumb-item active">رای گیری</li>
            </ol>
        </div>
    </div>
    <form action="{{ route('voting.store', [$group->slug, $event->slug, $election->slug]) }}" method="post">
        @csrf
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
                                    <h4 class="fw-medium mb-0">{{ $event->participants()->where('is_present', 1)->count() }}
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
                                        <h4 class="fw-medium mb-0">{{ $participant->total_stock }}</h4>
                                        <p class="mb-0 text-muted lh-lg">کل سهم</p>
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
                                                            value="{{ $participant->total_stock }}"
                                                            id="candidate_{{ $candidate->id }}">
                                                        <label for="candidate_{{ $candidate->id }}"
                                                            class="form-check-label">انتخاب</label>
                                                    </div>

                                                    {{-- ماده ۸۸: PRIVATE_JOINT_WITH_88 --}}
                                                @elseif ($election->type == App\Enums\ElectionType::PRIVATE_JOINT_WITH_88)
                                                    <div class="mt-2">
                                                        <label for="director-candidate-range-{{ $candidate->id }}"
                                                            class="form-label">
                                                            مقدار سهم قابل تخصیص:
                                                        </label>

                                                        <input type="range"
                                                            name="director_candidates[{{ $candidate->id }}]"
                                                            id="director-candidate-range-{{ $candidate->id }}"
                                                            class="form-range" min="0"
                                                            max="{{ $participant->total_stock }}" value="0"
                                                            oninput="updateRangeValue('director-candidate-range-{{ $candidate->id }}', 'director-candidate-value-{{ $candidate->id }}')">

                                                        <div class="d-flex justify-content-center">
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
                    <button type="submit" class="btn btn-primary">ثبت نهایی</button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const maxDirectorCandidates = parseInt("{{ $election->main_member_count }}");
            // const maxInspectorCandidates = parseInt("{{ $election->incpector_main_member_count }}");

            const directorRanges = document.querySelectorAll('input[name^="director_candidates"][type="range"]');
            const inspectorRanges = document.querySelectorAll('input[name^="inspector_candidates"][type="range"]');

            function enforceRangeLimit(ranges, max) {
                ranges.forEach(range => {
                    range.addEventListener('input', function() {
                        const activeRanges = Array.from(ranges).filter(r => parseInt(r.value) > 0)
                            .length;
                        if (parseInt(this.value) > 0 && activeRanges > max) {
                            Swal.fire({
                                icon: "error",
                                title: "خطا...",
                                text: `شما نمی‌توانید بیشتر از ${max} کاندیدای اصلی انتخاب کنید.`,
                                confirmButtonText: "باشه"
                            });
                            this.value = 0;
                            updateRangeValue(this.id, this.id.replace('range', 'value'));
                        }
                    });
                });
            }

            enforceRangeLimit(directorRanges, maxDirectorCandidates);
            enforceRangeLimit(inspectorRanges, maxInspectorCandidates);

            function updateRangeValue(rangeId, valueId) {
                const range = document.getElementById(rangeId);
                const value = document.getElementById(valueId);
                if (range && value) {
                    value.textContent = range.value;
                }
            }
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

            valueElement.textContent = rangeElement.value;
        }

        document.addEventListener('DOMContentLoaded', function() {
            var initialRangeId = 'director-candidate-range-{{ $candidate->id }}';
            var initialValueId = 'director-candidate-value-{{ $candidate->id }}';

            updateRangeValue(initialRangeId, initialValueId);
        });
    </script>
@endsection
