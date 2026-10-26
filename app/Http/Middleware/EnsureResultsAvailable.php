<?php

namespace App\Http\Middleware;

use App\Models\Election;
use App\Models\Event;
use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * گزارش‌ها، نتایج و خروجی‌های PDF/Excel انتخابات را تا بسته شدن رأی‌گیری قفل می‌کند.
 * هیچ نقشی (حتی مدیر سایت) از این محدودیت مستثنا نیست.
 */
class EnsureResultsAvailable
{
    public function handle(Request $request, Closure $next): Response
    {
        $group = $request->route('group');
        $event = $request->route('event');
        $election = $request->route('election');

        if (
            ! $group instanceof Group
            || ! $event instanceof Event
            || ! $election instanceof Election
        ) {
            abort(404);
        }

        // رویداد و انتخابات باید واقعاً متعلق به همین گروه باشند.
        if ((int) $event->group_id !== (int) $group->id || (int) $election->event_id !== (int) $event->id) {
            abort(404);
        }

        if (! $request->user()?->canAccessGroup($group)) {
            abort(403, 'شما به این گروه دسترسی ندارید.');
        }

        $election->finalizeIfExpired();

        if (! $election->resultsAvailable()) {
            abort(403, 'نتایج و گزارش‌های انتخابات پس از پایان رأی‌گیری در دسترس خواهد بود.');
        }

        return $next($request);
    }
}
