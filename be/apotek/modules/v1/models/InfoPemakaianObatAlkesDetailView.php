<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopemakaianobatalkesdetail_v".
 *
 * @property int $pemakaianobatdetail_id
 * @property int $pemakaianobat_id
 * @property string $tglpemakaianobat
 * @property int $ruangan_id
 * @property int $pegawai_id
 * @property string $nama_pegawai
 * @property int $obatalkes_id
 * @property string $obatalkes_namalain
 * @property string $qty_satuanpakai
 * @property int $satuankecil_id
 * @property string $satuankecil_nama
 * @property string $nopemakaian_obat
 * @property double $jumlah_input
 * @property int $satuanbesar_id
 * @property string $satuanbesar_nama
 * @property string $keterangan_pemakaianobat
 */
class InfoPemakaianObatAlkesDetailView extends \Doco\components\DocoActiveRecord
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
            [['pemakaianobatdetail_id', 'pemakaianobat_id', 'ruangan_id', 'pegawai_id', 'obatalkes_id', 'satuankecil_id', 'satuanbesar_id'], 'default', 'value' => null],
            [['pemakaianobatdetail_id', 'pemakaianobat_id', 'ruangan_id', 'pegawai_id', 'obatalkes_id', 'satuankecil_id', 'satuanbesar_id'], 'integer'],
            [['tglpemakaianobat'], 'safe'],
            [['obatalkes_namalain', 'satuankecil_nama', 'satuanbesar_nama', 'keterangan_pemakaianobat'], 'string'],
            [['qty_satuanpakai', 'jumlah_input'], 'number'],
            [['nama_pegawai'], 'string', 'max' => 50],
            [['nopemakaian_obat'], 'string', 'max' => 20],
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
            'ruangan_id' => 'Ruangan ID',
            'pegawai_id' => 'Pegawai ID',
            'nama_pegawai' => 'Nama Pegawai',
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_namalain' => 'Obatalkes Namalain',
            'qty_satuanpakai' => 'Qty Satuanpakai',
            'satuankecil_id' => 'Satuankecil ID',
            'satuankecil_nama' => 'Satuankecil Nama',
            'nopemakaian_obat' => 'Nopemakaian Obat',
            'jumlah_input' => 'Jumlah Input',
            'satuanbesar_id' => 'Satuanbesar ID',
            'satuanbesar_nama' => 'Satuanbesar Nama',
            'keterangan_pemakaianobat' => 'Keterangan Pemakaianobat',
        ];
    }
}
