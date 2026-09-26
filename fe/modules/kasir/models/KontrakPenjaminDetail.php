<?php

namespace app\modules\kasir\models;

use Yii;

class KontrakPenjaminDetail extends \yii\base\Model
{
    public $kontrakpenjamindetail_id;
    public $kontrakpenjamin_id;
    public $grade;
    public $lob_id;
    public $tipediskon_id;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_ddate;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;


    public function rules()
    {
        return [
            [['kontrakpenjamindetail_id'], 'required'],
            [['additional_data', 'created_by', 'modified_count', 'last_modified_by', 'last_modified_date', 'deleted_date','deleted_by'], 'default', 'value' => null],
            [['kontrakpenjamin_id', 'lob_id','tipediskon_id','created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['grade'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kontrakpenjamindetail_id' => 'Kontrak Penjamin Detail ID',
            'kontrakpenjamin_id' => 'Kontrak Penjamin ID',
            'grade' => 'Grade',
            'lob_id' => 'Line Of Business ID',
            'tipediskon_id' => 'Tipe Diskon ID',
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
