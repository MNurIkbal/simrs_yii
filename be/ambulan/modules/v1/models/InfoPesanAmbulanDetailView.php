<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopesanambulandetail_v".
 *
 * @property int $pesanambulan_id
 * @property string $tgl_pesanambulan
 * @property string $no_pesanambulan
 * @property int $pendaftaran_id
 * @property string $no_polisi
 * @property string $jenis_ambulan
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property int $qty_tindakan
 * @property int $obatalkes_id
 * @property string $obatalkes_nama
 * @property int $qty_obat
 * @property int $satuankecil_id
 */
class InfoPesanAmbulanDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopesanambulandetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pesanambulan_id', 'pendaftaran_id', 'daftartindakan_id', 'qty_tindakan', 'obatalkes_id', 'qty_obat', 'satuankecil_id'], 'default', 'value' => null],
            [['pesanambulan_id', 'pendaftaran_id', 'daftartindakan_id', 'qty_tindakan', 'obatalkes_id', 'qty_obat', 'satuankecil_id'], 'integer'],
            [['tgl_pesanambulan'], 'safe'],
            [['jenis_ambulan'], 'string'],
            [['no_pesanambulan'], 'string', 'max' => 100],
            [['no_polisi'], 'string', 'max' => 20],
            [['daftartindakan_nama'], 'string', 'max' => 200],
            [['obatalkes_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pesanambulan_id' => 'Pesanambulan ID',
            'tgl_pesanambulan' => 'Tgl Pesanambulan',
            'no_pesanambulan' => 'No Pesanambulan',
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_polisi' => 'No Polisi',
            'jenis_ambulan' => 'Jenis Ambulan',
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'qty_tindakan' => 'Qty Tindakan',
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_nama' => 'Obatalkes Nama',
            'qty_obat' => 'Qty Obat',
            'satuankecil_id' => 'Satuankecil ID',
        ];
    }
}
