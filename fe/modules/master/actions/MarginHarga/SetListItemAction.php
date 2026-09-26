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

class SetListItemAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $post = $request->post();
        $setItem = [];
        $cache = Yii::$app->cache;
        try {
            $user_login = Yii::$app->user->identity->loginpemakai_id;
            $header = $request->post('KonfigMarginForm');

            /*check if create and update method */
            if (!empty($header['konfigmargin_id']) ) {
                $konfigmargin_id = $header['konfigmargin_id'];
                $cacheMargin = $cache->get("margin-harga-".$user_login.'-'.$konfigmargin_id);
                $cacheMargin = $this->saveCache($cacheMargin, $user_login, $konfigmargin_id, $post);
                $cache->set("margin-harga-".$user_login.'-'.$konfigmargin_id, $cacheMargin);

            }else{
                $cacheMargin = $cache->get("margin-harga-".$user_login);
                $cacheMargin = $this->saveCacheTambah($cacheMargin, $user_login, $post);
                $cache->set("margin-harga-".$user_login, $cacheMargin, 3600);
            }

            if ($cacheMargin == true) {
                $response['response'] = [
                    'title' => 'Proses Berhasil !',
                    'text' => 'Data berhasil di tambah'
                ];
            }else{
                $response['response'] = [
                    'title' => 'Proses Gagal !',
                    'text' => 'Data gagal di tambah'
                ];
            }

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function saveCache($cacheMargin, $user_login, $konfigmargin_id, $post){
        $dataDetail = $post['KonfigMarginDetailForm'];
        $harga_min = preg_replace("([^0-9\.])","",str_replace(".",",",$dataDetail['harga_min']));
        $harga_max = preg_replace("([^0-9\.])","",str_replace(".",",",$dataDetail['harga_max']));
        $margin = preg_replace("([^0-9\.])","",str_replace(".",",",$dataDetail['margin']));

        if ($cacheMargin == false) {
            Yii::$app->cache->set("margin-harga-".$user_login,[]);
            $cacheMargin = [];
        }

        $setItem = [
            'konfigmargin_id' => $konfigmargin_id,
            'harga_min' => $harga_min,
            'harga_max' => $harga_max,
            'margin' => $margin,
        ];

        $cacheMargin[$konfigmargin_id] = $setItem;
        return $cacheMargin;
    }

    private function saveCacheTambah($cacheMargin, $user_login, $post){
        $dataDetail = $post['KonfigMarginDetailForm'];
        $harga_min = preg_replace("([^0-9\.])","",str_replace(".",",",$dataDetail['harga_min']));
        $harga_max = preg_replace("([^0-9\.])","",str_replace(".",",",$dataDetail['harga_max']));
        $margin = preg_replace("([^0-9\.])","",str_replace(".",",",$dataDetail['margin']));

        if (empty($cacheMargin)) {
            Yii::$app->cache->set("margin-harga-".$user_login,[]);
            $cacheMargin = [];
        }

        $setItem = [
            'harga_min' => $harga_min,
            'harga_max' => $harga_max,
            'margin' => $margin,
        ];

        $cacheMargin[] = $setItem;
        return $cacheMargin;
    }
}