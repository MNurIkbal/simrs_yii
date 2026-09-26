<?php

namespace Integrasi\Service\Mhg\Models;


class PendaftaranOl extends \Integrasi\Components\IntgrateActiveRepositories
{

    public static function tableName()
    {
        return 'pendaftaranol_int';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaranol_id'], 'required'],
            [[
                'state',
                'created_date',
                'created_by',
                'payload',
                'sync_respon',
            ], 'safe'],
        ];
    }

}