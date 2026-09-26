<?php

/**
 * @Author: Aris
 * @Date:   2020-09-15 14:00:00
 * @Description: 
 */

namespace app\modules\igd\models;

use Yii;

class SydneyForm extends \yii\base\Model
{
    public $pendaftaran_id;
    public $asesmenperawatrd_id;
    public $is_karena_jatuh;
    public $karena_jatuh;
    public $skor_karena_jatuh;
    public $is_dua_bulan_terakhir;
    public $dua_bulan_terakhir;
    public $skor_dua_bulan_terakhir;
    public $is_delirium;
    public $delirium;
    public $skor_delirium;

    public $is_disorientasi;
    public $disorientasi;
    public $skor_disorientasi;
    public $is_agitasi;
    public $agitasi;
    public $skor_agitasi;

    public $is_kacamata;
    public $kacamata;
    public $skor_kacamata;
    public $is_buram;
    public $buram;
    public $skor_buram;
    public $is_glaucoma;
    public $glaucoma;
    public $skor_glaucoma;
    public $is_berkemih;
    public $berkemih;
    public $skor_berkemih;
    public $is_mandiri;
    public $mandiri;
    public $skor_mandiri;
    public $is_bantuan_sedikit;
    public $bantuan_sedikit;
    public $skor_bantuan_sedikit;
    public $is_bantuan_nyata;
    public $bantuan_nyata;
    public $skor_bantuan_nyata;
    public $is_bantuan_total;
    public $bantuan_total;
    public $skor_bantuan_total;
    public $is_mobilitas_mandiri;
    public $mobilitas_mandiri;
    public $skor_mobilitas_mandiri;
    public $is_mobilitas_bantuan;
    public $mobilitas_bantuan;
    public $skor_mobilitas_bantuan;
    public $is_kursi_roda;
    public $kursi_roda;
    public $skor_kursi_roda;
    public $is_imobilisasi;
    public $imobilisasi;
    public $skor_imobilisasi;
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
                    'is_karena_jatuh',
                    'karena_jatuh',
                    'skor_karena_jatuh',
                    'is_dua_bulan_terakhir',
                    'dua_bulan_terakhir',
                    'skor_dua_bulan_terakhir',
                    'is_delirium',
                    'delirium',
                    'skor_delirium',
                    'is_disorientasi',
                    'disorientasi',
                    'skor_disorientasi',
                    'is_agitasi',
                    'agitasi',
                    'skor_agitasi',
                    'is_kacamata',
                    'kacamata',
                    'skor_kacamata',
                    'is_buram',
                    'buram',
                    'skor_buram',
                    'is_glaucoma',
                    'glaucoma',
                    'skor_glaucoma',
                    'is_berkemih',
                    'berkemih',
                    'skor_berkemih',
                    'is_mandiri',
                    'mandiri',
                    'skor_mandiri',
                    'is_bantuan_sedikit',
                    'bantuan_sedikit',
                    'skor_bantuan_sedikit',
                    'is_bantuan_nyata',
                    'bantuan_nyata',
                    'skor_bantuan_nyata',
                    'is_bantuan_total',
                    'bantuan_total',
                    'skor_bantuan_total',
                    'is_mobilitas_mandiri',
                    'mobilitas_mandiri',
                    'skor_mobilitas_mandiri',
                    'is_mobilitas_bantuan',
                    'mobilitas_bantuan',
                    'skor_mobilitas_bantuan',
                    'is_kursi_roda',
                    'kursi_roda',
                    'skor_kursi_roda',
                    'is_imobilisasi',
                    'imobilisasi',
                    'skor_imobilisasi',
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
            'pendaftaran_id'          => Yii::t('fe', ''),
            'asesmenperawatrd_id'     => Yii::t('fe', ''),
            'is_karena_jatuh'         => Yii::t('fe', ''),
            'karena_jatuh'            => Yii::t('fe', ''),
            'skor_karena_jatuh'       => Yii::t('fe', ''),
            'is_dua_bulan_terakhir'   => Yii::t('fe', ''),
            'dua_bulan_terakhir'      => Yii::t('fe', ''),
            'skor_dua_bulan_terakhir' => Yii::t('fe', ''),
            'is_delirium'             => Yii::t('fe', ''),
            'delirium'                => Yii::t('fe', ''),
            'skor_delirium'           => Yii::t('fe', ''),
            'is_kacamata'             => Yii::t('fe', ''),
            'kacamata'                => Yii::t('fe', ''),
            'skor_kacamata'           => Yii::t('fe', ''),
            'is_buram'                => Yii::t('fe', ''),
            'buram'                   => Yii::t('fe', ''),
            'skor_buram'              => Yii::t('fe', ''),
            'is_glaucoma'             => Yii::t('fe', ''),
            'glaucoma'                => Yii::t('fe', ''),
            'skor_glaucoma'           => Yii::t('fe', ''),
            'is_berkemih'             => Yii::t('fe', ''),
            'berkemih'                => Yii::t('fe', ''),
            'skor_berkemih'           => Yii::t('fe', ''),
            'is_mandiri'              => Yii::t('fe', ''),
            'mandiri'                 => Yii::t('fe', ''),
            'skor_mandiri'            => Yii::t('fe', ''),
            'is_bantuan_sedikit'      => Yii::t('fe', ''),
            'bantuan_sedikit'         => Yii::t('fe', ''),
            'skor_bantuan_sedikit'    => Yii::t('fe', ''),
            'is_bantuan_nyata'        => Yii::t('fe', ''),
            'bantuan_nyata'           => Yii::t('fe', ''),
            'skor_bantuan_nyata'      => Yii::t('fe', ''),
            'is_bantuan_total'        => Yii::t('fe', ''),
            'bantuan_total'           => Yii::t('fe', ''),
            'skor_bantuan_total'      => Yii::t('fe', ''),
            'is_mobilitas_mandiri'    => Yii::t('fe', ''),
            'mobilitas_mandiri'       => Yii::t('fe', ''),
            'skor_mobilitas_mandiri'  => Yii::t('fe', ''),
            'is_mobilitas_bantuan'    => Yii::t('fe', ''),
            'mobilitas_bantuan'       => Yii::t('fe', ''),
            'skor_mobilitas_bantuan'  => Yii::t('fe', ''),
            'is_kursi_roda'           => Yii::t('fe', ''),
            'kursi_roda'              => Yii::t('fe', ''),
            'skor_kursi_roda'         => Yii::t('fe', ''),
            'is_imobilisasi'          => Yii::t('fe', ''),
            'imobilisasi'             => Yii::t('fe', ''),
            'skor_imobilisasi'        => Yii::t('fe', ''),
            'total_skor'              => Yii::t('fe', ''),
            'additional_data'         => Yii::t('fe', ''),
            'created_by'              => Yii::t('fe', ''),
            'modified_count'          => Yii::t('fe', ''),
            'last_modified_date'      => Yii::t('fe', ''),
            'last_modified_by'        => Yii::t('fe', ''),
            'deleted_date'            => Yii::t('fe', ''),
            'deleted_by'              => Yii::t('fe', ''),
            'is_disorientasi'   => Yii::t('fe',''),
            'disorientasi' => Yii::t('fe', ''),
            'skor_disorientasi' => Yii::t('fe',''),
            'is_agitasi' => Yii::t('fe',''),
            'agitasi' => Yii::t('fe',''),
            'skor_agitasi' => Yii::t('fe','')
        ];
    }
}