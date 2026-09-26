<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "groupinacbg_m".
 *
 * @property int $groupinacbg_id
 * @property string $groupinacbg_nama
 * @property string $groupinacbg_namalainnya
 * @property string $groupinacbg_kode
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
 */
class GroupInaCbg extends \Doco\components\DocoActiveRecord
{
    
    public $daftartindakan_ids;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'groupinacbg_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['groupinacbg_nama','groupinacbg_kode', 'groupinacbg_namalainnya'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['catatan', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'daftartindakan_ids'], 'safe'],
            [['is_deleted', 'is_active', 'is_obat'], 'boolean'],
            [['groupinacbg_nama', 'groupinacbg_namalainnya'], 'string', 'max' => 255],
            [['groupinacbg_kode'], 'string', 'max' => 100],
            // [['groupinacbg_kode'], 'unique'],
            [['groupinacbg_kode'], 'chkKode'],
            [['groupinacbg_nama'], 'chkNama'],
        ];
    }

    public function chkKode($params, $attributes)
    {
        $groupinacbg_kode = $this->groupinacbg_kode;
        $rest = substr($this->groupinacbg_kode, 0, 1);
        if ($rest == " ") {
            $this->addError("groupinacbg_kode", "Kode INA CBGS mengandung spasi di awal kata");
            return false;
        }
        else {
            $model = self::find()->where(['LOWER (groupinacbg_kode)' => strtolower($this->groupinacbg_kode), 'is_deleted' => false])->one();
            if(!empty($model) && $model->groupinacbg_id != $this->groupinacbg_id ){
                $this->addError("groupinacbg_kode","Kode INA CBGS Sudah Dipakai");
                return false;
            }
        }
        
        return true;
    }

    public function chkNama($params, $attributes)
    {
        $groupinacbg_nama = $this->groupinacbg_nama;
        $rest = substr($this->groupinacbg_nama, 0, 1);
        if ($rest == " ") {
            $this->addError("groupinacbg_nama", "Nama INA CBGS mengandung spasi di awal kata");
            return false;
        }
        else {
            $model = self::find()->where(['LOWER (groupinacbg_nama)' => strtolower($this->groupinacbg_nama), 'is_deleted' => false])->one();
            if(!empty($model) && $model->groupinacbg_id != $this->groupinacbg_id ){
                $this->addError("groupinacbg_nama","Nama INA CBGS Sudah Dipakai");
                return false;
            }
        }
    
        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'groupinacbg_id' => 'Groupinacbg ID',
            'groupinacbg_nama' => 'Groupinacbg Nama',
            'groupinacbg_namalainnya' => 'Groupinacbg Namalainnya',
            'groupinacbg_kode' => 'Groupinacbg Kode',
            'catatan' => 'Catatan',
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
