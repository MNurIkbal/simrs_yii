<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "esselon_m".
 *
 * @property integer $esselon_id
 * @property string $esselon_nama
 * @property string $esselon_namalainnya
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class Edc extends \Doco\components\DocoActiveRecord
{
    const EDC_KODE = 'edclist_kode';
    const EDC_NAMA = 'edclist_namamesin';
    const EDC_BANK = 'edclist_bank';
    const STRINGS = 'string';
    const IS_DELETED = 'is_deleted';
    const IS_ACTIVE = 'is_active';

    protected $xssProtected = [
        self::EDC_KODE,
        self::EDC_NAMA,
    ];

    public static function tableName()
    {
        return 'edclist_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[self::EDC_KODE, self::EDC_NAMA, self::EDC_BANK, self::IS_ACTIVE], 'required'],
            [['additional_data'], self::STRINGS],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [[self::EDC_BANK, 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [[self::IS_DELETED, self::IS_ACTIVE], 'boolean'],
            [[self::EDC_KODE], self::STRINGS, 'max' => 50],
            [[self::EDC_NAMA], self::STRINGS, 'max' => 100],
            [[self::EDC_NAMA], 'checkUniqueNama'],
            [[self::EDC_KODE], 'checkUniqueKode'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'edclist_id' => 'EDC ID',
            self::EDC_KODE => 'Kode Mesin EDC',
            self::EDC_NAMA => 'Nama Mesin EDC',
            self::EDC_BANK => 'Bank',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            self::IS_DELETED => 'Is Deleted',
            self::IS_ACTIVE => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }

    public function checkUniqueNama($attribute, $params)
    {
        $edclist_namamesin = $this->edclist_namamesin;
        $query = Edc::find()->where([
           'LOWER (edclist_namamesin)' => strtolower($edclist_namamesin)
        ]);
        $query->andWhere([self::IS_DELETED => false]);
        $result = $query->one();
       
        if (!empty($result) && ($this->edclist_id != $result->edclist_id)) {
            $this->addError('edclist_namamesin', 'Nama '. $edclist_namamesin.' telah dipergunakan.');

            return false;
        }

        return true;
    }

    public function checkUniqueKode($attribute, $params)
    {
        $edclist_kode = $this->edclist_kode;
        $query = Edc::find()->where([
           'LOWER (edclist_kode)' => strtolower($edclist_kode)
        ]);
        $query->andWhere([self::IS_DELETED => false]);
        $result = $query->one();
       
        if (!empty($result) && ($this->edclist_id != $result->edclist_id)) {
            $this->addError('edclist_kode', 'Kode '. $edclist_kode.' telah dipergunakan.');

            return false;
        }

        return true;
    }

    public function getBank()
    {
        return $this->hasOne(Bank::className(), ['bank_id' => self::EDC_BANK]);
    }
}
