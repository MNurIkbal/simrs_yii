<?php

namespace app\components\Traits\Form;

class DietPasienForm extends \yii\base\Model
{
    public $pendaftaran_id;
    public $peg_pemesan_id;
    public $catatan_diet;
    public $created_by;
    public $created_date;
    public $modified_count;
    public $last_modified_by;
    public $last_modified_date;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

    public function rules()
    {
        return [
            [['pendaftaran_id', 'catatan_diet'], 'required'],
            [['pendaftaran_id', 'catatan_diet'], 'default', 'value' => null],
            [['pendaftaran_id'], 'integer'],
            [['created_date', 'last_modified_date', 'deleted_date', 'created_by'], 'safe'],
            [['catatan_diet'], 'string'],
            [['is_active', 'is_deleted'], 'boolean']
        ];
    }

    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'catatan_diet' => 'Jenis Diet',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By'
        ];
    }
}
?>
