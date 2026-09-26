<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-17 13:40:02
 */

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "fasilitasrs_m".
 *
 * @property int $fasilitasrs_id
 * @property string $jenis_fasilitas instalasi_m, free text
 * @property string $nama_fasilitas ruangan_m berdasarkan instalasi_id, free text
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
class FasilitasRsForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    public $fasilitasrs_id;
    public $jenis_fasilitas;
    public $nama_fasilitas;
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['jenis_fasilitas', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['nama_fasilitas', 'additional_data'], 'string'],
            [['nama_fasilitas'], 'required'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fasilitasrs_id' => Yii::t('fe', 'Fasilitas RS ID'),
            'jenis_fasilitas' => Yii::t('fe', 'Jenis Fasilitas'),
            'nama_fasilitas' => Yii::t('fe', 'Jenis Fasilitas'),
            'additional_data' => Yii::t('fe', 'Additional Data'),
            'created_date' => Yii::t('fe', 'Created Date'),
            'created_by' => Yii::t('fe', 'Created By'),
            'modified_count' => Yii::t('fe', 'Modified Count'),
            'last_modified_date' => Yii::t('fe', 'Last Modified Date'),
            'last_modified_by' => Yii::t('fe', 'Last Modified By'),
            'is_deleted' => Yii::t('fe', 'Is Deleted'),
            'is_active' => Yii::t('fe', 'Is Active'),
            'deleted_date' => Yii::t('fe', 'Deleted Date'),
            'deleted_by' => Yii::t('fe', 'Deleted By'),
        ];
    }
}
