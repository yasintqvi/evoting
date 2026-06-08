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

                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $election->title }}</a></li>

                <li class="breadcrumb-item active">تعیین نامزد ها</li>
            </ol>
        </div>
    </div>

    @php
        $seatTotal = (int) ($election->main_member_count ?? 0) + (int) ($election->substitute_member_count ?? 0);
        $minSeatsJs = $seatTotal > 0 ? $seatTotal : 1;
    @endphp

    @can(\App\Enums\Permission::CREATE_CANDIDATES->value)
        <form id="candidates-create-form" action="{{ route('candidates.store', [$group, $event, $election]) }}" method="post">
            @csrf
            <div class="card col-lg-6">
                <div class="card-header border-bottom border-dashed">
                    <h4 class="card-title">اطلاعات مربوط به همه پرسی</h4>
                    <p class="text-muted mb-0">شما در حال ایجاد همه پرسی جدید هستید</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="main_candidates" class="form-label">انتخاب نامزد</label>
                                <small class="text-muted d-block">
                                    حداقل باید به‌اندازهٔ صندلی‌ها (اصلی + علی‌البدل) نامزد انتخاب کنید؛ بیشتر از آن مشکلی نیست.
                                    صندلی‌ها در این همه‌پرسی:
                                    {{ $seatTotal > 0 ? $seatTotal . ' نفر' : '—' }}
                                </small>
                                <select class="form-select my-1 my-md-0 me-sm-3" name="main_candidates_ids[]"
                                    id="main_candidates" data-toggle="select2" multiple>
                                    @foreach ($group->users as $user)
                                        <option value="{{ $user->id }}"
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
                        {{-- <div class="col-md-12">
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
                </div> --}}
                    </div>
                </div>
                <div class="card-footer">
                    <div class="text-end mb-3 d-flex">
                        <button type="submit" class="btn btn-primary">ایجاد </button>
                    </div>
                </div>
            </div>
        </form>
    @endcan
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
            const info = document.getElementById('candidate-count-info');
            const infoText = document.getElementById('candidate-count-info-text');
            const form = document.getElementById('candidates-create-form');
            const minSeats = {{ $minSeatsJs }};
            const hasSeatCap = @json($seatTotal > 0);

            function getSelectedCount() {
                if (!select) {
                    return 0;
                }
                if (window.jQuery && jQuery(select).length) {
                    var v = jQuery(select).val();
                    if (v === null || v === undefined) {
                        return 0;
                    }
                    if (Array.isArray(v)) {
                        return v.length;
                    }
                    return String(v).length ? 1 : 0;
                }
                return Array.from(select.selectedOptions || []).length;
            }

            function updateInfo() {
                const selectedCount = getSelectedCount();
                if (!select || !info || !infoText) {
                    return;
                }
                info.style.display = 'block';
                var need = minSeats;
                if (selectedCount < need) {
                    info.className = 'alert alert-warning';
                    infoText.textContent = 'تعداد نامزد انتخاب‌شده: ' + selectedCount + ' نفر — حداقل ' + need +
                        ' نفر لازم است' + (hasSeatCap ? ' (برابر صندلی‌های همه‌پرسی)' : '') + '.';
                } else {
                    info.className = 'alert alert-info';
                    infoText.textContent = 'تعداد نامزد انتخاب‌شده: ' + selectedCount + ' نفر' + (need > 1 ?
                        ' (حداقل ' + need + ' نفر؛ می‌توانید بیشتر هم انتخاب کنید)' : '') + '.';
                }
            }

            if (window.jQuery && select) {
                jQuery(select).on('change', updateInfo);
            } else if (select) {
                select.addEventListener('change', updateInfo);
            }

            updateInfo();

            if (form) {
                form.addEventListener('submit', function(e) {
                    var n = getSelectedCount();
                    if (n < minSeats) {
                        e.preventDefault();
                        var msg = 'حداقل باید ' + minSeats +
                            ' نامزد انتخاب کنید. الان ' + n + ' نفر انتخاب شده است.';
                        showToast('error', msg);
                    }
                });
            }
        });
    </script>
@endsection
