<?php

namespace Integrasi\Service\Akunting\Models;

use Yii;

/**
 * This is the model class for table "SyncPengajuanKlaim".
 *
 */
class SyncPengajuanKlaim extends \Integrasi\Components\ActiveRepositories
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sync_pengajuanklaim';
    }

}