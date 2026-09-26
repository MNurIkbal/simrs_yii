<?php

namespace Integrasi\Service\Sirs\Models;


class InfoJadwalDokterView extends \Integrasi\Components\ActiveRepositories
{

    public static function primaryKey()
    {
        return [
            "jadwaldokter_id"
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infojadwaldokter_v';
    }
}