<?php

namespace app\modules\dcms\models;

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
class LoginpemakaiForm extends \yii\base\Model
{
    public $loginpemakai_id;
    public $pegawai_id;
    public $pasien_id;
    public $nama_pemakai;
    public $katakunci_pemakai;
    public $statuslogin;
    public $photouser;
    public $ruangan_aktifitas;
    public $link_aktifitas;
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
    public $akses_token;
    public $konfirm_katakunci;
    public $instalasi;
    public $pegawai_nama;
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
            [['nama_pemakai','instalasi','pegawai_nama'], 'required'],
            [['statuslogin', 'is_deleted', 'is_active'], 'boolean'],
            [['link_aktifitas', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','pegawai_nama'], 'safe'],
            [['nama_pemakai'], 'string', 'max' => 100],
            [['photouser', 'akses_token'], 'string', 'max' => 200],
            [['katakunci_pemakai'], 'default', 'value' => null],
            ['pegawai_nama', 'checkPegawai'],
            ['konfirm_katakunci', 'checkKataKunci'],
            ['nama_pemakai', 'checkNamaPemakai'],
        ];
    }

    public function checkPegawai($attribute, $params)
    {
        if (!$this->pegawai_id) {
            $this->addError('pegawai_nama','Nama pegawai tidak boleh kosong');
        }
        return true;
    }

    public function checkKataKunci($attribute, $params)
    {
        if ($this->konfirm_katakunci != $this->katakunci_pemakai) {
            $this->addError($attribute,'Kata kunci tidak sama');
        }
        return true;
    }

    public function checkNamaPemakai($attribute, $params)
    {
        if (preg_match('/\s+/', $this->nama_pemakai)) {
            $this->addError($attribute,'Nama tidak boleh mengandung spasi');
        }
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'loginpemakai_id' => Yii::t('fe','ID'),
            'pegawai_id' => Yii::t('fe','Pegawai'),
            'instalasi' => Yii::t('fe','Ruangan'),
            'pasien_id' => Yii::t('fe','Pasien ID'),
            'nama_pemakai' => Yii::t('fe','Nama Pemakai'),
            'katakunci_pemakai' => Yii::t('fe','Katakunci Pemakai'),
            'konfirm_katakunci' => Yii::t('fe','Konfirmasi Kata Kunci'),
            'statuslogin' => Yii::t('fe','Status Login'),
            'photouser' => Yii::t('fe','Foto Pemakai'),
            'ruangan_aktifitas' => Yii::t('fe','Ruangan Aktifitas'),
            'link_aktifitas' => Yii::t('fe','Link Aktifitas'),
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
            'akses_token' => Yii::t('fe','Akses Token'),
        ];
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        if ($this->katakunci_pemakai == '') {
            $this->addError('Katakunci Pemakai tidak boleh kosong');
        }else{
            $this->katakunci_pemakai = Yii::$app->security->generatePasswordHash($password);
        }
        
    }
}
