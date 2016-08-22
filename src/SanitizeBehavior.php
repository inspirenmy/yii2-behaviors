<?php
/**
 * @author: dep
 * Date: 12.07.16
 */

namespace demmonico\behaviors;
use yii\base\Behavior;
use yii\base\Model;
use yii\helpers\Html;


/**
 * Sanitize model string attributes
 */
class SanitizeBehavior extends Behavior
{
    const SCENARIO_SEARCH = 'search';
    
    /**
     * List of safe attributes
     * @var array
     */
    public $htmlSafeAttributes = [];
    /**
     * String which contain allowed tags
     * @var string
     * @see strip_tags
     */
    public $allowedHtmlTags;

    /**
     * Flag whether encoding string attributes
     * @var bool
     */
    public $isEncodeStringAttributes = true;



    /**
     * @inheritdoc
     */
    public function attach($owner)
    {
        if (!$owner instanceof Model)
            throw new \Exception('Invalid model');
        parent::attach($owner);
    }

    /**
     * @param string|array|null $attribute
     * @return array|mixed
     */
    public function decodeAttribute($attribute=null)
    {
        // get string attributes
        $stringAttributes = $this->getStringAttributes(false);

        // decode all attributes if param $attribute is null
        if (is_null($attribute)){
            foreach($this->owner->attributes as $k=>$v){
                if (in_array($k, $stringAttributes))
                    $this->owner->{$k} = Html::decode($v);
            }
            return $this->owner->attributes;

        // decode selected attributes
        } elseif (is_array($attribute)) {
            $r = [];
            foreach($attribute as $k){
                if (in_array($k, $stringAttributes))
                    $this->owner->{$k} = Html::decode($this->owner->{$k});
                $r[$k] = $this->owner->{$k};
            }
            return $r;

        // decode selected attribute
        } else {
            if (in_array($attribute, $stringAttributes))
                $this->owner->{$attribute} = Html::decode($this->owner->{$attribute});
            return $this->owner->{$attribute};
        }
    }

    /**
     * Returns array of string attributes of this model
     * @param bool $onlyHtmlUnsafeAttributes
     * @return array
     */
    protected function getStringAttributes($onlyHtmlUnsafeAttributes=true)
    {
        $r = [];
        foreach($this->owner->attributes as $k=>$v){
            if (!$onlyHtmlUnsafeAttributes || !in_array($k, $this->htmlSafeAttributes)
                AND !empty($v) AND is_string($v) AND !ctype_digit($v)
            ) {
                $r[] = $k;
            }
        }
        return $r;
    }


    
    /**
     * Returns sanitize rules array
     * @return array
     */
    public function getSanitizeRules()
    {
        $r = [];
        if (!isset($this->owner->scenario) || $this->owner->scenario !== self::SCENARIO_SEARCH)
            $r[] = $this->getSanitizeRule();
        if ($this->isEncodeStringAttributes)
            $r[] = $this->getEncodeRule();
        return $r;
    }

    /**
     * Returns sanitize validation rule
     * @return array
     */
    protected function getSanitizeRule()
    {
        return [$this->getStringAttributes(), 'filter', 'filter' => function($value){
            return trim(strip_tags($value, $this->allowedHtmlTags))?:null;
        }];
    }

    /**
     * Returns encode validation rule
     * @return array
     */
    protected function getEncodeRule()
    {
        return [$this->getStringAttributes(false), 'filter', 'filter' => function($value){
            return Html::encode($value, false)?:null;
        }];
    }
}