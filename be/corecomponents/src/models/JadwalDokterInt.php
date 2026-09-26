<?php

namespace Doco\models;

use Yii;

class JadwalDokterInt extends \yii\db\ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db_integration;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jadwaldokter_int';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jadwaldokter_id'], 'required'],
            [[
                'jadwaldokter_id',
                'is_sent',
                'is_sending',
                'id_sync_sercon',
                'sync_respon',
                'state',
                'created_date',
                'created_by',
                'payload',
            ], 'safe'],
            [[
                'is_sent',
                'is_sending',
            ], 'default', 'value' => false]
        ];
    }

    public static function batchInsert($data = [])
    {
        $tableName = self::getTableSchema()->name;
        $className = get_called_class();
        $primary = self::primaryKey();
        $newModel = new $className;
        $attributes = $newModel->attributes();
        $rows = [];
        foreach ($data as $key => $value) {
            $newModel = new $className;
            $newModel->attributes = $value;
            $attr = $newModel->attributes;

            if (isset($primary[0])) {
                unset($attr[$primary[0]]);
            }

            $rows[] = $attr;
        }
        foreach ($attributes as $key => $value) {
            if (isset($primary[0]) && $value == $primary[0]) {
                unset($attributes[$key]);
                break;
            }
        }
        return Yii::$app->db_integration->createCommand()->batchInsert($tableName,$attributes, $rows)->execute();
    }

}
