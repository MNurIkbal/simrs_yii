<?php

namespace app\modules\ranap\models;

use Yii;

class PagtMonevForm extends \yii\base\Model
{
    public $pagtmonev_id;
    public $pagt_id;
    public $tgl_monev;
    public $berat_badan;
    public $tekanan_darah;
    public $nilai_lab_abnormal;
    public $oral_energi;
    public $oral_protein;
    public $oral_lemak;
    public $oral_kh;
    public $enteral_energi;
    public $enteral_protein;
    public $enteral_lemak;
    public $enteral_kh;
    public $parenteral_energi;
    public $parenteral_protein;
    public $parenteral_lemak;
    public $parenteral_kh;
    public $total_asupan_energi;
    public $total_asupan_protein;
    public $total_asupan_lemak;
    public $total_asupan_kh;
    public $evaluasi_usulan;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

    public function rules()
    {
        return [
            [
                [
                    'pagtmonev_id',
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
                    'additional_data',
                    'created_date',
                    'created_by',
                    'modified_count',
                    'last_modified_date',
                    'last_modified_by',
                    'is_deleted',
                    'is_active',
                    'deleted_date',
                    'deleted_by',
                ],
                'safe'
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pagtmonev_id'         => 'Pagtmonev Id',
            'pagt_id'              => 'Pagt Id',
            'tgl_monev'            => 'Tgl Monev',
            'berat_badan'          => 'Berat Badan',
            'tekanan_darah'        => 'Tekanan Darah',
            'nilai_lab_abnormal'   => 'Nilai Lab Abnormal',
            'oral_energi'          => 'Oral Energi',
            'oral_protein'         => 'Oral Protein',
            'oral_lemak'           => 'Oral Lemak',
            'oral_kh'              => 'Oral KH',
            'enteral_energi'       => 'Enteral Energi',
            'enteral_protein'      => 'Enteral Protein',
            'enteral_lemak'        => 'Enteral Lemak',
            'enteral_kh'           => 'Enteral KH',
            'parenteral_energi'    => 'Parenteral Energi',
            'parenteral_protein'   => 'Parenteral Protein',
            'parenteral_lemak'     => 'Parenteral Lemak',
            'parenteral_kh'        => 'Parenteral KH',
            'total_asupan_energi'  => 'Total Asupan Energi',
            'total_asupan_protein' => 'Total Asupan Protein',
            'total_asupan_lemak'   => 'Total Asupan Lemak',
            'total_asupan_kh'      => 'Total Asupan KH',
            'evaluasi_usulan'      => 'Evaluasi Usulan',
            'additional_data'      => 'Additional Data',
            'created_date'         => 'Created Date',
            'created_by'           => 'Created By',
            'modified_count'       => 'Modified Count',
            'last_modified_date'   => 'Last Modified Date',
            'last_modified_by'     => 'Last Modified By',
            'is_deleted'           => 'Is Deleted',
            'is_active'            => 'Is Active',
            'deleted_date'         => 'Deleted Date',
            'deleted_by'           => 'Deleted By',
        ];
    }
}
