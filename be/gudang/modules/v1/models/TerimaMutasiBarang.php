<?php

namespace app\modules\v1\models;

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
 *
 * @property MutasibarangT $mutasibarang
 */
class TerimaMutasiBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */

    public $no_pemesanan;
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
            [['mutasibarang_id', 'ruanganpenerima_id', 'ruanganasalmutasi_id', 'pegawaipenerima_id', 'pegawaimengetahui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['mutasibarang_id', 'ruanganpenerima_id', 'ruanganasalmutasi_id', 'pegawaipenerima_id', 'pegawaimengetahui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglterima', 'noterimamutasi', 'ruanganpenerima_id', 'ruanganasalmutasi_id'], 'required'],
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
            'terimamutasibarang_id' => 'Terimamutasibarang ID',
            'mutasibarang_id' => 'Mutasibarang ID',
            'tglterima' => 'Tglterima',
            'noterimamutasi' => 'Noterimamutasi',
            'ruanganpenerima_id' => 'Ruanganpenerima ID',
            'ruanganasalmutasi_id' => 'Ruanganasalmutasi ID',
            'pegawaipenerima_id' => 'Pegawaipenerima ID',
            'pegawaimengetahui_id' => 'Pegawaimengetahui ID',
            'keterangan_terima' => 'Keterangan Terima',
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
