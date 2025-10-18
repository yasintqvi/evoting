@extends('app.layouts.app')
@section('head-tag')

@section('content')
<div class="page-title-head d-flex align-items-sm-center flex-sm-row flex-column gap-2">
    <div class="flex-grow-1">
        <h4 class="fs-18 fw-semibold mb-0">{{ $event->title }}</h4>
    </div>

    <div class="text-end">
        <ol class="breadcrumb m-0 py-0">
            <li class="breadcrumb-item"><a href="javascript: void(0);">داشبورد</a></li>

            <li class="breadcrumb-item"><a href="{{ route('groups.index', $group) }}">{{ $group->title }}</a></li>
            <li class="breadcrumb-item active">ساخت رویداد جدید</li>
        </ol>

    </div>
</div>

<div class="card">
    <div class="d-flex">
        <div class="email-sidebar">
            <div class="offcanvas-xxl offcanvas-start" tabindex="-1" id="email-sidebar" aria-labelledby="email-sidebarLabel">
                <div class="card-body">
                    <div class="email-menu-list d-flex flex-column gap-1">
                        <a href="javascript: void(0);">
                            <iconify-icon icon="solar:map-arrow-right-bold-duotone" class="me-2 fs-18 text-muted"></iconify-icon>
                            <span>همه</span>
                        </a>
                        <a href="{{ route('elections.index', [$group, $event]) }}" class="{{ is_active('elections') ? 'active' : '' }}">
                            <iconify-icon icon="solar:map-arrow-right-bold-duotone" class="me-2 fs-18 text-muted"></iconify-icon>
                            <span>انتخابات</span>
                        </a>
                        <a href="javascript: void(0);">
                            <iconify-icon icon="solare:inbox-bold-duotone" class="me-2 fs-18 text-muted"></iconify-icon>
                            <span>نظرسنجی ها</span>
                            <span class="badge bg-danger-subtle fs-12 text-danger ms-auto">21</span>
                        </a>

                        <a href="javascript: void(0);">
                            <iconify-icon icon="solar:map-arrow-right-bold-duotone" class="me-2 fs-18 text-muted"></iconify-icon>
                            <span>انتخابات</span>
                        </a>

                        <a href="javascript: void(0);">
                            <iconify-icon icon="solar:star-bold-duotone" class="me-2 fs-18 text-muted"></iconify-icon>
                            <span>ستاره دار</span>
                        </a>

                        <a href="javascript: void(0);">
                            <iconify-icon icon="solar:clock-circle-bold-duotone" class="me-2 fs-18 text-muted"></iconify-icon>
                            <span>برنامه ریزی شده</span>
                        </a>

                        <a href="javascript: void(0);">
                            <iconify-icon icon="solar:clapperboard-edit-bold-duotone" class="me-2 fs-18 text-muted"></iconify-icon>
                            <span>پیش نویس</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-grow-1 card rounded-0 shadow-none mb-0">
            <div class="border-start border-light h-100">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-light d-xxl-none d-flex p-1" data-bs-toggle="offcanvas" data-bs-target="#email-sidebar" aria-controls="email-sidebar">
                            <i class="ti ti-menu-2 fs-17"></i>
                        </button>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                        </div>

                        <div class="d-flex align-items-center">
                            <button type="button" class="btn btn-sm btn-icon btn-ghost-light text-dark rounded-circle" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" data-bs-title="&lt;span class='fs-12'&gt;خوانده شده&lt;/span&gt;">
                                <i class="ti ti-mail-opened fs-18"></i>
                            </button>

                            <button type="button" class="btn btn-sm btn-icon btn-ghost-light text-dark rounded-circle" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" data-bs-title="&lt;span class='fs-12'&gt;بایگانی&lt;/span&gt;">
                                <i class="ti ti-archive fs-18"></i>
                            </button>

                            <button type="button" class="btn btn-sm btn-icon btn-ghost-light text-dark rounded-circle" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" data-bs-title="&lt;span class='fs-12'&gt;حذف&lt;/span&gt;">
                                <i class="ti ti-trash fs-18"></i>
                            </button>

                            <button type="button" class="btn btn-icon btn-sm btn-ghost-light text-dark rounded-circle" data-bs-toggle="tooltip" data-bs-html="true" data-bs-trigger="hover" data-bs-placement="top" data-bs-title="&lt;span class='fs-12'&gt;گزارش&lt;/span&gt;">
                                <i class="ti ti-info-hexagon fs-18"></i>
                            </button>
                        </div>

                        <div class="ms-auto d-xl-flex d-none">
                            <div class="app-search">
                                <input type="text" class="form-control rounded-pill" placeholder="نامه جستجو...">
                                <i class="ti ti-mail-search fs-18 app-search-icon text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-top border-light">
                    @yield('event_content')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
