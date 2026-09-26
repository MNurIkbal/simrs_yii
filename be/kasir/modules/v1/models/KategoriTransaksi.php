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
class KategoriTransaksi extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'kategoritransaksi_kode',
        'kategoritransaksi_nama',
    ];

    public static function tableName()
    {
        return 'kategoritransaksi_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kategoritransaksi_kode', 'kategoritransaksi_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kategoritransaksi_kode'], 'string', 'max' => 50],
            [['kategoritransaksi_nama'], 'string', 'max' => 100],
            [['kategoritransaksi_nama'], 'checkUniqueNama'],
            [['kategoritransaksi_kode'], 'checkUniqueKode'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kategoritransaksi_id' => 'Esselon ID',
            'kategoritransaksi_kode' => 'Nama Esselon',
            'kategoritransaksi_nama' => 'Nama Lainnya',
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
        $kategoritransaksi_nama = $this->kategoritransaksi_nama;
        $query = KategoriTransaksi::find()->where([
           'LOWER (kategoritransaksi_nama)' => strtolower($kategoritransaksi_nama)
        ]);
        $query->andWhere(['is_deleted' => false]);
        $result = $query->one();
       
        if (!empty($result)) {
            if ($this->kategoritransaksi_id != $result->kategoritransaksi_id) {
                $this->addError('kategoritransaksi_nama', 'Kategori '.$kategoritransaksi_nama.' telah dipergunakan.');

                return false;
            }
        }

        return true;
    }

    public function checkUniqueKode($attribute, $params)
    {
        $kategoritransaksi_kode = $this->kategoritransaksi_kode;
        $query = KategoriTransaksi::find()->where([
           'LOWER (kategoritransaksi_kode)' => strtolower($kategoritransaksi_kode)
        ]);
        $query->andWhere(['is_deleted' => false]);
        $result = $query->one();
       
        if (!empty($result)) {
            if ($this->kategoritransaksi_id != $result->kategoritransaksi_id) {
                $this->addError('kategoritransaksi_kode', 'Kode Kategori '.$kategoritransaksi_kode.' telah dipergunakan.');

                return false;
            }
        }

        return true;
    }
}
