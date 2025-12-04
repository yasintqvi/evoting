<!-- Sidenav Menu Start -->
<div class="sidenav-menu">

    <!-- Brand Logo -->
    <a href="/" class="logo">
        <span class="logo-light">
            <span class="logo-lg"><img src="/assets/img/logo.webp" alt="logo" style="width: 3rem; height: 3rem"></span>
            <span class="logo-sm"><img src="/assets/img/logo.webp" alt="small logo"
                                       style="width: 3rem; height: 3rem"></span>
        </span>

        <span class="logo-dark">
            <span class="logo-lg"><img src="/assets/img/logo.webp" alt="dark logo"
                                       style="width: 3rem; height: 3rem"></span>
            <span class="logo-sm"><img src="/assets/img/logo.webp" alt="small logo"
                                       style="width: 3rem; height: 3rem"></span>
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <button class="button-sm-hover">
        <i class="ti ti-circle align-middle"></i>
    </button>

    <!-- Full Sidebar Menu Close Button -->
    <button class="button-close-fullsidebar">
        <i class="ti ti-x align-middle"></i>
    </button>

    <div data-simplebar>

        <!--- Sidenav Menu -->
        <ul class="side-nav">
            <li class="side-nav-item">
                <a href="{{ route('groups.index', $group) }}" class="side-nav-link">
                    <span class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-house-door-fill" viewBox="0 0 16 16">
                            <path
                                d="M6.5 14.5v-3.505c0-.245.25-.495.5-.495h2c.25 0 .5.25.5.5v3.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5"/>
                        </svg>
                    </span>
                    <span class="menu-text"> داشبورد </span>
                </a>
            </li>


            @canany([\App\Enums\Permission::VIEW_GROUP_USERS->value,\App\Enums\Permission::VIEW_GROUP_USERS_GROUPID->value.$group->id,\App\Enums\Permission::GROUP_OWNER_GROUPID->value.$group->id])
                <li class="side-nav-item">
                    <a href="{{ route('group.users.index', $group) }}" class="side-nav-link">
                        <span class="menu-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <circle cx="15" cy="6" r="3" fill="currentColor" opacity="0.4"/>
                                <ellipse cx="16" cy="17" fill="currentColor" opacity="0.4" rx="5"
                                         ry="3"/>
                                <circle cx="9.001" cy="6" r="4" fill="currentColor"/>
                                <ellipse cx="9.001" cy="17.001" fill="currentColor" rx="7" ry="4"/>
                            </svg>
                        </span>
                        <span class="menu-text"> اعضا </span>
                    </a>
                </li>
                {{--            @can("manage_group_{$group->id}",$group)--}}
                <li class="side-nav-item">
                    <a href="{{ route('group.permissions', $group) }}" class="side-nav-link">
                        <span class="menu-icon">
   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-lock"
        viewBox="0 0 16 16">
                             <path
                                 d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453 7.159 7.159 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7.158 7.158 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z"/>
                             <path
                                 d="M9.5 6.5a1.5 1.5 0 0 1-1 1.415l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99a1.5 1.5 0 1 1 2-1.415z"/>
                         </svg>                       </span>
                        <span class="menu-text"> دسترسی ها </span>
                    </a>
                </li>

                {{--            @endcan--}}

                <br>
                @canany([\App\Enums\Permission::VIEW_GROUP_EVENT_GROUPID->value.$group->id,\App\Enums\Permission::GROUP_OWNER_GROUPID->value.$group->id,\App\Enums\Permission::VIEW_GROUP_EVENT->value])
                    <li class="side-nav-title">لیست آخرین رویداد ها</li>
                    @php
                        $events = $group->events()
                            ->latest()
                            ->take(5)
                            ->get()
                            ->filter(function ($event)use($group) {
                                $permission = \App\Enums\Permission::SHOW_EVENT_EVENTID->value . $event->id;
                                $manage = \App\Enums\Permission::GROUP_OWNER_GROUPID->value . $group->id;
                                $admin=\App\Enums\Permission::VIEW_GROUP_EVENT->value;

                                return auth()->user()->can($permission) || auth()->user()->can($manage)||auth()->user()->can($admin);
                            });
                    @endphp

                    @forelse($events as $event)
                        <li class="side-nav-item active">
                            <a data-bs-toggle="collapse" href="#event-{{ $event->id }}" aria-expanded="false"
                               aria-controls="sidebarHospital" class="side-nav-link collapsed">
                            <span class="menu-icon">
                                @if ($event->logo)
                                    <img src="{{ asset($event->logo) }}"
                                         class="rounded-circle me-lg-2 d-flex object-fit-cover" width="20" height="20"
                                         alt="{{ $group->title }}">
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z"/>
                                    </svg>
                                @endif
                            </span>
                                <span class="menu-text text-wrap small" style="line-height: 1.2rem;">
                                {{ $event->title }}
                            </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="event-{{ $event->id }}">
                                <ul class="sub-menu">

                                    @canany([\App\Enums\Permission::CREATE_ATTENDANCE_GROUPID->value.$group->id,\App\Enums\Permission::GROUP_OWNER_GROUPID->value.$group->id,\App\Enums\Permission::VIEW_GROUP_EVENT->value,\App\Enums\Permission::ATTENDANCE_EVENT_EVENTID->value.$event->id])
                                        <li class="side-nav-item">
                                            <a href="{{ route('attendances.create', [$group, $event]) }}"
                                               class="side-nav-link">
                                                <span class="menu-text">حضور و غیاب</span>
                                            </a>
                                        </li>
                                    @endcanany

                                    @canany([\App\Enums\Permission::EVENT_ELECTION_EVENTID->value.$event->id,\App\Enums\Permission::GROUP_OWNER_GROUPID->value.$group->id,\App\Enums\Permission::VIEW_GROUP_EVENT->value,\App\Enums\Permission::GROUP_EVENT_ELECTION_GROUPID->value.$group->id])
                                        <li class="side-nav-item">
                                            <a href="{{ route('elections.index', [$group, $event]) }}"
                                               class="side-nav-link">
                                                <span class="menu-text">انتخابات</span>
                                            </a>
                                        </li>
                                    @endcanany
                                        @canany([\App\Enums\Permission::EVENT_SURVEY_EVENTID->value.$event->id,\App\Enums\Permission::GROUP_EVENT_SURVEY_GROUPID->value.$group->id,\App\Enums\Permission::VIEW_GROUP_EVENT->value,\App\Enums\Permission::GROUP_OWNER_GROUPID->value.$group->id])
                                    <li class="side-nav-item">
                                        <a href="{{ route('surveys.index', [$group, $event]) }}" class="side-nav-link">
                                            <span class="menu-text">نظرسنجی‌ها</span>
                                        </a>
                                    </li>
                                        @endcanany
                                    <li class="side-nav-item">
                                        <a href="{{ route('group.event.show', [$group, $event]) }}"
                                           class="side-nav-link">
                                            <span class="menu-text">نمایش جزئیات</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @empty
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="d-flex flex-column">
                                <div class="mt-2 side-nav-title"> هنوز هیچ رویدادی اضافه نشده است.</div>
                            </div>
                        </div>
                    @endforelse

                @endcan
                @canany([\App\Enums\Permission::CREATE_GROUP_EVENT_GROUPID->value.$group->id,\App\Enums\Permission::GROUP_OWNER_GROUPID->value.$group->id,\App\Enums\Permission::CREATE_GROUP_EVENT->value])
                    <div class="d-flex flex-column mt-3">
                        <a href="{{ route('events.create', $group) }}" class="btn btn-secondary btn-sm mx-auto">رویداد
                            جدید</a>
                    </div>
                @endcan
                @canany([\App\Enums\Permission::VIEW_GROUP_EVENT_GROUPID->value.$group->id,\App\Enums\Permission::GROUP_OWNER_GROUPID->value.$group->id,\App\Enums\Permission::VIEW_GROUP_EVENT->value])
                    <div class="d-flex flex-column mt-3">
                        <a href="{{ route('events.index', $group) }}" class="btn btn-secondary btn-sm mx-auto">نمایش
                            همه</a>
                @endcanany

            @endcan
        </ul>


    </div>
</div>
<!-- Sidenav Menu End -->
