<?php

namespace Doco\components;

use yii\di\Instance;
use yii\filters\auth\AuthMethod;
use Doco\components\DocoHelpers;
use Doco\models\UserMobile;
use yii\web\UnauthorizedHttpException;
/**
 * JwtHttpBearerAuth is an action filter that supports the authentication method based on JSON Web Token.
 *
 * You may use JwtHttpBearerAuth by attaching it as a behavior to a controller or module, like the following:
 *
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'bearerAuth' => [
 *             'class' => \sizeg\jwt\JwtHttpBearerAuth::className(),
 *         ],
 *     ];
 * }
 * ```
 *
 * @author Dmitriy Demin <sizemail@gmail.com>
 * @since 1.0.0-a
 */
class DocoJwtHttpBearerAuth extends AuthMethod
{

    /**
     * @var Jwt|string|array the [[Jwt]] object or the application component ID of the [[Jwt]].
     */
    public $jwt = 'jwt';

    /**
     * @var string A "realm" attribute MAY be included to indicate the scope
     * of protection in the manner described in HTTP/1.1 [RFC2617].  The "realm"
     * attribute MUST NOT appear more than once.
     */
    public $realm = 'api';

    /**
     * @var string Authorization header schema, default 'Bearer'
     */
    public $schema = 'Bearer';

    /**
     * @var callable a PHP callable that will authenticate the user with the JWT payload information
     *
     * ```php
     * function ($token, $authMethod) {
     *    return \app\models\User::findOne($token->getClaim('id'));
     * }
     * ```
     *
     * If this property is not set, the username information will be considered as an access token
     * while the password information will be ignored. The [[\yii\web\User::loginByAccessToken()]]
     * method will be called to authenticate and login the user.
     */
    public $auth;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->jwt = Instance::ensure($this->jwt, DocoJwt::className());
    }

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $authHeader = $request->getHeaders()->get('Authorization');
        $authModul = $request->getHeaders()->get('X-Modul-Id');
        $owner = $request->getHeaders()->get('X-Owner');
        $instalasi_id = $request->getHeaders()->get('X-Instalasi-Id');
        $ruangan_id = $request->getHeaders()->get('X-Ruangan-Id');

        $this->jwt->instalasi_id = $instalasi_id;
        $this->jwt->ruangan_id = $ruangan_id;
        $this->jwt->modul_id = DocoHelpers::decrypt($authModul);

        if($authHeader === null) {
            throw new UnauthorizedHttpException('Unauthorized!');
        }
        
        if ($authHeader !== null && preg_match('/^' . $this->schema . '\s+(.*?)$/', $authHeader, $matches)) {
            $token = $this->loadToken($matches[1]);

            if ($token === null) {
                return null;
            }

            if ($token->getClaim("is_mobile",false)) {
                $this->jwt->is_mobile = true;
                $identity = UserMobile::findIdentityByAccessToken($token->getClaim("id"), get_class($this));
            } else {
                if ($this->auth) {
                    $identity = call_user_func($this->auth, $token, get_class($this));
                } else {
                    $identity = $user->loginByAccessToken($token->getClaim("id"), get_class($this));
                }
            }

            $this->jwt->user = $identity;
            $this->jwt->token = $matches[1];

            $uniqueToken = $token->getClaim('jti');
            if ($uniqueToken != null)
                $this->jwt->uniqueToken = $uniqueToken;

            if (YII_ENV == 'dev') {
                $this->jwt->owner = DocoHelpers::decrypt($owner);
            }
            
            return $identity;
        }

        return isset(\Yii::$app->params['enableAutoLogin']) ? true : null;
    }

    /**
     * @inheritdoc
     */
    public function challenge($response)
    {
        $response->getHeaders()->set(
            'WWW-Authenticate',
            "{$this->schema} realm=\"{$this->realm}\", error=\"invalid_token\", error_description=\"The access token invalid or expired\""
        );
    }

    /**
     * Parses the JWT and returns a token class
     * @param string $token JWT
     * @return Token|null
     */
    public function loadToken($token)
    {
        return $this->jwt->loadToken($token);
    }
}
