<?php

namespace app\modules\v1\models;

use Yii;

class AsesmenResikoJatuhSydneyRD extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'asesmenrdresikojatuhsydney_t';
    }

    public function rules()
    {
        return [[
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
            ],
        ];
     }


}