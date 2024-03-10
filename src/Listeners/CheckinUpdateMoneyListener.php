<?php

namespace Gtdxyz\Checkin\Listeners;

use Illuminate\Contracts\Events\Dispatcher;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;

use Gtdxyz\Checkin\Event\CheckinHistoryEvent;
use Gtdxyz\Money\Event\MoneyUpdated;
use Gtdxyz\Money\History\Event\MoneyHistoryEvent;


class CheckinUpdateMoneyListener
{
    private string $source = "CHECKINSAVED";
    private string $sourceDesc;

    private $events;
    private $settings;

    public function __construct(Dispatcher $events, SettingsRepositoryInterface $settings, Translator $translator)
    {
        $this->events = $events;
        $this->settings = $settings;
        $this->source = "CHECKINSAVED";
        $this->sourceDesc = $translator->trans("gtdxyz-checkin.forum.source-desc");
    }

    public function handle(CheckinHistoryEvent $checkin) {
        $user = $checkin->user;
        $amount = $checkin->reward_money;

        $user->money = bcadd($user->money, $amount);
        $user->save();

        $this->events->dispatch(new MoneyUpdated($user));

        $this->events->dispatch(new MoneyHistoryEvent($user, $amount, $this->source, $this->sourceDesc));
    }
}