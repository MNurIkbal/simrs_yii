<?php

namespace app\modules\bankdarah\models;

use Yii;

/**
 * This is the model class for table "terimadarahpmidetail_t".
 *
 * @property int $terimadarahpmidetail_id
 * @property int $terimadarahpmi_id
 * @property int $pesandarahpmidetail_id
 * @property int $jenisdarah_id
 * @property int $golongandarah_id
 * @property string $no_kantongdarah
 * @property string $tgl_pengambilan
 * @property double $harga
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
class TerimaDarahPmiDetailForm extends \yii\base\Model
{
     public $terimadarahpmidetail_id;
     public $terimadarahpmi_id;
     public $pesandarahpmidetail_id;
     public $jenisdarah_id;
     public $golongandarah_id;
     public $no_kantongdarah;
     public $tgl_pengambilan;
     public $harga;
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
    public static function tableName()
    {
        return 'terimadarahpmidetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['terimadarahpmi_id', 'pesandarahpmidetail_id', 'jenisdarah_id', 'golongandarah_id'], 'required'],
            [['terimadarahpmi_id', 'pesandarahpmidetail_id', 'jenisdarah_id', 'golongandarah_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['terimadarahpmi_id', 'pesandarahpmidetail_id', 'jenisdarah_id', 'golongandarah_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_pengambilan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['harga'], 'number'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_kantongdarah'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'terimadarahpmidetail_id' => 'Terimadarahpmidetail ID',
            'terimadarahpmi_id' => 'Terimadarahpmi ID',
            'pesandarahpmidetail_id' => 'Pesandarahpmidetail ID',
            'jenisdarah_id' => 'Jenisdarah ID',
            'golongandarah_id' => 'Golongandarah ID',
            'no_kantongdarah' => 'No Kantongdarah',
            'tgl_pengambilan' => 'Tgl Pengambilan',
            'harga' => 'Harga',
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
