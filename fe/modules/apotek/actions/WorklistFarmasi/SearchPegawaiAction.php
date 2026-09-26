<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\WorklistFarmasi;

use Yii;
use yii\base\Action;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class SearchPegawaiAction extends Action {
    public function run() {
        try {
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $response = $this->controller->guzzleExec($this->controller->_restApotek, [
                'url' => 'worklist/get-list-pegawai',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'term' => Yii::$app->request->get('term', null),
                        'ruangan_id' => $ruangan_id
                    ]
                ]
            ]);

            $list = [];
            foreach ($response as $pegawai) {
                $list[] = [
                    'id' => $pegawai['pegawai_id'],
                    'text' => $pegawai['nomorindukpegawai']." / ".$pegawai['nama_pegawai']
                ];
            }

            return DocoHelpers::response(['results' => $list]);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }
}
