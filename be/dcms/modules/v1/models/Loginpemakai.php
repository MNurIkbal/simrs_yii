<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "loginpemakai_k".
 *
 * @property integer $loginpemakai_id
 * @property integer $pegawai_id
 * @property integer $pasien_id
 * @property string $nama_pemakai
 * @property string $katakunci_pemakai
 * @property boolean $statuslogin
 * @property string $photouser
 * @property integer $ruangan_aktifitas
 * @property string $link_aktifitas
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
 * @property string $akses_token
 */
class Loginpemakai extends \app\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loginpemakai_k';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pegawai_id', 'pasien_id', 'ruangan_aktifitas', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['nama_pemakai'], 'required'],
            [['statuslogin', 'is_deleted', 'is_active'], 'boolean'],
            [['link_aktifitas', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['nama_pemakai'], 'string', 'max' => 100],
            [['nama_pemakai'], 'unique'],
            [['katakunci_pemakai', 'photouser', 'akses_token'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'loginpemakai_id' => 'Loginpemakai ID',
            'pegawai_id' => 'Pegawai ID',
            'pasien_id' => 'Pasien ID',
            'nama_pemakai' => 'Nama Pemakai',
            'katakunci_pemakai' => 'Katakunci Pemakai',
            'statuslogin' => 'Status login',
            'photouser' => 'Photo Pemakai',
            'ruangan_aktifitas' => 'Ruangan Aktifitas',
            'link_aktifitas' => 'Link Aktifitas',
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
            'akses_token' => 'Akses Token',
        ];
    }

    public function fields() 
    {
        $fields = parent::fields();
        if (in_array('katakunci_pemakai', $fields))
            unset($fields['katakunci_pemakai']);
        return $fields;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->katakunci_pemakai = Yii::$app->security->generatePasswordHash($password);
    }
}
