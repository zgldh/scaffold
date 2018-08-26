<?php namespace Modules\Setting\Requests;

use Modules\Setting\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use App\Scaffold\Traits\HasWithParameter;

class UpdateSettingItemRequest extends FormRequest
{
    use HasWithParameter;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return  bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return  array
     */
    public function rules()
    {
        $rules = Setting::$rules;
        return $rules;
    }

    public function attributes()
    {
        return __('setting.fields');
    }
}
