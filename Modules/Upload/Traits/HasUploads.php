<?php namespace Modules\Upload\Traits;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 08/01/2017
 * Time: 15:16
 */

use Modules\Upload\Models\Upload;

trait HasUploads
{

    /**
     * A user may have multiple roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function uploads()
    {
        return $this->hasMany(Upload::class, 'user_id', 'id');
    }
}