<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Integrasi\Components;

class IntgrateActiveRepositories extends ActiveRepositories
{

    public static function getDb() 
    {
        return \Yii::$app->db_integration;
    }

    public $_repositori;

}
