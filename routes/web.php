<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';
// routes/web.php

Route::middleware('auth')->group(function () {

    Route::get('/', DashboardController::class)->name('app.index');

    require __DIR__ . '/attendance.php';

    require __DIR__ . '/my.php';

    require __DIR__ . '/group.php';

    require __DIR__ . '/user.php';

    require __DIR__ . '/event.php';

    require __DIR__ . '/election.php';

    require __DIR__ . '/survey.php';

});
