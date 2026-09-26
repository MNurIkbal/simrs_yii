<?php

namespace Doco\components;

use Yii;
use yii\di\Instance;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

class DocoPostgreFunctionAQ extends \yii\db\ActiveQuery
{
	public $paramObj;
	public $modelClass;

	public function __construct()
    {
        parent::__construct($this->modelClass);
    }

    protected function getPrimaryTableName()
    {
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        return $modelClass::tableName($this->paramObj);
    }
}