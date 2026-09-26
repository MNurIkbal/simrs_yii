<?php

namespace app\modules\master\models;
use Yii;
use app\components\DocoHelpers;


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

class TarifAkomodasiBedahForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */

    public $kegiatanoperasi_id;
    public $kelaspelayanan_id;
    public $perdatarif_id;
    public $persen_cyto;
    public $tarif;
    public $is_active;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kegiatanoperasi_id', 'kelaspelayanan_id', 'perdatarif_id', 'tarif'], 'required', 'message'=>''.Yii::t('fe','Tidak boleh kosong')],
            [['tarif'], 'number'],
            [['is_active'], 'boolean'],
            [['persen_cyto'], 'safe'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'kegiatanoperasi_id' => Yii::t('fe','Kegiatan Operasi'),
            'kelaspelayanan_id' => Yii::t('fe','Kelas Pelayanan'),
            'perdatarif_id' => 'Perda / SK',
            'tarif' => Yii::t('fe','Tarif'),
            'is_active' => Yii::t('fe','Status'),
            'persen_cyto' => Yii::t('fe','Persen Cyto'),
        ];
    }

}
