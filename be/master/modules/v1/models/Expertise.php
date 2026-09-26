<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "expertise_m".
 *
 * @property int $expertise_id
 * @property string $nama_expertise
 * @property int $pemeriksaanrad_id
 * @property string $hasil_expertise
 * @property string $kesan
 * @property string $kesimpulan
 * @property int $pegawai_id
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
class Expertise extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'expertise_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pemeriksaanrad_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pemeriksaanrad_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['nama_expertise'], 'checkDuplicate'],
            [['hasil_expertise', 'kesan', 'kesimpulan', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nama_expertise'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'expertise_id' => 'Expertise ID',
            'nama_expertise' => 'Nama Expertise',
            'pemeriksaanrad_id' => 'Pemeriksaanrad ID',
            'hasil_expertise' => 'Hasil Expertise',
            'kesan' => 'Kesan',
            'kesimpulan' => 'Kesimpulan',
            'pegawai_id' => 'Pegawai ID',
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
    public function checkDuplicate()
    {
        $nama_expertise = $this->nama_expertise;
        $model = self::find()->where([
            'LOWER (nama_expertise)' => strtolower($this->nama_expertise),
            'is_deleted' => false
        ])->one();
        if (!empty($model) && ($model->expertise_id != $this->expertise_id)){
            $this->addError("nama_expertise","Nama expertise Sudah Digunakan");
            return false;
        }
    
        return true;
    }
}
