<?php

namespace Doco\models;

use Yii;
use Doco\models\Loginpemakai;
use Doco\models\RuanganPemakai;
use yii\base\NotSupportedException;

class User extends \yii\base\BaseObject implements \yii\web\IdentityInterface
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
    public $authKey;
    public $accessToken;

    public $akses_pengguna;

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        $db = Yii::$app->db;
        $object = $db->cache(function ($db) use ($id) {
            $user = Loginpemakai::findOne($id);
            if ($user) {
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
        $chace = Yii::$app->cache;
        return $chace->getOrSet($token, function () use ($token) {
            $user = Loginpemakai::find()->joinWith([
                'aksesPengguna' => function ($query) {
                    $query->select([
                        'aksespengguna_k.aksespengguna_id',
                        'peranpengguna_k.peranpengguna_akses',
                        'peranpengguna_k.modul_id',
                        'aksespengguna_k.loginpemakai_id',
                    ])->joinWith([
                        'peranPengguna'
                    ]);
                }
            ])->where(['loginpemakai_k.loginpemakai_id' => $token])->one();
            if ($user) {
                $hakAkses = [];
                foreach ($user->aksesPengguna as $value) {
                    $hakAkses[$value->modul_id] = unserialize($value->peranpengguna_akses);
                }
                $user =  new static($user);
                $user->akses_pengguna = $hakAkses;
                return $user;
            }
            return null;
        }, 3600);
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
        $user = Loginpemakai::find()->where(['nama_pemakai' => $username])->one();
        if ($user) {
            return new static($user);
        }

        return null;
        // $object = $db->cache(function ($db) use($username) {
        //     $user = Loginpemakai::find()->where(['nama_pemakai'=>$username])->one(); 
        //     if($user){
        //         return new static($user);
        //     }
        //     return null;
        // });
        // return $object;
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


    /**
     * This function will retrieve bucket of user_id by modul id
     * 
     * @param $modulId
     * @return Array
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public static function usersIdByModule($modulId)
    {
        return AksesPengguna::find()
            ->select(['peranpengguna_k.peranpenggunanama', 'aksespengguna_k.loginpemakai_id'])
            ->join('JOIN', 'peranpengguna_k', 'aksespengguna_k.peranpengguna_id = peranpengguna_k.peranpengguna_id')
            ->andWhere([
                'peranpengguna_k.modul_id' => $modulId
            ])
            ->asArray()
            ->all();
    }

    public static function usersIdByRuangan($ruangan_id)
    {
        return RuanganPemakai::find()->select(['loginpemakai_id'])
            ->where(['ruangan_id'=>$ruangan_id, 'is_active'=>true, 'is_deleted'=>false])
            ->column();
    }
}
