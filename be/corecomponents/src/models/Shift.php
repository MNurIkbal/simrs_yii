<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "shift_m".
 *
 * @property int $shift_id
 * @property string $shift_nama
 * @property string $shift_namalainnya
 * @property string $shift_jamawal
 * @property string $shift_jamakhir
 * @property string $shift_kode
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
 * @property AsuhankeperawatanT[] $asuhankeperawatanTs
 * @property ClosingkasirT[] $closingkasirTs
 * @property FormasishiftM[] $formasishiftMs
 * @property JamkerjaM[] $jamkerjaMs
 * @property MasukkamarT[] $masukkamarTs
 * @property PendaftaranT[] $pendaftaranTs
 * @property PenjualanresepT[] $penjualanresepTs
 * @property PindahkamarT[] $pindahkamarTs
 * @property TandabuktibayarT[] $tandabuktibayarTs
 * @property TandabuktikeluarT[] $tandabuktikeluarTs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 */
class Shift extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shift_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shift_nama', 'shift_jamawal', 'shift_jamakhir'], 'required'],
            [['shift_jamawal', 'shift_jamakhir', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['shift_nama', 'shift_namalainnya'], 'string', 'max' => 50],
            [['shift_kode'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'shift_id' => 'Shift ID',
            'shift_nama' => 'Shift Nama',
            'shift_namalainnya' => 'Shift Namalainnya',
            'shift_jamawal' => 'Shift Jamawal',
            'shift_jamakhir' => 'Shift Jamakhir',
            'shift_kode' => 'Shift Kode',
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
