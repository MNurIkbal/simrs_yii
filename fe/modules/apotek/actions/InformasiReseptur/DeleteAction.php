<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class DeleteAction extends Action {
    public function run($id) {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = Yii::$app->docoRest->apotek->delete('inf-reseptur/batal-reseptur', [
                'query' => [
                    'id' => $id
                ]
            ]);
            $result = json_decode($response->getBody(), true);

            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}