@if ($message = session()->get('info'))
    <script>
        toastr.options = {
            positionClass: 'toast-bottom-left', 
            timeOut: 5000, 
            closeButton: true, 
            progressBar: true, 
        };
        toastr.info("{{ $message }}", "اطلاع");
    </script>
@endif