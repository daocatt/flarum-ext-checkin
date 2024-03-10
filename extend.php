<?php

use Flarum\Extend;
use Flarum\Api\Serializer\UserSerializer;
use Flarum\Api\Serializer\CurrentUserSerializer;
use Flarum\Api\Serializer\ForumSerializer;

// use Flarum\User\User;
use Flarum\User\Event\Saving;
use Flarum\Settings\Event\Saving as SettingsSaving;

use Gtdxyz\Checkin\Access\UserPolicy;

use Gtdxyz\Checkin\Attributes\AddCheckinAttributes;
use Gtdxyz\Checkin\Listeners\CheckinListener;
use Gtdxyz\Checkin\Listeners\SettingsListener;
use Gtdxyz\Checkin\Event\CheckinHistoryEvent;
use Gtdxyz\Checkin\Listeners\CheckinHistoryListener;
use Gtdxyz\Checkin\Listeners\CheckinUpdateMoneyListener;
use Gtdxyz\Checkin\Api\Controller\ListUserCheckinHistoryController;

$extend = [
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),
    
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less')
        ->route('/u/{username}/checkin/history', 'gtdxyz-checkin.forum.checkin'),

    (new Extend\Locales(__DIR__ . '/locale')),

    (new Extend\Policy())->modelPolicy(User::class, UserPolicy::class),

    (new Extend\Event())
        ->listen(Saving::class, [CheckinListener::class, 'checkinSaving'])
        ->listen(SettingsSaving::class, [SettingsListener::class, 'settingsSaving'])
        ->listen(CheckinHistoryEvent::class, CheckinHistoryListener::class),


    (new Extend\Routes('api'))
        ->get('/checkin/history', 'user.checkin.history', ListUserCheckinHistoryController::class),

    
    // (new Extend\ApiSerializer(UserSerializer::class))
    //     ->attributes(AddCheckinAttributes::class),

    (new Extend\ApiSerializer(CurrentUserSerializer::class))
        ->attributes(AddCheckinAttributes::class),
    
    (new Extend\Settings())
        ->serializeToForum('checkinPosition', 'gtdxyz-checkin.position', 'intval', 0)
        ->serializeToForum('checkinReward', 'gtdxyz-checkin.reward','intval', 0)
        ->serializeToForum('checkinAuto', 'gtdxyz-checkin.auto', 'intval', 0)
        ->serializeToForum('checkinConstant', 'gtdxyz-checkin.constant', 'intval', 0)
        ->serializeToForum('checkinConstantForce', 'gtdxyz-checkin.constant-force', 'intval', 0)
        ->serializeToForum('checkinConstantDays', 'gtdxyz-checkin.constant-days', 'intval', 0)
        ->serializeToForum('checkinConstantReward', 'gtdxyz-checkin.constant-reward', 'intval', 0)
        ->serializeToForum('checkinSuccessType', 'gtdxyz-checkin.success-type', 'intval', 0)
        ->serializeToForum('checkinSuccessText', 'gtdxyz-checkin.success-text', 'strval')
        ->serializeToForum('checkinSuccessRewardText', 'gtdxyz-checkin.success-reward-text', 'strval'),

    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attribute('allowCheckin', function (ForumSerializer $serializer) {
            return $serializer->getActor()->hasPermission("checkin.allowCheckin");
        }),
];

if (class_exists('Gtdxyz\Money\History\Event\MoneyHistoryEvent')) {
    $extend[] =
        (new Extend\Event())
            ->listen(CheckinHistoryEvent::class, CheckinUpdateMoneyListener::class)
    ;
}


return $extend;