<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-20 09:41:07
 */

namespace Doco\models;

use Yii;
use Doco\models\LoginMobile;
use yii\base\NotSupportedException;

class UserMobile extends \yii\base\BaseObject implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public $loginmobile_id;
    public $nama_pemakai;
    public $katakunci_pemakai;
    public $statuslogin;
    public $no_handphone;
    public $email;
    public $link_aktifitas;
    public $akses_token;
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
    public $player_id;

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        $db = Yii::$app->db;
        $object = $db->cache(function ($db) use($id) {
            $user = LoginMobile::findOne($id);
            if(count($user)) {
                return new static($user);
            }
            return null;
        });
        return $object;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $db = Yii::$app->db;
        $chace = Yii::$app->cache->get('mobile-'.$token);
        if (empty($chace)) {
            $chace = $db->cache(function ($db) use($token) {
                $user = LoginMobile::find()->where(['loginmobile_k.loginmobile_id'=>$token])->one();

                return $user;
            });

            Yii::$app->cache->set('mobile-'.$token, $chace, 3600);
        }
        return $chace;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $db = Yii::$app->db;
        $user = LoginMobile::find()->where(['nama_pemakai'=>$username])->one();
        if(count($user)){
            return new static($user);
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->loginmobile_id;
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

    /**
     * Finds user by no_handphone
     *
     * @param string $no_handphone
     * @return static|null
     */
    public static function findByNoHandphone($no_handphone)
    {
        $db = Yii::$app->db;
        $user = LoginMobile::find()->where(['no_handphone' => $no_handphone])->one();
        if (count($user)) {
            return new static($user);
        }
        else {
            $model = new LoginMobile;
            $model->no_handphone = $no_handphone;
            $model->save();
            if ($model->save()) {
                $user = LoginMobile::find()->where(['no_handphone' => $no_handphone])->one();
                return new static($user);
            }
        }
    }
}
