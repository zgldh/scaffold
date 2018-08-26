<?php namespace Modules\Setting\Requests;

use Illuminate\Http\Request;

class UpdateSettingRequest extends Request
{
    /**
     * Is reset this bundle?
     */
    public function isReset()
    {
        return 1 == @$_GET['reset'];
    }
}
