<?php

namespace app\modules\v1\models;

use SirsCore\models\InfoPasienOperasiView;
use Yii;

/**
 * This is the model class for table "preanestesi_m".
 *
 * @property integer $ruangan
 */
class PreAnesthetic extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'preanestesi_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasienmasukpenunjang_id','ruangan_id','surgical_procedure','surgical_status','tinggi_badan','berat_badan','suhu_tubuh','pernafasan','tekanan_darah_systolic','tekanan_darah_diastolic','nevous_system','urinary_tracking_system','cardiovascular_system','gastrointestinal_system','metabolic_system','coexist_condition','respitory_system','musculo_skeletal_system','status_asa', 'medication_taken','type_1', 'type_2', 'type_3','size','fluite_1','fluite_2','fluite_3','rate_of_influsion_1','rate_of_influsion_2', 'rate_of_influsion_3', 'fluid_left_1','fluid_left_2','fluid_left_3' ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasienmasukpenunjang_id' => "Pasien ID",
            'ruangan_id' => \Yii::t('fe', 'Ruangan'),
            'surgical_procedure' => "Surgical Procedure",
            "surgical_status" => "Surgical Status",
            "tinggi_badan" => "Tinggi Badan",
            "berat_badan" => "Berat Badan",
            "suhu_tubuh" => "Suhu tubuh",
            "pernafasan" => "Pernafasan",
            "tekanan_darah_systolic" => "Tekanan Darah Systolic",
            "tekanan_darah_diastolic" => "Tekanan Darah Diastolic",
            "nevous_system" => "Nevous System",
            "urinary_tracking_system" => "Urinary Tracking System",
            "cardiovascular_system" => "Cardiovascular System",
            "gastrointestinal_system" => "Gasrtointestinal System",
            "metabolic_system" => "Metabolic System",
            "coexist_condition" => "Coexist Condition",
            "respitory_system" => "Respitory System",
            "musculo_skeletal_system" => "Musculo Skeletal System",
            "status_asa" => "Status Asa",
            "medication_taken" => "Medication Taken",
            "type_1" => "Type 1",
            "type_2" => "Type 2",
            "type_3" => "Type 3",
            "size" => "Size",
            "fluite_1" => "Fluite 1",
            "fluite_2" => "Fluite 2",
            "fluite_3" => "Fluite 3",
            "rate_of_influsion_1" => "Rate of influsion 1",
            "rate_of_influsion_2" => "Rate of influsion 2",
            "rate_of_influsion_3" => "Rate of influsion 3",
            "fluid_left_1" => "Fluid Left in the container/syringer 1",
            "fluid_left_2" => "Fluid Left in the container/syringer 2",
            "fluid_left_3" => "Fluid Left in the container/syringer 3"
        ];
    }
    
    public static function getDatePasienOperasi($id)
    {
        return InfoPasienOperasiView::find()->where(['pasienmasukpenunjang_id' => $id])->asArray()->select(['tgl_operasi'])->one();
    }
}
