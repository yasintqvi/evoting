<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\ViewServiceProvider::class,
    // ثبت دستی برای هاست‌هایی که package:discover اجرا نشده
    Mccarlosen\LaravelMpdf\LaravelMpdfServiceProvider::class,
];
