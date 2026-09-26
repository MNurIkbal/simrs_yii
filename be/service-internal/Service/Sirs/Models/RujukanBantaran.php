<?php

namespace Integrasi\Service\Sirs\Models;

class RujukanBantaran extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rujukanbantaran_t';
    }

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return \Yii::$app->db;
    }
}
