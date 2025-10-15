@extends('app.layouts.app')

@section('content')
<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0">ویرایش همه پرسی</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>

            <li class="breadcrumb-item"><a href="javascript: void(0);">انتخابات</a></li>

            <li class="breadcrumb-item active">ویرایش</li>
        </ol>
    </div>
</div>

<form action="{{ route('elections.update', [$group->slug, $event, $election->id]) }}" method="post">
    @csrf
    @method('PUT')
    <div class="card col-lg-6">
        <div class="card-header border-bottom border-dashed">
            <h4 class="card-title">اطلاعات مربوط به همه پرسی</h4>
            <p class="text-muted mb-0">شما در حال ویرایش همه پرسی هستید</p>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="mb-3">
                        <label for="title" class="form-label">عنوان همه پرسی</label>
                        <input type="text" name="title" value="{{ old('title', $election->title) }}" class="form-control" id="title">
                        @error('title')
                        <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="election_type" class="form-label">نوع همه پرسی</label>
                        <select name="type" onchange="checkElectionType(event)" id="election_type" data-toggle="select2" class="form-select">
                            <option value="">یک نوع همه پرسی را انتخاب نمایید</option>
                            @if ($group->type === App\Enums\GroupType::SPECIAL)
                            <option @selected(old('type', $election->type)==App\Enums\ElectionType::PRIVATE_JOINT_WITH_88) value="{{ App\Enums\ElectionType::PRIVATE_JOINT_WITH_88->value }}">سهامی خاص با ماده ۸۸</option>
                            <option @selected(old('type', $election->type)==App\Enums\ElectionType::PRIVATE_JOINT) value="{{ App\Enums\ElectionType::PRIVATE_JOINT->value }}">سهامی خاص بدون ماده ۸۸</option>
                            @else
                            <option @selected(old('type', $election->type)==App\Enums\ElectionType::PUBLIC_JOINT) value="{{ App\Enums\ElectionType::PUBLIC_JOINT->value }}">انتخابات تعاونی</option>
                            @endif
                        </select>
                        @error('type')
                        <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="position_id" class="form-label">مقام انتخاباتی</label>
                        <select name="position_id" id="position_id" class="form-select">
                            <option value="">یک مقام را انتخاب نمایید</option>
                            @foreach ($positions as $position)
                            <option value="{{ $position->id }}" @selected(old('position_id', $election->position_id)==$position->id)>{{ $position->title }}</option>
                            @endforeach
                        </select>
                        @error('position_id')
                        <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="main_member_count" class="form-label">تعداد عضو اصلی </label>
                        <input type="number" class="form-control" id="main_member_count" name="main_member_count" value="{{ old('main_member_count', $election->main_member_count) }}">
                        @error('main_member_count')
                        <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mb-3">
                        <label for="substitute_member_count" class="form-label">تعداد عضو علی البدل </label>
                        <input type="number" class="form-control" name="substitute_member_count" value="{{ old('substitute_member_count', $election->substitute_member_count) }}" id="substitute_member_count">
                        @error('substitute_member_count')
                        <span class="strong text-danger font-weight-bold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="text-end mb-3 d-flex gap-2">
                <a href="{{ route('elections.index', [$group->slug, $event]) }}" class="btn btn-secondary">انصراف</a>
                <button type="submit" class="btn btn-primary">بروزرسانی</button>
            </div>
        </div>
    </div>
</form>

@endsection

@section('scripts')

<script>
    function checkElectionType(event) {
        const privateJoint = "{{ App\Enums\ElectionType::PRIVATE_JOINT->value }}";
        const privateJointWith88 = "{{ App\Enums\ElectionType::PRIVATE_JOINT_WITH_88->value }}";

        const selectedValue = event.target.value;

        const preferredStockWeightField = document.getElementById('prefered_stock_weight');

        if (selectedValue === privateJoint || selectedValue === privateJointWith88) {
            preferredStockWeightField.classList.remove('d-none');
        } else {
            preferredStockWeightField.classList.add('d-none');
        }
    }

    $("#position_id").select2({
        tags: true,
        createTag: function(params) {
            return {
                id: params.term,
                text: params.term,
                newOption: true
            }
        },
        insertTag: function(data, tag) {
            if (tag.newOption) {
                data.push(tag);
            }
        }
    }).on('select2:select', function(e) {
        const selectedData = e.params.data;

        if (selectedData.newOption) {
            const newPositionTitle = selectedData.text;

            createNewPosition(newPositionTitle);
        }
    });

    function createNewPosition(positionTitle) {
        const selectElement = $('#position_id');
        selectElement.prop('disabled', true);

        const formData = new FormData();
        formData.append('title', positionTitle);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("positions.store") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    $('#position_id').find('option[value="' + positionTitle + '"]').remove();

                    const newOption = new Option(positionTitle, data.position_id, true, true);
                    $('#position_id').append(newOption).trigger('change');

                }
            })
            .catch(error => {
                console.error('Error:', error);

                $('#position_id').find('option[value="' + positionTitle + '"]').remove();
                $('#position_id').val('').trigger('change');

            })
            .finally(() => {
                selectElement.prop('disabled', false);
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        checkElectionType({
            target: document.getElementById('election_type')
        });
    });
</script>

@endsection
