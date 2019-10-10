<?php

namespace app\components\db;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

/**
 * Class ActiveRecord
 *
 * @package app\components\db
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $requiredBehaviors = [
            TimestampBehavior::class,
        ];

        if (!(Yii::$app->controller instanceof Controller) && !(Yii::$app instanceof \yii\console\Application)) {
            $columns = array_keys($this->attributes);

            if (array_search('created_by', $columns) && array_search('created_by', $columns)) {
                $requiredBehaviors[] = BlameableBehavior::class;
            }
        }

        return ArrayHelper::merge($requiredBehaviors, parent::behaviors());
    }
}
