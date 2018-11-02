<?php namespace Modules\Post\Repositories;

use Modules\Post\Models\Post;
use App\Scaffold\BaseRepository;

class PostRepository extends BaseRepository
{
    /**
     * @var  array
     */
    protected $fieldSearchable = [
        "title",
        "content",
        "password",
        "email",
        "category",
        "status",
        "created_by",
        "comments"
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Post::class;
    }
}
