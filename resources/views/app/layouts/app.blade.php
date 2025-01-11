<!DOCTYPE html>
<html dir="rtl" lang="en" data-layout-mode="detached">

<head>
    @include('app.layouts.partials.head-tag')
    @yield('head-tag')
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        @include('app.layouts.partials.top-nav')


        @include('app.layouts.partials.header')

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->
        <div class="page-content">

            <div class="page-container">

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