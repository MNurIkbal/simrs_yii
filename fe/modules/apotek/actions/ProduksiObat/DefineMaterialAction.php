<?php

/**
 * @author : Maulana Muhammad Rizky
 * Powered by Sirs
 */

namespace Doco\apotek\actions\ProduksiObat;

use app\components\DocoHelpers;
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;

class DefineMaterialAction extends Action
{
    protected $_restApotek;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    public function run()
    {
        $request = Yii::$app->request;
        try {
            $pemesananId = $request->get("pemesananproduksi_id");
            $isEdit = filter_var($request->get("isEdit"), FILTER_VALIDATE_BOOLEAN);
            $pemesananId = DocoHelpers::decrypt($pemesananId);

            $title  = \Yii::t('fe', 'Informasi Produksi Obat');
            $cacheProduksiObat = Yii::$app->cache->get('cache-produksi-obat-' . $pemesananId.'-'.Yii::$app->docoVars->user('id_pegawai'));
            $response = $this->controller->guzzleExec($this->_restApotek, [
                'url' => 'inf-produksi-obat/define-material?pemesananproduksi_id',
                'payload' => [
                    'query' => [
                        "pemesananproduksi_id" => $pemesananId
                    ]
                ],
            ]);

            $defaultValue = [];
            $existingValue = ArrayHelper::getValue($response, 'bahanProduksi', []);
            $headerValue = ArrayHelper::getValue($response, 'headerData', []);
            $ruangan = ArrayHelper::getValue($response, 'ruangan', []);
            $produksiObatAlkesId = isset($headerValue['produksiobatalkes_id']) ? DocoHelpers::encrypt($headerValue['produksiobatalkes_id']) : null;

            foreach ($existingValue as $value) {
                if (is_array($cacheProduksiObat)) {
                    if (isset($cacheProduksiObat['racikan-' . $value['produksiObatId']])) {
                        $mergeArray = array_merge($cacheProduksiObat['racikan-' . $value['produksiObatId']]);
                        $value['detailObat'] = $mergeArray;
                    }
                }

                $defaultValue[] = $value;
            }

            $defaultValue = json_encode($defaultValue);
            return $this->controller->render('define-material', compact('defaultValue', 'title', 'headerValue', 'pemesananId','ruangan', 'isEdit', 'produksiObatAlkesId'));
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }
}
