<?php

namespace app\components;

use Yii;

use Doco\components\DocoConstants;
use app\modules\v1\models\KonfigFarmasi;

class Cache {

	const METHOD_ANTRIAN = 'METHOD_ANTRIAN';

	public static function methodAntrian() {
		return Yii::$app->cache->getOrSet(self::METHOD_ANTRIAN, function ($cache) {
            return KonfigFarmasi::find()->select(['metodeantrian'])->scalar();
        });
	}
}