<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\master\actions\MarginHarga;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoDatatableHelper;

class GetListItemAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $resetCache = [];
        $no_urut = $request->get('start', 1);

        $user_login = Yii::$app->user->identity->loginpemakai_id;
        if (!empty($request->get('id')) ) {
            $id = $request->get('id');
            $cacheMargin = Yii::$app->cache->get("margin-harga-".$user_login.'-'.$id);
            $result = $this->listDataMargin($cacheMargin, $no_urut, $draw);
        } else {
            $cacheMargin = Yii::$app->cache->get("margin-harga-".$user_login);
            $result = $this->listDataMargin($cacheMargin, $no_urut, $draw);
        }

        return $result;
    }
}