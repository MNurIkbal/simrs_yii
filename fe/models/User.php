<?php

namespace app\models;

use Yii;
use app\models\LoginpemakaiK;
use yii\base\NotSupportedException;

class User extends \yii\base\BaseObject implements \yii\web\IdentityInterface
{
    
    public $loginpemakai_id;
    public $pegawai_id;
    public $id_pegawai;
    public $pasien_id;
    public $id_pasien;
    public $nama_pemakai;
    public $nama;
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
    public $authKey;
    public $accessToken;
    public $nama_pegawai;
    public $kelompokpegawai_id;
    public $kelompokpegawai_nama;
    public $kelompokpegawai_namalainnya;
    public $photo;
    public $roles;
    public $gelarbelakang;
    public $spesialis_id;

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        $data = Yii::$app->session->get('user_identity');
        unset($data['id']);
        $data = (object) $data;
        return new static($data);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $user = LoginpemakaiK::find()->where(['nama_pemakai'=>$username])->one(); 
        if($user){
            return new static($user);
        }
        return null;
    }

    /**
     * Finds user by Api
     *
     * @param array $data
     * @return static|null
     */
    public static function setByRestApi(array $data)
    {
        $user = (object) $data; 
        if($user){
            return new static($user);
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->loginpemakai_id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->katakunci_pemakai);
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

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
