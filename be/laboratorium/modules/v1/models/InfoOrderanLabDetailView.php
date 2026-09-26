<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoorderanlabdetail_v".
 *
 * @property int $permintaankepenunjang_id
 * @property int $pasienkirimkeunitlain_id
 * @property string $no_rujukan
 * @property string $jenispemeriksaanlab_nama
 * @property string $daftartindakan_nama
 * @property int $qtypermintaan
 * @property bool $is_cyto
 */
class InfoOrderanLabDetailView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoorderanlabdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['permintaankepenunjang_id', 'pasienkirimkeunitlain_id', 'qtypermintaan'], 'default', 'value' => null],
            [['permintaankepenunjang_id', 'pasienkirimkeunitlain_id', 'qtypermintaan'], 'integer'],
            [['is_cyto'], 'boolean'],
            [['no_rujukan'], 'string', 'max' => 100],
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
            'permintaankepenunjang_id' => 'Permintaankepenunjang ID',
            'pasienkirimkeunitlain_id' => 'Pasienkirimkeunitlain ID',
            'no_rujukan' => 'No Rujukan',
            'jenispemeriksaanlab_nama' => 'Jenispemeriksaanlab Nama',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'qtypermintaan' => 'Qtypermintaan',
            'is_cyto' => 'Is Cyto',
        ];
    }
}
