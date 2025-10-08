@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">نظرسنجی</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">خانه</a></li>

                <li class="breadcrumb-item"><a href="javascript: void(0);">انتخابات</a></li>

                <li class="breadcrumb-item active">همه</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between border-bottom border-light">
                    <h4 class="header-title">لیست نظرسنجی</h4>
                    <div>
                        <a href="{{ route('surveys.create', [$group->slug, $event->id]) }}"
                            class="btn btn-success bg-gradient"><i class="ti ti-plus me-1"></i>ایجاد نظرسنجی</a>
                    </div>
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
                                            <a href="{{ route('surveys.edit', [$group->slug, $event->id, $survey->id]) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <a href="{{ route('surveys.answer', [$group->slug, $event->id, $survey->id]) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="ti ti-edit"></i> نمایش نظرسنجی
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
