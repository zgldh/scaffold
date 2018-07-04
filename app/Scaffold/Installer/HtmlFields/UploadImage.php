<?php namespace App\Scaffold\Installer\HtmlFields;

use App\Scaffold\Installer\HtmlFields\Traits\TextSearch;

/**
 * Created by PhpStorm.
 * User: zhangwb-pc
 * Date: 09/14/2017
 * Time: 18:00
 */
class UploadImage extends BaseField
{
    use TextSearch;

    public function html()
    {
        $html = <<<HTML
            <form-item :label="{$this->getFieldLang(true)}" prop="{$this->getProperty()}">
              <upload-component v-model="form.{$this->getProperty()}" list-type="picture" accept="image/*"></upload-component>
            </form-item>
HTML;

        return $html;
    }
}