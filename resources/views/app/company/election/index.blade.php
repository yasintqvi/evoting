@extends('app.layouts.app')

@section('content')
<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0">انتخابات</h4>
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
                <h4 class="header-title">لیست انتخابات</h4>
                <div>
                    @can(\App\Enums\Permission::CREATE_ELECTIONS->value)
                    <a href="{{route('elections.create', $company->slug)}}" class="btn btn-success bg-gradient"><i class="ti ti-plus me-1"></i>ایجاد همه پرسی</a>
                    @endcan
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-nowrap mb-0">
                    <thead class="bg-light-subtle">
                        <tr>
                            <th class="ps-3" style="width: 50px;">
                            </th>
                            <th>عنوان</th>
                            <th>افراد حاضر</th>
                            <th>درصد افراد حاضر</th>
                            <th>نوع همه پرسی</th>
                            <th>وضعیت</th>
                            <th class="text-center" style="width: 120px;">اقدامات</th>
                        </tr>
                    </thead><!-- end thead -->

                    <tbody>
                        @forelse ($elections as $election)
                        <tr>
                            <td class="ps-3">
                            </td>
                            <td>
                                @can(\App\Enums\Permission::SHOW_ELECTION->value)
                                <a href="{{ $election['operations']['show'] }}" class="text-dark fw-medium">{{ $election['title'] }}</a>
                                @else
                                <span class="text-dark fw-medium">{{ $election['title'] }}</span>
                                @endcan
                            </td>
                            <td>
                                <div class="avatar-group">
                                    @if($election['participants']->count() === 0)
                                    <b>-</b>
                                    @endif
                                    @foreach ($election['participants'] as $participant)
                                    <div class="avatar" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-secondary" data-bs-placement="top" aria-label="Vicki" data-bs-original-title="{{ $participant->user->full_name }}">
                                        <img src="{{ asset($participant->user->profile_image) }}" alt="" class="rounded-circle avatar-sm">
                                    </div>
                                    @endforeach
                                    @if($election['participants']->count() > 10)
                                    <div class="avatar avatar-sm" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-danger" data-bs-placement="top" data-bs-original-title="">
                                        <span class="avatar-title bg-danger rounded-circle fw-bold">
                                            {{ $election['participants']->count() - 10 }}+
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                % {{ $election['participant_percent'] }}
                            </td>
                            <td>
                                <small>{{ $election['fa_type'] }}</small>
                            </td>
                            <td>
                                <span class="badge badge-soft-success">{{ $election['fa_status'] }}</span>
                            </td>
                            <td class="pe-3">
                                <div class="hstack gap-1 justify-content-end">
                                    @isset($election['operations']['next_step'])
                                    <a href="{{ $election['operations']['next_step']['url'] }}" class="btn btn-primary btn-sm">
                                        {{ $election['operations']['next_step']['title'] }}
                                    </a>
                                    @endisset
                                    @can(\App\Enums\Permission::SHOW_ELECTION->value)
                                    <a href="{{ $election['operations']['show'] }}" class="btn btn-success btn-sm"><i class="ti ti-eye"></i></a>
                                    @endcan
                                    @can(\App\Enums\Permission::EDIT_ELECTIONS->value)
                                    <a href="{{ $election['operations']['edit'] }}" class="btn btn-secondary btn-sm"><i class="ti ti-edit"></i></a>
                                    @endcan
                                    @can(\App\Enums\Permission::DELETE_ELECTIONS->value)
                                    <a href="javascript:void(0);" class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-muted">هنوز هیچ انتخاباتی برگزار نشده است.</td>
                        </tr>
                        @endforelse

                    </tbody><!-- end tbody -->
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