<?php

namespace app\modules\rm\models;

use Yii;

class FormKunjunganPasien extends \yii\base\Model
{

    public $koreksi_diagnosa;
    public $dokter_dpjp_id;
    public $pasien_id;
    public $data_koreksi;
    public $total_data;

    public function rules()
    {
        return [
            [['koreksi_diagnosa'], 'required'],
            [['koreksi_diagnosa','dokter_dpjp_id','pasien_id','data_koreksi','total_data'],'safe'],
            [['koreksi_diagnosa'],'checkKoreksi'],
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
