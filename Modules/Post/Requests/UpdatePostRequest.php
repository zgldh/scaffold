<?php namespace Modules\Post\Requests;

use Modules\Post\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use App\Scaffold\Traits\HasWithParameter;

class UpdatePostRequest extends FormRequest
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
        $rules = Post::$rules;
        $item = Post::find($this->route()->parameters['post']);
        $itemId = $item->id;
        $rules['title'] = "required|unique:posts,title,$itemId";
        return [];
        return $rules;
    }

    public function attributes()
    {
        return __('post.fields');
    }
}
