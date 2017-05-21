<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    public function getWith()
    {
        $with = $this->input('_with');
        if (is_string($with)) {
            return preg_split('/,/', $with);
        } elseif (is_array($with)) {
            return $with;
        }
        return [];
    }
}
