<?php namespace Modules\ActivityLog\Traits;

use Spatie\Activitylog\ActivityLogger;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use  Spatie\Activitylog\Traits\LogsActivity as BasicLogsActivity;

trait LogsActivity
{
    use BasicLogsActivity {
        BasicLogsActivity::bootLogsActivity as basicBootLogsActivity;
        BasicLogsActivity::attributeValuesToBeLogged as basicAttributeValuesToBeLogged;
    }

    protected $enableLoggingModelsEvents = true;

    public function getOldAttributes()
    {
        return $this->oldAttributes;
    }

    protected static function bootLogsActivity()
    {
        static::eventsToBeRecorded()->each(function ($eventName) {
            return static::$eventName(function (Model $model) use ($eventName) {
                if (!$model->shouldLogEvent($eventName)) {
                    return;
                }

                $description = $model->getDescriptionForEvent($eventName);

                $logName = $model->getLogNameToUse($eventName);

                if ($description == '') {
                    return;
                }

                $log = app(ActivityLogger::class)
                    ->useLog($logName)
                    ->performedOn($model)
                    ->withProperties($model->attributeValuesToBeLogged($eventName))
                    ->log($description);

                $subjectModel = $model->getLogsActivitySubjectModel($eventName);
                $log->collector_type = get_class($subjectModel);
                $log->collector_id = $subjectModel->getKey();
                $log->save();
            });
        });
    }

    /**
     * Get activity logs subject model
     * @param $eventName
     * @return $this
     */
    protected function getLogsActivitySubjectModel($eventName)
    {
        return $this;
    }

    /**
     * @return MorphMany
     */
    public function activity_logs(): MorphMany
    {
        return $this->morphMany(ActivitylogServiceProvider::determineActivityModel(), 'collector');
    }

    public function attributeValuesToBeLogged(string $processingEvent): array
    {
        if (!count($this->attributesToBeLogged())) {
            return [];
        }

        $properties['attributes'] = static::logChanges(
            $this->exists
                ? $this->fresh() ?? $this
                : $this
        );

        if (static::eventsToBeRecorded()->contains('updated') && $processingEvent == 'updated') {
            $nullProperties = array_fill_keys(array_keys($properties['attributes']), null);

            $properties['old'] = array_merge($nullProperties, $this->oldAttributes);
        }

        if ($this->shouldlogOnlyDirty() && isset($properties['old'])) {
            $properties['attributes'] = array_udiff_assoc(
                $properties['attributes'],
                $properties['old'],
                function ($new, $old) {
                    return $new <=> $old;
                }
            );
            $properties['old'] = collect($properties['old'])
                ->only(array_keys($properties['attributes']))
                ->all();
        }

        foreach ($properties['attributes'] as $newKey => $newValue) {
            if (isset($properties['old']) && $properties['old'][$newKey] == $newValue) {
                unset($properties['attributes'][$newKey]);
                unset($properties['old'][$newKey]);
            }
        }

        return $properties;
    }
}
