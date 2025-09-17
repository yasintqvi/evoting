@extends('app.layouts.app')

@section('content')
    <div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold mb-0">افزودن کاربران به گروه</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="#">خانه</a></li>
                <li class="breadcrumb-item"><a href="#">گروه‌ها</a></li>
                <li class="breadcrumb-item active">افزودن کاربران</li>
            </ol>
        </div>
    </div>

    <form action="#" method="GET" class="mb-3">
        <div class="row">
            <div class="col-lg-6">
                <input type="text" name="search" class="form-control" placeholder="جستجوی کاربر..."
                    value="{{ request('search') }}" onchange="this.form.submit()">
            </div>
        </div>
    </form>

    <form action="{{ route('group.users.store-participant', $group->slug) }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">لیست کاربران</h4>
            </div>

            <div class="card-body">
                @if ($users->isEmpty())
                    <p>کاربری یافت نشد.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>نام</th>
                                    <th>نام خانوادگی</th>
                                    <th>سهام عادی</th>
                                    <th>سهام ممتاز</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    @php
                                        $pivot = $group->users->firstWhere('id', $user->id)?->pivot;
                                        $normal = old(
                                            "users.{$user->id}.normal_stock_count",
                                            $pivot->normal_stock_count ?? 0,
                                        );
                                        $prefered = old(
                                            "users.{$user->id}.prefered_stock_count",
                                            $pivot->prefered_stock_count ?? 0,
                                        );
                                    @endphp

                                    <tr>
                                        <td>{{ $user->first_name }}</td>
                                        <td>{{ $user->last_name }}</td>
                                        <td>
                                            <input type="number" name="users[{{ $user->id }}][normal_stock_count]"
                                                class="form-control" min="0" value="{{ $normal }}">
                                        </td>
                                        <td>
                                            <input type="number" name="users[{{ $user->id }}][prefered_stock_count]"
                                                class="form-control" min="0" value="{{ $prefered }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-success" @disabled($users->isEmpty())>افزودن به گروه</button>
            </div>
        </div>
    </form>
@endsection
