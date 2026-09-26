<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "kelompokdiagnosa_m".
 *
 * @property int $kelompokdiagnosa_id
 * @property string $kelompokdiagnosa_nama
 * @property string $kelompokdiagnosa_namalainnya
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
 * @property int $status_diagnosa 0=utama, 1=masuk, 2=lain-lain
 */
class KelompokDiagnosaForm extends \yii\base\Model
{

  public $kelompokdiagnosa_id;
  public $kelompokdiagnosa_nama;
  public $kelompokdiagnosa_namalainnya;
  public $additional_data;
  public $created_date;
  public $created_by;
  public $modified_count;
  public $last_modified_date;
  public $last_modified_by;
  public $is_deleted;
  public $is_active;
  public $deleted_date;
  public $deleted_by;
  public $status_diagnosa;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kelompokdiagnosa_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kelompokdiagnosa_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'status_diagnosa'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'status_diagnosa'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelompokdiagnosa_nama', 'kelompokdiagnosa_namalainnya'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kelompokdiagnosa_id' => 'Kelompokdiagnosa ID',
            'kelompokdiagnosa_nama' => 'Kelompokdiagnosa Nama',
            'kelompokdiagnosa_namalainnya' => 'Kelompokdiagnosa Namalainnya',
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
            'status_diagnosa' => 'Status Diagnosa',
        ];
    }
}
