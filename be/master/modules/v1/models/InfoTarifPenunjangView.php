<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infotarifpenunjang_v".
 *
 * @property string $jenis_tindakan
 * @property int $tariftindakan_id
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $perdatarif_id
 * @property string $perdanama_sk
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $kelompokpemeriksaanlab_id
 * @property string $nama_kelompok
 * @property int $jenispemeriksaanlab_id
 * @property string $jenispemeriksaanlab_nama
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property int $pemeriksaanlab_id
 * @property string $pemeriksaanlab_nama
 * @property int $komponentarif_id
 * @property string $komponentarif_nama
 * @property double $harga_tariftindakan
 * @property int $persencyto_tindakan
 * @property int $persendiskon_tindakan
 * @property bool $is_default
 * @property int $instalasi_id
 */
class InfoTarifPenunjangView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infotarifpenunjang_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis_tindakan', 'jenispemeriksaanlab_nama', 'pemeriksaanlab_nama'], 'string'],
            [['tariftindakan_id', 'ruangan_id', 'perdatarif_id', 'kelaspelayanan_id', 'penjamin_id', 'kelompokpemeriksaanlab_id', 'jenispemeriksaanlab_id', 'daftartindakan_id', 'pemeriksaanlab_id', 'komponentarif_id', 'persencyto_tindakan', 'persendiskon_tindakan', 'instalasi_id'], 'default', 'value' => null],
            [['tariftindakan_id', 'ruangan_id', 'perdatarif_id', 'kelaspelayanan_id', 'penjamin_id', 'kelompokpemeriksaanlab_id', 'jenispemeriksaanlab_id', 'daftartindakan_id', 'pemeriksaanlab_id', 'komponentarif_id', 'persencyto_tindakan', 'persendiskon_tindakan', 'instalasi_id'], 'integer'],
            [['harga_tariftindakan'], 'number'],
            [['is_default'], 'boolean'],
            [['ruangan_nama', 'kelaspelayanan_nama', 'penjamin_nama'], 'string', 'max' => 50],
            [['perdanama_sk', 'daftartindakan_nama'], 'string', 'max' => 200],
            [['nama_kelompok'], 'string', 'max' => 255],
            [['komponentarif_nama'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenis_tindakan' => 'Jenis Tindakan',
            'tariftindakan_id' => 'Tariftindakan ID',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'perdatarif_id' => 'Perdatarif ID',
            'perdanama_sk' => 'Perdanama Sk',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'kelompokpemeriksaanlab_id' => 'Kelompokpemeriksaanlab ID',
            'nama_kelompok' => 'Nama Kelompok',
            'jenispemeriksaanlab_id' => 'Jenispemeriksaanlab ID',
            'jenispemeriksaanlab_nama' => 'Jenispemeriksaanlab Nama',
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'pemeriksaanlab_id' => 'Pemeriksaanlab ID',
            'pemeriksaanlab_nama' => 'Pemeriksaanlab Nama',
            'komponentarif_id' => 'Komponentarif ID',
            'komponentarif_nama' => 'Komponentarif Nama',
            'harga_tariftindakan' => 'Harga Tariftindakan',
            'persencyto_tindakan' => 'Persencyto Tindakan',
            'persendiskon_tindakan' => 'Persendiskon Tindakan',
            'is_default' => 'Is Default',
            'instalasi_id' => 'Instalasi ID',
        ];
    }
}
