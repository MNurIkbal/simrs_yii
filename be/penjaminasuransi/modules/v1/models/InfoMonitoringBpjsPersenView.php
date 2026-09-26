<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infomonitoringbpjspersen_v".
 *
 * @property string $jenis
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $no_pendaftaran
 * @property int $monitorbpjs_id
 * @property string $kelompok
 * @property int $groupinacbg_id
 * @property string $groupinacbg_nama
 * @property double $sub_total
 */
class InfoMonitoringBpjsPersenView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infomonitoringbpjspersen_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis'], 'string'],
            [['pendaftaran_id', 'pasienadmisi_id', 'monitorbpjs_id', 'groupinacbg_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'monitorbpjs_id', 'groupinacbg_id'], 'integer'],
            [['sub_total'], 'number'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['kelompok'], 'string', 'max' => 100],
            [['groupinacbg_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenis' => 'Jenis',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'monitorbpjs_id' => 'Monitorbpjs ID',
            'kelompok' => 'Kelompok',
            'groupinacbg_id' => 'Groupinacbg ID',
            'groupinacbg_nama' => 'Groupinacbg Nama',
            'sub_total' => 'Sub Total',
        ];
    }
}
