<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-06 13:51:30
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "penomoran_k".
 *
 * @property int $penomoran_id
 * @property string $penomoran_nama
 * @property string $prefix
 * @property string $last_generate
 * @property string $last_number
 * @property string $flag_refresh 0=autoincrement, 1=refresh
 */
class Penomoran extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'penomoran_k';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['penomoran_nama', 'last_generate'], 'string', 'max' => 255],
            [['prefix'], 'string', 'max' => 50],
            [['last_number'], 'string', 'max' => 100],
            [['flag_refresh'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'penomoran_id' => 'Penomoran ID',
            'penomoran_nama' => 'Penomoran Nama',
            'prefix' => 'Prefix',
            'last_generate' => 'Last Generate',
            'last_number' => 'Last Number',
            'flag_refresh' => 'Flag Refresh',
        ];
    }
}
