<?php namespace Modules\Upload\Traits;

use Modules\Upload\Models\Upload;

trait EditorUploadImage
{
    public function updateImage($newContent, $oldContent, $uploadableId)
    {
        $oldUploadIds = $this->getUploadIds($newContent, $oldContent);
        $newUploadIds = $this->getUploadIds($oldContent, $newContent);

        $upload = new Upload();
        $upload->whereIn('id', $oldUploadIds)->delete();
        $upload->whereIn('id', $newUploadIds)->update(['type' => 'editor', 'uploadable_type' => $this->model(), 'uploadable_id' => $uploadableId]);
    }

    public function getUploadIds($newContent, $oldContent)
    {
        preg_match_all('/src="(.*?)"/', $oldContent, $oldResults);
        preg_match_all('/src="(.*?)"/', $newContent, $newResults);

        $scrapImgOldPath = array_diff($oldResults[1], $newResults[1]);

        $scrapImgOldPath = array_map(function ($v) {
            $pathArray = explode('/', $v);
            return $pathArray[count($pathArray) - 2] . '/' . $pathArray[count($pathArray) - 1];
        }, $scrapImgOldPath);

        $uploadOldRows = Upload::whereIn('path', $scrapImgOldPath)->get();
        $oldUploadIds = array();
        foreach ($uploadOldRows as $uploadOldRow) {
            array_push($oldUploadIds, $uploadOldRow['id']);
        }

        return $oldUploadIds;
    }
}
