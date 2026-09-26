<?php

namespace Doco\components;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Claim\Factory as ClaimFactory;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Parsing\Decoder;
use Lcobucci\JWT\Parsing\Encoder;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use Yii;
use yii\base\Component;
use yii\base\InvalidParamException;

/**
 * JSON Web Token implementation, based on this library:
 * https://github.com/lcobucci/jwt
 *
 * @author Dmitriy Demin <sizemail@gmail.com>
 * @since 1.0.0-a
 */
class DocoJwt extends Component
{

    /**
     * @var array Supported algorithms
     * @todo Add RSA, ECDSA suppport
     */
    public $supportedAlgs = [
        'HS256' => 'Lcobucci\JWT\Signer\Hmac\Sha256',
        'HS384' => 'Lcobucci\JWT\Signer\Hmac\Sha384',
        'HS512' => 'Lcobucci\JWT\Signer\Hmac\Sha512',
    ];

    /**
     * @var string|array|null $key The key, or map of keys.
     * @todo Add RSA, ECDSA key file support
     */
    public $key;

    /**
     * @var array $user [untuk menampung user object] app\models\User
     */
    public $user;

    /**
     * @var string|array untuk menampung segala activity loginUser
     */
    public $activity;

    /**
     * @var string untuk menampung semua action
     */
    public $action;

    /**
     * @var integer untuk menampung modul user sekarang
     **/
    public $modul_id;

    /**
     * @var string untuk menyatakan bahwa untuk skip modul
     **/

    public $owner;

    /**
     * @var string untuk menyatakan instalasi id
     **/

    public $instalasi_id;

    /**
     * @var string untuk menyatakan ruangan id
     **/

    public $ruangan_id;

    /**
     * @var string untuk menyatakan token
     **/

    public $token;

    /**
     * @var string untuk menyatakan mobile
     **/

    public $is_mobile;

    /**
     * @var string untuk menyatakan mobile
     **/
    public $is_all_expertise_lab = false;

    /**
     * @var string untuk menampung path digital signature
     **/
    public $signature_path = '';

    /**
     * @var string token unik untuk data akses di cache.
     */
    public $uniqueToken = '';


    /**
     * @see [[Lcobucci\JWT\Builder::__construct()]]
     * @return Builder
     */

    public function getBuilder(Encoder $encoder = null, ClaimFactory $claimFactory = null)
    {
        return new Builder($encoder, $claimFactory);
    }

    /**
     * @see [[Lcobucci\JWT\Parser::__construct()]]
     * @return Parser
     */
    public function getParser(Decoder $decoder = null, ClaimFactory $claimFactory = null)
    {
        return new Parser($decoder, $claimFactory);
    }

    /**
     * @see [[Lcobucci\JWT\ValidationData::__construct()]]
     * @return ValidationData
     */
    public function getValidationData($currentTime = null)
    {
        return new ValidationData($currentTime);
    }

    /**
     * Parses the JWT and returns a token class
     * @param string $token JWT
     * @return Token|null
     */
    public function loadToken($token, $validate = true, $verify = true)
    {
        try {
            $token = $this->getParser()->parse((string) $token);
        } catch (\RuntimeException $e) {
            Yii::warning("Invalid JWT provided: " . $e->getMessage(), 'jwt');
            return null;
        } catch (\InvalidArgumentException $e) {
            Yii::warning("Invalid JWT provided: " . $e->getMessage(), 'jwt');
            return null;
        }

        if ($validate && !$this->validateToken($token)) {
            return null;
        }

        if ($verify && !$this->verifyToken($token)) {
            return null;
        }
        $payloadToken = $token->getClaims();
        $this->is_all_expertise_lab = isset($payloadToken['is_all_expertise_lab']) ? $payloadToken['is_all_expertise_lab']->getValue() : false;
        $this->signature_path = isset($payloadToken['signature_path']) ? $payloadToken['signature_path']->getValue() : false;

        return $token;
    }

    /**
     * Validate token
     * @param Token $token token object
     * @return bool
     */
    public function validateToken(Token $token, $currentTime = null)
    {
        $data = $this->getValidationData($currentTime);
        // @todo Add claims for validation

        return $token->validate($data);
    }

    /**
     * Validate token
     * @param Token $token token object
     * @return bool
     */
    public function verifyToken(Token $token)
    {
        $alg = $token->getHeader('alg');

        if (empty($this->supportedAlgs[$alg])) {
            throw new InvalidParamException('Algorithm not supported');
        }

        $signer = Yii::createObject($this->supportedAlgs[$alg]);

        return $token->verify($signer, $this->key);
    }
}
