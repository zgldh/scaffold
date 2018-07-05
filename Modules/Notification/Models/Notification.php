<?php namespace Modules\Notification\Models;

use Illuminate\Notifications\DatabaseNotification;
use Modules\ActivityLog\Traits\LogsActivity;

class Notification extends DatabaseNotification
{
    use LogsActivity;

    public $fillable = [
        "read_at"
    ];

    protected static $logAttributes = [
        "type",
        "data",
        "read_at"
    ];

    /**
     * Validation rules
     *
     * 提醒的标题
     * 提醒的目标链接
     * 提醒发起人
     * 提醒正文，存成HTML了。
     * data =>[
     *      'url_title'=>'abcdefg',
     *      'url'=>'/post/233/comments/998',
     *      'notifier_id'=>1234, null is system
     *      'content'=>'<html></html>
     * ]
     *
     * @var  array
     */
    public static $rules = [
        'type'    => 'required',
        'data'    => 'required',
        'read_at' => '',
    ];
}
