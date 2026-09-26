<?php
namespace app\modules\pendaftaran\models;

use Yii;
use app\components\DocoBaseModel;

class PenanggungBiayaForm extends DocoBaseModel
{
    public $penanggungbiaya_nama;
    public $namabagian;
    public $noindukkaryawan;
    public $jpkm;
    public $instansi;
    public $pasien_id;
    public $carabayar_id;
    public $penanggungbiaya_id;
    public $ruangcarabayar_id;

    public function rules()
    {
         return [
            [[
                'penanggungbiaya_nama',
                'namabagian',
                'instansi',
                'ruangcarabayar_id',
            ], 'required', 'on' => 'default'],
            [[
                'penanggungbiaya_nama',
                'namabagian',
                'instansi',
            ], 'required', 'on' => 'instansi'],
            [[
                'penanggungbiaya_nama',
                'ruangcarabayar_id',
            ], 'required', 'on' => 'rk_karyawan-personil'],
            [[
                'penanggungbiaya_nama', 
                'namabagian', 
                'noindukkaryawan', 
                'jpkm', 
                'instansi',
                'pasien_id',
                'carabayar_id',
                'penanggungbiaya_id',
                'ruangcarabayar_id',
            ], 'safe'],
            // [[
            //     'noindukkaryawan',
            // ], 'string', 'max' => 20],
            [
                'noindukkaryawan', 'checkNoIdentitas'],
        ];
    }

    public function checkNoIdentitas()
    {
        if (isset($this->noindukkaryawan) && $this->noindukkaryawan) {
            if (!preg_match("/^[0-9][0-9]*$/", $this->noindukkaryawan)) {
                # code...
                $this->addError('noindukkaryawan', 'NIP tidak berupa angka');
            }
            if (strlen($this->noindukkaryawan) > 8) {
                # code...
                $this->addError('noindukkaryawan', 'NIP tidak boleh lebih dari 8 char');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'penanggungbiaya_nama' => \Yii::t('fe', 'Nama'),
            'namabagian' => \Yii::t('fe', 'Nama Bagian'),
            'noindukkaryawan' => \Yii::t('fe', 'NIP'),
            'jpkm' => \Yii::t('fe', 'JPKM'),
            'instansi' => \Yii::t('fe', 'Instansi'),
            'pasien_id' => \Yii::t('fe', 'Pasien ID'),
            'penanggungbiaya_id' => \Yii::t('fe', 'Penanggung Biaya ID'),
            'carabayar_id' => \Yii::t('fe', 'Carabayar ID'),
            'ruangcarabayar_id' => \Yii::t('fe', 'Nama Bagian'),
        ];
    }
}
