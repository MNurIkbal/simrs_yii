<?php
namespace app\modules\v1\models;

use Yii;
use Doco\components\DocoActiveRecord;

class AsesmenResikoJatuh extends DocoActiveRecord {
    public static function tableName()
    {
        return "asesmenrdresikojatuh_t";
    }

    public function rules()
    {
        return [
            [
                [
                    'asesmenperawatrd_id',
                    'asesmenperawat_id',
                    'tgl_pengkajian',
                    'skala_nyeri',
                    'lokasi_nyeri',
                    'intesitas_nyeri',
                    'durasi_nyeri',
                    'frekuensi_nyeri',
                    'karakteristik_nyeri',
                    'lamanya_nyeri',
                    'faktor_nyeri',
                    'rencana_tindakan',
                ], 'safe'
            ]
        ];
    }
}