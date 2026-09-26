<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopasienlabdetail_v".
 *
 * @property int $tindakanpelayanan_id
 * @property int $pasienmasukpenunjang_id
 * @property string $tgl_tindakan
 * @property string $jenispemeriksaanlab_nama
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property double $tarif_satuan
 * @property bool $cyto_tindakan
 * @property double $tarifcyto_tindakan
 * @property double $tarif_tindakan
 */
class InfoPasienLabDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopasienlabdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tindakanpelayanan_id', 'pasienmasukpenunjang_id', 'daftartindakan_id'], 'default', 'value' => null],
            [['tindakanpelayanan_id', 'pasienmasukpenunjang_id', 'daftartindakan_id'], 'integer'],
            [['tgl_tindakan'], 'safe'],
            [['tarif_satuan', 'tarifcyto_tindakan', 'tarif_tindakan'], 'number'],
            [['cyto_tindakan'], 'boolean'],
            [['jenispemeriksaanlab_nama'], 'string', 'max' => 30],
            [['daftartindakan_nama'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'tgl_tindakan' => 'Tgl Tindakan',
            'jenispemeriksaanlab_nama' => 'Jenispemeriksaanlab Nama',
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'tarif_satuan' => 'Tarif Satuan',
            'cyto_tindakan' => 'Cyto Tindakan',
            'tarifcyto_tindakan' => 'Tarifcyto Tindakan',
            'tarif_tindakan' => 'Tarif Tindakan',
        ];
    }
}
