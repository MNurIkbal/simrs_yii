<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "penjamin_m".
 *
 * @property integer $penjamin_id
 * @property integer $carabayar_id
 * @property string $penjamin_nama
 * @property string $penjamin_namalainnya
 * @property string $alamat_penjamin
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class PenjaminGrade extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'penjamingrade_m';
    }
}
