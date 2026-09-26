<?php

/**
 * @Author: ardi.pratama@sirs.co.id
 */

namespace app\modules\api\models;
use app\components\DocoBaseModel;

use Yii;

class HasilUsgForm extends DocoBaseModel
{
    // protected $xssProtected = [
    //     'hasilusg',
    // ];

    public $hasilusg;
    public $pendaftaran_id;
    public $tgl_pemeriksaan;
    public $dokterpemeriksa_id;
    public $ruangan_id;
    public $hasil_pemeriksaan_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'hasilusg',
                    'tgl_pemeriksaan',
                    'dokterpemeriksa_id',
                    'ruangan_id',
                    'hasil_pemeriksaan_id',
                ],
                'required'
            ],
            [['dokterpemeriksa_id', 'ruangan_id', 'hasil_pemeriksaan_id'], 'integer'],
            [['tgl_pemeriksaan', 'dokterpemeriksa_id', 'hasilusg','pendaftaran_id', 'ruangan_id', 'hasil_pemeriksaan_id'], 'safe'],
            [['hasilusg'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tgl_pemeriksaan' => 'Tanggal Pemeriksaan',
            'dokterpemeriksa_id' => 'Dokter Pemeriksa',
            'hasilusg' => 'Hasil Pemeriksaan',
            'hasil_pemeriksaan_id' => 'Jenis Pemeriksaan',
        ];
    }
}
