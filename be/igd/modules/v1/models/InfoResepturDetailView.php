<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-20 17:24:19
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inforesepturdetail_v".
 *
 * @property int $resepturdetail_id
 * @property int $reseptur_id
 * @property int $pendaftaran_id
 * @property int $pasien_id
 * @property int $obatalkes_id
 * @property int $satuankecil_id
 * @property int $racikan_id
 * @property int $signa_id
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $noresep
 * @property string $tglreseptur
 * @property string $racikan_nama
 * @property string $r
 * @property int $rke
 * @property string $obatalkes_nama
 * @property double $qty_reseptur
 * @property string $satuan_kecil
 * @property double $hargajual_satuan
 * @property double $totalharga_jual
 */
class InfoResepturDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inforesepturdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['resepturdetail_id', 'reseptur_id', 'pendaftaran_id', 'pasien_id', 'obatalkes_id', 'satuankecil_id', 'racikan_id', 'signa_id', 'rke'], 'default', 'value' => null],
            [['resepturdetail_id', 'reseptur_id', 'pendaftaran_id', 'pasien_id', 'obatalkes_id', 'satuankecil_id', 'racikan_id', 'signa_id', 'rke'], 'integer'],
            [['tglreseptur'], 'safe'],
            [['qty_reseptur', 'hargajual_satuan', 'totalharga_jual'], 'number'],
            [['satuan_kecil'], 'string'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'noresep', 'racikan_nama'], 'string', 'max' => 50],
            [['r'], 'string', 'max' => 2],
            [['obatalkes_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'resepturdetail_id' => 'Resepturdetail ID',
            'reseptur_id' => 'Reseptur ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasien_id' => 'Pasien ID',
            'obatalkes_id' => 'Obatalkes ID',
            'satuankecil_id' => 'Satuankecil ID',
            'racikan_id' => 'Racikan ID',
            'signa_id' => 'Signa ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'noresep' => 'Noresep',
            'tglreseptur' => 'Tglreseptur',
            'racikan_nama' => 'Racikan Nama',
            'r' => 'R',
            'rke' => 'Rke',
            'obatalkes_nama' => 'Obatalkes Nama',
            'qty_reseptur' => 'Qty Reseptur',
            'satuan_kecil' => 'Satuan Kecil',
            'hargajual_satuan' => 'Hargajual Satuan',
            'totalharga_jual' => 'Totalharga Jual',
        ];
    }
}
