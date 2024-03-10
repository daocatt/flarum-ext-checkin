<?php

use Flarum\Database\Migration;
use Flarum\Group\Group;

return Migration::addPermissions([
    'checkin.allowCheckin' => Group::MEMBER_ID
]);
