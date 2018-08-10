<?php namespace App\Http\Requests;

use App\Scaffold\Traits\HasWithParameter;
use Dingo\Api\Http\FormRequest;

class IndexRequest extends FormRequest
{
    use HasWithParameter;

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

    public function getColumns()
    {
        return $this->input('columns', []);
    }

    public function getExportFileName()
    {
        return $this->input('_export', null);
    }
}
