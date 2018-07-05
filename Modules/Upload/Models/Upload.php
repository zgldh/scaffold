<?php namespace Modules\Upload\Models;

use Illuminate\Database\Eloquent\Model;
use zgldh\UploadManager\UploadManager;
use Modules\ActivityLog\Traits\LogsActivity;

/**
 * Class Upload
 * @package Modules\Upload\Models
 * @version December 13, 2016, 7:30 am UTC
 */
class Upload extends Model
{
    use LogsActivity;

    const TYPE_AVATAR = 'avatar';

    public $table = 'z_uploads';

    public $fillable = [
        'name',
        'description',
        'disk',
        'path',
        'size',
        'type',
        'user_id',
    ];

    protected static $logAttributes = [
        'name',
        'description',
        'disk',
        'path',
        'size',
        'type',
        'user_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name'            => 'string',
        'description'     => 'string',
        'disk'            => 'string',
        'path'            => 'string',
        'size'            => 'integer',
        'type'            => 'string',
        'user_id'         => 'integer',
        'uploadable_id'   => 'integer',
        'uploadable_type' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'disk' => ''
    ];

    protected $appends = array(
        'url'
    );

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function user()
    {
        return $this->belongsTo(\Modules\User\Models\User::class, 'user_id', 'id');
    }

    public function uploadable()
    {
        return $this->morphTo();
    }

    public function getUrlAttribute()
    {
        $manager = UploadManager::getInstance();
        $url = $manager->getUploadUrl($this->disk, $this->path);
        return $url;
    }

    public function deleteFile($autoSave = true)
    {
        if ($this->path) {
            $disk = \Storage::disk($this->disk);
            if ($disk->exists($this->path)) {
                $disk->delete($this->path);
                $this->path = '';
                if ($autoSave) {
                    $this->save();
                }
            }
        }
    }

    public function isInDisk($diskName)
    {
        return $this->disk == $diskName ? true : false;
    }

    public function moveToDisk($newDiskName)
    {
        if ($newDiskName == $this->disk) {
            return true;
        }
        $currentDisk = \Storage::disk($this->disk);
        $content = $currentDisk->get($this->path);

        $newDisk = \Storage::disk($newDiskName);
        $newDisk->put($this->path, $content);
        if ($newDisk->exists($this->path)) {
            $this->disk = $newDiskName;
            $this->save();
            $currentDisk->delete($this->path);
            return true;
        }
        return false;
    }

    public function getLogsActivitySubjectModel($eventName)
    {
        if ($this->uploadable) {
            return $this->uploadable;
        }
        return $this;
    }
}
