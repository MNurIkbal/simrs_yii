<?php
namespace Doco\master\models;


class MappinganKlasifikasiKamarForm extends \yii\base\Model
{
    public $klasifikasikamar_id;
    public $ruangan_id;
    public $klasifikasikamar_nama;
    public $kamarruangan_id;
    public $kamarruangan_nokamar;
    public $ruangan_nama;
    public $kelaspelayanan_id;
    public $kelaspelayanan_nama;
    public $sirsonline_id;
    public $eiscovid_id;
    public $applicare_id;
    public $spgdt_id;
    public $sirsonline_nama;
    public $applicare_nama;
    public $spgdt_nama;

    /**
     *
     * @return void
     */
    public function rules()
    {
        return [
            [['kamarruangan_deskripsi', 'ruangan_id', 'klasifikasikamar_nama', 'kamarruangan_id', 'kamarruangan_nokamar', 'ruangan_nama', 'kelaspelayanan_id', 'kelaspelayanan_nama', 'sirsonline_id', 'eiscovid_id', 'applicare_id', 'spgdt_id', 'sirsonline_nama', 'applicare_nama', 'spgdt_nama'], 'safe'],
            [['klasifikasikamar_id', 'kamarruangan_nokamar'], 'required', 'on' => 'create'],
            [['klasifikasikamar_id', 'kamarruangan_nokamar'], 'required', 'on' => 'update'],
        ];
    }

    /**
     * @todo for attribute label form
     */
    public function attributeLabels()
    {
        return [
            'ruangan_id' => \Yii::t('fe', 'Ruangan'),
            'klasifikasikamar_id' => \Yii::t('fe', 'Klasifikasi Kamar'),
            'kamarruangan_nokamar' => \Yii::t('fe', 'Kamar'),
        ];
    }

}
