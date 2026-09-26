<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infotarifbedah_v".
 *
 * @property string $kegiatanoperasi_nama
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $tarifbedah_id
 * @property int $kegiatanoperasi_id
 * @property int $perdatarif_id
 * @property string $perdanama_sk
 * @property int $persen_cyto
 * @property double $tarif
 * @property bool $is_active
 * @property datetime $created_date
 */
class MasterTarifAkomodasiBedahView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infotarifbedah_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kegiatanoperasi_nama', 'kelaspelayanan_nama', 'perdanama_sk'], 'string'],
            [['tarifbedah_id', 'kegiatanoperasi_id', 'kelaspelayanan_id', 'perdatarif_id', 'persen_cyto'], 'integer'],
            [['tarif'], 'number'],
            [['is_active'], 'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tarifbedah_id' => 'Tarif Bedah Id',
            'kegiatanoperasi_id' => 'Tariftindakan ID',
            'kegiatanoperasi_nama' => 'Tindakan Paket ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'perdatarif_id' => 'Perdatarif ID',
            'perdanama_sk' => 'Perdanama Sk',
            'persen_cyto' => 'Persen cyto ',
            'is_active' => 'Is Active',
            'created_date' => 'Created Date',
        ];
    }
}
