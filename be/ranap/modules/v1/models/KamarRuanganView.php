<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kamarruangan_v".
 *
 * @property int $kamartempattidur_id
 * @property int $kamarruangan_id
 * @property int $ruangan_id
 * @property int $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property string $ruangan_nama
 * @property string $kamarruangan_nokamar
 * @property string $no_tempattidur
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property bool $status_isi
 * @property int $kettempattidur_id
 * @property string $kettempattidur_nama
 * @property string $kettempattidur_warna
 * @property string $kode_warna
 */
class KamarRuanganView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kamarruangan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kamartempattidur_id', 'kamarruangan_id', 'ruangan_id', 'jeniskasuspenyakit_id', 'kelaspelayanan_id', 'kettempattidur_id'], 'default', 'value' => null],
            [['kamartempattidur_id', 'kamarruangan_id', 'ruangan_id', 'jeniskasuspenyakit_id', 'kelaspelayanan_id', 'kettempattidur_id'], 'integer'],
            [['status_isi'], 'boolean'],
            [['jeniskasuspenyakit_nama'], 'string', 'max' => 100],
            [['ruangan_nama', 'kelaspelayanan_nama'], 'string', 'max' => 50],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['no_tempattidur', 'kettempattidur_nama', 'kettempattidur_warna', 'kode_warna'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kamartempattidur_id' => 'Kamartempattidur ID',
            'kamarruangan_id' => 'Kamarruangan ID',
            'ruangan_id' => 'Ruangan ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'kamarruangan_nokamar' => 'Kamarruangan Nokamar',
            'no_tempattidur' => 'No Tempattidur',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'status_isi' => 'Status Isi',
            'kettempattidur_id' => 'Kettempattidur ID',
            'kettempattidur_nama' => 'Kettempattidur Nama',
            'kettempattidur_warna' => 'Kettempattidur Warna',
            'kode_warna' => 'Kode Warna',
        ];
    }
}
