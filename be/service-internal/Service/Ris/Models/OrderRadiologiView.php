<?php

namespace Integrasi\Service\Ris\Models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "sync_tindakan".
 *
 */
class OrderRadiologiView extends \Integrasi\Components\ActiveRepositories
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infoorderanraddetail_v';
    }
}