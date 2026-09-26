<?php

namespace Doco\models\antrian;

use Yii;

class AntrianjknR extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'antrianjkn_r';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'antrianjkn_id', 'pendaftaranol_id', 'antrian_id', 'tanggal_periksa', 'nomorkartu', 'jenis_cara_bayar',
                    'jeniskunjungan', 'nomorreferensi','keterangan', 'is_checkin', 'is_selesai_periksa', 'is_batal_periksa', 
                    'additional_data', 'created_by', 'is_deleted','is_active', 'deleted_date', 'deleted_by', 'no_rekam_medik',
                    'pendaftaran_id', 'additional_jkn', 'kodebooking', 'tgl_checkin'
                ],
                'safe'
            ],
        ];
    }

}