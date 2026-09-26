<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "informasipemakaianobatalkes_v".
 *
 * @property int $pemakaianobatdetail_id
 * @property int $pemakaianobat_id
 * @property string $tglpemakaianobat
 * @property int $pegawai_id
 * @property string $nama_pegawai
 * @property int $obatalkes_id
 * @property string $obatalkes_namalain
 * @property string $qty_satuanpakai
 * @property int $satuankecil_id
 * @property string $satuankecil_nama
 */
class InformasiPemakaianObatalkesDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infopemakaianobatalkesdetail_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pemakaianobatdetail_id', 'pemakaianobat_id', 'pegawai_id', 'obatalkes_id', 'satuankecil_id','ruangan_id'], 'default', 'value' => null],
            [['pemakaianobatdetail_id', 'pemakaianobat_id', 'pegawai_id', 'obatalkes_id', 'satuankecil_id','ruangan_id'], 'integer'],
            [['tglpemakaianobat'], 'safe'],
            [['obatalkes_namalain', 'satuankecil_nama'], 'string'],
            [['qty_satuanpakai'], 'number'],
            [['nama_pegawai'], 'string', 'max' => 50],
        ];
    }

}
