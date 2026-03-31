<!DOCTYPE html>
<html dir="rtl" lang="en" data-layout-mode="detached">

<head>
    @include('app.layouts.partials.head-tag')
    @yield('head-tag')
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        @if (request()->route('group'))
            @include('app.layouts.partials.group-sidebar')
        @else
            @include('app.layouts.partials.dashboard-sidebar')
        @endif

        @include('app.layouts.partials.header')

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->
        <div class="page-content">
            <div class="page-container">
                @if ($group = request()->route('group'))
                    @if ($remainingNormal > 0 || ($group->type == App\Enums\GroupType::SPECIAL && $remainingPrefered > 0))
                        <div class="alert alert-warning mt-3" style="position: relative" role="alert">
                            <button type="button" style="position: absolute; left: 1rem;" class="btn-close"
                                data-bs-dismiss="alert" aria-label="Close"></button>
                            <h4 class="alert-heading">کاربر گرامی</h4>
                            <p>
                                برای برگزاری انتخابات و نظرسنجی در رویدادها، لازم است تمام سهام‌های عادی و ممتاز به
                                سهام‌داران اختصاص داده شوند.
                                <br>
                                در حال حاضر هنوز برخی از سهام‌ها اختصاص نیافته‌اند:
                                <br>
                                <strong>سهام عادی باقیمانده:</strong> {{ $remainingNormal }}<br>
                                <strong>سهام ممتاز باقیمانده:</strong> {{ $remainingPrefered }}
                                <br><br>
                                پس از تکمیل تخصیص سهام، تمامی قابلیت‌های رویداد برای شما فعال خواهند شد.
                            </p>

                        </div>
                    @endif

                @endif


                @yield('content')
                <!-- contect here -->

            </div>
            <!-- container -->

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    @include('app.layouts.partials.scripts')

    @yield('scripts')

</body>

</html>
