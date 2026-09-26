<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\master\actions\MarginHarga;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class DeleteListItemAction extends Action {
    public function run($id = null) {
        $id = DocoHelpers::decrypt($id);
        $userLogin = Yii::$app->user->identity->loginpemakai_id;
        $cacheMargin = Yii::$app->cache->get("margin-harga-{$userLogin}");
        if ($cacheMargin !== false) {
            if (isset($cacheMargin[$id])) {
                unset($cacheMargin[$id]);
                Yii::$app->cache->set("margin-harga-{$userLogin}",$cacheMargin);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }
}