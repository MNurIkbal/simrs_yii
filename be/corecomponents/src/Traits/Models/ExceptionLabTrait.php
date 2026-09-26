<?php

namespace Doco\Traits\Models;

use Yii;

trait ExceptionLabTrait
{
    /**
     * Override find query
     * 
     * @return Class
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function find($ignoreIsDeleted = false)
    {
        if (Yii::$app->jwt->is_all_expertise_lab) {
            return parent::find();
        } else {
            return parent::find()->andWhere([
                self::tableName() . '.is_exception' => false
            ]);
        }
    }
}
