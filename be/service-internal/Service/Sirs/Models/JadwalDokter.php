<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;
use Integrasi\Service\Sirs\Repositories\JadwalDokterRepositoris;

class JadwalDokter extends \Integrasi\Components\ActiveRepositories
{
    public $_repositori = JadwalDokterRepositoris::class;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jadwaldokter_m';
    }

    public function rules()
    {
        return [
            [[
                'jadwaldokter_id',
                'jadwaldokter_tgl',
                'jadwaldokter_mulai',
                'jadwaldokter_tutup',
                'is_loaddokter',
                'jumlah_loaddokter',
                'is_bersedia',
            ], 'safe']
        ];
    }

}
