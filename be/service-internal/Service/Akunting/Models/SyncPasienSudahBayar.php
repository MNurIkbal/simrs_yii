<?php

namespace Integrasi\Service\Akunting\Models;

use Yii;

/**
 * This is the model class for table "infopasiensudahbayar_v".
 *
 */
class SyncPasienSudahBayar extends \Integrasi\Components\ActiveRepositories
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infopasiensudahbayar_v';
    }

}