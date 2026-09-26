<?php

namespace Doco\components;

use Yii;
use Doco\models\PublicUser;
use yii\web\TooManyRequestsHttpException;

//relate to DocoPublicRateLimiter in EMR - RSPAD

class DocoPublicRateLimiter extends \yii\filters\RateLimiter
{
	public $enableRateLimitHeaders = false;
	public $errorMessage = 'Request Limit Exceed';
	/**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        if ($this->user === null && Yii::$app->getUser()) {
            $this->user = PublicUser::getIdentityFromJwt();
        }

        if ($this->user instanceof \yii\filters\RateLimitInterface) {
            Yii::debug('Check rate limit', __METHOD__);
            $this->checkRateLimit($this->user, $this->request, $this->response, $action);
        } elseif ($this->user) {
            Yii::info('Rate limit skipped: "user" does not implement RateLimitInterface.', __METHOD__);
        } else {
            Yii::info('Rate limit skipped: user not logged in.', __METHOD__);
        }

        return true;
    }

    public function checkRateLimit($user, $request, $response, $action)
    {
        list($limit, $window) = $user->getRateLimit($request, $action);
        list($allowance, $timestamp) = $user->loadAllowance($request, $action);

        $current = time();
        $allowance += (int) (($current - $timestamp) * $limit / $window);
        if ($allowance > $limit) {
            $allowance = $limit;
        }

        if ($allowance < 1) {
            $user->saveAllowance($request, $action, 0, $current);
            $this->addRateLimitHeaders($response, $limit, 0, $window);
            throw new TooManyRequestsHttpException($this->errorMessage);
        }

        $user->saveAllowance($request, $action, $allowance - 1, $current);
        $this->addRateLimitHeaders($response, $limit, $allowance - 1, (int) (($limit - $allowance + 1) * $window / $limit));
    }
}