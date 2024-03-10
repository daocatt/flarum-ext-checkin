<?php

use Illuminate\Database\Schema\Blueprint;

use Flarum\Database\Migration;

return Migration::createTable(
    'user_checkin_history',
    function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id')->index();
        $table->integer('event_id')->default(0)->index();//活动ID，预留
        $table->char("type", 1)->default('N')->comment("签到方式 N-正常签到 R-补充签到");
        $table->dateTime('checkin_time')->index()->comment("签到时间");
        $table->integer('reward_money')->default(0)->comment("奖励金额");
        $table->tinyInteger("constant")->default(0)->index()->comment("连续");
        $table->string("remark")->nullable()->comment("说明");
        $table->timestamp('created_at')->nullable();
    }
);
