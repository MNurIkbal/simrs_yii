<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use yii\filters\AccessControl;
use yii\web\Response;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\pengadaan\models\KontrakSupplierForm;
use yii\helpers\ArrayHelper;

class DetailAction extends Action
{
    private function callApiDetail($id)
    {
        $request = Yii::$app->docoRest->pengadaan
            ->get('kontrak-supplier/edit-filler', [
                'query' => ['id' => $id]
            ]);
        $response = json_decode($request->getBody(), true);
        $response = ArrayHelper::getValue($response, 'response');
        return $response;
    }

    private function normalizeDataDetails($details)
    {
        $newValue = [];
        $counter = 1;
        foreach ($details as $key => $value) {
            $tempNewValue = $value;
            $harga = ArrayHelper::getValue($value, 'harga');
            $totalHarga = ArrayHelper::getValue($value, 'total_harga');
            $tanggalUpdate = ArrayHelper::getValue($value, 'last_updated_time');
            $pengurang = ArrayHelper::getValue($value, 'pengurang');
            $pengurangRp = $harga * ($pengurang / 100);
            $pengurangRp = DocoHelpers::rupiahDisplay($pengurangRp);
            if ($harga) $harga = DocoHelpers::rupiahDisplay($harga);
            if ($totalHarga) $totalHarga = DocoHelpers::rupiahDisplay($totalHarga);
            if ($tanggalUpdate) $tanggalUpdate = DocoHelpers::convDateTime($tanggalUpdate);
            $tempNewValue['rowNum'] = $counter;
            $tempNewValue['harga'] = $harga;
            $tempNewValue['total_harga'] = $totalHarga;
            $tempNewValue['last_updated_time'] = $tanggalUpdate;
            $tempNewValue['pengurang_rp'] = $pengurangRp;
            $newValue[] = $tempNewValue;
            $counter++;
        }
        return $newValue;
    }

    public function run($id)
    {
        $request = Yii::$app->request;
        $idEnc = $request->get('id');
        $id = DocoHelpers::decrypt($idEnc);
        $response = $this->callApiDetail($id);
        $header = ArrayHelper::getValue($response, 'data.header');
        $details = ArrayHelper::getValue($response, 'data.detail');
        $details = $this->normalizeDataDetails($details);
        $model = new KontrakSupplierForm;
        return $this->controller->renderAjax('detail', get_defined_vars());
    }
}
