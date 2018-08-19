<?php namespace Modules\Setting\Sets;

class System extends AbstractSet
{

    /**
     * Register any application services.
     *
     * @return  array
     */
    public function defaults()
    {
        return [
            'site_name'         => '管理平台',
            'site_introduction' => '<b>各种介绍</b>',
            'default_language'  => 'zh-CN',
            'target_planets'    => [
                'earth',
                'mars'
            ]
        ];
    }

    /**
     * 设置管理平台的名字
     * @param $newValue
     * @param $oldValue
     * @return mixed 返回该选项的最终值
     */
    public function setSiteName($newValue, $oldValue)
    {
        return $newValue;   //新的值会被应用
    }

    /**
     * 设置默认语言
     * @param $newValue
     * @param $oldValue
     * @return mixed 返回该选项的最终值
     */
    public function setDefaultLanguage($newValue, $oldValue)
    {
        return $oldValue;   // 新的值会被丢弃，default_language 将保持不变
    }
}
