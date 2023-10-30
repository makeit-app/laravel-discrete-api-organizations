<?php

namespace MakeIT\DiscreteApi\Organizations\Listeners;

use DateTime;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use MakeIT\DiscreteApi\Organizations\Events\InviteMemberToOrganizationEvent;

class InviteMemberToOrganizationListener implements ShouldQueue
{
    /**
     * The name of the connection the job should be sent to.
     */
    public ?string $connection = 'redis';

    /**
     * The time (seconds) before the job should be processed.
     */
    public int $delay = 0;

    /**
     * The number of times the queued listener may be attempted.
     */
    public int $tries = 3;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Determine the time at which the listener should timeout.
     */
    public function retryUntil(): DateTime
    {
        return now()->addMinutes(5);
    }

    /**
     * Handle the event.
     */
    public function handle(InviteMemberToOrganizationEvent $event): void
    {
        $Admin = $event->admin;
        $Admin->load(['profile']);
        $Member = $event->member;
        $Member->load(['profile']);
        $Organization = $event->organization;
        $Membership = $event->membership;
        $AcceptUrl = URL::signedRoute(
            'organizations.invite.member.accept',
            ['user' => $Member->id, 'organization' => $Organization->id],
            null,
            false
        );
        $DeclineUrl = URL::signedRoute(
            'organizations.invite.member.decline',
            ['user' => $Member->id, 'organization' => $Organization->id],
            null,
            false
        );
        //Log::info($Admin->email.' HAS INVITED YOU TO THE ORGANIZATION <<'.$Organization->title.'>>');
        //Log::info(json_encode($Membership));
        //Log::info('WITH ROLE <<'.__($Membership['role']).'>>. DO YOU ACCEPT THE INVITATION?');
        //Log::info('DO YOU ACCEPT THE INVITATION?');
        //Log::info('ACCEPT URL:');
        Log::info($AcceptUrl);
        //Log::info('DECLINE URL');
        Log::info($DeclineUrl);
        //Log::info('OR JUST IGNORE THIS NOTIFICATION');
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return true;
    }

    /**
     * Determine whether the listener should be queued.
     * Condition: stuff - immediately
     */
    public function shouldQueue(InviteMemberToOrganizationEvent $event): bool
    {
        return !in_array($event->membership['invite_role'], ['super', 'owner', 'admin', 'support', 'moderator']);
    }
}
