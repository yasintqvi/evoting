@if ($message = session()->get('success'))
    <script>
        toastr.options = {
            positionClass: 'toast-bottom-left', 
            timeOut: 5000, 
            closeButton: true, 
            progressBar: true, 
        };
        toastr.error("{{ $message }}", "ناموفق");
    </script>
@endif