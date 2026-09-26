<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopasienraddetail_v".
 *
 * @property string $jenis
 * @property int $tindakanpelayanan_id
 * @property int $pasienmasukpenunjang_id
 * @property string $tgl_tindakan
 * @property string $jenispemeriksaanrad_nama
 * @property int $tipepaket_id
 * @property string $tipepaket_nama
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property double $tarif_satuan
 * @property bool $cyto_tindakan
 * @property double $tarifcyto_tindakan
 * @property double $tarif_tindakan
 * @property int $qty_tindakan
 */
class InfoPasienRadDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopasienraddetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis', 'tipepaket_nama'], 'string'],
            [['tindakanpelayanan_id', 'pasienmasukpenunjang_id', 'tipepaket_id', 'daftartindakan_id', 'qty_tindakan'], 'default', 'value' => null],
            [['tindakanpelayanan_id', 'pasienmasukpenunjang_id', 'tipepaket_id', 'daftartindakan_id', 'qty_tindakan'], 'integer'],
            [['tgl_tindakan'], 'safe'],
            [['tarif_satuan', 'tarifcyto_tindakan', 'tarif_tindakan'], 'number'],
            [['cyto_tindakan'], 'boolean'],
            [['jenispemeriksaanrad_nama'], 'string', 'max' => 100],
            [['daftartindakan_nama'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenis' => 'Jenis',
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'tgl_tindakan' => 'Tgl Tindakan',
            'jenispemeriksaanrad_nama' => 'Jenispemeriksaanrad Nama',
            'tipepaket_id' => 'Tipepaket ID',
            'tipepaket_nama' => 'Tipepaket Nama',
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'tarif_satuan' => 'Tarif Satuan',
            'cyto_tindakan' => 'Cyto Tindakan',
            'tarifcyto_tindakan' => 'Tarifcyto Tindakan',
            'tarif_tindakan' => 'Tarif Tindakan',
            'qty_tindakan' => 'Qty Tindakan',
        ];
    }
}
