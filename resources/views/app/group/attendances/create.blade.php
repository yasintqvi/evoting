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
                    <form action="{{ route('attendances.store', [$group->slug, $event->id]) }}" method="post">
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
                                @foreach($event->participants as $participant)
                                    <tr id="participant-{{$participant->id}}" class="participant-{{$participant->id}}">
                                        <td>{{ $participant->user->full_name }}</td>
                                        <td>
                                            @if($participant->attorney_id)
                                                <div id="present-{{$participant->id}}">
                                                اهدای وکالت
                                                </div>
                                            @else
                                                <div id="present-{{$participant->id}}">
                                                    <input type="hidden" name="" value="0">
                                                    <input type="checkbox"
                                                           name="participant-present"
                                                           id="participant-present-{{$participant->id}}"
                                                           value="1"
                                                           data-switch="1" {{$participant->is_present?'checked':''}}>
                                                    <label for="participant-present-{{$participant->id}}"
                                                           data-on-label="حاضر"
                                                           data-off-label="غایب"
                                                           data-id="{{$participant->id}}"
                                                           class="mb-0 d-block present"></label>
                                                </div>
                                            @endif
                                        </td>
                                        <td id="attorney-col-{{$participant->id}}">
                                            @if(!in_array($participant->id,$attorneyIds))
                                                <input type="hidden"
                                                       name="attendance[{{ $participant->id }}][attorney_id]"
                                                       id="attorney-id-{{ $participant->id }}"
                                                       value="{{ old("attendance.{$participant->id}.attorney_id", $participant->attorney_id ?? '') }}">
                                                @if($participant->attorney_id)
                                                    <button type="button"
                                                            class="btn btn-warning btn-sm attorney-btn"
                                                            data-participant-id="{{ $participant->id }}">
                                                        ویرایش
                                                    </button>
                                                    <button type="button"
                                                            class="btn btn-danger btn-sm attorney-delete-btn"
                                                            data-participant-id="{{ $participant->id }}">
                                                        حذف
                                                    </button>
                                                @else

                                                    <button type="button"
                                                            class="btn btn-secondary btn-sm attorney-btn"
                                                            data-participant-id="{{ $participant->id }}">
                                                        انتخاب
                                                    </button>
                                                @endif


                                        </td>
                                        <td>

     <span id="attorney-{{ $participant->id }}-name" class="ms-2">
                                            @if($participant->attorney_id)
             {{ $participant->attorney->user->first_name   }}
         @endif
                                        </span>
                                            <span id="attorney-{{ $participant->id }}-last-name" class="">
                                            @if($participant->attorney_id)
                                                    {{ $participant->attorney->user->last_name   }}
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
                        </div>                       <div class="mb-3">
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
                    data: function (params) {
                        return {
                            q: params.term,   // search term
                            page: params.page || 1
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data.results, function (item) {
                                return {
                                    id: item.phone,
                                    text: item.first_name +" "+item.last_name +'-'+ item.phone
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
        document.addEventListener('DOMContentLoaded', function () {
            const modal = new bootstrap.Modal(document.getElementById('attorneyModal'));
            const attorneyForm = document.getElementById('attorneyForm');
            const currentParticipantId = document.getElementById('current-participant-id');
            const attorneyNameInput = document.getElementById('attorney-name');
            const attorneyLastNameInput = document.getElementById('attorney-l-name');
            const attorneyPhoneInput = document.getElementById('attorney-phone');

            document.addEventListener('click', function (event) {
                if (event.target.classList.contains('attorney-btn')) {
                    const participantId = event.target.getAttribute('data-participant-id');
                    currentParticipantId.value = participantId;

                    const attorneyIdInput = document.getElementById(`attorney-id-${participantId}`);
                    const attorneyNameSpan = document.getElementById(`attorney-${participantId}-name`);

                    if (attorneyIdInput?.value && attorneyNameSpan?.textContent) {
                        attorneyNameInput.value = attorneyNameSpan.textContent.trim();
                        attorneyLastNameInput.value = document.getElementById(`attorney-${participantId}-last-name`).textContent.trim() ?? '';
                    } else {
                        attorneyNameInput.value = '';
                    }

                    attorneyPhoneInput.value = '';
                    modal.show();
                }
            });

            document.getElementById('save-attorney').addEventListener('click', function () {
                const participantId = currentParticipantId.value;
                const attorneyName = attorneyNameInput.value.trim();
                const attorneyLastName = attorneyLastNameInput.value.trim();
                const attorneyPhone = attorneyPhoneInput.value.trim();

                if (!attorneyName && !attorneyLastName) {
                    alert('لطفا نام وکیل را وارد کنید.');
                    return;
                }

                axios.post('{{route('attorneys.store')}}', {
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

                            console.log(data);
                            if (data.data[1]?.id) {
                                document.getElementById('participant-' + data.data[1].id).style.display = 'none';
                            }

                            $('#present-' + participantId).html('اهدای وکالت');
                            let tbody = document.querySelector('#participants-table');
                            let participant = data.data[0]; // from your response
                            participant = participant.attorney;

                            let tr = document.createElement('tr');
                            tr.id = 'participant-' + participant.id;

                            tr.innerHTML = `
    <td>${participant.user.first_name + " " + participant.user.last_name}</td>
    <td>
        <div id="present-${participant.id}">
            <input type="hidden" name="" value="0">
            <input type="checkbox"
                   name="${participant.id}"
                   id="participant-present-${participant.id}"
                   value="1"
                   data-switch="1">
            <label for="participant-present-${participant.id}"
                   data-on-label="حاضر"
                    data-id="${participant.id}"
                   data-off-label="غایب"
                   class="mb-0 d-block present"></label>
        </div>
    </td>
    <td id="attorney-col-${participant.id}">

دارای وکالت
    </td>
`;

                            tbody.appendChild(tr);


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
                        if (error.response && error.response.status == 'error') {
                            const errorMessage = error.response.data.errors;
                            Swal.fire('خطا', errorMessage, 'error');
                        } else {
                            Swal.fire('خطا', 'عملیات با خطا مواجه شد.', 'error');
                        }
                    });

                modal.hide();
            });

            document.getElementById('attorneyModal').addEventListener('hidden.bs.modal', function () {
                attorneyForm.reset();
            });

            $('#attorney-phone').on('change', function () {
                const value = $(this).val(); // selected value
                if (value) {
                    axios.post('{{route('attorneys.index')}}', {phone: value})
                        .then(response => {
                            const data = response.data
                            document.getElementById('attorney-name').value = data.first_name ?? '';
                            document.getElementById('attorney-l-name').value = data.last_name ?? '';
                        })
                        .catch(error => {
                        });
                }
            });
            $('#attorneyModal').on('shown.bs.modal', function () {
                const value = $('#attorney-phone').val(); // selected value
                console.log(value);
                if (value) {
                    axios.post('{{route('attorneys.index')}}', {phone: value})
                        .then(response => {
                            const data = response.data
                            document.getElementById('attorney-name').value = data.first_name ?? '';
                            document.getElementById('attorney-l-name').value = data.last_name ?? '';
                        })
                        .catch(error => {
                        });
                }
            });
            document.addEventListener('click', function (e) {
                if (e.target.matches('.present')) {
                    const id = e.target.dataset.id;
                    axios.post('/present/' + id)
                        .then(response => {
                            toastr.success('', 'عملیات با موفقیت انجام شد.', 'success');
                        })
                        .catch(error => {
                            toastr.error('', 'عملیات با خطا مواجه شد.', 'error');
                        });
                }
            });


            document.addEventListener('click', function (event) {
                if (event.target.classList.contains('attorney-delete-btn')) {
                    const id = event.target.dataset.participantId;
                    axios.post('/delete-attorney/' + id, {}).then(response => {
                        const data = response.data;
                        if (data.status == 'success') {
                            Swal.fire('موفق', 'وکیل با موفقیت حذف شد.', 'success');
                            document.getElementById('participant-' + data.data).style.display = 'none';
                            $('#present-' + id).html(`
                             <input type="hidden" name="" value="0">
                                                    <input type="checkbox"
                                                           name="participant-present"
                                                           id="participant-present-${id}"
                                                           value="1"
                                                           data-switch="1" >
                                                    <label for="participant-present-${id}"
                                                           data-on-label="حاضر"
                                                           data-off-label="غایب"
                                                           data-id="${id}"
                                                           class="mb-0 d-block present"></label>`)
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
                }
            });

        });

    </script>
@endsection
