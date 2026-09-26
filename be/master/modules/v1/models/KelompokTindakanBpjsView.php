<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "monitorbpjs_v".
 *
 * @property int $monitorbpjs_id
 * @property string $kelompoktindakan_nama
 * @property string $monitorbpjsdetail
 * @property bool $is_active
 */
class KelompokTindakanBpjsView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'monitorbpjs_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['monitorbpjs_id', 'kelompoktindakan_nama', 'monitorbpjsdetail'], 'default', 'value' => null],
            [['monitorbpjs_id'], 'integer'],
            [['kelompoktindakan_nama'], 'string'],
            [['is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'monitorbpjs_id' => 'Monitoring BPJS ID',
            'monitorbpjsdetail' => 'Inacbgs Detail',
            'kelompoktindakan_nama' => 'Nama Kelompok Tindakan',
            'is_active' => 'Is Active',
        ];
    }
}
