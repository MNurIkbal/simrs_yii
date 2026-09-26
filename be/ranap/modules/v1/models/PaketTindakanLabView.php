<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pakettindakanlab_v".
 *
 * @property int $tariftindakan_id
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $tipepaket_id
 * @property string $tipepaket_nama
 * @property int $komponentarif_id
 * @property string $komponentarif_nama
 * @property double $harga_tariftindakan
 * @property int $persencyto_tindakan
 * @property double $hargadiskon_tindakan
 */
class PaketTindakanLabView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pakettindakanlab_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tariftindakan_id', 'ruangan_id', 'kelaspelayanan_id', 'penjamin_id', 'tipepaket_id', 'komponentarif_id', 'persencyto_tindakan'], 'default', 'value' => null],
            [['tariftindakan_id', 'ruangan_id', 'kelaspelayanan_id', 'penjamin_id', 'tipepaket_id', 'komponentarif_id', 'persencyto_tindakan'], 'integer'],
            [['harga_tariftindakan', 'hargadiskon_tindakan'], 'number'],
            [['ruangan_nama', 'kelaspelayanan_nama', 'penjamin_nama', 'tipepaket_nama'], 'string', 'max' => 50],
            [['komponentarif_nama'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tariftindakan_id' => 'Tariftindakan ID',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'tipepaket_id' => 'Tipepaket ID',
            'tipepaket_nama' => 'Tipepaket Nama',
            'komponentarif_id' => 'Komponentarif ID',
            'komponentarif_nama' => 'Komponentarif Nama',
            'harga_tariftindakan' => 'Harga Tariftindakan',
            'persencyto_tindakan' => 'Persencyto Tindakan',
            'hargadiskon_tindakan' => 'Hargadiskon Tindakan',
        ];
    }
    public function getPaketDetailView()
    {
        return $this->hasMany(PaketDetailView::className(), ['tipepaket_id'=>'tipepaket_id']);
    }
    public function extraFields() 
    {
        return [
          'paketdetail_v'=>function($model){
             return $model->paketDetailView;
           }
        ];
    }
}
