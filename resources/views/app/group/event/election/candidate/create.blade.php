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
        $mainSeatCount = (int) ($election->main_member_count ?? 0);
        $minSeatsJs = $mainSeatCount > 0 ? $mainSeatCount : 1;
        $isInspectorElection = $election->position?->title === 'بازرس';
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
                                    حداقل باید به‌اندازهٔ تعداد اعضای اصلی نامزد انتخاب کنید؛ بیشتر از آن مشکلی نیست.
                                    تعداد اعضای اصلی در این همه‌پرسی:
                                    {{ $mainSeatCount > 0 ? $mainSeatCount . ' نفر' : '—' }}
                                    @if ($isInspectorElection)
                                        <span class="text-info d-block mt-1">جستجو فقط بین سهام‌داران این گروه است؛ برای نامزدی که سهام‌دار نیست، از دکمهٔ «افزودن بازرس جدید» استفاده کنید.</span>
                                    @endif
                                </small>
                                @if ($isInspectorElection)
                                    <select class="form-select my-1 my-md-0 me-sm-3" name="main_candidates_ids[]"
                                        id="main_candidates" multiple>
                                        @foreach (collect(old('main_candidates_ids', []))->unique() as $selectedId)
                                            @php $selectedUser = \App\Models\User::find($selectedId); @endphp
                                            @if ($selectedUser)
                                                <option value="{{ $selectedUser->id }}" selected>
                                                    {{ $selectedUser->full_name }} - {{ $selectedUser->phone }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                @else
                                    <select class="form-select my-1 my-md-0 me-sm-3" name="main_candidates_ids[]"
                                        id="main_candidates" data-toggle="select2" multiple>
                                        @foreach ($group->users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ collect(old('main_candidates_ids', $election->candidates()->where('candidate_type', App\Enums\CandidateType::DIRECTOR)->get()->pluck('user_id')->toArray()))->contains($user->id) ? 'selected' : '' }}>
                                                {{ $user->fullName }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('main_candidates_ids')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                @if ($isInspectorElection)
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2"
                                        data-bs-toggle="modal" data-bs-target="#quickAddInspectorModal">
                                        <i class="ti ti-plus me-1"></i> افزودن بازرس جدید
                                    </button>
                                @endif
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

        @if ($isInspectorElection)
            <div class="modal fade" id="quickAddInspectorModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">افزودن بازرس جدید</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="quickAddInspectorForm">
                                <div class="mb-3">
                                    <label for="inspector-first-name" class="form-label">نام</label>
                                    <input type="text" class="form-control" id="inspector-first-name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="inspector-last-name" class="form-label">نام خانوادگی</label>
                                    <input type="text" class="form-control" id="inspector-last-name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="inspector-phone" class="form-label">شماره تلفن</label>
                                    <input type="text" class="form-control" id="inspector-phone" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                            <button type="button" class="btn btn-primary" id="save-quick-inspector">افزودن</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan
@endsection

@section('scripts')
    @if ($isInspectorElection)
        <script src="{{ asset('assets/js/axios.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('#main_candidates').select2({
                    placeholder: "نام یا شماره تلفن را جستجو کنید (سهام‌داران این گروه)",
                    dir: "rtl",
                    ajax: {
                        url: '{{ route('candidates.search-shareholders', [$group, $event, $election]) }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term,
                                page: params.page || 1
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data.results, function(item) {
                                    return {
                                        id: item.id,
                                        text: item.first_name + ' ' + item.last_name + ' - ' + item.phone
                                    };
                                }),
                                pagination: {
                                    more: data.pagination.more
                                }
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 0
                });
            });

            document.getElementById('save-quick-inspector')?.addEventListener('click', function() {
                const firstName = document.getElementById('inspector-first-name').value.trim();
                const lastName = document.getElementById('inspector-last-name').value.trim();
                const phone = document.getElementById('inspector-phone').value.trim();

                if (!firstName || !lastName || !phone) {
                    showToast('error', 'لطفاً نام، نام‌خانوادگی و شماره تلفن را وارد کنید.');
                    return;
                }

                axios.post('{{ route('candidates.quick-create-user', [$group, $event, $election]) }}', {
                        first_name: firstName,
                        last_name: lastName,
                        phone: phone,
                    })
                    .then(function(response) {
                        const user = response.data.data;
                        const select = $('#main_candidates');

                        if (select.find("option[value='" + user.id + "']").length === 0) {
                            const option = new Option(user.full_name + ' - ' + user.phone, user.id, true, true);
                            select.append(option);
                        } else {
                            const values = select.val() || [];
                            if (!values.includes(String(user.id))) {
                                values.push(String(user.id));
                            }
                            select.val(values);
                        }
                        select.trigger('change');

                        document.getElementById('quickAddInspectorForm').reset();
                        bootstrap.Modal.getInstance(document.getElementById('quickAddInspectorModal')).hide();
                        showToast('success', 'بازرس جدید به لیست نامزدها اضافه شد.');
                    })
                    .catch(function(error) {
                        const message = error.response?.data?.message || 'خطا در افزودن بازرس جدید.';
                        showToast('error', message);
                    });
            });
        </script>
    @endif
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
            const hasSeatCap = @json($mainSeatCount > 0);

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
                        ' نفر لازم است' + (hasSeatCap ? ' (برابر تعداد اعضای اصلی)' : '') + '.';
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
