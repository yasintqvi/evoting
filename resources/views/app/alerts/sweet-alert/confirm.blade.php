<script>
    $('.delete').on("click", function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'مطمئن هستید؟',
            text: "در صورت حذف امکان بازیابی مجدد وجود ندارد",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'بله حذف شود!'
        }).then(function(result) {
            if (result.value) {
                e.currentTarget.submit();
            }
        });
        e.preventDefault();
    });
</script>
