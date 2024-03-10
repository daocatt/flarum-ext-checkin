<?php

namespace Gtdxyz\Checkin\Api\Serializer;

use Carbon\Carbon;
use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\BasicUserSerializer;

// use Flarum\Settings\SettingsRepositoryInterface;


class CheckinHistorySerializer extends AbstractSerializer
{
    protected $type = 'UserCheckinHistory';

    protected function getDefaultAttributes($data){
        
        $attributes = [
            'id' => $data->id,
            'type' => $data->type,
            'event_id' => $data->event_id,
            'user_id' => $data->user_id,
            'checkin_time' => Carbon::parse($data->checkin_time)->format('Y-m-d'),
            'reward_money' => $data->reward_money,
            'constant' => $data->constant,
            'remark' => $data->remark
        ];

        return $attributes;
    }

    // protected function User($checkinHistory){
    //     return $this->hasOne($checkinHistory, BasicUserSerializer::class);
    // }
}
