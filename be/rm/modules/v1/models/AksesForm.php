<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "aksesform_k".
 *
 * @property integer $pasien_id
 * @property string $akses
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
class AksesForm extends \Doco\components\DocoActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'aksesform_k';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['pasien_id','akses','additional_data','created_date','created_by','modified_count','last_modified_by','is_deleted','is_active','last_modified_date','deleted_date','deleted_by'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }

}
