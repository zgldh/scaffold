<?php namespace App\Scaffold\Traits;

trait HasWithParameter
{

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
