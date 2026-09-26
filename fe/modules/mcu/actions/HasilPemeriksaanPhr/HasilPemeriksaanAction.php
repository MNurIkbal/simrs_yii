<?php

/**
 * @author : Maulana Muhammad Rizky(maulana.rizky@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\mcu\actions\HasilPemeriksaanPhr;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\modules\mcu\models\HasilPemeriksaanPhrForm;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;

class HasilPemeriksaanAction extends Action
{
    protected $model;
    protected $requestData;
    protected $dataPasien;

    /**
     * Main execute.
     */
    public function run()
    {
        $request = Yii::$app->request;
        $this->model = new HasilPemeriksaanPhrForm;

        $this->requestData = [
            'pendaftaran_id' => $request->get('id') != null ? DocoHelpers::decrypt($request->get('id')) : null,
            'pasien_id' => $request->get("pasien_id") != null ? DocoHelpers::decrypt($request->get('pasien_id')) : null,
        ];
        $this->dataPasien = Yii::$app->cache->get('data-pasien-' . $this->requestData['pasien_id']);

        if ($request->post()) {
            $this->model->attributes = $request->post("HasilPemeriksaanPhrForm");
            return $this->saveProcessData();
        } else {
            $title = Yii::t('fe', 'Status Kesehatan');
            $resultPhr = $this->getResultPhr();

            $hasilPemeriksaan = ArrayHelper::getValue($resultPhr, 'hasil_pemeriksaan_kesehatan');
            $hasilPemeriksaan = $hasilPemeriksaan != null ? json_decode($hasilPemeriksaan, true ) : null;
            $this->model->attributes = $hasilPemeriksaan;
            
            return $this->controller->renderAjax('partials_prima/_hasil_pemeriksaan_phr', [
                'pendaftaran_id' => $request->get('id'),
                'pasien_id' => $request->get('pasien_id'),
                'title' => $title,
                'model' => $this->model,
            ]);
        }
    }

    /**
     * Function ini untuk melakukan simpan data.
     * 
     * @author Maulana Muhammad Rizky
     * @return json $response
     */
    private function saveProcessData()
    {
        try {
            $formName = substr(strrchr(get_class($this->model), "\\"), 1);

            if (!$this->model->validate()) {
                $response = $this->model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }
            
            $this->model->pendaftaran_id = ArrayHelper::getValue($this->requestData, 'pendaftaran_id');
            $this->model->pasien_id = ArrayHelper::getValue($this->requestData, 'pasien_id');
            return $this->controller->guzzleExec(Yii::$app->docoRest->mcu, [
                'method' => 'POST',
                'url' => 'pemeriksaan/save-hasil-pemeriksaan-phr',
                'payload' => [
                    'query' => [
                        'pendaftaran_id' => $this->requestData['pendaftaran_id'],
                        'ruangan_id' => ArrayHelper::getValue($this->dataPasien, 'ruangan_id'),
                    ],
                    'form_params' => $this->model->attributes
                ],
                'returnResponse' => true
            ]);
        } catch (\Exception $th) {
            return DocoHelpers::response($th->getMessage(), 500);
        }
    }

    /**
     * Get result PHR Data.
     * 
     * @author Maulana Muhammad Rizky.
     * @return array $data;
     */
    private function getResultPhr()
    {
        return $this->controller->guzzleExec(Yii::$app->docoRest->mcu, [
            'method' => 'GET',
            'url' => 'pemeriksaan/get-status-kesehatan',
            'payload' => [
                'query' => [
                    'id' => $this->requestData['pendaftaran_id']
                ],
            ],
        ]);
    }
}
