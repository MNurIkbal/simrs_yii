<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "groupmargin_m".
 *
 * @property integer $groupmargin_id
 * @property integer $groupmargin_kode
 * @property integer $groupmargin_nama
 * @property integer $additional_data
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
class GroupMargin extends \Doco\components\DocoActiveRecord
{
    public $detail;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'groupmargin_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['groupmargin_kode'], 'required'],
            [['groupmargin_kode'], 'chkKode'],
            [['groupmargin_nama'], 'required'],
            [['groupmargin_nama'], 'chkNama'],
            [['additional_data'], 'string'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['groupmargin_id','created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['groupmargin_kode'], 'string', 'max' => 50],
            [['groupmargin_nama'], 'string', 'max' => 255]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'groupmargin_id' => 'ID Grup Margin',
            'groupmargin_kode' => 'Kode Grup Margin',
            'groupmargin_nama' => 'Nama Grup Margin',
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

    public function chkNama($params, $attributes)
    {
        $groupmargin_nama = $this->groupmargin_nama;
        $rest = substr($this->groupmargin_nama, 0, 1);
        $model = self::find()->where([
            'TRIM(LOWER (groupmargin_nama))' => strtolower($groupmargin_nama),
            'is_deleted' => false
        ])->one();
        if(!empty($model) && $model->groupmargin_id != $this->groupmargin_id ){
            $this->addError("groupmargin_nama","Group Margin  Sudah Dipakai");
            return false;
        }

        return true;
    }

    public function chkKode($params, $attributes)
    {
        $groupmargin_kode = $this->groupmargin_kode;
        $rest = substr($this->groupmargin_kode, 0, 1);
        $model = self::find()->where([
            'TRIM(LOWER (groupmargin_kode))' => strtolower($groupmargin_kode),
            'is_deleted' => false
        ])->one();
        if(!empty($model) && $model->groupmargin_id != $this->groupmargin_id ){
            $this->addError("groupmargin_kode","Kode Sudah Dipakai");
            return false;
        }

        return true;
    }
}
