@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">لیست حضور غیاب</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>
                <li class="breadcrumb-item active">لیست حاضران</li>
            </ol>
        </div>
    </div>

    <div class="w-full">
        <div class="w-full">
            <div class="card">
                <div class="card-header border-bottom border-dashed d-flex align-items-center">
                    <h4 class="header-title">لیست ثبت حضور و غیاب کاربران و حق وکالت</h4>
                </div>

                <div class="card-body">
                    @can(\App\Enums\Permission::STORE_ATTENDANCE->value)
                        <form action="{{ route('attendances.store', [$group, $event]) }}" method="post">
                            @csrf
                            <div class="table-responsive-sm">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>نام و نام خانوادگی</th>
                                            <th>وضعیت حضور کاربر</th>
                                            <th>واگذاری وکالت</th>
                                            <th>نام وکیل</th>
                                        </tr>
                                    </thead>
                                    <tbody id="participants-table">
                                        @foreach ($event->participants as $participant)
                                            @php
                                                $isAttorney = in_array($participant->id, $attorneyIds);
                                                $hasAttorney = (bool) $participant->attorney_id;
                                            @endphp
                                            <tr id="participant-{{ $participant->id }}"
                                                class="participant-{{ $participant->id }}">
                                                <td>{{ $participant->user->full_name }}</td>
                                                <td>
                                                    @if ($hasAttorney)
                                                        <div id="present-{{ $participant->id }}">اهدای وکالت</div>
                                                    @elseif ($isAttorney)
                                                        <div id="present-{{ $participant->id }}">دارای وکالت</div>
                                                    @else
                                                        <div id="present-{{ $participant->id }}">
                                                            <input type="hidden" value="0">
                                                            <input type="checkbox" name="participant-present"
                                                                id="participant-present-{{ $participant->id }}" value="1"
                                                                data-switch="1"
                                                                {{ $participant->is_present ? 'checked' : '' }}>
                                                            <label for="participant-present-{{ $participant->id }}"
                                                                data-on-label="حاضر" data-off-label="غایب"
                                                                data-id="{{ $participant->id }}"
                                                                class="mb-0 d-block present"
                                                                style="{{ $participant->is_present ? 'pointer-events:none;opacity:0.6' : '' }}"></label>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td id="attorney-col-{{ $participant->id }}">
                                                    @if ($isAttorney)
                                                        دارای وکالت
                                                    @else
                                                        <input type="hidden"
                                                            name="attendance[{{ $participant->id }}][attorney_id]"
                                                            id="attorney-id-{{ $participant->id }}"
                                                            value="{{ old("attendance.{$participant->id}.attorney_id", $participant->attorney_id ?? '') }}">

                                                        @if ($hasAttorney)
                                                            <button type="button"
                                                                class="btn btn-warning btn-sm attorney-btn"
                                                                data-participant-id="{{ $participant->id }}"
                                                                data-attorney-phone="{{ $participant->attorney?->user?->phone }}"
                                                                data-attorney-first-name="{{ $participant->attorney?->user?->first_name }}"
                                                                data-attorney-last-name="{{ $participant->attorney?->user?->last_name }}">
                                                                ویرایش
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm attorney-delete-btn"
                                                                data-participant-id="{{ $participant->id }}">
                                                                حذف
                                                            </button>
                                                        @elseif ($participant->is_present)
                                                            <span class="text-muted small">حضور ثبت شده</span>
                                                        @else
                                                            <button type="button"
                                                                class="btn btn-secondary btn-sm attorney-btn"
                                                                data-participant-id="{{ $participant->id }}"
                                                                data-attorney-phone=""
                                                                data-attorney-first-name=""
                                                                data-attorney-last-name="">
                                                                انتخاب
                                                            </button>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($hasAttorney)
                                                        <span id="attorney-{{ $participant->id }}-name" class="ms-2">
                                                            {{ $participant->attorney->user->first_name }}
                                                        </span>
                                                        <span id="attorney-{{ $participant->id }}-last-name">
                                                            {{ $participant->attorney->user->last_name }}
                                                        </span>
                                                    @else
                                                        <span id="attorney-{{ $participant->id }}-name" class="ms-2"></span>
                                                        <span id="attorney-{{ $participant->id }}-last-name"></span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    {{-- Modal وکالت --}}
    <div class="modal fade" id="attorneyModal" tabindex="-1" aria-labelledby="attorneyModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attorneyModalLabel">انتخاب وکیل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="attorney-form-alert" class="alert alert-danger d-none" role="alert"></div>

                    <form id="attorneyForm" novalidate>
                        <div class="mb-3">
                            <label for="attorney-phone" class="form-label">شماره تماس <span class="text-danger">*</span></label>
                            <select id="attorney-phone" class="form-control" style="direction: rtl; width: 100%;"
                                aria-describedby="attorney-phone-error"></select>
                            <div id="attorney-phone-error" class="invalid-feedback d-block"></div>
                            <small class="text-muted">شماره باید ۱۱ رقم و با ۰۹ شروع شود. می‌توانید از لیست انتخاب کنید یا شماره جدید وارد کنید.</small>
                        </div>

                        <div class="mb-3">
                            <label for="attorney-name" class="form-label">نام وکیل <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="attorney-name" name="first_name"
                                value="{{ old('first_name') }}" autocomplete="given-name">
                            <div id="attorney-name-error" class="invalid-feedback d-block"></div>
                        </div>

                        <div class="mb-3">
                            <label for="attorney-l-name" class="form-label">نام خانوادگی وکیل <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="attorney-l-name" name="last_name"
                                value="{{ old('last_name') }}" autocomplete="family-name">
                            <div id="attorney-l-name-error" class="invalid-feedback d-block"></div>
                        </div>

                        <input type="hidden" id="current-participant-id" name="participant_id"
                            value="{{ old('participant_id') }}">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="button" class="btn btn-primary" id="save-attorney">
                        <span class="save-label">ذخیره</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Printable Attorney Info Modal --}}
    <div class="modal fade" id="attorneyInfoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">اطلاعات ورود وکیل</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="attorneyInfoBody">
                    <div class="border border-2 border-success rounded p-4 bg-light" id="attorneyPrintCard">
                        <div class="text-center mb-3">
                            <h5 class="fw-bold text-success mb-1">اطلاعات ورود وکیل</h5>
                            <small class="text-muted">این اطلاعات را در اختیار وکیل قرار دهید</small>
                        </div>
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="fw-semibold text-muted" style="width:45%">وکیل:</td>
                                    <td class="fw-bold" id="info-attorney-name"></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">موکل:</td>
                                    <td class="fw-bold" id="info-principal-name"></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">نام کاربری (شماره تماس):</td>
                                    <td class="fw-bold text-primary" id="info-phone"></td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-muted">رمز عبور:</td>
                                    <td class="fw-bold text-danger" id="info-password"></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="alert alert-warning small mt-3 mb-0">
                            <i class="ti ti-alert-triangle me-1"></i>
                            رمز عبور ۴ رقم آخر شماره تماس است. لطفاً این اطلاعات را در اختیار وکیل قرار دهید.
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" onclick="printAttorneyInfo()">
                        <i class="ti ti-printer me-1"></i> چاپ اطلاعات
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/js/axios.js') }}"></script>
    <script>
        (function() {
            const PHONE_REGEX = /^09\d{9}$/;
            const DIGIT_MAP = {
                '۰': '0', '۱': '1', '۲': '2', '۳': '3', '۴': '4',
                '۵': '5', '۶': '6', '۷': '7', '۸': '8', '۹': '9',
                '٠': '0', '١': '1', '٢': '2', '٣': '3', '٤': '4',
                '٥': '5', '٦': '6', '٧': '7', '٨': '8', '٩': '9'
            };

            function toEnglishDigits(value) {
                return String(value || '').replace(/[۰-۹٠-٩]/g, function(d) {
                    return DIGIT_MAP[d] || d;
                });
            }

            function normalizePhone(value) {
                let phone = toEnglishDigits(value).replace(/\D+/g, '');
                if (phone.startsWith('98') && phone.length === 12) {
                    phone = '0' + phone.slice(2);
                }
                if (phone.startsWith('9') && phone.length === 10) {
                    phone = '0' + phone;
                }
                return phone;
            }

            function isValidPhone(value) {
                return PHONE_REGEX.test(normalizePhone(value));
            }

            function clearAttorneyErrors() {
                ['attorney-phone', 'attorney-name', 'attorney-l-name'].forEach(function(id) {
                    const el = document.getElementById(id);
                    if (el) el.classList.remove('is-invalid');
                });
                document.getElementById('attorney-phone-error').textContent = '';
                document.getElementById('attorney-name-error').textContent = '';
                document.getElementById('attorney-l-name-error').textContent = '';
                const alertBox = document.getElementById('attorney-form-alert');
                alertBox.classList.add('d-none');
                alertBox.textContent = '';
            }

            function setFieldError(field, message) {
                const map = {
                    phone: ['attorney-phone', 'attorney-phone-error'],
                    first_name: ['attorney-name', 'attorney-name-error'],
                    last_name: ['attorney-l-name', 'attorney-l-name-error']
                };
                const pair = map[field];
                if (!pair) return;
                const input = document.getElementById(pair[0]);
                const errorEl = document.getElementById(pair[1]);
                if (input) input.classList.add('is-invalid');
                if (errorEl) errorEl.textContent = message;
            }

            function showFormAlert(message) {
                const alertBox = document.getElementById('attorney-form-alert');
                alertBox.textContent = message;
                alertBox.classList.remove('d-none');
            }

            function setSaveLoading(isLoading) {
                const btn = document.getElementById('save-attorney');
                const label = btn.querySelector('.save-label');
                const spinner = btn.querySelector('.spinner-border');
                btn.disabled = isLoading;
                label.textContent = isLoading ? 'در حال ذخیره...' : 'ذخیره';
                spinner.classList.toggle('d-none', !isLoading);
            }

            function setPhoneSelectValue(phone, label) {
                const $phone = $('#attorney-phone');
                $phone.empty();
                if (!phone) {
                    $phone.val(null).trigger('change');
                    return;
                }
                const text = label || phone;
                const option = new Option(text, phone, true, true);
                $phone.append(option).trigger('change');
            }

            function fillNameFromPhone(phone) {
                if (!isValidPhone(phone)) return;
                axios.post('{{ route('attorneys.index') }}', {
                        phone: normalizePhone(phone)
                    })
                    .then(function(response) {
                        const data = response.data || {};
                        if (data.first_name) {
                            document.getElementById('attorney-name').value = data.first_name;
                        }
                        if (data.last_name) {
                            document.getElementById('attorney-l-name').value = data.last_name;
                        }
                    })
                    .catch(function() {});
            }

            $(document).ready(function() {
                $('#attorney-phone').select2({
                    placeholder: 'شماره تماس را جستجو یا وارد کنید',
                    allowClear: true,
                    dir: 'rtl',
                    tags: true,
                    dropdownParent: $('#attorneyModal'),
                    width: '100%',
                    ajax: {
                        url: '{{ route('user.select2', $group) }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term,
                                page: params.page || 1,
                                event_id: '{{ $event->id }}',
                                current_participant_id: $('#current-participant-id').val() || ''
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data.results || [], function(item) {
                                    const phone = normalizePhone(item.phone);
                                    return {
                                        id: phone,
                                        text: (item.first_name || '') + ' ' + (item.last_name || '') + ' - ' + phone,
                                        first_name: item.first_name,
                                        last_name: item.last_name
                                    };
                                }),
                                pagination: {
                                    more: !!(data.pagination && data.pagination.more)
                                }
                            };
                        },
                        cache: true
                    },
                    createTag: function(params) {
                        const term = normalizePhone(params.term);
                        if (!term) return null;
                        if (!PHONE_REGEX.test(term)) {
                            return null;
                        }
                        return {
                            id: term,
                            text: term,
                            newTag: true
                        };
                    },
                    minimumInputLength: 0
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                const modalEl = document.getElementById('attorneyModal');
                const modal = new bootstrap.Modal(modalEl);
                const attorneyForm = document.getElementById('attorneyForm');
                const currentParticipantId = document.getElementById('current-participant-id');
                const attorneyNameInput = document.getElementById('attorney-name');
                const attorneyLastNameInput = document.getElementById('attorney-l-name');
                let keepFormValuesOnClose = false;

                document.addEventListener('click', function(event) {
                    const btn = event.target.closest('.attorney-btn');
                    if (!btn) return;

                    keepFormValuesOnClose = false;
                    clearAttorneyErrors();

                    const participantId = btn.getAttribute('data-participant-id');
                    currentParticipantId.value = participantId;

                    const phone = btn.getAttribute('data-attorney-phone') || '';
                    const firstName = btn.getAttribute('data-attorney-first-name') || '';
                    const lastName = btn.getAttribute('data-attorney-last-name') || '';

                    attorneyNameInput.value = firstName;
                    attorneyLastNameInput.value = lastName;

                    if (phone) {
                        const normalized = normalizePhone(phone);
                        setPhoneSelectValue(normalized, firstName || lastName ?
                            (firstName + ' ' + lastName + ' - ' + normalized) : normalized);
                    } else {
                        setPhoneSelectValue(null);
                    }

                    modal.show();
                });

                $('#attorney-phone').on('select2:select', function(e) {
                    clearAttorneyErrors();
                    const data = e.params.data || {};
                    const phone = normalizePhone(data.id || $(this).val());

                    if (data.first_name) {
                        attorneyNameInput.value = data.first_name;
                    }
                    if (data.last_name) {
                        attorneyLastNameInput.value = data.last_name;
                    }

                    if (!data.first_name && !data.last_name) {
                        fillNameFromPhone(phone);
                    }

                    if (!isValidPhone(phone)) {
                        setFieldError('phone', 'شماره تماس باید ۱۱ رقم و با ۰۹ شروع شود.');
                    }
                });

                $('#attorney-phone').on('change', function() {
                    const phone = normalizePhone($(this).val());
                    if (phone && !isValidPhone(phone)) {
                        setFieldError('phone', 'شماره تماس باید ۱۱ رقم و با ۰۹ شروع شود.');
                    }
                });

                document.getElementById('save-attorney').addEventListener('click', function() {
                    clearAttorneyErrors();

                    const participantId = currentParticipantId.value;
                    const attorneyName = attorneyNameInput.value.trim();
                    const attorneyLastName = attorneyLastNameInput.value.trim();
                    const attorneyPhone = normalizePhone($('#attorney-phone').val());

                    let hasError = false;

                    if (!attorneyPhone) {
                        setFieldError('phone', 'شماره تماس وکیل الزامی است.');
                        hasError = true;
                    } else if (!isValidPhone(attorneyPhone)) {
                        setFieldError('phone', 'شماره تماس باید ۱۱ رقم و با ۰۹ شروع شود (مثال: ۰۹۱۲۳۴۵۶۷۸۹).');
                        hasError = true;
                    }

                    if (!attorneyName) {
                        setFieldError('first_name', 'نام وکیل الزامی است.');
                        hasError = true;
                    }

                    if (!attorneyLastName) {
                        setFieldError('last_name', 'نام خانوادگی وکیل الزامی است.');
                        hasError = true;
                    }

                    if (!participantId) {
                        showFormAlert('شناسه موکل نامعتبر است. صفحه را تازه کنید.');
                        hasError = true;
                    }

                    if (hasError) {
                        keepFormValuesOnClose = true;
                        showFormAlert('لطفاً خطاهای فرم را برطرف کنید.');
                        return;
                    }

                    // نگه داشتن مقدار نرمال‌شده در select2 برای نمایش بعد از خطا
                    setPhoneSelectValue(
                        attorneyPhone,
                        (attorneyName + ' ' + attorneyLastName + ' - ' + attorneyPhone).trim()
                    );

                    Swal.fire({
                        title: 'تایید انتخاب وکیل',
                        text: 'آیا از انتخاب «' + attorneyName + ' ' + attorneyLastName + '» به عنوان وکیل مطمئن هستید؟',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'بله، ذخیره شود',
                        cancelButtonText: 'انصراف'
                    }).then(function(result) {
                        if (!result.isConfirmed) {
                            return;
                        }

                        setSaveLoading(true);
                        keepFormValuesOnClose = true;

                        axios.post('{{ route('attorneys.store') }}', {
                            first_name: attorneyName,
                            last_name: attorneyLastName,
                            phone: attorneyPhone,
                            participant_id: participantId
                        })
                        .then(function(response) {
                            const data = response.data || {};

                            if (data.status === 'error') {
                                keepFormValuesOnClose = true;
                                showFormAlert(data.message || 'عملیات با خطا مواجه شد.');
                                if (data.errors) {
                                    Object.keys(data.errors).forEach(function(field) {
                                        const messages = data.errors[field];
                                        if (messages && messages[0]) {
                                            setFieldError(field, messages[0]);
                                        }
                                    });
                                }
                                return;
                            }

                            keepFormValuesOnClose = false;
                            Swal.fire('موفق', 'وکیل با موفقیت انتخاب شد.', 'success');

                            if (data.is_new_user && data.login_info) {
                                const info = data.login_info;
                                document.getElementById('info-attorney-name').textContent = info.full_name;
                                document.getElementById('info-principal-name').textContent = info.attorney_name;
                                document.getElementById('info-phone').textContent = info.phone;
                                document.getElementById('info-password').textContent = info.password;
                                new bootstrap.Modal(document.getElementById('attorneyInfoModal')).show();
                            }

                            if (data.data && data.data[1] && data.data[1].id) {
                                const oldAttorneyRow = document.getElementById('participant-' + data.data[1].id);
                                if (oldAttorneyRow) {
                                    oldAttorneyRow.style.display = 'none';
                                }
                            }

                            $('#present-' + participantId).html('اهدای وکالت');

                            const attorneyParticipant = data.data && data.data[0] ? data.data[0].attorney : null;
                            if (attorneyParticipant) {
                                const existingRow = document.getElementById('participant-' + attorneyParticipant.id);
                                if (existingRow) {
                                    $('#present-' + attorneyParticipant.id).html('دارای وکالت');
                                    const attorneyOwnCol = document.getElementById('attorney-col-' + attorneyParticipant.id);
                                    if (attorneyOwnCol) {
                                        attorneyOwnCol.innerHTML = 'دارای وکالت';
                                    }
                                    existingRow.style.display = '';
                                } else {
                                    const tbody = document.querySelector('#participants-table');
                                    const tr = document.createElement('tr');
                                    tr.id = 'participant-' + attorneyParticipant.id;
                                    tr.innerHTML = `
                                        <td>${attorneyParticipant.user.first_name} ${attorneyParticipant.user.last_name}</td>
                                        <td><div id="present-${attorneyParticipant.id}">دارای وکالت</div></td>
                                        <td id="attorney-col-${attorneyParticipant.id}">دارای وکالت</td>
                                        <td>
                                            <span id="attorney-${attorneyParticipant.id}-name" class="ms-2"></span>
                                            <span id="attorney-${attorneyParticipant.id}-last-name"></span>
                                        </td>
                                    `;
                                    tbody.appendChild(tr);
                                }
                            }

                            const attorneyIdInput = document.getElementById('attorney-id-' + participantId);
                            if (attorneyIdInput && attorneyParticipant) {
                                attorneyIdInput.value = attorneyParticipant.id;
                            }

                            $(`#attorney-col-${participantId}`).html(`
                                <input type="hidden" name="attendance[${participantId}][attorney_id]"
                                    id="attorney-id-${participantId}" value="${attorneyParticipant ? attorneyParticipant.id : ''}">
                                <button type="button" class="btn btn-warning btn-sm attorney-btn"
                                    data-participant-id="${participantId}"
                                    data-attorney-phone="${attorneyPhone}"
                                    data-attorney-first-name="${attorneyName}"
                                    data-attorney-last-name="${attorneyLastName}">
                                    ویرایش
                                </button>
                                <button type="button" class="btn btn-danger btn-sm attorney-delete-btn"
                                    data-participant-id="${participantId}">
                                    حذف
                                </button>
                            `);

                            const nameEl = document.getElementById('attorney-' + participantId + '-name');
                            const lastNameEl = document.getElementById('attorney-' + participantId + '-last-name');
                            if (nameEl) nameEl.textContent = attorneyName;
                            if (lastNameEl) lastNameEl.textContent = attorneyLastName;

                            modal.hide();
                        })
                        .catch(function(error) {
                            keepFormValuesOnClose = true;
                            const payload = error.response && error.response.data ? error.response.data : {};
                            const message = payload.message || 'عملیات با خطا مواجه شد.';
                            showFormAlert(message);

                            if (payload.errors) {
                                Object.keys(payload.errors).forEach(function(field) {
                                    const messages = payload.errors[field];
                                    if (messages && messages[0]) {
                                        setFieldError(field, messages[0]);
                                    }
                                });
                            } else {
                                Swal.fire('خطا', message, 'error');
                            }
                        })
                        .finally(function() {
                            setSaveLoading(false);
                        });
                    });
                });

                modalEl.addEventListener('hidden.bs.modal', function() {
                    if (keepFormValuesOnClose) {
                        keepFormValuesOnClose = false;
                        return;
                    }
                    clearAttorneyErrors();
                    attorneyForm.reset();
                    setPhoneSelectValue(null);
                    currentParticipantId.value = '';
                });

                document.addEventListener('click', function(e) {
                    toastr.options.positionClass = 'toast-bottom-left';

                    if (!e.target.matches('.present')) return;

                    const id = e.target.dataset.id;
                    const checkbox = document.getElementById('participant-present-' + id);

                    if (checkbox.disabled) return;

                    Swal.fire({
                        title: 'تایید حضور',
                        text: 'آیا از ثبت حضور این کاربر مطمئن هستید؟ بعد از تایید امکان غیرفعال کردن وجود ندارد.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'بله، تایید می‌کنم',
                        cancelButtonText: 'انصراف'
                    }).then(function(result) {
                        if (!result.isConfirmed) {
                            checkbox.checked = false;
                            return;
                        }

                        axios.post('/present/' + id)
                            .then(function() {
                                toastr.success('', 'حضور با موفقیت ثبت شد.', 'success');
                                checkbox.checked = true;
                                checkbox.disabled = true;
                                e.target.style.pointerEvents = 'none';
                                e.target.style.opacity = '0.6';

                                const attorneyCol = document.getElementById('attorney-col-' + id);
                                if (attorneyCol) {
                                    attorneyCol.innerHTML =
                                        '<span class="text-muted small">حضور ثبت شده</span>';
                                }
                            })
                            .catch(function() {
                                toastr.error('', 'عملیات با خطا مواجه شد.', 'error');
                                checkbox.checked = false;
                            });
                    });
                });

                document.addEventListener('click', function(event) {
                    if (!event.target.classList.contains('attorney-delete-btn')) return;

                    const id = event.target.dataset.participantId;

                    Swal.fire({
                        title: 'تایید حذف وکالت',
                        text: 'آیا از حذف این وکالت مطمئن هستید؟',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'بله، حذف شود',
                        cancelButtonText: 'انصراف'
                    }).then(function(result) {
                        if (!result.isConfirmed) return;

                        axios.post('/delete-attorney/' + id, {}).then(function(response) {
                            const data = response.data;
                            if (data.status !== 'success') {
                                Swal.fire('خطا', data.message, 'error');
                                return;
                            }

                            Swal.fire('موفق', 'وکیل با موفقیت حذف شد.', 'success');
                            const payload = data.data;
                            const attorneyPid = typeof payload === 'object' && payload !== null ?
                                payload.attorney_participant_id :
                                payload;

                            if (attorneyPid != null && String(attorneyPid) !== String(id)) {
                                const attorneyRow = document.getElementById('participant-' + attorneyPid);
                                if (attorneyRow) {
                                    attorneyRow.style.display = 'none';
                                }
                            }

                            $('#present-' + id).html(`
                                <input type="hidden" value="0">
                                <input type="checkbox" name="participant-present"
                                    id="participant-present-${id}" value="1" data-switch="1" checked>
                                <label for="participant-present-${id}"
                                    data-on-label="حاضر" data-off-label="غایب"
                                    data-id="${id}" class="mb-0 d-block present"></label>
                            `);

                            document.getElementById('attorney-col-' + id).innerHTML = `
                                <input type="hidden" name="attendance[${id}][attorney_id]" id="attorney-id-${id}" value="">
                                <button type="button" class="btn btn-secondary btn-sm attorney-btn"
                                    data-participant-id="${id}"
                                    data-attorney-phone=""
                                    data-attorney-first-name=""
                                    data-attorney-last-name="">
                                    انتخاب
                                </button>
                            `;

                            const nameEl = document.getElementById('attorney-' + id + '-name');
                            const lastNameEl = document.getElementById('attorney-' + id + '-last-name');
                            if (nameEl) nameEl.textContent = '';
                            if (lastNameEl) lastNameEl.textContent = '';
                        }).catch(function(error) {
                            console.log(error);
                            Swal.fire('خطا', 'عملیات با خطا مواجه شد.', 'error');
                        });
                    });
                });
            });

            window.printAttorneyInfo = function() {
                const printContent = document.getElementById('attorneyPrintCard').innerHTML;
                const originalBody = document.body.innerHTML;
                document.body.innerHTML = `
                    <div style="direction:rtl;font-family:Tahoma,Arial,sans-serif;padding:40px;">
                        <div style="max-width:500px;margin:0 auto;border:2px solid #198754;border-radius:8px;padding:30px;background:#f8f9fa;">
                            ${printContent}
                        </div>
                    </div>
                `;
                window.print();
                document.body.innerHTML = originalBody;
                location.reload();
            };
        })();
    </script>
@endsection
