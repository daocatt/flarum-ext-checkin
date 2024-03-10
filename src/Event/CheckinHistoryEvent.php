<?php

namespace Gtdxyz\Checkin\Event;

use Flarum\User\User;

class CheckinHistoryEvent
{

    public $user;
    public $checkin_time;
    public $reward_money;
    public $type;
    public $event_id;
    public $constant;
    public $remark;

    public function __construct(User $user = null, $checkin_time = null, $reward_money = 0, $constant = 0, $type='N', $event_id = 0, $remark="")
    {
        $this->user = $user;
        $this->checkin_time = $checkin_time;
        $this->reward_money = $reward_money;
        $this->type = $type;
        $this->event_id = $event_id;
        $this->constant = $constant;
        $this->remark = $remark;
    }

}
