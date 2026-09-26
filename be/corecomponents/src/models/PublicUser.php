<?php

namespace Doco\models;

use Yii;
use Doco\models\LoginUserPublic;
use Doco\components\DocoConstants;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;

class PublicUser extends ActiveRecord implements \yii\web\IdentityInterface, \yii\filters\RateLimitInterface
{
	public $id;
	public $user_id;
	public $api_key;

	public $rateLimit = DocoConstants::RATELIMITER_NUMBEROFREQUEST; // number of request per seconds
	public $allowance;
	public $allowance_updated_at;

	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'publicuser_k';
    }

    public static function findIdentity($id)
    {
        $db = Yii::$app->db;
        $object = $db->cache(function ($db) use($id) {
            $user = self::findOne($id); 
            if($user) {
                return $user;
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
    	return static::findOne(['id'=>1]);
    }
    /**
     * @inheritdoc
     */
    public function getId()
    {
    	return null;
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
     * @inheritdoc
     */
    public static function findPublicUserIdentity($token, $type = null)
    {
    	if($token === null){
    		return null;
    	}
        $db = Yii::$app->db;
        $chace = Yii::$app->cache->get($token);
        if ($chace === false) {
            $chace = $db->cache(function ($db) use($token) {
                $user_id = 1;
                $user = static::findOne(['user_id'=>$token]); 
                return $user;
                
                return null;
            });

            Yii::$app->cache->set($token,$chace,3600);
        }
        return $chace;
    }

    /**
     * @inheritdoc
     */
    public function getRateLimit($request, $action)
    {
    	return [$this->rateLimit, DocoConstants::RATELIMITER_INSECONDS];
    }

    /**
     * @inheritdoc
     */
    public function loadAllowance($request, $action)
    {
	    $cache = Yii::$app->cache;

	    $cache_id = 'limiter-'.Yii::$app->jwt->user->user_id;
	    if($cache->get($cache_id) === false){
	    	$val = [
		    	'allowance' => 0,
		    	'allowance_updated_at' => time()
		    ];
		    $cache->set($cache_id,$val,3600);
	    }
	    $data_cache = $cache->get($cache_id);
	    $this->allowance = $data_cache['allowance'];
	    $this->allowance_updated_at = $data_cache['allowance_updated_at'];
    	return [$this->allowance, $this->allowance_updated_at];
    }

    /**
     * @inheritdoc
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
	    // $this->allowance = $allowance;
	    // $this->allowance_updated_at = $timestamp;
	    // $this->save();
	    $cache = Yii::$app->cache;

	    $cache_id = 'limiter-'.Yii::$app->jwt->user->user_id;
	    $val = [
		    	'allowance' => $allowance,
		    	'allowance_updated_at' => $timestamp
		    ];
		$cache->set($cache_id,$val,3600);
    }

    public function getIdentityFromJwt()
    {
        return Yii::$app->jwt->user;
    }

    public static function findIdentityWithKey($uid,$sign,$type=null)
    {
        $db = Yii::$app->db;
        $chace = Yii::$app->cache->get($uid.'-'.$sign);
        if ($chace === false) {
            $chace = $db->cache(function ($db) use($uid) {
                $user = static::findOne(['user_id'=>$uid]);
                return $user;
                
                return null;
            });

            Yii::$app->cache->set($uid.'-'.$sign,$chace,3600);
        }
        return $chace;
    }
}