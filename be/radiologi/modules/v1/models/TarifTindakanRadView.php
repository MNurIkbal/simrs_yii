<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tariftindakanrad_v".
 *
 * @property int $tariftindakan_id
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $perdatarif_id
 * @property string $perdanama_sk
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $jenispemeriksaanrad_id
 * @property string $jenispemeriksaanrad_nama
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property int $pemeriksaanradiologi_id
 * @property string $pemeriksaanrad_nama
 * @property int $komponentarif_id
 * @property string $komponentarif_nama
 * @property double $harga_tariftindakan
 * @property int $persencyto_tindakan
 * @property int $persendiskon_tindakan
 * @property bool $is_default
 */
class TarifTindakanRadView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tariftindakanrad_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tariftindakan_id', 'ruangan_id', 'perdatarif_id', 'kelaspelayanan_id', 'penjamin_id', 'jenispemeriksaanrad_id', 'daftartindakan_id', 'pemeriksaanradiologi_id', 'komponentarif_id', 'persencyto_tindakan', 'persendiskon_tindakan'], 'default', 'value' => null],
            [['tariftindakan_id', 'ruangan_id', 'perdatarif_id', 'kelaspelayanan_id', 'penjamin_id', 'jenispemeriksaanrad_id', 'daftartindakan_id', 'pemeriksaanradiologi_id', 'komponentarif_id', 'persencyto_tindakan', 'persendiskon_tindakan'], 'integer'],
            [['harga_tariftindakan'], 'number'],
            [['is_default'], 'boolean'],
            [['ruangan_nama', 'kelaspelayanan_nama', 'penjamin_nama'], 'string', 'max' => 50],
            [['perdanama_sk', 'daftartindakan_nama'], 'string', 'max' => 200],
            [['jenispemeriksaanrad_nama', 'pemeriksaanrad_nama'], 'string', 'max' => 100],
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
            'perdatarif_id' => 'Perdatarif ID',
            'perdanama_sk' => 'Perdanama Sk',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'jenispemeriksaanrad_id' => 'Jenispemeriksaanrad ID',
            'jenispemeriksaanrad_nama' => 'Jenispemeriksaanrad Nama',
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'pemeriksaanradiologi_id' => 'Pemeriksaanradiologi ID',
            'pemeriksaanrad_nama' => 'Pemeriksaanrad Nama',
            'komponentarif_id' => 'Komponentarif ID',
            'komponentarif_nama' => 'Komponentarif Nama',
            'harga_tariftindakan' => 'Harga Tariftindakan',
            'persencyto_tindakan' => 'Persencyto Tindakan',
            'persendiskon_tindakan' => 'Persendiskon Tindakan',
            'is_default' => 'Is Default',
        ];
    }
}
