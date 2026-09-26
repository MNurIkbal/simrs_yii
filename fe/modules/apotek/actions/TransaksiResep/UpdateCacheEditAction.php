<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\TransaksiResep;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class UpdateCacheEditAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $cacheKey = isset($post['cacheKey']) ? $post['cacheKey'] : null;
            $cacheLabel = 'addObatEditReseptur' . $cacheKey;
            $cacheTrans = Yii::$app->cache->get($cacheLabel);
            $transApotek = json_decode($cacheTrans, true);

            $resultTransApotek  = [];
            if (is_array($transApotek)) {
                foreach ($transApotek as $key => $value) {
                    if (!isset($value['posisi'])) {
                        continue;
                    }
    
                    if ($value['posisi'] == $post['position']) {
                        $value['signa_id'] = is_numeric($post['signa_id']) ? $post['signa_id'] : null;
                        $value['signa'] = $post['signa_nama'];
                    }
    
                    $resultTransApotek[] = $value;
                }
            }

            $listTrans = json_encode($resultTransApotek); 
            $final_list = $this->controller->groupingResep($resultTransApotek);
            Yii::$app->cache->set($cacheLabel, $listTrans);
            $result['data'] = $final_list;
            $result['message'] = Yii::t('fe', 'Data berhasil di simpan');
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
}
