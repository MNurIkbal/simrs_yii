<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "terimamutasiobat_t".
 *
 * @property int $terimamutasiobat_id
 * @property int $mutasiobatruangan_id
 * @property string $tglterima
 * @property string $noterimamutasi
 * @property double $totalharganetto
 * @property double $totalhargajual
 * @property string $keterangan_terima
 * @property int $ruanganpenerima_id
 * @property int $ruanganasal_id
 * @property int $pegawaipenerima_id
 * @property int $pegawaimengetahui_id
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
class TerimaMutasiObat extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'terimamutasiobat_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mutasiobatruangan_id', 'ruanganpenerima_id', 'ruanganasal_id', 'pegawaipenerima_id', 'pegawaimengetahui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['mutasiobatruangan_id', 'ruanganpenerima_id', 'ruanganasal_id', 'pegawaipenerima_id', 'pegawaimengetahui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglterima', 'noterimamutasi', 'ruanganpenerima_id', 'ruanganasal_id'], 'required'],
            [['tglterima', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['totalharganetto', 'totalhargajual'], 'number'],
            [['keterangan_terima', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['noterimamutasi'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'terimamutasiobat_id' => 'Terimamutasiobat ID',
            'mutasiobatruangan_id' => 'Mutasiobatruangan ID',
            'tglterima' => 'Tglterima',
            'noterimamutasi' => 'Noterimamutasi',
            'totalharganetto' => 'Totalharganetto',
            'totalhargajual' => 'Totalhargajual',
            'keterangan_terima' => 'Keterangan Terima',
            'ruanganpenerima_id' => 'Ruanganpenerima ID',
            'ruanganasal_id' => 'Ruanganasal ID',
            'pegawaipenerima_id' => 'Pegawaipenerima ID',
            'pegawaimengetahui_id' => 'Pegawaimengetahui ID',
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
