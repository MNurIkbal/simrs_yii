<?php

namespace Doco\penjaminasuransi\models;
use Yii;

class FormKunjunganPasien extends \yii\base\Model
{

    public $koreksi_diagnosa;
    public $koreksi_diagnosa_ina;
    public $dokter_nama;
    public $kunjungan_id;
    public $data_koreksi;
    public $total_data;
    public $is_inacbg;
    public $is_icdprimer;
    public $is_icdprimer_ina;
    public $dokterdpjp_id;

    public function rules()
    {
        return [
            [['koreksi_diagnosa'], 'required'],
            [['koreksi_diagnosa','dokter_nama','kunjungan_id','data_koreksi','total_data', 'is_inacbg', 'is_icdprimer', 'dokterdpjp_id'],'safe'],
        ];
    }

    public function checkKoreksi($params, $attributes)
    {
        // if (count($this->koreksi_diagnosa) < $this->total_data) {
        //     $this->addError('koreksi_diagnosa','Data koreksi tidak boleh ada yang kosong');
        //     return false;
        // }
        if (isset($this->koreksi_diagnosa[0]) && empty($this->koreksi_diagnosa[0])) {

        }
        if (is_array($this->koreksi_diagnosa)) {
            // foreach ($this->koreksi_diagnosa as $value) {
            //     if (empty($value)) {
            //         $this->addError('koreksi_diagnosa','Data koreksi tidak boleh ada yang kosong');
            //     }
            // }
            if($this->koreksi_diagnosa[0] == $this->koreksi_diagnosa[1]){
                $this->addError('koreksi_diagnosa','Diagnosa Utama dan Penyerta Tidak Boleh Sama');
            }
        }
    }

}
