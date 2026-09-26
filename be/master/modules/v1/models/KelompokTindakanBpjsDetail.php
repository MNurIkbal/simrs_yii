<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "monitorbpjsdetail_m".
 *
 * @property int $monitorbpjsdetail_id
 * @property string $monitorbpjs_id
 * @property string $groupinacbg_id
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
 *
 */
class KelompokTindakanBpjsDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'monitorbpjsdetail_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['monitorbpjs_id', 'groupinacbg_id'], 'required'],
            [['groupinacbg_id'], 'checkUniqueCase', 'message' => 'Kelompok tindakan nama sudah dipergunakan.'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    public function checkUniqueCase($attribute, $params)
    {
        $groupinacbg_id = strtoupper($this->groupinacbg_id);
        
        $sql_nama = "select monitorbpjsdetail_id, groupinacbg_id from monitorbpjsdetail_m where groupinacbg_id = {$groupinacbg_id} and is_deleted = false "; 
        $dataNama = Yii::$app->db->createCommand($sql_nama)->queryOne();
        
        $return = 1;
        if (!empty($dataNama)) {
            if ($this->monitorbpjsdetail_id != $dataNama['monitorbpjsdetail_id']) {
                $this->addError('groupinacbg_id','"'.$this->groupinacbg_id.'" telah dipergunakan.');
                throw new \Exception("Ada Kelompok Inacbgs sudah dipergunakan", 1);
                
                $return ++;
            }
        }
        
        if ($return == 1) {
            return true;
        }else{
            return false;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'monitorbpjsdetail_id' => 'Monitoring BPJS Detail ID',
            'monitorbpjs_id' => 'Monitoring BPJS ID',
            'groupinacbg_id' => 'Group Inacbgs ID',
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
