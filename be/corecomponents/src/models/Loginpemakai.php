<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "loginpemakai_k".
 *
 * @property int $loginpemakai_id
 * @property int $pegawai_id
 * @property int $pasien_id
 * @property string $nama_pemakai
 * @property string $katakunci_pemakai
 * @property bool $statuslogin
 * @property string $photouser
 * @property int $ruangan_aktifitas
 * @property string $link_aktifitas
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
 * @property AksespenggunaK[] $aksespenggunaKs
 * @property RuanganpemakaiK[] $ruanganpemakaiKs
 * @property RuanganM[] $ruangans
 */
class Loginpemakai extends \Doco\components\DocoActiveRecord
{

    public $akses_pengguna;
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
            [['nama_pemakai', 'katakunci_pemakai'], 'required'],
            [['pegawai_id', 'pasien_id', 'ruangan_aktifitas', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pegawai_id', 'pasien_id', 'ruangan_aktifitas', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['statuslogin', 'is_deleted', 'is_active'], 'boolean'],
            [['link_aktifitas', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','loginpemakai_id','akses_pengguna'], 'safe'],
            [['nama_pemakai'], 'string', 'max' => 100],
            [['katakunci_pemakai', 'photouser'], 'string', 'max' => 200],
            [['akses_token'], 'string', 'max' => 255],
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
            'statuslogin' => 'Statuslogin',
            'photouser' => 'Photouser',
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangPemakai()
    {
        return $this->hasMany(\Doco\models\RuanganPemakai::className(), ['loginpemakai_id' => 'loginpemakai_id']);
    }

    public function getPegawai()
    {
        return $this->hasOne(\Doco\models\Pegawai::classname(),['pegawai_id' => 'pegawai_id']);
    }

    public function getAksesPengguna()
    {
        return $this->hasMany(\Doco\models\AksesPengguna::classname(),['loginpemakai_id' => 'loginpemakai_id']);
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields["katakunci_pemakai"]);
        return $fields;
    }
}
