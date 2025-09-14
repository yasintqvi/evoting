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

<div class="row">
    <div class="col-xl-10">
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($event->participants as $participant)
                                <tr>
                                    <td>{{ $participant->user->full_name }}</td>
                                    <td>
                                        <div>
                                            <input type="hidden" name="" value="0">
                                            <input type="checkbox"
                                                name="11"
                                                id="11"
                                                value="1"
                                                data-switch="1">
                                            <label for="11"
                                                data-on-label="حاضر"
                                                data-off-label="غایب"
                                                class="mb-0 d-block"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="hidden" name="attendance[{{ $participant->id }}][attorney_id]"
                                            id="attorney-id-{{ $participant->id }}"
                                            value="{{ old("attendance.{$participant->id}.attorney_id", $participant->attorney_id ?? '') }}">
                                        <button type="button"
                                            class="btn btn-secondary btn-sm attorney-btn"
                                            data-participant-id="{{ $participant->id }}">
                                            انتخاب
                                        </button>
                                        <span id="attorney-{{ $participant->id }}-name" class="ms-2">
                                            @if($participant->attorney_id)
                                            {{ $participant->attorney->user->first_name   }}
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="text-start mt-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">ایجاد</button>
                        </div>
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
                        <label for="attorney-name" class="form-label">نام وکیل</label>
                        <input type="text" class="form-control" id="attorney-name" required>
                    </div>
                    <div class="mb-3">
                        <label for="attorney-phone" class="form-label">شماره تلفن</label>
                        <input type="number" class="form-control" id="attorney-phone" required>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = new bootstrap.Modal(document.getElementById('attorneyModal'));
        const attorneyForm = document.getElementById('attorneyForm');
        const currentParticipantId = document.getElementById('current-participant-id');
        const attorneyNameInput = document.getElementById('attorney-name');
        const attorneyPhoneInput = document.getElementById('attorney-phone');

        document.querySelectorAll('.attorney-btn').forEach(button => {
            button.addEventListener('click', function() {
                const participantId = this.getAttribute('data-participant-id');
                currentParticipantId.value = participantId;

                const attorneyIdInput = document.getElementById(`attorney-id-${participantId}`);
                const attorneyNameSpan = document.getElementById(`attorney-${participantId}-name`);

                if (attorneyIdInput.value && attorneyNameSpan.textContent) {
                    attorneyNameInput.value = attorneyNameSpan.textContent;
                } else {
                    attorneyNameInput.value = '';
                }

                attorneyPhoneInput.value = '';
                modal.show();
            });
        });

        document.getElementById('save-attorney').addEventListener('click', function() {
            const participantId = currentParticipantId.value;
            const attorneyName = attorneyNameInput.value.trim();
            const attorneyPhone = attorneyPhoneInput.value.trim();

            if (!attorneyName) {
                alert('لطفا نام وکیل را وارد کنید.');
                return;
            }

            const attorneyNameSpan = document.getElementById(`attorney-${participantId}-name`);
            const attorneyBtn = document.querySelector(`.attorney-btn[data-participant-id="${participantId}"]`);

            const attorneyIdInput = document.getElementById(`attorney-id-${participantId}`);

            attorneyIdInput.value = attorneyPhone;

            attorneyNameSpan.textContent = attorneyName;
            attorneyBtn.textContent = 'تغییر';
            attorneyBtn.classList.add('btn-warning');

            modal.hide();
        });

        document.getElementById('attorneyModal').addEventListener('hidden.bs.modal', function() {
            attorneyForm.reset();
        });
    });
</script>
@endsection