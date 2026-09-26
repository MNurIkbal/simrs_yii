<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Integrasi\Components;

class ActiveRepositories extends DocoActiveRecord
{

    public $_repositori;

    public static function find($ignoreIsDeleted=false)
    {
        $className = get_called_class();
        $newModel = new $className;
        if (!empty($newModel->_repositori)) {
            $model = new $newModel->_repositori(get_called_class());
        } else {
            $model = parent::find($ignoreIsDeleted);
        }
        if ($ignoreIsDeleted == true) return $model;

        $table = self::getTableSchema()->name;
        $attributes = $newModel->attributes();
        if (in_array('is_deleted', $attributes)) {
            return $model->onCondition([$table.'.is_deleted' => false]);
        }

        return $model;
    }
}
