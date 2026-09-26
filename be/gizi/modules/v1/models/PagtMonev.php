<?php
namespace app\modules\v1\models;

use Yii;
use Doco\components\DocoActiveRecord;

class PagtMonev extends DocoActiveRecord {
    public static function tableName()
    {
        return "pagtmonev_t";
    }

    public function rules()
    {
        return [
            [
                [
                    'pagt_id',
                    'tgl_monev',
                    'berat_badan',
                    'tekanan_darah',
                    'nilai_lab_abnormal',
                    'oral_energi',
                    'oral_protein',
                    'oral_lemak',
                    'oral_kh',
                    'enteral_energi',
                    'enteral_protein',
                    'enteral_lemak',
                    'enteral_kh',
                    'parenteral_energi',
                    'parenteral_protein',
                    'parenteral_lemak',
                    'parenteral_kh',
                    'total_asupan_energi',
                    'total_asupan_protein',
                    'total_asupan_lemak',
                    'total_asupan_kh',
                    'evaluasi_usulan',
                ], 'safe'
            ]
        ];
    }
}