<?php
namespace Doco\components;

class NoCountDataProvider extends \yii\data\ActiveDataProvider
{
    public $fixedTotalCount = 200;

    protected function prepareTotalCount()
    {
        return $this->fixedTotalCount ? $this->fixedTotalCount : parent::prepareTotalCount();
    }
}
