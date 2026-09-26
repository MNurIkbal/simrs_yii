<?php

namespace app\modules\v1\models;

class BpjsAntrianTanggal extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpjs_antrian_tanggal_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'tanggal',
                'kodebooking',
                'kodepoli',
                'kodedokter',
                'jampraktek',
                'nik',
                'nokapst',
                'nohp',
                'norekammedis',
                'jeniskunjungan',
                'nomorreferensi',
                'sumberdata',
                'ispeserta',
                'noantrean',
                'estimasidilayani',
                'createdtime',
                'status',
                'created_date',
                'created_by',
                'is_deleted',
                'last_sync',
            ], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }
}