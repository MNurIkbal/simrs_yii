<?php

namespace app\components\Traits\Pelayanan;

use Yii;
/**
 * This is the model class for table "soaprj_t".
 *
 * @property string $kegiatan_perawat

 */

class NursingNoteForm extends \yii\base\Model
{
    public $tanggal;
    public $jam;
    public $kegiatan_perawat;
    public $catatan;
    public $active;
    public $nama_pegawai;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tanggal','jam', 'kegiatan_perawat', 'catatan','nama_pegawai'], 'required'],
            [['active'],'safe'],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tanggal' => Yii::t("fe", "Tanggal"),
            'jam' => Yii::t("fe", "Jam"),
            'kegiatan_perawat' => Yii::t("fe", "Jenis Kegiatan"),
            'catatan' => Yii::t("fe", "Catatan"),
            'active' => Yii::t("fe", " "),
            'nama_pegawai' => Yii::t("fe", " "),
        ];
    }
}
