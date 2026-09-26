<?php

namespace app\modules\v1\models;

use Yii;

class AsesmenResikoJatuhMorseRD extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'asesmenrdresikojatuhmorse_t';
    }

    public function rules()
    {
        return [[
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
            ],
        ];
     }


}