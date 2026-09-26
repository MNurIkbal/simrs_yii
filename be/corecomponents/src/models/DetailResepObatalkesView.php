<?php

namespace Doco\models;

use Yii;

class DetailResepObatalkesView extends \Doco\components\DocoActiveRecord
{

    public static function tableName()
    {
        return 'detailresepobatalkes_v';
    }

    public static function primaryKey()
    {
          return ["reseptur_id", "obatalkes_id"];
    }
}