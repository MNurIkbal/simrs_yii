<?php

namespace Doco\actions;

use Yii;
use yii\rest\Action;
use yii\db\Exception;
use yii\base\ErrorException;

class GetRestDataAction extends Action
{
    protected $_keyWords;
    protected $_page;
    protected $_limit;

    public $selected = [];

    const DEFAULT_LIMIT = 10;

    public function init()
    {
        parent::init();
        $this->setAttribute(Yii::$app->request);
    }

    public function run()
    {
        try {
            $offset = ($this->_page - 1) * $this->_limit;
            $model = (new $this->modelClass([
                'extParam' => [$this->_keyWords, $this->_limit, $offset]
            ]));

            $query = $model::find();
            if (!empty($this->selected)) {
                $query->select($this->selected);
            }

            return $query->asArray()->all();
        } catch(Exception $e) {
            Yii::error($e);
        } catch(ErrorException $e) {
            Yii::error($e);
        }
    }

    protected function setAttribute($request)
    {
        $this->_page = $request->get('page', 1);
        $this->_limit = $request->get('limit', self::DEFAULT_LIMIT);
        
        $term = $request->get('term', null);
        $this->_keyWords = !empty($term) ? $term : null;
    }
}