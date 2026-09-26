<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "monitorbpjs_m".
 *
 * @property int $monitorbpjs_id
 * @property string $kelompoktindakan_nama
 * @property string $catatan
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
class KelompokTindakanBpjs extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'monitorbpjs_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kelompoktindakan_nama',], 'required'],
            [['kelompoktindakan_nama'], 'checkUniqueCase'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['kelompoktindakan_nama', 'catatan', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    public function checkUniqueCase($attribute, $params)
    {
        $kelompoktindakan_namaUpper = strtoupper($this->kelompoktindakan_nama);
        $kelompoktindakan_nama = str_replace(' ', '', $kelompoktindakan_namaUpper);
        
        $sql_nama = "select monitorbpjs_id, replace(kelompoktindakan_nama, ' ', '') as kelompoktindakan_nama from monitorbpjs_m where UPPER( kelompoktindakan_nama ) = '{$kelompoktindakan_nama}' and is_deleted = false "; 
        $dataNama = Yii::$app->db->createCommand($sql_nama)->queryOne();
        
        $return = 1;
        if (!empty($dataNama)) {
            if ($this->monitorbpjs_id !== $dataNama['monitorbpjs_id']) {
                $this->addError('kelompoktindakan_nama','"'.$this->kelompoktindakan_nama.'" telah dipergunakan.');
                $return ++;
            }
        }
        
        if ($return >= 1) {
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
            'monitorbpjs_id' => 'Monitoring BPJS ID',
            'kelompoktindakan_nama' => 'Nama Kelompok Tindakan',
            'catatan' => 'Kelompok Inacbgs / Catatan',
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
