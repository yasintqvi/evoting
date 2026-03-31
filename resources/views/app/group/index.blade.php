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
                <li class="breadcrumb-item"><a href="{{route('groups.index',$group)}}">داشبورد</a></li>

                <li class="breadcrumb-item active">{{ $group->title}}</li>
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

                <a href="{{ route('group.users.index', $group) }}"
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

    <canvas id="attendanceChart" style="max-height: 300px; width: 300%;"></canvas>
@endsection
