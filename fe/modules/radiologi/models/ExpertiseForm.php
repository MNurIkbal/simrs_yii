<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-25 11:31:44
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-25 14:39:01
 */

namespace Doco\radiologi\models;

use Yii;

class ExpertiseForm extends \yii\base\Model
{
    public $expertise_id;
    public $nama_expertise;
    public $pemeriksaanrad_id;
    public $hasil_expertise;
    public $kesan;
    public $kesimpulan;
    public $pegawai_id;

    public function rules()
    {
        return [
            [['nama_expertise','pemeriksaanrad_id'], 'required', 'message'=>'{attribute} Tidak boleh kosong'],
            [['nama_expertise'], 'trimWhitespace'],
            [['nama_expertise'], 'string', 'max' => 255],
            [['hasil_expertise', 'kesan', 'kesimpulan','expertise_id','pegawai_id'], 'safe']
        ];
    }
    public function attributeLabels()
    {
        return [
            'nama_expertise' => Yii::t('fe','Nama expertise'),
            'pemeriksaanrad_id' => Yii::t('fe','Nama pemeriksaan'),
            'hasil_expertise' => Yii::t('fe','Hasil expertise'),
            'kesan' => Yii::t('fe','Kesan'),
            'kesimpulan' => Yii::t('fe','Kesimpulan'),
            'pegawai_id' => Yii::t('fe','Dokter'),
        ];
    }
    public function trimWhitespace(){
        $nama_expertise = $this->nama_expertise;
        if (strpos(substr($nama_expertise, 0, 1), ' ') !== FALSE) {
            $this->addError('nama_expertise', 'Nama expertise mengandung spasi di awal kata');
            return false;
        }
        return true;
    }
}