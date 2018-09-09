<?php namespace App\Scaffold\Traits;

trait HasWithParameter
{

    public function getWith()
    {
        $with = $this->input('_with');
        $result = [];
        if (is_string($with) && $with) {
            $result = preg_split('/,/', $with);
        } elseif (is_array($with)) {
            $result = $with;
        }
        return $result;
    }
}
