<?php
/**
 * @author: dep
 * Date: 11.07.16
 */

namespace inspirenmy\behaviors;
use yii\db\ActiveRecord;
use yii\db\Expression;


/**
 * Class TimestampBehavior
 * Extends standard yii class to use with not required attributes
 * @see \yii\behaviors\TimestampBehavior
 */
class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
    /**
     * @inheritdoc
     */
    public $createdAtAttribute = 'created';

    /**
     * @inheritdoc
     */
    public $updatedAtAttribute = 'updated';



    /**
     * @inheritdoc
     * @param \yii\base\Event $event
     */
    public function evaluateAttributes($event)
    {
        if (!$this->owner instanceof ActiveRecord
            || !isset($this->owner) || !isset($this->owner->attributes)
            || $this->skipUpdateOnClean
                && $event->name == ActiveRecord::EVENT_BEFORE_UPDATE
                && empty($this->owner->dirtyAttributes)
        ) {
            return;
        }

        if (!empty($this->attributes[$event->name])) {
            $attributes = (array) $this->attributes[$event->name];
            $value = $this->getValue($event);
            foreach ($attributes as $attribute) {
                // ignore attribute names which are not string (e.g. when set by TimestampBehavior::updatedAtAttribute)
                if (is_string($attribute)
                    // or attribute exists at class
                    && array_key_exists($attribute, $this->owner->attributes)
                ) {
                    $this->owner->$attribute = $value;
                }
            }
        }
    }


    /**
     * @inheritdoc
     * @param \yii\base\Event $event
     */
    protected function getValue($event)
    {
        if ($this->value === null) {
            return new Expression('UTC_TIMESTAMP');
        }
        return parent::getValue($event);
    }
}