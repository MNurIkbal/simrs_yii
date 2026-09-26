<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-20 09:20:38
 */

namespace app\models;

use Yii;
use yii\base\Model;
use Doco\components\DocoConstants;
use Doco\components\DocoAes;
use Doco\components\DocoHelpers;
use Doco\models\UserMobile;

/**
 * LoginMobileForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginMobileForm extends Model
{
    public $loginmobile_id;
    public $username;
    public $password;
    public $no_handphone;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            // [['username', 'password'], 'required'],
            [['no_handphone'], 'required'],
            [['loginmobile_id'], 'safe'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['no_handphone', 'validateNoHandphone', 'params' => ['is_mobile' => true]],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            // $aes = new DocoAes();
            // $aes->data = $this->password;
            // $aes->key = DocoConstants::VAR_LOGIN_KEY;
            // $aes->setMethode(128, 'CBC');
            // $decryptedPassword = $aes->decrypt();
            $decryptedPassword = DocoHelpers::aes128Decrypt(DocoConstants::VAR_LOGIN_KEY, $this->password);

            if (!$user || $decryptedPassword == '' || !$user->validatePassword($decryptedPassword)) {
                $this->addError($attribute, 'Nama pengguna atau password salah.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        // return $this->validate();
        if ($this->validate()) {
            $this->loginmobile_id = $this->getUser()->loginmobile_id;

            return true;
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = UserMobile::findByNoHandphone($this->no_handphone);
        }

        return $this->_user;
    }

    /**
     * @todo validate no_handphone
     * @author Randy Vianda Putra <randy@docotel.com>
     * @param integer no handphone
     */
    public function validateNoHandphone($attribute, $params)
    {
        $user = $this->getUser();
        return $user;
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            $no_handphone = $this->no_handphone;
            if ($no_handphone == '') {
                $this->addError($attribute, 'No handphone tidak boleh kosong.');
            }
        }
    }
}
