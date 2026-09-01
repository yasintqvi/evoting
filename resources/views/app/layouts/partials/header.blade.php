<!-- Topbar Start -->
<header class="app-topbar">
    <div class="page-container topbar-menu">
        @if (request()->route('group'))
        <!-- Brand Logo -->
        <div style="margin-top: 1rem;">
            <div class="btn-group mb-2">
                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="true">
                    <span class="menu-icon"><img src="{{ asset($group->logo ?? 'assets/img/group.jpg') }}"
                            class="rounded-circle me-lg-2 d-flex object-fit-cover" width="20" height="20"
                            alt="{{ $group->title }}"></span>
                    {{ $group->title }}
                </button>
                @isset(user()->groups)
                <div class="dropdown-menu" data-popper-placement="bottom-end"
                    style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 40px);">
                    @foreach (user()->groups->except($group->id) as $otherGroup)
                    <a class="dropdown-item"
                        href="{{ route('groups.index', $otherGroup) }}">{{ $otherGroup->title }}</a>
                    @endforeach
                    <hr>
                    <a class="dropdown-item" href="{{ route('app.index') }}">همه گروه ها</a>
                    <a class="dropdown-item text-primary"
                        href="{{ route('groups.edit', $group) }}">ویرایش</a>
                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#leave-group" href="#!">ترک
                        کردن</a>
                    <a class="dropdown-item active fw-semibold text-danger" data-bs-toggle="modal"
                        data-bs-target="#delete-group" href="#!">حذف گروه</a>
                </div>
                @endif
            </div>
        </div>
        @endif
        <div class="d-flex align-items-center gap-2">
            <!-- Sidebar Menu Toggle Button -->
            <button class="sidenav-toggle-button px-2" type="button" aria-label="باز کردن منو">
                <i class="ti ti-menu-deep fs-24"></i>
            </button>

            @if (!isVoterOnly())
                <!-- Horizontal Menu Toggle Button -->
                <button class="topnav-toggle-button px-2" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                    <i class="ti ti-menu-deep fs-22"></i>
                </button>
            @endif
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Notification Dropdown -->
            <div class="topbar-item">
                <div class="dropdown">
                    <button class="topbar-link dropdown-toggle drop-arrow-none" data-bs-toggle="dropdown"
                        data-bs-offset="0,25" type="button" data-bs-auto-close="outside" aria-haspopup="false"
                        aria-expanded="false">
                        <i class="ti ti-bell animate-ring fs-22"></i>
                        <span class="noti-icon-badge"></span>
                    </button>

                    <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg" style="min-height: 300px;">
                        <div class="p-3 border-bottom border-dashed">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-16 fw-semibold">نوتیفیکیشن</h6>
                                </div>
                                <div class="col-auto">
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle drop-arrow-none link-dark"
                                            data-bs-toggle="dropdown" data-bs-offset="0,15" aria-expanded="false">
                                            <i class="ti ti-settings fs-22 align-middle"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <!-- item-->
                                            <a href="javascript:void(0);" class="dropdown-item">خوانده شده</a>
                                            <!-- item-->
                                            <a href="javascript:void(0);" class="dropdown-item">حذف همه</a>
                                            <!-- item-->
                                            <a href="javascript:void(0);" class="dropdown-item">مزاحم نشو</a>
                                            <!-- item-->
                                            <a href="javascript:void(0);" class="dropdown-item">تنظیمات دیگر</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="position-relative z-2 card shadow-none rounded-0" style="max-height: 300px;"
                            data-simplebar>
                            <!-- item-->
                            <div class="dropdown-item notification-item py-2 text-wrap active" id="notification-1">
                                <span class="d-flex align-items-center">
                                    <span class="me-3 position-relative flex-shrink-0">
                                        <img src="assets/images/users/avatar-2.jpg" class="avatar-md rounded-circle"
                                            alt="" />
                                        <span class="position-absolute rounded-pill bg-danger notification-badge">
                                            <i class="ti ti-message-circle"></i>
                                            <span class="visually-hidden">پیام های خوانده نشده</span>
                                        </span>
                                    </span>
                                    <span class="flex-grow-1 text-muted">
                                        <span class="fw-medium text-body"> گلدی هیاد </span>نظر داد روی<span
                                            class="fw-medium text-body"> تغییر ادمین </span>
                                        <br />
                                        <span class="fs-12">25 دقیقه قبل</span>
                                    </span>
                                    <span class="notification-item-close">
                                        <button type="button" class="btn btn-ghost-danger rounded-circle btn-sm btn-icon"
                                            data-dismissible="#notification-1">
                                            <i class="ti ti-x fs-16"></i>
                                        </button>
                                    </span>
                                </span>
                            </div>

                            <!-- item-->
                            <div class="dropdown-item notification-item py-2 text-wrap" id="notification-2">
                                <span class="d-flex align-items-center">
                                    <span class="me-3 position-relative flex-shrink-0">
                                        <img src="assets/images/users/avatar-4.jpg" class="avatar-md rounded-circle"
                                            alt="" />
                                        <span class="position-absolute rounded-pill bg-info notification-badge">
                                            <i class="ti ti-currency-dollar"></i>
                                            <span class="visually-hidden">پیام های خوانده نشده</span>
                                        </span>
                                    </span>
                                    <span class="flex-grow-1 text-muted">
                                        <span class="fw-medium text-body"> تامی بری </span>اهدا کرد<span
                                            class="text-success">1000 تومان</span> برای <span class="fw-medium text-body">
                                            برنامه حذف کربن</span>
                                        <br />
                                        <span class="fs-12">58 دقیقه قبل</span>
                                    </span>
                                    <span class="notification-item-close">
                                        <button type="button" class="btn btn-ghost-danger rounded-circle btn-sm btn-icon"
                                            data-dismissible="#notification-2">
                                            <i class="ti ti-x fs-16"></i>
                                        </button>
                                    </span>
                                </span>
                            </div>

                            <!-- item-->
                            <div class="dropdown-item notification-item py-2 text-wrap" id="notification-3">
                                <span class="d-flex align-items-center">
                                    <div class="avatar-md flex-shrink-0 me-3">
                                        <span class="avatar-title bg-success-subtle text-success rounded-circle fs-22">
                                            <iconify-icon icon="solar:wallet-money-bold-duotone"></iconify-icon>
                                        </span>
                                    </div>
                                    <span class="flex-grow-1 text-muted">
                                        شما انتقال دادید <span class="fw-medium text-body">500 تومان</span> توسط <span
                                            class="fw-medium text-body"> ای تی ام </span>
                                        <br />
                                        <span class="fs-12">2 ساعت قبل</span>
                                    </span>
                                    <span class="notification-item-close">
                                        <button type="button" class="btn btn-ghost-danger rounded-circle btn-sm btn-icon"
                                            data-dismissible="#notification-3">
                                            <i class="ti ti-x fs-16"></i>
                                        </button>
                                    </span>
                                </span>
                            </div>

                            <!-- item-->
                            <div class="dropdown-item notification-item py-2 text-wrap" id="notification-4">
                                <span class="d-flex align-items-center">
                                    <span class="me-3 position-relative flex-shrink-0">
                                        <img src="assets/images/users/avatar-7.jpg" class="avatar-md rounded-circle"
                                            alt="" />
                                        <span class="position-absolute rounded-pill bg-secondary notification-badge">
                                            <i class="ti ti-plus"></i>
                                            <span class="visually-hidden">پیام های خوانده نشده</span>
                                        </span>
                                    </span>
                                    <span class="flex-grow-1 text-muted">
                                        <span class="fw-medium text-body"> ریچارد الن </span>شما را دنبال کرد در<span
                                            class="fw-medium text-body">فیسبوک</span>
                                        <br />
                                        <span class="fs-12">3 ساعت قبل</span>
                                    </span>
                                    <span class="notification-item-close">
                                        <button type="button" class="btn btn-ghost-danger rounded-circle btn-sm btn-icon"
                                            data-dismissible="#notification-4">
                                            <i class="ti ti-x fs-16"></i>
                                        </button>
                                    </span>
                                </span>
                            </div>

                            <!-- item-->
                            <div class="dropdown-item notification-item py-2 text-wrap" id="notification-5">
                                <span class="d-flex align-items-center">
                                    <span class="me-3 position-relative flex-shrink-0">
                                        <img src="assets/images/users/avatar-10.jpg" class="avatar-md rounded-circle"
                                            alt="" />
                                        <span class="position-absolute rounded-pill bg-danger notification-badge">
                                            <i class="ti ti-heart-filled"></i>
                                            <span class="visually-hidden">پیام های خوانده نشده</span>
                                        </span>
                                    </span>
                                    <span class="flex-grow-1 text-muted">
                                        <span class="fw-medium text-body">ویکتور کولیر</span> پست شما را لایک کرد در <span
                                            class="fw-medium text-body">اینستاگرام</span>
                                        <br />
                                        <span class="fs-12">10 ساعت قبل</span>
                                    </span>
                                    <span class="notification-item-close">
                                        <button type="button" class="btn btn-ghost-danger rounded-circle btn-sm btn-icon"
                                            data-dismissible="#notification-5">
                                            <i class="ti ti-x fs-16"></i>
                                        </button>
                                    </span>
                                </span>
                            </div>
                        </div>

                        <div style="height: 300px;"
                            class="d-flex align-items-center justify-content-center text-center position-absolute top-0 bottom-0 start-0 end-0 z-1">
                            <div>
                                <iconify-icon icon="line-md:bell-twotone-alert-loop"
                                    class="fs-80 text-secondary mt-2"></iconify-icon>
                                <h4 class="fw-semibold mb-0 fst-italic lh-base mt-3">سلام! 👋 <br />اطلاعیه ای ندارید</h4>
                            </div>
                        </div>

                        <!-- All-->
                        <a href="javascript:void(0);"
                            class="dropdown-item notification-item position-fixed z-2 bottom-0 text-center text-reset text-decoration-underline link-offset-2 fw-bold notify-item border-top border-light py-2">
                            مشاهده همه
                        </a>
                    </div>
                </div>
            </div>
            <!-- Light/Dark Mode Button -->
            <div class="topbar-item d-none d-sm-flex">
                <button class="topbar-link" id="light-dark-mode" type="button">
                    <i class="ti ti-moon fs-22"></i>
                </button>
            </div>
            <!-- User Dropdown -->
            <div class="topbar-item nav-user">
                <div class="dropdown">
                    <a class="topbar-link dropdown-toggle drop-arrow-none px-2" data-bs-toggle="dropdown"
                        data-bs-offset="0,19" type="button" aria-haspopup="false" aria-expanded="false">
                        <img src="{{ asset(user()->profile_image) }}" width="32"
                            class="rounded-circle me-lg-2 d-flex" alt="user-image">
                        <span class="d-lg-flex flex-column gap-1 d-none">
                            <h5 class="my-0">{{ user()->full_name }}</h5>
                        </span>
                        <i class="ti ti-chevron-down d-none d-lg-block align-middle ms-2"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">خوش آمدید!</h6>
                        </div>

                        <!-- item-->
                        <a href="{{ route('profile.show') }}" class="dropdown-item">
                            <i class="ti ti-user-hexagon me-1 fs-17 align-middle"></i>
                            <span class="align-middle">پروفایل من</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <!-- item-->
                        <a href="{{ route('logout') }}" class="dropdown-item active fw-semibold text-danger">
                            <i class="ti ti-logout me-1 fs-17 align-middle"></i>
                            <span class="align-middle">خروج</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Topbar End -->

@isset($group)
<div id="delete-group" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">حذف گروه</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p> آیا از حذف گروه <b>{{ $group->title }}</b> مطمئن هستید ؟</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">انصراف</button>
                <form action="{{ route('groups.delete', $group) }}" method="post">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">بله مطمئن هستم</button>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<div id="leave-group" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="standard-modalLabel">ترک گروه</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p> آیا از ترک گروه <b>{{ $group->title }}</b> مطمئن هستید ؟</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">انصراف</button>
                <form action="{{ route('groups.leave', $group) }}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-danger">بله مطمئن هستم</button>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


@endisset
