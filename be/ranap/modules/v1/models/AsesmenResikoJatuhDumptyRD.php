<?php

namespace app\modules\v1\models;

use Yii;

class AsesmenResikoJatuhDumptyRD extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'asesmenrdresikojatuhdumpty_t';
    }

    public function rules()
    {
        return [[
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
            ],
        ];
     }


}