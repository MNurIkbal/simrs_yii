<?php

/**
 * @Author: Aris
 * @Date:   2020-09-15 14:00:00
 * @Description: 
 */

namespace app\modules\igd\models;

use Yii;

class MorseForm extends \yii\base\Model
{   
    public $pendaftaran_id;
    public $asesmenperawatrd_id;
    public $resikojatuh_id;
    public $tanggal;
    public $jam;
    public $riwayat_jatuh;
    public $diagnosis_sekunder;
    public $alat_bantu;
    public $catheter;
    public $kemampuan_berjalan;
    public $status_mental;
    public $total_skor;
    public $kesimpulan;
    public $additional_data;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $deleted_date;
    public $deleted_by;
    public $penyebabjatuh_id;

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
                    'resikojatuh_id',
                    'tanggal',
                    'jam',
                    'riwayat_jatuh',
                    'diagnosis_sekunder',
                    'alat_bantu',
                    'catheter',
                    'kemampuan_berjalan',
                    'status_mental',
                    'total_skor',
                    'kesimpulan',
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
            'resikojatuh_id'      => Yii::t('fe', 'Faktor Penyebab Risiko Jatuh'),
            'tanggal'             => Yii::t('fe', ''),
            'jam'                 => Yii::t('fe', ''),
            'riwayat_jatuh'       => Yii::t('fe', ''),
            'diagnosis_sekunder'  => Yii::t('fe', ''),
            'alat_bantu'          => Yii::t('fe', ''),
            'catheter'            => Yii::t('fe', ''),
            'kemampuan_berjalan'  => Yii::t('fe', ''),
            'status_mental'       => Yii::t('fe', ''),
            'total_skor'          => Yii::t('fe', ''),
            'kesimpulan'          => Yii::t('fe', ''),
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