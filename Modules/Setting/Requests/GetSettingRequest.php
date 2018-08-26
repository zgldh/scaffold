<?php namespace Modules\Setting\Requests;

use Illuminate\Http\Request;

class GetSettingRequest extends Request
{
    /**
     * Is getting default settings
     */
    public function isDefault()
    {
        return 1 == @$_GET['d'];
    }
}
