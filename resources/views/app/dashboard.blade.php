@extends('app.layouts.app')

@section('head-tag')
    <style>
        .dashboard-shortcut-card {
            display: block;
            height: 100%;
            text-decoration: none;
            color: inherit;
            border-radius: 0.75rem;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .dashboard-shortcut-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1.25rem rgba(0, 0, 0, 0.08);
            color: inherit;
        }

        .dashboard-shortcut-card__icon {
            width: 44px;
            height: 44px;
            border-radius: 0.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        .dashboard-shortcut-card__title {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 0.15rem;
        }

        .dashboard-shortcut-card__desc {
            font-size: 0.78rem;
            color: var(--bs-secondary);
            margin-bottom: 0;
        }

        @media (max-width: 767.98px) {
            .dashboard-page .page-title-head .breadcrumb {
                display: none;
            }

            .dashboard-shortcut-card .card-body {
                padding: 1rem 0.85rem;
            }

            .dashboard-shortcut-card__icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
        }
    </style>
@endsection

@section('content')
    <div class="dashboard-page">
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2 mb-3">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0"> خوش آمدید ، {{ user()->first_name }} </h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="{{ route('app.index') }}">خانه</a></li>
                <li class="breadcrumb-item active">همه</li>
            </ol>
        </div>
    </div>

    @if (isVoterOnly() || user()->groups->isNotEmpty())
        <div class="row g-3 mb-3 dashboard-participation-shortcuts">
            <div class="col-6">
                <a href="{{ route('my-elections.index') }}" class="card border border-primary border-opacity-25 dashboard-shortcut-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                            <span class="dashboard-shortcut-card__icon bg-primary-subtle text-primary">
                                <i class="ti ti-checkbox"></i>
                            </span>
                            @if ($sidebarAvailableElections->count() > 0)
                                <span class="badge bg-danger">{{ $sidebarAvailableElections->count() }}</span>
                            @endif
                        </div>
                        <div class="dashboard-shortcut-card__title">انتخابات من</div>
                        <p class="dashboard-shortcut-card__desc">
                            @if ($sidebarAvailableElections->count() > 0)
                                {{ $sidebarAvailableElections->count() }} انتخابات فعال
                            @else
                                مشاهده انتخابات
                            @endif
                        </p>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('my-surveys.index') }}" class="card border border-info border-opacity-25 dashboard-shortcut-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                            <span class="dashboard-shortcut-card__icon bg-info-subtle text-info">
                                <i class="ti ti-clipboard-text"></i>
                            </span>
                            @if ($sidebarAvailableSurveys->count() > 0)
                                <span class="badge bg-danger">{{ $sidebarAvailableSurveys->count() }}</span>
                            @endif
                        </div>
                        <div class="dashboard-shortcut-card__title">نظرسنجی‌های من</div>
                        <p class="dashboard-shortcut-card__desc">
                            @if ($sidebarAvailableSurveys->count() > 0)
                                {{ $sidebarAvailableSurveys->count() }} نظرسنجی فعال
                            @else
                                مشاهده نظرسنجی‌ها
                            @endif
                        </p>
                    </div>
                </a>
            </div>
        </div>
    @endif

    @can(App\Enums\Permission::LIST_GROUPS->value)
        <div class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 align-items-center">
            @foreach ($groups as $group)
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('groups.edit', $group) }}" class="text-muted float-end mt-n1 fs-18"><i
                                    class="ti ti-edit"></i></a>
                            <h5 class="text-muted fs-13 text-uppercase" title="Number of Orders">{{ $group->title }}</h5>
                            <div class="d-flex align-items-center gap-2 my-3">
                                <div class="avatar-md flex-shrink-0">
                                    <span class="avatar-title bg-primary-subtle text-primary rounded fs-22">
                                        <img src="{{ $group->logo }}" width="20" height="20" alt="">
                                    </span>
                                </div>
                                {{ $group->title }}
                            </div>
                            <p class="mb-1">
                                <span class="text-primary me-1"><i class="ti ti-point-filled"></i></span>
                                <span class="text-nowrap text-muted">تعداد اعضا</span>
                                <span class="float-end"><b>{{ $group->users->count() }}</b></span>
                            </p>
                            <p class="mb-0">
                                <span class="text-primary me-1"><i class="ti ti-point-filled"></i></span>
                                <span class="text-nowrap text-muted">تعداد کل سهام عادی</span>
                                <span class="float-end"><b>{{ $group->normal_stock_count }}</b></span>
                            </p>
                            <p class="mb-0">
                                <span class="text-primary me-1"><i class="ti ti-point-filled"></i></span>
                                <span class="text-nowrap text-muted">تعداد کل سهام ممتاز</span>
                                <span class="float-end"><b>{{ $group->prefered_stock_count }}</b></span>
                            </p>

                            <p class="mb-0">
                                <span class="text-primary me-1"><i class="ti ti-point-filled"></i></span>
                                <span class="text-nowrap text-muted">تعداد سهم عادی شما</span>
                                <span class="float-end">
                                    <b>{{ $group->users()->where('user_id', auth()->id())->first()->pivot->normal_stock_count ?? 0 }}</b>
                                </span>
                            </p>

                            <p class="mb-0">
                                <span class="text-primary me-1"><i class="ti ti-point-filled"></i></span>
                                <span class="text-nowrap text-muted"> تعداد سهم ممتاز شما</span>
                                <span class="float-end">
                                    <b>{{ $group->users()->where('user_id', auth()->id())->first()->pivot->prefered_stock_count ?? 0 }}</b>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    @endcan

    <div class="row">
        <div class="col-12 col-xl-6">
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

                            @forelse(user()->notifications->take(10) as $notification)
                                <tr>
                                    @if (empty($notification->is_read))
                                        <th>{{ $notification->data['message'] }}</th>
                                    @else
                                        <td>{{ $notification->data['message'] }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted">هیچ اعلانی وجود ندارد</td>
                                </tr>
                            @endforelse

                        </thead>

                        <tbody>
                            <tr></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @can(App\Enums\Permission::LOG_ACTIVITIES->value)
            <div class="col-12">
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
                                            @if ($activity->subject)
                                                {{ $activity->subject->name ?? ($activity->subject->title ?? ($activity->subject->name ?? ($activity->subject->full_name ?? 'ID: ' . $activity->subject_id))) }}
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
    </div>
    </div>
@endsection
