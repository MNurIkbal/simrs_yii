<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inpostoperasi2_v".
 *
 * @property int $inpostoperasi_id
 * @property int $pasienmasukpenunjang_id
 * @property int $tindakanpelayanan_id
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property bool $cyto_tindakan
 * @property string $golonganoperasi_nama
 * @property string $jenis_luka
 * @property string $jenisanastesi_nama
 */
class PelayananOperasiView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inpostoperasi2_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'tindakanpelayanan_id', 'daftartindakan_id'], 'default', 'value' => null],
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'tindakanpelayanan_id', 'daftartindakan_id'], 'integer'],
            [['cyto_tindakan'], 'boolean'],
            [['jenis_luka', 'jenisanastesi_nama'], 'string'],
            [['daftartindakan_nama'], 'string', 'max' => 200],
            [['golonganoperasi_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'inpostoperasi_id' => 'Inpostoperasi ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'cyto_tindakan' => 'Cyto Tindakan',
            'golonganoperasi_nama' => 'Golonganoperasi Nama',
            'jenis_luka' => 'Jenis Luka',
            'jenisanastesi_nama' => 'Jenisanastesi Nama',
        ];
    }
}
