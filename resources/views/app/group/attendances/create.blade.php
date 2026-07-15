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
                                            @if (in_array((int) $participant->user_id, $hiddenUserIds ?? [], true))
                                                @continue
                                            @endif
                                            <tr id="participant-{{ $participant->id }}"
                                                class="participant-{{ $participant->id }}">
                                                <td>{{ $participant->user->full_name }}</td>
                                                <td>
                                                    @if ($participant->attorney_id)
                                                        <div id="present-{{ $participant->id }}">
                                                            اهدای وکالت
                                                        </div>
                                                    @elseif(in_array($participant->id, $attorneyIds))
                                                        دارای وکالت
                                                    @else
                                                        <div id="present-{{ $participant->id }}">
                                                            <input type="hidden" name="" value="0">
                                                            <input type="checkbox" name="participant-present"
                                                                id="participant-present-{{ $participant->id }}" value="1"
                                                                data-switch="1" {{ $participant->is_present ? 'checked' : '' }}>
                                                            <label for="participant-present-{{ $participant->id }}"
                                                                data-on-label="حاضر" data-off-label="غایب"
                                                                data-id="{{ $participant->id }}" class="mb-0 d-block present"
                                                                style="{{ $participant->is_present ? 'pointer-events:none;opacity:0.6' : '' }}"></label>

                                                        </div>
                                                    @endif
                                                </td>
                                                <td id="attorney-col-{{ $participant->id }}">
                                                    @if (!in_array($participant->id, $attorneyIds))
                                                        <input type="hidden"
                                                            name="attendance[{{ $participant->id }}][attorney_id]"
                                                            id="attorney-id-{{ $participant->id }}"
                                                            value="{{ old("attendance.{$participant->id}.attorney_id", $participant->attorney_id ?? '') }}">
                                                        @if ($participant->attorney_id)
                                                            <button type="button" class="btn btn-warning btn-sm attorney-btn"
                                                                data-participant-id="{{ $participant->id }}">
                                                                ویرایش
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm attorney-delete-btn"
                                                                data-participant-id="{{ $participant->id }}">
                                                                حذف
                                                            </button>
                                                        @elseif(in_array($participant->id, $attorneyIds))
                                                            وکیل
                                                        @elseif($participant->is_present)
                                                            <span class="text-muted small">حضور ثبت شده</span>
                                                        @else
                                                            <button type="button" class="btn btn-secondary btn-sm attorney-btn"
                                                                data-participant-id="{{ $participant->id }}">
                                                                انتخاب
                                                            </button>
                                                        @endif


                                                </td>
                                                <td>

                                                    <span id="attorney-{{ $participant->id }}-name" class="ms-2">
                                                        @if ($participant->attorney_id)
                                                            {{ $participant->attorney->user->first_name }}
                                                        @endif
                                                    </span>
                                                    <span id="attorney-{{ $participant->id }}-last-name" class="">
                                                        @if ($participant->attorney_id)
                                                            {{ $participant->attorney->user->last_name }}
                                                        @endif
                                                    </span>
                                                @else
                                                    دارای وکالت
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

    <!-- Modal -->
    <div class="modal fade" id="attorneyModal" tabindex="-1" aria-labelledby="attorneyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attorneyModalLabel">انتخاب وکیل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="attorneyForm">
                        <div class="mb-3">
                            <label for="attorney-phone" class="form-label">شماره تلفن</label>
                            <select id="attorney-phone" class="form-control" style="direction: rtl; width: 100%;" required>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="attorney-name" class="form-label">نام وکیل</label>
                            <input type="text" class="form-control" id="attorney-name" required>
                        </div>

                        <div class="mb-3">
                            <label for="attorney-l-name" class="form-label">نام خانوادگی وکیل</label>
                            <input type="text" class="form-control" id="attorney-l-name" required>
                        </div>
                        <input type="hidden" id="current-participant-id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="button" class="btn btn-primary" id="save-attorney">ذخیره</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Printable Attorney Info Modal -->
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
        $(document).ready(function() {
            $('#attorney-phone').select2({
                placeholder: "شماره تلفن را انتخاب کنید",
                allowClear: true,
                dir: "rtl",
                tags: true,
                dropdownParent: $('#attorneyModal'),
                ajax: {
                    url: '/user/select2',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            page: params.page || 1
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data.results, function(item) {
                                return {
                                    id: item.phone,
                                    text: item.first_name + " " + item.last_name + '-' + item
                                        .phone
                                }
                            }),
                            pagination: {
                                more: data.pagination.more
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0 // only start searching after typing 1 char
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('attorneyModal'));
            const attorneyForm = document.getElementById('attorneyForm');
            const currentParticipantId = document.getElementById('current-participant-id');
            const attorneyNameInput = document.getElementById('attorney-name');
            const attorneyLastNameInput = document.getElementById('attorney-l-name');
            const attorneyPhoneInput = document.getElementById('attorney-phone');

            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('attorney-btn')) {
                    const participantId = event.target.getAttribute('data-participant-id');
                    currentParticipantId.value = participantId;

                    const attorneyIdInput = document.getElementById(`attorney-id-${participantId}`);
                    const attorneyNameSpan = document.getElementById(`attorney-${participantId}-name`);

                    if (attorneyIdInput?.value && attorneyNameSpan?.textContent) {
                        attorneyNameInput.value = attorneyNameSpan.textContent.trim();
                        attorneyLastNameInput.value = document.getElementById(
                            `attorney-${participantId}-last-name`).textContent.trim() ?? '';
                    } else {
                        attorneyNameInput.value = '';
                    }

                    attorneyPhoneInput.value = '';
                    modal.show();
                }
            });

            document.getElementById('save-attorney').addEventListener('click', function() {
                const participantId = currentParticipantId.value;
                const attorneyName = attorneyNameInput.value.trim();
                const attorneyLastName = attorneyLastNameInput.value.trim();
                const attorneyPhone = attorneyPhoneInput.value.trim();

                if (!attorneyName && !attorneyLastName) {
                    alert('لطفا نام وکیل را وارد کنید.');
                    return;
                }

                axios.post('{{ route('attorneys.store') }}', {
                        first_name: attorneyName,
                        last_name: attorneyLastName,
                        phone: attorneyPhone,
                        participant_id: participantId
                    })
                    .then(response => {
                        if (response.data && response.data.status == 'error') {
                            const errorMessage = response.data.message;
                            Swal.fire('خطا', errorMessage, 'error');
                        } else {
                            Swal.fire('موفق', 'وکیل با موفقیت انتخاب شد.', 'success');
                            const data = response.data;

                            // نمایش اطلاعات ورود اگر کاربر جدید ایجاد شده
                            if (data.is_new_user && data.login_info) {
                                const info = data.login_info;
                                document.getElementById('info-attorney-name').textContent = info
                                    .full_name;
                                document.getElementById('info-principal-name').textContent = info
                                    .attorney_name;
                                document.getElementById('info-phone').textContent = info.phone;
                                document.getElementById('info-password').textContent = info.password;
                                const infoModal = new bootstrap.Modal(document.getElementById(
                                    'attorneyInfoModal'));
                                infoModal.show();
                            }

                            console.log(data);
                            if (data.data[1]?.id) {
                                const oldAttorneyRow = document.getElementById('participant-' + data
                                    .data[1].id);
                                if (oldAttorneyRow) {
                                    oldAttorneyRow.style.display = 'none';
                                }
                            }

                            $('#present-' + participantId).html('اهدای وکالت');
                            let tbody = document.querySelector('#participants-table');
                            let participant = data.data[0]; // from your response
                            participant = participant.attorney;

                            const existingRow = document.getElementById('participant-' + participant
                                .id);
                            if (existingRow) {
                                // وکیل، سهام‌دار موجود در جدول است؛ همان ردیف به «دارای وکالت» به‌روز می‌شود.
                                $('#present-' + participant.id).html('دارای وکالت');
                                const attorneyOwnCol = document.getElementById('attorney-col-' +
                                    participant.id);
                                if (attorneyOwnCol) {
                                    attorneyOwnCol.innerHTML = 'دارای وکالت';
                                }
                                existingRow.style.display = '';
                            } else {
                                let tr = document.createElement('tr');
                                tr.id = 'participant-' + participant.id;

                                tr.innerHTML = `
    <td>${participant.user.first_name + " " + participant.user.last_name}</td>
    <td>
        <div id="present-${participant.id}">
         دارای وکالت
        </div>
    </td>
    <td id="attorney-col-${participant.id}">

دارای وکالت
    </td>
`;
                                tbody.appendChild(tr);
                            }


                            if (attorneyPhone) {
                                attorneyPhoneInput.value = attorneyPhone;
                            }

                            $(`#attorney-col-${participantId}`).html(`
  <button type="button"
                                                            class="btn btn-warning btn-sm attorney-btn"
                                                            data-participant-id="${participantId}">
                                                        ویرایش
                                                    </button>
       <button type="button"
                                                            class="btn btn-danger btn-sm attorney-delete-btn"
                                                            data-participant-id="${participantId}">
                                                        حذف
                                                    </button>
                            `);
                            $('#attorney-' + participantId + '-name').text(attorneyName);
                            $('#attorney-' + participantId + '-last-name').text(attorneyLastName);

                        }
                    })
                    .catch(error => {

                        console.log(error);
                        if (error.response && error.response.data && error.response.data.message) {
                            Swal.fire('خطا', error.response.data.message, 'error');
                        } else {
                            Swal.fire('خطا', 'عملیات با خطا مواجه شد.', 'error');
                        }
                    });

                modal.hide();
            });

            document.getElementById('attorneyModal').addEventListener('hidden.bs.modal', function() {
                attorneyForm.reset();
            });

            $('#attorney-phone').on('change', function() {
                const value = $(this).val(); // selected value
                if (value) {
                    axios.post('{{ route('attorneys.index') }}', {
                            phone: value
                        })
                        .then(response => {
                            const data = response.data
                            document.getElementById('attorney-name').value = data.first_name ?? '';
                            document.getElementById('attorney-l-name').value = data.last_name ?? '';
                        })
                        .catch(error => {});
                }
            });
            $('#attorneyModal').on('shown.bs.modal', function() {
                const value = $('#attorney-phone').val(); // selected value
                console.log(value);
                if (value) {
                    axios.post('{{ route('attorneys.index') }}', {
                            phone: value
                        })
                        .then(response => {
                            const data = response.data
                            document.getElementById('attorney-name').value = data.first_name ?? '';
                            document.getElementById('attorney-l-name').value = data.last_name ?? '';
                        })
                        .catch(error => {});
                }
            });
            document.addEventListener('click', function(e) {
                toastr.options.positionClass = "toast-bottom-left";

                if (e.target.matches('.present')) {

                    const id = e.target.dataset.id;
                    const checkbox = document.getElementById('participant-present-' + id);

                    // اگر قبلا قفل شده بود هیچ کاری نکن
                    if (checkbox.disabled) {
                        return;
                    }

                    Swal.fire({
                        title: 'تایید حضور',
                        text: 'آیا از ثبت حضور این کاربر مطمئن هستید؟ بعد از تایید امکان غیرفعال کردن وجود ندارد.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'بله، تایید می‌کنم',
                        cancelButtonText: 'انصراف'
                    }).then((result) => {

                        if (result.isConfirmed) {

                            axios.post('/present/' + id)
                                .then(response => {

                                    toastr.success('', 'حضور با موفقیت ثبت شد.', 'success');

                                    // قفل کامل
                                    checkbox.checked = true;
                                    checkbox.disabled = true;

                                    // label هم دیگر کلیک نشود
                                    e.target.style.pointerEvents = "none";
                                    e.target.style.opacity = "0.6";

                                    // دیگر نمی‌تواند وکالت بدهد چون حضورش ثبت شده است
                                    const attorneyCol = document.getElementById('attorney-col-' + id);
                                    if (attorneyCol) {
                                        attorneyCol.innerHTML =
                                            '<span class="text-muted small">حضور ثبت شده</span>';
                                    }

                                })
                                .catch(error => {

                                    toastr.error('', 'عملیات با خطا مواجه شد.', 'error');
                                    checkbox.checked = false;

                                });

                        } else {
                            checkbox.checked = false;
                        }

                    });
                }
            });



            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('attorney-delete-btn')) {
                    const id = event.target.dataset.participantId;

                    Swal.fire({
                        title: 'تایید حذف وکالت',
                        text: 'آیا از حذف این وکالت مطمئن هستید؟',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'بله، حذف شود',
                        cancelButtonText: 'انصراف'
                    }).then((result) => {
                        if (!result.isConfirmed) {
                            return;
                        }

                        axios.post('/delete-attorney/' + id, {}).then(response => {
                            const data = response.data;
                            if (data.status == 'success') {
                                Swal.fire('موفق', 'وکیل با موفقیت حذف شد.', 'success');
                                const payload = data.data;
                                const attorneyPid = typeof payload === 'object' && payload !== null ?
                                    payload.attorney_participant_id :
                                    payload;
                                // هرگز ردیف موکل (principal) را مخفی نکن؛ فقط در صورت تفاوت id، ردیف رکورد وکیل را مخفی کن.
                                if (attorneyPid != null && String(attorneyPid) !== String(id)) {
                                    const attorneyRow = document.getElementById('participant-' +
                                        attorneyPid);
                                    if (attorneyRow) {
                                        attorneyRow.style.display = 'none';
                                    }
                                }
                                $('#present-' + id).html(`
                             <input type="hidden" name="" value="0">
                                                    <input type="checkbox"
                                                           name="participant-present"
                                                           id="participant-present-${id}"
                                                           value="1"
                                                           data-switch="1" checked >
                                                    <label for="participant-present-${id}"
       data-on-label="حاضر"
       data-off-label="غایب"
       data-id="${id}"
       class="mb-0 d-block present"
       style=""></label>
`)
                                document.getElementById('attorney-col-' + id).innerHTML = `
                    <button type="button"
                            class="btn btn-secondary btn-sm attorney-btn"
                            data-participant-id="${id}">
                        انتخاب
                    </button>`;
                                document.getElementById(`attorney-${id}-name`).innerHTML = '';
                                document.getElementById(`attorney-${id}-last-name`).innerHTML = '';
                            } else {
                                Swal.fire('خطا', data.message, 'error');
                            }
                        }).catch(error => {
                            console.log(error);
                            Swal.fire('خطا', 'عملیات با خطا مواجه شد.', 'error');
                        });
                    });
                }
            });
        });

        function printAttorneyInfo() {
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
        }
    </script>
@endsection
