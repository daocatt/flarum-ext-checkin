<?php

namespace Gtdxyz\Checkin\Listeners;

use Carbon\Carbon;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Grammars\Grammar;
use Flarum\Foundation\ValidationException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Flarum\User\User;
use Flarum\User\Event\Saving;
use Illuminate\Support\Arr;

use Gtdxyz\Checkin\Event\CheckinHistoryEvent;
use Gtdxyz\Checkin\Model\UserCheckinHistory;

class CheckinListener{
    protected $settings;
    protected $events;
    protected $translator;
    protected $db;
    protected $grammar;

    public function __construct(SettingsRepositoryInterface $settings, ConnectionInterface $connection, Grammar $grammar, Dispatcher $events, TranslatorInterface $translator){
        $this->settings = $settings;
        $this->events = $events;
        $this->translator = $translator;
        $this->db = $connection;
        $this->grammar = $grammar;
    }

    public function checkinSaving(Saving $event){

        $actor = $event->actor;
        $user = $event->user;
        $allowCheckin = $actor->can('checkin.allowCheckin', $user);

        $attributes = Arr::get($event->data, 'attributes', []);
        
        //check permissions
        if($allowCheckin && Arr::has($attributes, "checkin_days_count") && is_int($attributes['checkin_days_count'])){

            $userID = $user->id;    
            //daily reward
            $daily_reward = $this->settings->get('gtdxyz-checkin.reward', 0);

            //constant reward
            $constant = $this->settings->get('gtdxyz-checkin.constant', 0);
            $constant_force = $this->settings->get('gtdxyz-checkin.constant-force', 0);
            $constant_days = $this->settings->get('gtdxyz-checkin.constant-days', 0);
            $constant_reward = $this->settings->get('gtdxyz-checkin.constant-reward', 0);

            $checkin_constant_met = false;
            $checkin_today = false;

            $start_week = Carbon::today()->startofWeek()->format('Y-m-d 00:00:00');
            $start_date = $start_week;
            if($constant == 1){
                $start_date = Carbon::today()->subDays($constant_days)->format('Y-m-d 00:00:00');
            }
            $end_date = Carbon::today()->subDays(1)->format('Y-m-d 23:59:59');

            $last_history = UserCheckinHistory::where('user_id',$user->id)
                ->where('checkin_time', '>', $start_date) //from recent date
                ->orderBy('checkin_time','desc')
                ->first();
            
            //check if checkin yesterday
            if($last_history) {

                $checkin_today = Carbon::parse($last_history->checkin_time)->isToday();
                //finished checkin today
                if($checkin_today){
                    return;
                    // throw new ValidationException([
                    //     'message' => $this->translator->trans('gtdxyz-checkin.forum.errors.today-has-checkin')
                    // ]);
                }
                
                //get constant 6 days checkin data
                if($constant && $constant_days > 0) {

                    if($constant_force) {
                        $sql = "select user_id, checkin_time, check_days from";
                        $sql .= " (select user_id, checkin_time, constant, @pre_check := IF(constant, @pre_check+1, 0) as 'check_days'";
                        $sql .= " from ".$this->grammar->getTablePrefix()?$this->grammar->getTablePrefix().".":""."user_checkin_history, (select @pre_check :=0) init where user_id={$userID}";
                        $sql .= " and checkin_time > '{$start_date}' and checkin_time < '{$end_date}'";
                        $sql .= " ) tmp ";
                        $sql .= "order by checkin_time desc limit 1";
                        $data = $this->db->selectOne($sql);
                        $constant_count = $data ? $data->check_days : 0;
                    } else {
                        $constant_count = UserCheckinHistory::where('user_id',$userID)
                            ->where('event_id',0)
                            ->where('constant',1)
                            ->where('checkin_time','>', $start_date)
                            ->where('checkin_time','<', $end_date)
                            ->orderBy('checkin_time','desc')
                            ->count();
                    }
                    
                    //constant days met
                    if($constant_count == intval($constant_days-1)){
                        $checkin_constant_met = true;
                    }
                }
            }else{
                // if 1day match constant reward
                if($constant && intval($constant_days) == 1){
                    $checkin_constant_met = true;
                }
            }
            

            if(!$checkin_today){
                
                $checkin_time = Carbon::now()->format('Y-m-d H:i:s');
                $reward_money = $daily_reward;
                $type = 'N';
                $event_id = 0;
                $constant = 1;
                $remark = 'daily';
                //check met constant days or not
                if($checkin_constant_met){
                    $reward_money = bcadd($reward_money, $constant_reward);
                    $event_id = 1; //constant event
                    $constant = 2; //constant reset
                    $remark = 'constant';
                }
                
                $this->events->dispatch(new CheckinHistoryEvent($user, $checkin_time, $reward_money, $constant, $type, $event_id, $remark));
                
            }
        }

        
    }
}
