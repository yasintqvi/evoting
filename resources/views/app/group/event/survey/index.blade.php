@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">نظرسنجی</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('groups.index', $group) }}">داشبورد</a></li>

                <li class="breadcrumb-item"><a
                        href="{{ route('surveys.index', ['group' => $group, 'event' => $event]) }}">نظرسنجی</a>
                </li>

                <li class="breadcrumb-item active">همه</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between border-bottom border-light">
                    <form class="col-lg-9 gap-2 d-flex" method="get" action="">
                        <h4 class="header-title">لیست نظرسنجی</h4>

                        <div class="position-relative">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control ps-4"
                                   placeholder="جستجو...">
                        </div>
                        <div class="position-relative">
                            <select class="form-control" name="status">
                                <option value="" disabled selected>وضعیت</option>
                                <option value="">همه</option>
                                <option value="1" @selected(request('status') == 1)>فعال</option>
                                <option value="2" @selected(request('status') == 2)>غیر فعال</option>
                            </select>
                        </div>
                        <div class="position-relative">
                            <select class="form-control" name="is_anonymous">
                                <option value="" disabled selected>ناشناس</option>
                                <option value="">همه</option>
                                <option value="1" @selected(request('is_anonymous') == 1)>بله</option>
                                <option value="2" @selected(request('is_anonymous') == 2)>خیر</option>
                            </select>
                        </div>


                        <button class=" btn btn-primary bg-gradient">جست و جو
                        </button>
                        <a href="{{ route('surveys.index', [$group, $event]) }}" class="btn btn-danger bg-gradient">حذف
                            فیلتر </a>

                    </form>

                    @canany([\App\Enums\Permission::GROUP_EVENT_CREATE_SURVEY_GROUPID->value.$group->id,\App\Enums\Permission::GROUP_OWNER_GROUPID->value.$group->id,\App\Enums\Permission::VIEW_GROUP_EVENT->value])
                        <a href="{{ route('surveys.create', [$group, $event]) }}" class="btn btn-success bg-gradient"><i
                                class="ti ti-plus me-1"></i>ایجاد نظرسنجی</a>
                    @endcanany
                </div>
                <div class="table-responsive">
                    <table class="table table-nowrap mb-0">
                        <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-3" style="width: 50px;">
                            </th>
                            <th>عنوان</th>
                            <th>ناشناس</th>
                            <th>وضعیت</th>
                            <th class="text-center" style="width: 120px;">اقدامات</th>
                        </tr>
                        </thead><!-- end thead -->

                        <tbody>
                        @forelse ($surveys as $survey)
                            @canany([\App\Enums\Permission::SHOW_SURVEY_SURVEYID->value . $survey->id,\App\Enums\Permission::GROUP_OWNER_GROUPID->value.$group->id,\App\Enums\Permission::VIEW_GROUP_EVENT->value,\App\Enums\Permission::EVENT_SURVEY_EVENTID->value.$event->id])
                                <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td class="fw-semibold">
                                    {{ $survey->title }}
                                </td>

                                <td>
                                    @if ($survey->is_anonymous)
                                        <span class="badge bg-info">بله</span>
                                    @else
                                        <span class="badge bg-secondary">خیر</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($survey->status == 1)
                                        <span class="badge bg-success">فعال</span>
                                    @else
                                        <span class="badge bg-danger">غیرفعال</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <div class="hstack gap-1 justify-content-center">

                                        @canany([\App\Enums\Permission::GROUP_EVENT_EDIT_SURVEY_GROUPID->value.$group->id,\App\Enums\Permission::GROUP_OWNER_GROUPID->value.$group->id,\App\Enums\Permission::VIEW_GROUP_EVENT->value,\App\Enums\Permission::UPDATE_SURVEY_SURVEYID->value.$survey->id,\App\Enums\Permission::EVENT_SURVEY_EVENTID->value.$event->id])
                                        <a href="{{ route('surveys.edit', ['group' => $group, 'event' => $event, 'survey' => $survey]) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        @endcanany

                                        <a href="{{ route('surveys.answer', [$group, $event, $survey]) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            نمایش نظرسنجی
                                        </a>
                                        <a href="{{ route('surveys.statistics', [$group, $event, $survey]) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            آمار
                                        </a>


                                        {{-- <form
                                        action="#"
                                        method="POST"
                                        onsubmit="return confirm('آیا مطمئن هستید که این نظرسنجی حذف شود؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form> --}}
                                    </div>
                                </td>
                            </tr>
                            @endcanany
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    هنوز هیچ نظرسنجی برگزار نشده است.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table><!-- end table -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/assets/samples/assets/irregular-data-series.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="/assets/js/pages/chart-apex-bar.js"></script>
@endsection
