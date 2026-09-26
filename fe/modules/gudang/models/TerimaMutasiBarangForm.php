<?php

namespace app\modules\gudang\models;

use Yii;

/**
 * This is the model class for table "terimamutasibarang_t".
 *
 * @property int $terimamutasibarang_id
 * @property int $mutasibarang_id
 * @property string $tglterima
 * @property string $noterimamutasi
 * @property int $ruanganpenerima_id
 * @property int $ruanganasalmutasi_id
 * @property int $pegawaipenerima_id
 * @property int $pegawaimengetahui_id
 * @property string $keterangan_terima
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
 * @property int $pegawaimenyetujui_id
 *
 * @property MutasibarangT $mutasibarang
 */
class TerimaMutasiBarangForm extends \yii\base\Model
{
    /**
     * @inheritdoc
     */

    public $terimamutasibarang_id;
    public $tglterima;
    public $noterimamutasi;
    public $ruanganpenerima_id;
    public $ruanganasalmutasi_id;
    public $pegawaipenerima_id;
    public $pegawaimengetahui_id;
    public $keterangan_terima;
    public $pegawaimenyetujui_id;
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
    public $no_pemesanan;
    public $mutasibarang_id;
    
    public static function tableName()
    {
        return 'terimamutasibarang_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mutasibarang_id', 'ruanganpenerima_id', 'ruanganasalmutasi_id', 'pegawaipenerima_id', 'pegawaimengetahui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'pegawaimenyetujui_id'], 'default', 'value' => null],
            [['mutasibarang_id', 'ruanganpenerima_id', 'ruanganasalmutasi_id', 'pegawaipenerima_id', 'pegawaimengetahui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'pegawaimenyetujui_id'], 'integer'],
            [['ruanganpenerima_id', 'ruanganasalmutasi_id'], 'required'],
            [['tglterima', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['keterangan_terima', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['noterimamutasi'], 'string', 'max' => 20],
            [['noterimamutasi'], 'unique'],
            // [['mutasibarang_id'], 'exist', 'skipOnError' => true, 'targetClass' => MutasibarangT::className(), 'targetAttribute' => ['mutasibarang_id' => 'mutasibarang_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'terimamutasibarang_id' => Yii::t('fe','Terimamutasibarang ID'),
            'mutasibarang_id' => Yii::t('fe','Mutasibarang ID'),
            'tglterima' => Yii::t('fe','Tglterima'),
            'noterimamutasi' => Yii::t('fe','Noterimamutasi'),
            'ruanganpenerima_id' => Yii::t('fe','Ruanganpenerima ID'),
            'ruanganasalmutasi_id' => Yii::t('fe','Ruanganasalmutasi ID'),
            'pegawaipenerima_id' => Yii::t('fe','Pegawaipenerima ID'),
            'pegawaimengetahui_id' => Yii::t('fe','Pegawaimengetahui ID'),
            'keterangan_terima' => Yii::t('fe','Keterangan Terima'),
            'additional_data' => Yii::t('fe','Additional Data'),
            'created_date' => Yii::t('fe','Created Date'),
            'created_by' => Yii::t('fe','Created By'),
            'modified_count' => Yii::t('fe','Modified Count'),
            'last_modified_date' => Yii::t('fe','Last Modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Is Active'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
            'pegawaimenyetujui_id' => Yii::t('fe','Pegawaimenyetujui ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getMutasibarang()
    // {
    //     return $this->hasOne(MutasibarangT::className(), ['mutasibarang_id' => 'mutasibarang_id']);
    // }
}
