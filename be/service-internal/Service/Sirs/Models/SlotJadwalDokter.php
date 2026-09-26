<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;


class SlotJadwalDokter extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'slotjadwaldokter_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'slotjadwaldokter_id',
                'jadwaldokter_id',
                'slot_sequence',
                'jam_mulai',
                'jam_selesai',
                'slot_type',
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
            ], 'safe']
        ];
    }
}
