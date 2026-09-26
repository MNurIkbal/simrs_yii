<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "warnadokrekammedik_m".
 *
 * @property int $warnadokrm_id
 * @property string $warnadokrm_namawarna
 * @property string $warnadokrm_kodewarna
 * @property string $warnadokrm_fungsi
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class WarnaDokumen extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'warnadokrekammedik_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['warnadokrm_namawarna'], 'required'],
            [['warnadokrm_fungsi', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['warnadokrm_namawarna', 'warnadokrm_kodewarna'], 'string', 'max' => 20],
            [['warnadokrm_kodewarna'], 'chkUnique'],
        ];
    }

    public function chkUnique()
    {
        // $warnadokrm_kodewarna = $this->warnadokrm_kodewarna;
        $model = self::find()->where(['warnadokrm_kodewarna' => $this->warnadokrm_kodewarna, 'is_deleted' => false])->one();
        if (!empty($model) && $model->warnadokrm_kodewarna != $this->warnadokrm_id) {
            $this->addError("warnadokrm_kodewarna", "Digit Pertama Nomor Primer Sudah Dipakai");
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'warnadokrm_id' => 'Warnadokrm ID',
            'warnadokrm_namawarna' => 'Warnadokrm Namawarna',
            'warnadokrm_kodewarna' => 'Warnadokrm Kodewarna',
            'warnadokrm_fungsi' => 'Warnadokrm Fungsi',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
}
