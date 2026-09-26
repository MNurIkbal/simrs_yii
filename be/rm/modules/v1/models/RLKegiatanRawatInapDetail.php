<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rl3_1_kegiatanrawatinapdetail_v".
 *
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $no_pendaftaran
 * @property string $tgl_admisi
 * @property string $tglpasienpulang
 * @property int $carakeluar_id
 * @property string $carakeluar_nama
 * @property string $tgl_meninggal
 * @property int $lama_rawat
 * @property int $kamarruangan_id
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $jeniskasuspenyakit_id
 */
class RLKegiatanRawatInapDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rl3_1_kegiatanrawatinapdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'carakeluar_id', 'lama_rawat', 'kamarruangan_id', 'ruangan_id', 'kelaspelayanan_id', 'jeniskasuspenyakit_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'carakeluar_id', 'lama_rawat', 'kamarruangan_id', 'ruangan_id', 'kelaspelayanan_id', 'jeniskasuspenyakit_id'], 'integer'],
            [['tgl_admisi', 'tglpasienpulang', 'tgl_meninggal'], 'safe'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['carakeluar_nama'], 'string', 'max' => 100],
            [['ruangan_nama', 'kelaspelayanan_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_admisi' => 'Tgl Admisi',
            'tglpasienpulang' => 'Tglpasienpulang',
            'carakeluar_id' => 'Carakeluar ID',
            'carakeluar_nama' => 'Carakeluar Nama',
            'tgl_meninggal' => 'Tgl Meninggal',
            'lama_rawat' => 'Lama Rawat',
            'kamarruangan_id' => 'Kamarruangan ID',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
        ];
    }
}
