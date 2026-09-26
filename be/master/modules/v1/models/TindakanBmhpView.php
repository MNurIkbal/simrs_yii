<?php

/**
 * @Author: Wahyu Saepuloh
 * @Date:   5 November 2019
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tindakanbmhp_v".
 *
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property int $obatalkes_id
 * @property int $satuaninput_id
 * @property int $satuanunit_id
 * @property int $nilai_konversi
 * @property string $obatalkes_nama
 * @property string $satuaninput_nama
 * @property string $satuanunit_nama
 */
class TindakanBmhpView extends \Doco\components\DocoActiveRecord
{
    public static function primaryKey()
	{
		return ['daftartindakan_id'];
    }
    /**
     * {@inheritdoc}
     */
    protected $xssProtected = [
        'daftartindakan_nama', 
        'obatalkes_nama', 
        'satuaninput_nama', 
        'satuanunit_nama',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tindakanbmhp_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['daftartindakan_id', 'obatalkes_id'], 'required'],
            [['daftartindakan_id', 'daftartindakan_nama', 'obatalkes_id', 'satuaninput_id', 'satuanunit_id', 'nilai_konversi', 'obatalkes_nama', 'satuaninput_nama', 'satuanunit_nama'], 'default', 'value' => null],
            [['daftartindakan_id', 'obatalkes_id', 'satuaninput_id', 'satuanunit_id'], 'integer'],
            [['daftartindakan_nama', 'obatalkes_nama', 'satuaninput_nama', 'satuanunit_nama'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_nama' => 'Nama Daftar Tindakan',
            // 'obatalkes_id' => 'Obat Alkes ID',
            // 'satuaninput_id' => 'Satuan Input ID',
            // 'satuanunit_id' => 'Satuan Unit ID',
            // 'nilai_konversi' => 'Nilai Konversi',
            // 'obatalkes_nama' => 'Nama Obat Alkes',
            // 'satuaninput_nama' => 'Satuan Input Nama',
            // 'satuanunit_nama' => 'Satuan Unit Nama',
        ];
    }
}
