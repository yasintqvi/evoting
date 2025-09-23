@extends('app.layouts.app')
@section('head-tag')
    <style>
        .event-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            color: #2c3e50;
        }

        .badge {
            padding: 5px 8px;
            border-radius: 8px;
            font-weight: 500;
        }
    </style>
@endsection

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">{{ $group->name }}</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">داشبورد</a></li>

                <li class="breadcrumb-item active">{{ $group->name }}</li>
            </ol>
        </div>
    </div>

    <div class="d-flex flex-row gap-3 p-3">

        <!-- مرحله 1 -->
        <div class="d-flex flex-column flex-fill p-3 rounded shadow-sm border-start border-4 border-success ">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="bg-success text-white rounded px-3 py-2 fw-bold">✓</div>
                <div class="text-success fw-semibold">ایجاد گروه</div>
            </div>
            <small class="text-success align-self-end">تکمیل شده</small>
        </div>

        @can(\App\Enums\Permission::VIEW_GROUP_USERS->value)
            @php
                $isApproved = $usersCount >= 3;
            @endphp

            <div
                class="d-flex flex-column flex-fill p-3 rounded shadow-sm border-start border-4
        {{ $isApproved ? 'border-success' : 'border-primary bg-white' }}
        transition-all hover:shadow-lg">

                <a href="{{ route('group.users.index', $group->slug) }}"
                    class="text-decoration-none {{ $isApproved ? 'text-success' : 'text-primary' }}">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div
                            class="{{ $isApproved ? 'bg-success text-white' : 'bg-secondary text-white' }} rounded px-3 py-2 fw-bold">
                            {{ $isApproved ? '✓' : '2' }}
                        </div>
                        <div class="{{ $isApproved ? 'text-success fw-semibold' : 'text-primary fw-semibold' }}">
                            {{ $isApproved ? 'تایید شده - اعضا اضافه شدند' : 'ایجاد اعضا' }}
                        </div>
                    </div>
                </a>

                <small class="{{ $isApproved ? 'text-success' : 'text-primary' }} align-self-end">
                    {{ $isApproved ? 'تکمیل شده' : 'در حال انتظار' }}
                </small>
            </div>
        @endcan

        <!-- مرحله 3 -->
    </div>
    <h4 class="mb-4 text-primary fw-bold">{{ $group->name }}</h4>

    <div class="row g-4">
        @foreach ($events as $event)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card event-card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 d-flex flex-column">
                        <h5 class="card-title fw-bold mb-3 text-primary">
                            <i class="fas fa-calendar-check me-2 text-primary"></i>
                            {{ $event->name }}
                        </h5>

                        <div class="d-flex justify-content-between mb-3">
                            <div class="text-center flex-fill">
                                <div class="icon-circle bg-success bg-opacity-10 text-success mb-2">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <span class="fw-semibold">{{ $event->present_count1 }}</span>
                                <small class="d-block text-muted">حاضر</small>
                            </div>
                            <div class="text-center flex-fill">
                                <div class="icon-circle bg-danger bg-opacity-10 text-danger mb-2">
                                    <i class="fas fa-user-times"></i>
                                </div>
                                <span class="fw-semibold">{{ $event->absent_count1 }}</span>
                                <small class="d-block text-muted">غایب</small>
                            </div>
                        </div>

                        <div class="mt-auto">
                            <div class="d-flex gap-2 flex-nowrap">
                                <a href="{{ route('group.event.show', [$group->slug, $event->id]) }}"
                                    class="btn btn-outline-primary rounded-pill flex-fill text-nowrap">
                                    مشاهده جزئیات
                                </a>
                                <a href="{{ route('group.event.edit', [$group->slug, $event->id]) }}"
                                    class="btn btn-outline-warning rounded-pill flex-fill text-nowrap">
                                    ویرایش رویداد
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- فوتر -->
                    <div class="card-footer bg-light border-0 text-center py-2">
                        <small class="text-muted">
                            <i class="far fa-clock me-1"></i> {{ $event->created_at->format('Y/m/d') }}
                        </small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <canvas id="attendanceChart" style="max-height: 300px; width: 300%;"></canvas>
@endsection
