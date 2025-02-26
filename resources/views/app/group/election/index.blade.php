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
                    <a href="{{route('elections.create', $group->slug)}}" class="btn btn-success bg-gradient"><i class="ti ti-plus me-1"></i>ایجاد همه پرسی</a>
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
                            <th class="text-center" style="width: 120px;">فعالیت</th>
                        </tr>
                    </thead><!-- end thead -->

                    <tbody>
                        @forelse ($elections as $election)
                        <tr>
                            <td class="ps-3">
                            </td>
                            <td>
                                <a href="{{ route('elections.show', [$group->slug, $election->id]) }}" class="text-dark fw-medium">{{ $election->title }}</a>
                            </td>
                            <td>
                                <div class="avatar-group">
                                    @if($election->precentParticipants()->count() === 0)
                                    <b>-</b>
                                    @endif
                                    @foreach ($election->precentParticipants()->take(10) as $participant)
                                    <div class="avatar" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-secondary" data-bs-placement="top" aria-label="Vicki" data-bs-original-title="{{ $participant->user->full_name }}">
                                        <img src="{{ asset($participant->user->profile_image) }}" alt="" class="rounded-circle avatar-sm">
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
                                % {{ (int) (100 * ($election->precentParticipants()->count() / $group->users->count())) }}
                            </td>
                            <td>
                                <small>{{ $election->type->toFa() }}</small>
                            </td>
                            <td>
                                <span class="badge badge-soft-success">{{ $election->status->toFa() }}</span>
                            </td>
                            <td class="pe-3">
                                <div class="hstack gap-1 justify-content-end">
                                    @php
                                    $participant = $election->participants()->where('user_id', user()->id)->first()
                                    @endphp
                                    @switch($election->status)
                                    @case(App\Enums\ElectionStatus::CREATED)
                                        <!-- دکمه برای باز کردن مودال -->
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#confirmModal">
                                            تعیین نامزد ها
                                        </button>
                                        <!-- مودال -->
                                        <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="confirmModalLabel">تأیید عملیات</h5>
                                                        
                                                    </div>
                                                    <div class="modal-body">
                                                        در صورت تعیین نامزد دیگر نمیتوانید انتخابات را ویرایش کنید برای ادامه مطمئن هستید؟
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">لغو</button>
                                                        <a href="{{ route('candidates.create', [$group->slug, $election->id]) }}" class="btn btn-primary">تأیید</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @break

                                    @case(App\Enums\ElectionStatus::PARTICIPANTS_PENDING)
                                    <a href="{{ route('participants.create', [$group->slug, $election->id]) }}" class="btn btn-primary btn-sm">تعیین مشارکت کنندگان</a>
                                    @break

                                    @case(App\Enums\ElectionStatus::PARTICIPANTS_ATTENDEES)
                                    @if ($participant)
                                    <form action="{{ route('participants.update', [$group->slug, $election->id, $participant->id]) }}" method="post">
                                        @method('PUT')
                                        @csrf
                                        @if (!$participant->is_present)
                                        <button class="btn btn-primary btn-sm d-inline">اعلام حضور</button>
                                        @endif
                                    </form>
                                    @endif
                                    @break

                                    @case(App\Enums\ElectionStatus::WAITING_TO_START)
                                    @if (!$election->rounds()->where('is_active', true)->first())
                                    <form action="{{ route('election-rounds.store', [$group->slug, $election->id]) }}" method="post">
                                        @csrf
                                        <button class="btn btn-primary btn-sm d-inline">شروع انتخابات</button>
                                    </form>
                                    @endif
                                    @break

                                    @case(App\Enums\ElectionStatus::ONGOING)
                                    @if ($election->rounds()->where('is_active', true)->exists())
                                    @if($participant?->votes?->isNotEmpty())
                                    <small class="text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <g fill="none">
                                                <path fill="currentColor" d="M4.565 12.407a.75.75 0 1 0-1.13.986zM7.143 16.5l-.565.493a.75.75 0 0 0 1.13 0zm8.422-8.507a.75.75 0 1 0-1.13-.986zm-5.059 3.514a.75.75 0 0 0 1.13.986zm-.834 3.236a.75.75 0 1 0-1.13-.986zm-6.237-1.35l3.143 3.6l1.13-.986l-3.143-3.6zm4.273 3.6l1.964-2.25l-1.13-.986l-1.964 2.25zm3.928-4.5l1.965-2.25l-1.13-.986l-1.965 2.25zm1.965-2.25l1.964-2.25l-1.13-.986l-1.964 2.25z" />
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m20 7.563l-4.286 4.5M11 16l.429.563l2.143-2.25" />
                                            </g>
                                        </svg>
                                        مشارکت صورت گرفت
                                    </small>
                                    @else
                                    <a href="{{ route('voting.create', [$group->slug, $election->id]) }}" class="btn btn-primary btn-sm">شرکت در همه پرسی</a>
                                    @endif
                                    <form action="{{ route('voting.terminate', [$group->slug, $election->id]) }}" method="post">
                                        @csrf
                                        <button class="btn btn-danger btn-sm">خاتمه دادن به انتخابات</button>
                                    </form>
                                    @endif
                                    @break

                                    @endswitch
                                    <a href="{{ route('elections.show', [$group->slug, $election->id]) }}" class="btn btn-soft-primary btn-icon btn-sm rounded-circle"> <i class="ti ti-eye"></i></a>
                                    @switch($election->status)
                                    @case(app\Enums\ElectionStatus::CREATED)
                                    <a href="{{ route('elections.edit' , [$group->slug , $election->id]) }}" class="btn btn-soft-success btn-icon btn-sm rounded-circle"> <i class="ti ti-edit fs-16"></i></a>
                                    @break
                                    {{-- @case(app\Enums\ElectionStatus::PARTICIPANTS_PENDING)
                                        <a href="{{ route('candidates.edit-candidate', [$group->slug, $election->id]) }}" class="btn btn-soft-success btn-icon btn-sm rounded-circle">
                                            <i class="ti ti-edit fs-16"></i>
                                        </a>
                                    @break --}}
                                    @endswitch

                                   
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

@section('scripts')
<script src="/assets/samples/assets/irregular-data-series.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script src="/assets/js/pages/chart-apex-bar.js"></script>
@include('app.alerts.toastr.success')

@endsection 