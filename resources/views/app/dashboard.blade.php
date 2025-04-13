@extends('app.layouts.app')

@section('content')
<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0"> خوش آمدید ، {{ user()->first_name }} </h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="{{route('app.index')}}">خانه</a></li>
            <li class="breadcrumb-item active">همه</li>
        </ol>
    </div>
</div>
@can(App\Enums\Permission::LIST_COMPANIES->value)
<div class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 align-items-center">
    @foreach ($companies as $company)

    <div class="col">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('companies.edit', $company->slug) }}" class="text-muted float-end mt-n1 fs-18"><i class="ti ti-edit"></i></a>
                <h5 class="text-muted fs-13 text-uppercase" title="Number of Orders">{{ $company->title }}</h5>
                <div class="d-flex align-items-center gap-2 my-3">
                    <div class="avatar-md flex-shrink-0">
                        <span class="avatar-title bg-primary-subtle text-primary rounded fs-22">
                            <img src="{{ $company->logo }}" width="20" height="20" alt="">
                        </span>
                    </div>
                    {{ $company->title }}
                </div>
                <p class="mb-1">
                    <span class="text-primary me-1"><i class="ti ti-point-filled"></i></span>
                    <span class="text-nowrap text-muted">تعداد اعضا</span>
                    <span class="float-end"><b>{{ $company->users->count() }}</b></span>
                </p>
                <p class="mb-0">
                    <span class="text-primary me-1"><i class="ti ti-point-filled"></i></span>
                    <span class="text-nowrap text-muted">تعداد کل سهام عادی</span>
                    <span class="float-end"><b>{{ $company->normal_stock_count }}</b></span>
                </p>
                <p class="mb-0">
                    <span class="text-primary me-1"><i class="ti ti-point-filled"></i></span>
                    <span class="text-nowrap text-muted">تعداد کل سهام ممتاز</span>
                    <span class="float-end"><b>{{ $company->prefered_stock_count }}</b></span>
                </p>

                <p class="mb-0">
                    <span class="text-primary me-1"><i class="ti ti-point-filled"></i></span>
                    <span class="text-nowrap text-muted">تعداد سهم عادی شما</span>
                    <span class="float-end">
                        <b>{{ $company->users()->where('user_id', auth()->id())->first()->pivot->normal_stock_count ?? 0 }}</b>
                    </span>
                </p>

                <p class="mb-0">
                    <span class="text-primary me-1"><i class="ti ti-point-filled"></i></span>
                    <span class="text-nowrap text-muted"> تعداد سهم ممتاز شما</span>
                    <span class="float-end">
                        <b>{{ $company->users()->where('user_id', auth()->id())->first()->pivot->prefered_stock_count ?? 0 }}</b>
                    </span>
                </p>
            </div>
        </div>
    </div>
    @endforeach

</div>
@endcan

<div class="row">
    @can(App\Enums\Permission::LOG_ACTIVITIES->value)
    <div class="col-8">
        <div class="card">
            <div class="card-header border-bottom border-light">
                <div class="row justify-content-between gy-2 position-relative">
                    <div class="col-lg-3">
                        <h4 class="header-title">لیست آخرین فعالیت ها</h4>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-nowrap mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-3" style="width: 50px;">#</th>
                            <th>توضیحات</th>
                            <th>عامل</th>
                            <th>نوع رویداد</th>
                            <th>موضوع</th>
                            <th>تاریخ</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($activities as $activity)
                        <tr>
                            <td class="ps-3">{{ $loop->iteration }}</td>
                            <td>{{ $activity->description }}</td>
                            <td>{{ $activity->causer?->full_name }}</td>
                            <td>{{ __($activity->event) }}</td>
                            <td>
                                @if($activity->subject)
                                {{ $activity->subject->name ?? $activity->subject->title ?? $activity->subject->name ?? $activity->subject->full_name ?? 'ID: ' . $activity->subject_id }}
                                @else
                                N/A
                                @endif
                            </td>
                            <td>{{ verta($activity->created_at)->format('Y/m/d H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-muted">هیچ فعالیتی یافت نشد</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endcan
    <div class="col-4">
        <div class="card">
            <div class="card-header border-bottom border-light">
                <div class="row justify-content-between gy-2 position-relative">
                    <div class="col-lg-12">
                        <h4 class="header-title">اطلاع رسانی ها</h4>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-nowrap mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-3" style="width: 50px;">#</th>
                            <th>توضیحات</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection