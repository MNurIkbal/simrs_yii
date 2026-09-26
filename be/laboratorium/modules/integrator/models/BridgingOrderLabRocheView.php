<?php

namespace app\modules\integrator\models;

class BridgingOrderLabRocheView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bridging_orderlab_roche_v';
    }

    public static function primaryKey()
    {
        return ['pasienmasukpenunjang_id'];
    }
}