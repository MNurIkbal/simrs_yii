<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "bank_m".
 *
 * @property int $bank_id
 * @property int $propinsi_id
 * @property int $matauang_id
 * @property int $kabupaten_id
 * @property string $nama_bank
 * @property string $no_rekening
 * @property string $alamatbank
 * @property string $telp_bank1
 * @property string $telp_bank2
 * @property string $fax_bank
 * @property string $email_bank
 * @property string $website
 * @property string $kodepos
 * @property string $cabangdari
 * @property string $negara
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
class Bank extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bank_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['propinsi_id', 'matauang_id', 'kabupaten_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['propinsi_id', 'matauang_id', 'kabupaten_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['nama_bank', 'no_rekening'], 'required'],
            [['alamatbank', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nama_bank', 'no_rekening', 'cabangdari', 'negara'], 'string', 'max' => 100],
            [['telp_bank1', 'telp_bank2', 'fax_bank', 'email_bank', 'website', 'kodepos'], 'string', 'max' => 50],
            // [['kabupaten_id'], 'exist', 'skipOnError' => true, 'targetClass' => KabupatenM::className(), 'targetAttribute' => ['kabupaten_id' => 'kabupaten_id']],
            // [['matauang_id'], 'exist', 'skipOnError' => true, 'targetClass' => MatauangM::className(), 'targetAttribute' => ['matauang_id' => 'matauang_id']],
            // [['propinsi_id'], 'exist', 'skipOnError' => true, 'targetClass' => PropinsiM::className(), 'targetAttribute' => ['propinsi_id' => 'propinsi_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bank_id' => Yii::t('app', 'Bank ID'),
            'propinsi_id' => Yii::t('app', 'Propinsi ID'),
            'matauang_id' => Yii::t('app', 'Matauang ID'),
            'kabupaten_id' => Yii::t('app', 'Kabupaten ID'),
            'nama_bank' => Yii::t('app', 'Nama Bank'),
            'no_rekening' => Yii::t('app', 'No Rekening'),
            'alamatbank' => Yii::t('app', 'Alamatbank'),
            'telp_bank1' => Yii::t('app', 'Telp Bank1'),
            'telp_bank2' => Yii::t('app', 'Telp Bank2'),
            'fax_bank' => Yii::t('app', 'Fax Bank'),
            'email_bank' => Yii::t('app', 'Email Bank'),
            'website' => Yii::t('app', 'Website'),
            'kodepos' => Yii::t('app', 'Kodepos'),
            'cabangdari' => Yii::t('app', 'Cabangdari'),
            'negara' => Yii::t('app', 'Negara'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
        ];
    }
}
