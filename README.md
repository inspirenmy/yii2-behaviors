#Yii2 behaviors library
##Description
Yii2 behaviors library which used in web-application development.



##Composition
###SanitizeBehavior

Sanitize model string attributes (all string attributes by default). You can exclude some fields or allow some html tags. 

#####Usage:

```php
// in model ActiveRecord
public function behaviors()
{
    return [
        SanitizeBehavior::className(),
    ];
}
```

###TimestampBehavior
Extends standard yii class to use with not required attributes.

#####Usage:

```php
// in model ActiveRecord
public function behaviors()
{
    return [
        TimestampBehavior::className(),
    ];
}
```

