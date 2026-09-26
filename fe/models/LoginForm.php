<?php

namespace app\models;

use Yii;
use yii\base\Model;
use GuzzleHttp\Exception\RequestException;
/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $username_forgot;
    public $password;
    public $nip;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            // ['password', 'validatePassword'],
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

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('fe','Username atau kata sandi salah'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $client = Yii::$app->docoRest->dcms;
            try {
                $response = $client->post('auth/get-token', [
                    'form_params' => [
                        'username' => $this->username, 
                        'password' => $this->password
                    ],
                ]);
                $responseBody = json_decode($response->getBody(), true);
                if ($responseBody['metadata']['status'] == 422) {
                    $this->addError('password', Yii::t('fe','Username atau kata sandi salah'));
                    return false;
                }
            } catch (RequestException $e) {
                return false;
            }

            $jsonRule = json_encode([
                'actions' => ['logout', 'index','notif'],
                'allow' => true,
                'roles' => ['@'],
            ]);
            $dataLogin = $responseBody['response']['user_identity'];

            $session = Yii::$app->session;
            $session->set('uid', $responseBody['response']['uid']);
            $session->set('token', $responseBody['response']['access_token']);
            $session->set('user_identity', $dataLogin);
            $session->set('rules', $jsonRule);
            $session->set('active_workspace', $responseBody['response']['active_workspace']);
            $session->set('workspace', $responseBody['response']['workspace']);
            $session->set('disable_workspace', $responseBody['response']['all_modul']);
            $session->set('menu_module', $responseBody['response']['menus']);
            $session->set('all_rooms', $responseBody['response']['all_rooms']);
            $session->set('notifications', $responseBody['response']['notifications']);
            $session->set('auto_direct', true);

            return Yii::$app->user->login($this->dataUser($dataLogin), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }

    public function dataUser(array $data)
    {
        if ($this->_user === false) {
            unset($data['id']);
            $this->_user = User::setByRestApi($data);
        }

        return $this->_user;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    public function getToken()
    {
        if ($this->validate()) {
            $client = Yii::$app->docoRest->dcms;
            try {
                $response = $client->post('auth/get-token', [
                    'form_params' => [
                        'username' => $this->username, 
                        'password' => $this->password
                    ],
                ]);
                $responseBody = json_decode($response->getBody(), true);
                if ($responseBody['metadata']['status'] == 422) {
                    $this->addError('password', Yii::t('fe','Username atau kata sandi salah'));
                    return false;
                }
            } catch (RequestException $e) {
                return false;
            }

            return $responseBody;

        }
        return false;
    }

    public function getAuthorization()
    {
        if ($this->validate()) {
            $client = Yii::$app->docoRest->dcms;
            try {
                $response = $client->post('auth/check-authorization', [
                    'form_params' => [
                        'username' => $this->username, 
                        'password' => $this->password,
                        'modul_id' => Yii::$app->docoVars->workspace('modul_id')
                    ],
                ]);
                $responseBody = json_decode($response->getBody(), true);
                if ($responseBody['metadata']['status'] == 422) {
                    $this->addError('password', Yii::t('fe','Username atau kata sandi salah'));
                    return false;
                }
            } catch (RequestException $e) {
                return false;
            }

            return $responseBody;

        }
        return false;
    }

    public function userValidasi()
    {
         if ($this->validate()) {
            $client = Yii::$app->docoRest->dcms;
            try {
                $response = $client->post('auth/cek-user', [
                    'form_params' => [
                        'username' => $this->username, 
                        'password' => $this->password
                    ],
                ]);
                $response = json_decode($response->getBody(),true);
                return $response;
            } catch (RequestException $e) {
                return false;
            }

            return true;
        }

        return false;
    }
}
