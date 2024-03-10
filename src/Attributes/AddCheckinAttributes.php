<?php

namespace Gtdxyz\Checkin\Attributes;

use Carbon\Carbon;
use Illuminate\Database\ConnectionInterface;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Flarum\Api\Serializer\UserSerializer;
use Flarum\User\User;
use Gtdxyz\Checkin\Model\UserCheckinHistory;

class AddCheckinAttributes
{
    protected $settings;
    protected $events;
    protected $history;
    protected $db;

    public function __construct(SettingsRepositoryInterface $settings, ConnectionInterface $connection, UserCheckinHistory $history,  Dispatcher $events)
    {
        $this->settings = $settings;
        $this->events = $events;
        $this->history = $history;
        $this->db = $connection;
    }

    public function histories()
    {
        return $this->history()
            ->where('event_id', 0);
    }
    
    public function __invoke(UserSerializer $serializer, User $user)
    {
        $constant = intval($this->settings->get('gtdxyz-checkin.constant', 0));
        $constant_force = intval($this->settings->get('gtdxyz-checkin.constant-force', 0));
        $constant_days = intval($this->settings->get('gtdxyz-checkin.constant-days', 0));

        $checkin_days_count = 0;
        $checkin_constant_count = 0;
        $checkin_last_time = null;
        $checkin_yesterday = false;
        $checkin_today = false;
        $attributes = [];

        $userID = $user->id;

        $last_history = UserCheckinHistory::where('user_id',$user->id)
            ->orderBy('checkin_time','desc')
            ->first();
        
        //if any checkin
        if($last_history) {

            $checkin_last_time = $last_history->checkin_time;

            $last_date = Carbon::parse($last_history->checkin_time);
            $checkin_yesterday = $last_date->isYesterday();
            $checkin_today = $last_date->isToday();

            $checkin_days_count = $checkin_yesterday?1:0;
            $checkin_days_count += $checkin_today?1:0;
            
            //get checked days checkin data
            $start_week = Carbon::today()->startOfWeek()->format('Y-m-d 00:00:00');
            if($constant == 1 && $constant_days > 0){
                $start_date = Carbon::today()->subDays($constant_days-1)->format('Y-m-d 00:00:00');
            }else{
                // from monday
                $start_date = $start_week;
            }
            $end_date = Carbon::now()->format('Y-m-d H:i:s');
                
            $days_count = UserCheckinHistory::where('user_id',$user->id)
                ->where('type','N')
                ->where('constant','>',0)
                ->where('checkin_time','>', $start_week)
                ->where('checkin_time','<', $end_date)
                ->orderBy('checkin_time','desc')
                ->count();
            
            $checkin_days_count = $days_count;

            // get constantly count
            if($constant_force){
                $sql = "select user_id, checkin_time, check_days from";
                $sql .= " (select user_id, checkin_time, constant, @pre_check := IF(constant, @pre_check+1, 0) as 'check_days'";
                $sql .= " from user_checkin_history, (select @pre_check :=0) init where user_id={$userID}";
                // $sql .= " and event_id=0";
                $sql .= " and checkin_time > '{$start_date}' and checkin_time < '{$end_date}'";
                $sql .= " ) tmp ";
                $sql .= "order by checkin_time desc limit 1";
                $data = $this->db->selectOne($sql);
                $checkin_constant_count = $data ? $data->check_days : 0;
            }
            
            
        }

        
        $attributes['checkin_last_time'] = $checkin_last_time;
        // $attributes['checkin_yesterday'] = $checkin_yesterday;
        $attributes['checkin_days_count'] = $checkin_days_count;
        $attributes['checkin_constant_count'] = $checkin_constant_count;
        $attributes['checkin_sync'] = 0;
        
        $actor = $serializer->getActor();
        $attributes['allowCheckin'] = (!$checkin_today && $actor->can('checkin.allowCheckin', $user))?true:false;

        return $attributes;
    }
}
