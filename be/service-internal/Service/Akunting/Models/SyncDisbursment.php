<?php

namespace Integrasi\Service\Akunting\Models;

use Yii;

/**
 * This is the model class for table "sync_disbursment".
 *
 */
class SyncDisbursment extends \Integrasi\Components\ActiveRepositories
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sync_disbursment';
    }

}