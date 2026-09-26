<?php

/**
 * @Author: Aris
 * @Date:   2020-09-15 14:00:00
 * @Description: 
 */

namespace app\modules\ranap\models;

use Yii;

class HumptyDumptyForm extends \yii\base\Model
{   
    public $pendaftaran_id;
    public $asesmenperawatrd_id;
    public $tanggal;
    public $jam;
    public $usia;
    public $jenis_kelamin;
    public $diagnosis;
    public $gangguan_kognitif;
    public $faktor_lingkungan;
    public $anastesi;
    public $medika_mentosa;
    public $total_skor;
    public $additional_data;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $deleted_date;
    public $deleted_by;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'pendaftaran_id',
                    'asesmenperawatrd_id',
                    'tanggal',
                    'jam',
                    'usia',
                    'jenis_kelamin',
                    'diagnosis',
                    'gangguan_kognitif',
                    'faktor_lingkungan',
                    'anastesi',
                    'medika_mentosa',
                    'total_skor',
                    'additional_data',
                    'created_by',
                    'modified_count',
                    'last_modified_date',
                    'last_modified_by',
                    'deleted_date',
                    'deleted_by'
                ],
                'safe'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id'      => Yii::t('fe', ''),     
            'asesmenperawatrd_id' => Yii::t('fe', ''),
            'tanggal'             => Yii::t('fe', ''),            
            'jam'                 => Yii::t('fe', ''),                
            'usia'                => Yii::t('fe', ''),               
            'jenis_kelamin'       => Yii::t('fe', ''),      
            'diagnosis'           => Yii::t('fe', ''),          
            'gangguan_kognitif'   => Yii::t('fe', ''),  
            'faktor_lingkungan'   => Yii::t('fe', ''),  
            'anastesi'            => Yii::t('fe', ''),           
            'medika_mentosa'      => Yii::t('fe', ''),     
            'total_skor'          => Yii::t('fe', ''),         
            'additional_data'     => Yii::t('fe', ''),    
            'created_by'          => Yii::t('fe', ''),         
            'modified_count'      => Yii::t('fe', ''),     
            'last_modified_date'  => Yii::t('fe', ''), 
            'last_modified_by'    => Yii::t('fe', ''),   
            'deleted_date'        => Yii::t('fe', ''),       
            'deleted_by'          => Yii::t('fe', '')         
        ];
    }
}