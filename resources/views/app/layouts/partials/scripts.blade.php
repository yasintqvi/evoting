@include('app.alerts.toastr.success')

<!-- Vendor js -->
<script src="{{ asset('assets/js/vendor.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/@majidh1/jalalidatepicker@0.9.12/dist/jalalidatepicker.min.js"></script>

<!-- App js -->
<script src="{{ asset('assets/js/app.js') }}"></script>

<!-- Apex Chart js -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>

<!-- Projects Analytics Dashboard App js -->
<script src="{{ asset('assets/js/pages/dashboard-sales.js') }}"></script>

<script src="{{ asset('assets/js/sweetalert2.js') }}"></script>

<script src="{{ asset('assets/js/toastr.min.js') }}"></script>

@include('app.alerts.toastr.error')
@include('app.alerts.toastr.success')
