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

class DeleteCacheAction extends Action {
    public function run($id) {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $user_login = Yii::$app->user->identity->loginpemakai_id;
        $cache = Yii::$app->cache;

        /*check if create and update method */
        if (!empty($request->get('konfigmargin_id')) ) {
            $konfigmargin_id = $request->get('konfigmargin_id');
            $cacheMargin = $cache->get("margin-harga-".$user_login.'-'.$konfigmargin_id);
            if ($cacheMargin !== false) {
                if (isset($cacheMargin[$konfigmargin_id])) {
                    unset($cacheMargin[$konfigmargin_id]);
                    $cache->set("margin-harga-".$user_login.'-'.$konfigmargin_id, $cacheMargin);
                }
                $cacheMargin = $cache->get("margin-harga-".$user_login.'-'.$konfigmargin_id);
            }
        }else{
            $cacheMargin = $cache->get("margin-harga-".$user_login);
            if ($cacheMargin !== false) {
                if (isset($cacheMargin[$id])) {
                    unset($cacheMargin[$id]);
                    $cache->set("margin-harga-".$user_login, $cacheMargin);
                }
            }
        }

        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }
}