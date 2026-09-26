<?php

namespace app\modules\master\models;

use Yii;

/**
 *
 * @property int $perdatarif_id
 * @property string $perdanama_sk
 * @property string $perda_no
 * @property string $perda_tgl
 * @property string $perda_tentang
 * @property string $ditetapkan_oleh
 * @property string $tempat_ditetapkan
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
 * @property string $nama_lainnya
 */
class PerdaTarifForm extends \yii\base\Model
{
    public $perdatarif_id;
    public $perda_no;
    public $perdanama_sk;
    public $nama_lainnya;
    public $perda_tgl;
    public $perda_tentang;
    public $ditetapkan_oleh;
    public $tempat_ditetapkan;
    public $is_active;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['perda_no','perdanama_sk','nama_lainnya','perda_tgl'], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            [['perda_tgl','nama_lainnya'], 'safe'],
            [['perda_tentang'], 'string'],
            [['perda_no','perdanama_sk','nama_lainnya','perda_tgl'], 'default', 'value' => null],
            [[], 'integer'],
            [['is_active'], 'boolean'],
            [['perdanama_sk'], 'string', 'max' => 200],
            [['perda_no'], 'string', 'max' => 20],
            [['ditetapkan_oleh', 'tempat_ditetapkan'], 'string', 'max' => 30],
            [['nama_lainnya'], 'string', 'max' => 255],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'perdatarif_id' => 'Perdatarif ID',
            'perdanama_sk' => Yii::t('fe', 'Nama Perda'),
            'perda_no' => Yii::t('fe', 'Nomor Perda'),
            'perda_tgl' => Yii::t('fe', 'Tanggal Berlaku Mulai'),
            'perda_tentang' => Yii::t('fe', 'Detail'),
            'ditetapkan_oleh' => Yii::t('fe', 'Ditetapkan Oleh'),
            'tempat_ditetapkan' => 'Tempat Ditetapkan',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Aktif',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'nama_lainnya' => Yii::t('fe', 'Nama Lainnya'),
        ];
    }
}
