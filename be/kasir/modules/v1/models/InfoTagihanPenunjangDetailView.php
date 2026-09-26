<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infotagihanpenunjangdetail_v".
 *
 * @property int $pendaftaran_id
 * @property string $tglmasukpenunjang
 * @property string $no_pendaftaran
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $tgl_tindakan
 * @property string $daftartindakan_nama
 * @property double $tarif_satuan
 * @property bool $cyto_tindakan
 * @property double $tarifcyto_tindakan
 * @property int $qty_tindakan
 * @property double $tarif_tindakan
 */
class InfoTagihanPenunjangDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infotagihanpenunjangdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'qty_tindakan'], 'default', 'value' => null],
            [['pendaftaran_id', 'qty_tindakan'], 'integer'],
            [['tglmasukpenunjang', 'tgl_tindakan'], 'safe'],
            [['tarif_satuan', 'tarifcyto_tindakan', 'tarif_tindakan'], 'number'],
            [['cyto_tindakan'], 'boolean'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['instalasi_nama', 'ruangan_nama', 'nama_pasien'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['daftartindakan_nama'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'tglmasukpenunjang' => 'Tglmasukpenunjang',
            'no_pendaftaran' => 'No Pendaftaran',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'tgl_tindakan' => 'Tgl Tindakan',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'tarif_satuan' => 'Tarif Satuan',
            'cyto_tindakan' => 'Cyto Tindakan',
            'tarifcyto_tindakan' => 'Tarifcyto Tindakan',
            'qty_tindakan' => 'Qty Tindakan',
            'tarif_tindakan' => 'Tarif Tindakan',
        ];
    }
}
