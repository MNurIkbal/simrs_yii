<?php

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Service\Sirs\Models\Ruangan;

class ClearCache extends \Integrasi\Contracts\DocoImplement
{
    const C_C_DEF_RUANGAN_REGIS = 'default_kelas_registrasi';
    const C_KEY_RUANGAN_REGIS = 'get-kelas-pelayanan-';

    public function execute()
    {
        $state = $this->state;

        if($state == self::C_C_DEF_RUANGAN_REGIS) {
            $getRuangan = Ruangan::find()
                ->select([
                    'ruangan_id'
                ])->asArray()->all();
            if(!empty($getRuangan)) {
                foreach($getRuangan as $key => $value) {
                    Yii::$app->cache->delete(self::C_KEY_RUANGAN_REGIS.$value['ruangan_id']);
                }
            }    
        }

        return json_encode([
            'service' => 'Clear-cache',
            'state' => $state,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

}