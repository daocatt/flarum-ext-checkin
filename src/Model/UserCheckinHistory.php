<?php

namespace Gtdxyz\Checkin\Model;

use Flarum\Database\AbstractModel;
use Flarum\Formatter\Formatter;
use Flarum\User\User;

class UserCheckinHistory extends AbstractModel
{
    protected $table = "user_checkin_history";


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at'];
    
    /**
     * The text formatter instance.
     *
     * @var \Flarum\Formatter\Formatter
     */
    protected static $formatter;

    /**
     * Get the text formatter instance.
     *
     * @return \Flarum\Formatter\Formatter
     */
    public static function getFormatter()
    {
        return static::$formatter;
    }

    /**
     * Set the text formatter instance.
     *
     * @param \Flarum\Formatter\Formatter $formatter
     */
    public static function setFormatter(Formatter $formatter)
    {
        static::$formatter = $formatter;
    }

    public function User(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
