<?php

namespace App\Http\Responses;

use App\Models\TeamInvitation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginViewResponse as LoginViewResponseContract;
use Laravel\Fortify\Features;
use Symfony\Component\HttpFoundation\Response;

class LoginViewResponse implements LoginViewResponseContract
{
    public function toResponse($request): Response
    {
        return Inertia::render('auth/Login', [
            'canResetPassword' => Features::enabled(Features::resetPasswords()),
            'status' => $request->session()->get('status'),
            'teamInvitation' => $this->teamInvitation($request),
        ])->toResponse($request);
    }

    private function teamInvitation(Request $request): ?array
    {
        $invitationCode = $request->query('invitation');

        if (! is_string($invitationCode)) {
            return null;
        }

        $invitation = TeamInvitation::query()
            ->with('team')
            ->where('code', $invitationCode)
            ->whereNull('accepted_at')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->first();

        return $invitation
            ? ['code' => $invitation->code, 'teamName' => $invitation->team->name]
            : null;
    }
}
