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

                    <div class="btn-group mb-2">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">ایجاد همه پرسی</button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ route('elections.create', ['group' => $group->slug, 'electionType' => \App\Enums\ElectionType::PUBLIC_JOINT]) }}">انتخابات تعاونی</a>
                            <a class="dropdown-item" href="#">انتخابات سهامی عام</a>
                        </div>
                    </div>
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
                            <th>وضعیت</th>
                            <th class="text-center" style="width: 120px;">فعالیت</th>
                        </tr>
                    </thead><!-- end thead -->

                    <tbody>
                        @forelse ($elections as $election)
                        <tr>
                            <td class="ps-3">
                            </td>
                            <td>
                                <a href="{{ route('elections.show', [$group->slug, $election->id]) }}" class="text-dark fw-medium">انتخابات هیت مدیره سال ۱۴۰۲-۱۴۰۳</a>
                            </td>
                            <td>
                                <div class="avatar-group">
                                    @foreach ($election->participants->take(10) as $participant)
                                    <div class="avatar" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-secondary" data-bs-placement="top" aria-label="Vicki" data-bs-original-title="{{ $participant->user->full_name }}">
                                        <img src="{{ $participant->user->avatar }}" alt="" class="rounded-circle avatar-sm">
                                    </div>
                                    @endforeach
                                    @if($election->participants->count() > 10)
                                    <div class="avatar avatar-sm" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-danger" data-bs-placement="top" data-bs-original-title="">
                                        <span class="avatar-title bg-danger rounded-circle fw-bold">
                                            {{ $election->participants->count() - 10 }}+
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                % {{ 100 * ($election->participants->count() / $group->users->count()) }}
                            </td>
                            <td>
                                <span class="badge badge-soft-success">در حال برگزاری</span>
                            </td>
                            <td class="pe-3">
                                <div class="hstack gap-1 justify-content-end">
                                    <a href="javascript:void(0);" class="btn btn-soft-primary btn-icon btn-sm rounded-circle"> <i class="ti ti-eye"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-soft-success btn-icon btn-sm rounded-circle"> <i class="ti ti-edit fs-16"></i></a>
                                    <a href="javascript:void(0);" class="btn btn-soft-danger btn-icon btn-sm rounded-circle"> <i class="ti ti-trash"></i></a>
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