<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "jenisdarah_m".
 *
 * @property int $jenisdarah_id
 * @property string $jenisdarah_nama
 * @property int $lama_penyimpanan
 * @property int $suhu_penyimpanan
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
class JenisDarahForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    
    public $jenisdarah_id;
    public $jenisdarah_nama;
    public $lama_penyimpanan;
    public $suhu_penyimpanan;
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
    public function rules()
    {
        return [
            [['jenisdarah_nama', 'lama_penyimpanan', 'suhu_penyimpanan', 'harga'], 'required'],
            [['modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['jenisdarah_nama', 'lama_penyimpanan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jenisdarah_nama'], 'string', 'max' => 255],
            // [['suhu_penyimpanan'], 'number'],
            [['lama_penyimpanan'], 'is4NumbersOnly'],
            [['harga'], 'is9NumbersOnly'],
        ];
    }

    public function is4NumbersOnly($attribute)
    {
        if (!preg_match('/^[0-9]{1,4}$/', $this->$attribute)) {
            $this->addError($attribute, 'Lama Penyimpanan maksimal harus 4 digit angka.');
        }
    }

    public function is9NumbersOnly($attribute)
    {
        if (!preg_match('/^[0-9]{1,9}$/', $this->$attribute)) {
            $this->addError($attribute, 'Harga maksimal harus 9 digit angka.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenisdarah_id' => 'Jenisdarah ID',
            'jenisdarah_nama' => 'Nama Jenis Darah',
            'lama_penyimpanan' => 'Lama Penyimpanan',
            'suhu_penyimpanan' => 'Suhu Penyimpanan',
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
