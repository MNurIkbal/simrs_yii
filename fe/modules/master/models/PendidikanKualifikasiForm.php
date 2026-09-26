<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "pendidikankualifikasi_m".
 *
 * @property integer $pendidikan_id
 * @property integer $kelompokpegawai_id
 * @property string $pendkualifikasi_kode
 * @property string $pendkualifikasi_nama
 * @property string $pendkualifikasi_namalainnya
 * @property integer $jmlkeblaki
 * @property integer $jmlkebperempuan
 * @property string $additional_data
 */
class PendidikanKualifikasiForm extends \yii\base\Model
{
    
    public $pendidikan_id;
    public $kelompokpegawai_id;
    public $pendkualifikasi_kode;
    public $pendkualifikasi_nama;
    public $pendkualifikasi_namalainnya;
    public $jmlkeblaki;
    public $jmlkebperempuan;
    public $is_active;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendidikan_id', 'kelompokpegawai_id', 'pendkualifikasi_kode', 'pendkualifikasi_nama'], 'required'],
            [['pendidikan_id', 'kelompokpegawai_id', 'jmlkeblaki', 'jmlkebperempuan', ], 'integer'],
            [['pendkualifikasi_kode', 'pendkualifikasi_nama', 'pendkualifikasi_namalainnya'], 'string'],
            [['is_active'], 'boolean'],
            [['pendkualifikasi_kode'], 'string', 'max' => 10],
            [['pendkualifikasi_nama', 'pendkualifikasi_namalainnya'], 'string', 'max' => 100],
            [['pendkualifikasi_kode'], 'trimWhitespace'],
        ];
    }

    public function trimWhitespace(){
        $pendkualifikasi_kode = $this->pendkualifikasi_kode;
        $return = true;
        if (strpos(substr($pendkualifikasi_kode, 0, 1), ' ') !== FALSE) {
            $this->addError('pendkualifikasi_kode', 'Kode mengandung spasi di awal kata');
            $return = false;
        }
        
        return $return;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendidikan_id' => Yii::t('fe','Pendidikan'),
            'kelompokpegawai_id' => Yii::t('fe','Kelompok Pegawai'),
            'pendkualifikasi_kode' => Yii::t('fe','Kode Kualifikasi'),
            'pendkualifikasi_nama' => Yii::t('fe','Nama Kualifikasi'),
            'pendkualifikasi_namalainnya' => Yii::t('fe','Nama Lainnya'),
            // 'pendkualifikasi_keterangan' => Yii::t('fe','Keterangan'),
            'jmlkeblaki' => Yii::t('fe','Jumlah Kebutuhan Laki-laki'),
            'jmlkebperempuan' => Yii::t('fe','Jumlah Kebutuhan Perempuan'),
            'is_active' => Yii::t('fe','Status'),
        ];
    }
}
