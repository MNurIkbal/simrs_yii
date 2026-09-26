<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "monitorbpjspersen_r".
 *
 * @property int $monitorbpjspersen_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $monitorbpjs_id
 * @property double $persen
 * @property double $total_persen
 */
class MonitorBpjsPersen extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'monitorbpjspersen_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id'], 'required'],
            // [['pendaftaran_id', 'pasienadmisi_id', 'monitorbpjs_id'], 'default', 'value' => null],
            [['monitorbpjspersen_id', 'pendaftaran_id', 'pasienadmisi_id', 'monitorbpjs_id'], 'integer'],
            [['persen', 'total_persen'], 'number'],
            // [['monitorbpjspersen_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'monitorbpjspersen_id' => 'Monitorbpjspersen ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'monitorbpjs_id' => 'Monitorbpjs ID',
            'persen' => 'Persen',
            'total_persen' => 'Total Persen',
        ];
    }
}
