@if ($message = session()->get('warning'))
    <script>
        toastr.options = {
            positionClass: 'toast-bottom-left', 
            timeOut: 5000, 
            closeButton: true, 
            progressBar: true, 
        };
        toastr.warning("{{ $message }}", "توجه");
    </script>
@endif