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
class InformasiPemakaianObatalkesView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'informasipemakaianobatalkes_v';
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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pemakaianobatdetail_id' => 'Pemakaianobatdetail ID',
            'pemakaianobat_id' => 'Pemakaianobat ID',
            'tglpemakaianobat' => 'Tglpemakaianobat',
            'pegawai_id' => 'Pegawai ID',
            'nama_pegawai' => 'Nama Pegawai',
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_namalain' => 'Obatalkes Namalain',
            'qty_satuanpakai' => 'Qty Satuanpakai',
            'satuankecil_id' => 'Satuan Kecil ID',
            'satuanbesar_id' => 'Satuan Besar ID',
            'satuankecil_nama' => 'Satuan Kecil',
            'satuanbesar_nama' => 'Satuan Besar',
            'nopemakaian_obat' => 'Nomor Pemakaian',
            'keterangan_pemakaianobat' => 'Keterangan',
            'ruangan_id' => 'Ruangan'
        ];
    }
}
