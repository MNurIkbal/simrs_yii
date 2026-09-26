<?php 

namespace app\components\Services\Gudang;

use Yii;
use app\components\Traits\ControllerHelperTrait;

class KonfigFarmasiService {

    use ControllerHelperTrait;
    
    protected $service; 

    public function __construct() {
        $this->service = Yii::$app->docoRest->apotek;
    }

    public function execute() {
        $tagOrKey = Yii::$app->request->get('id', null);
        $clearBy = Yii::$app->request->get('by', null);
        if($clearBy == 'tag') {
            $url = 'allow/delete-cache-by-tag';
            $params = ['tag' => $tagOrKey];
        } else {
            $url = 'allow/delete-cache-by-key';
            $params = ['key' => $tagOrKey];
        }

        return $this->guzzleExec($this->service, [
            'url' => $url,
            'payload' => [
                'query' => $params
            ],
            'returnResponse' => true
        ]);
    }
}