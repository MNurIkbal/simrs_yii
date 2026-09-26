<?php

namespace app\modules\igd\models;

use Yii;

class AsesmenKeperawatanResikoJatuhHistory extends \yii\base\Model
{
    public $skala_nyeri;
    public $lokasi_nyeri;
    public $intesitas_nyeri;
    public $durasi_nyeri;
    public $frekuensi_nyeri;
    public $lamanya_nyeri;
    public $faktor_nyeri;
    public $rencana_tindakan;
    public $karakteristik_nyeri;
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
                    'skala_nyeri',
                    'lokasi_nyeri',
                    'intesitas_nyeri',
                    'durasi_nyeri',
                    'frekuensi_nyeri',
                    'lamanya_nyeri',
                    'faktor_nyeri',
                    'rencana_tindakan',
                    'karakteristik_nyeri',
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
}
