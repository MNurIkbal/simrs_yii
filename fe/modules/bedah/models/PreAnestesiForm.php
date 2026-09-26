<?php

namespace app\modules\bedah\models;

class PreAnestesiForm extends \yii\base\Model
{
    public $pasienmasukpenunjang_id;
    public $ruangan_id;
    public $surgical_procedure;
    public $surgical_status;
    public $tinggi_badan;
    public $berat_badan;
    public $suhu_tubuh;
    public $pernafasan;
    public $tekanan_darah_systolic;
    public $tekanan_darah_diastolic;
    public $nevous_system;
    public $urinary_tracking_system;
    public $cardiovascular_system;
    public $gastrointestinal_system;
    public $metabolic_system;
    public $coexist_condition;
    public $respitory_system;
    public $musculo_skeletal_system;
    public $status_asa;
    public $medication_taken;
    public $type_1;
    public $type_2;
    public $type_3;
    public $size;
    public $fluite_1;
    public $fluite_2;
    public $fluite_3;
    public $rate_of_influsion_1;
    public $rate_of_influsion_2;
    public $rate_of_influsion_3;
    public $fluid_left_1;
    public $fluid_left_2;
    public $fluid_left_3;


    public function rules()
    {
        return [
            [['pasienmasukpenunjang_id','ruangan_id','surgical_procedure','surgical_status','tinggi_badan','berat_badan','suhu_tubuh','pernafasan','tekanan_darah_systolic','tekanan_darah_diastolic','nevous_system','urinary_tracking_system','cardiovascular_system','gastrointestinal_system','metabolic_system','coexist_condition','respitory_system','musculo_skeletal_system','status_asa', 'medication_taken','type_1', 'type_2', 'type_3','size','fluite_1','fluite_2','fluite_3','rate_of_influsion_1','rate_of_influsion_2', 'rate_of_influsion_3', 'fluid_left_1','fluid_left_2','fluid_left_3' ], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            "pasienmasukpenunjang_id" => "Pasien ID",
            'ruangan_id' => 'Ruangan',
            'surgical_procedure' => "Surgical Procedure",
            "surgical_status" => "Surgical Status",
            "tinggi_badan" => "Tinggi Badan",
            "berat_badan" => "Berat Badan",
            "suhu_badan" => "Suhu tubuh",
            "pernafasan" => "Pernafasan",
            "tekanan_darah_systolic" => "Tekanan Darah Systolic",
            "tekanan_darah_diastolic" => "Tekanan Darah Diastolic",
            "nevous_system" => "Nevous System",
            "urinary_tracking_system" => "Urinary Tracking System",
            "cardiovascular_system" => "Cardiovascular System",
            "gastrointestinal_system" => "Gastrointestinal System",
            "metabolic_system" => "Metabolic System",
            "coexist_condition" => "Coexist Condition",
            "respitory_system" => "Respitory System",
            "musculo_skeletal_system" => "Musculo Skeletal System",
            "medication_taken" => "Medication Taken",
            "type_1" => "T",
            "type_2" => "T",
            "type_3" => "T",
            "size" => "S",
            "fluite_1" => "F",
            "fluite_2" => "F",
            "fluite_3" => "F",
            "rate_of_influsion_1" => "R",
            "rate_of_influsion_2" => "R",
            "rate_of_influsion_3" => "R",
            "fluid_left_1" => "Fluid Left in the container/syringer",
            "fluid_left_2" => "Fluid Left in the container/syringer",
            "fluid_left_3" => "Fluid Left in the container/syringer"
        ];
    }
}
