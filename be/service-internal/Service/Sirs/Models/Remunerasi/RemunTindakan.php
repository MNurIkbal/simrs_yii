<?php

namespace Integrasi\Service\Sirs\Models\Remunerasi;

use Yii;

class RemunTindakan extends \Integrasi\Components\ActiveRepositories
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tmp_daftartindakan_remun_m';
    }
}
