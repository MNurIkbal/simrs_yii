<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "infoorderanraddetail_v".
 *
 * @property int $permintaankepenunjang_id
 * @property int $pasienkirimkeunitlain_id
 * @property string $no_rujukan
 * @property string $jenispemeriksaanrad_nama
 * @property string $daftartindakan_nama
 * @property string $tipepaket_nama
 * @property int $qtypermintaan
 * @property bool $is_cyto
 * @property double $tarif_pelayanan
 * @property int $daftartindakan_id
 * @property int $tipepaket_id
 * @property double $tarif_cytotindakan
 * @property int $satuan_tindakan
 */
class InfoOrderanRadDetailV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoorderanraddetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['permintaankepenunjang_id', 'pasienkirimkeunitlain_id', 'qtypermintaan', 'daftartindakan_id', 'tipepaket_id', 'satuan_tindakan'], 'default', 'value' => null],
            [['permintaankepenunjang_id', 'pasienkirimkeunitlain_id', 'qtypermintaan', 'daftartindakan_id', 'tipepaket_id', 'satuan_tindakan'], 'integer'],
            [['daftartindakan_nama', 'tipepaket_nama'], 'string'],
            [['is_cyto'], 'boolean'],
            [['tarif_pelayanan', 'tarif_cytotindakan'], 'number'],
            [['no_rujukan', 'jenispemeriksaanrad_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'permintaankepenunjang_id' => 'Permintaankepenunjang ID',
            'pasienkirimkeunitlain_id' => 'Pasienkirimkeunitlain ID',
            'no_rujukan' => 'No Rujukan',
            'jenispemeriksaanrad_nama' => 'Jenispemeriksaanrad Nama',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'tipepaket_nama' => 'Tipepaket Nama',
            'qtypermintaan' => 'Qtypermintaan',
            'is_cyto' => 'Is Cyto',
            'tarif_pelayanan' => 'Tarif Pelayanan',
            'daftartindakan_id' => 'Daftartindakan ID',
            'tipepaket_id' => 'Tipepaket ID',
            'tarif_cytotindakan' => 'Tarif Cytotindakan',
            'satuan_tindakan' => 'Satuan Tindakan',
        ];
    }
}
