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
class JenisNonTunai extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'kode',
        'nama',
    ];

    public static function tableName()
    {
        return 'jenisnontunai_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kode', 'nama', 'is_active'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'bank_id'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kode'], 'string', 'max' => 50],
            [['nama'], 'string', 'max' => 100],
            [['nama'], 'checkUniqueNama'],
            [['kode'], 'checkUniqueKode'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jenisnontunai_id' => 'Jenis Non Tunai ID',
            'kode' => 'Kode',
            'nama' => 'Jenis',
            'bank_id' => 'Bank',
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

    public function checkUniqueNama($attribute, $params)
    {
        $nama = $this->nama;
        $query = JenisNonTunai::find()->where([
           'LOWER (nama)' => strtolower($nama)
        ]);
        $query->andWhere(['is_deleted' => false]);
        $result = $query->one();
       
        if (!empty($result)) {
            if ($this->jenisnontunai_id != $result->jenisnontunai_id) {
                $this->addError('nama', 'Jenis Pembayaran '.$nama.' telah dipergunakan.');

                return false;
            }
        }

        return true;
    }

    public function checkUniqueKode($attribute, $params)
    {
        $kode = $this->kode;
        $query = JenisNonTunai::find()->where([
           'LOWER (kode)' => strtolower($kode)
        ]);
        $query->andWhere(['is_deleted' => false]);
        $result = $query->one();
       
        if (!empty($result)) {
            if ($this->jenisnontunai_id != $result->jenisnontunai_id) {
                $this->addError('kode', 'Kode '.$kode.' telah dipergunakan.');

                return false;
            }
        }

        return true;
    }

    public function getBank()
    {
        return $this->hasOne(Bank::className(), ['bank_id' => 'bank_id']);
    }
}
