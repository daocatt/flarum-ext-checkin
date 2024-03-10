<?php

namespace Gtdxyz\Checkin\Listeners;

use Flarum\User\User;
use Gtdxyz\Checkin\Event\CheckinHistoryEvent;
use Gtdxyz\Checkin\Model\UserCheckinHistory;

class CheckinHistoryListener
{
    public $user;
    public $checkin_time;
    public $reward_money;
    public $type;
    public $event_id;
    public $constant;
    public $remark;

    public function handle(CheckinHistoryEvent $event) {
        
        $this->user = $event->user;
        $this->checkin_time = $event->checkin_time;
        $this->reward_money = $event->reward_money;
        $this->type = $event->type;
        $this->event_id = $event->event_id;
        $this->constant = $event->constant;
        $this->remark = $event->remark;

        $this->exec($event->user);
    }


    public function exec(?User $user) {

        $UserCheckinHistory = new UserCheckinHistory();

        $UserCheckinHistory->user_id = $user->id;
        $UserCheckinHistory->event_id = $this->event_id;
        $UserCheckinHistory->type = $this->type;
        $UserCheckinHistory->checkin_time = $this->checkin_time;
        $UserCheckinHistory->constant = $this->constant;
        $UserCheckinHistory->reward_money = $this->reward_money;
        
        if($this->remark){
            $UserCheckinHistory->remark = $this->remark;
        }

        $UserCheckinHistory->save();
    }
}
