<?php

namespace app\modules\bedah\models;

use Yii;


class RescheduleOperasiForm extends \yii\base\Model
{
    public $tgl_kirimpasien;
    public $jam_mulai;
    public $jam_selesai;
    public $dr_operator_id;
    public $dr_anestesi_id;
    public $ruangan_id;
    public $rencanaoperasi_id;
    public $alasan;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_kirimpasien','jam_mulai', 'jam_selesai', 'alasan'], 'required'],
            [['ruangan_id', 'rencanaoperasi_id', 'alasan', 'dr_operator_id', 'dr_anestesi_id'], 'safe'],
            // [['dr_anestesi_id'], 'validateDokterSama']
        ];
    }

    // public function validateDokterSama($attribute, $params)
    // {
    //     if ($this->dr_anestesi_id == $this->dr_operator_id) {
    //         $this->addError($attribute, Yii::t('fe', 'Dokter Anestesi tidak boleh sama dengan Dokter Operator'));
    //         return false;
    //     }
    // }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tgl_kirimpasien' => Yii::t("fe", "Tanggal"),
            'jam_mulai' => Yii::t("fe", "Jam Mulai"),
            'jam_selesai' => Yii::t("fe", "Jam Selesai"),
            'dr_operator_id' => Yii::t("fe", "Dokter Operator"),
            'dr_anestesi_id' => Yii::t("fe", "Dokter Anestesi"),
            'alasan' => Yii::t("fe", "Alasan"),
        ];
    }
}
