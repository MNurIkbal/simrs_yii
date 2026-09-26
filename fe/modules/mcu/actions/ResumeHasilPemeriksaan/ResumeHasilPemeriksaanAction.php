<?php

/**
 * @author : Maulana Muhammad Rizky(maulana.rizky@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\mcu\actions\ResumeHasilPemeriksaan;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\modules\mcu\models\HasilPemeriksaanPhrForm;
use app\modules\mcu\models\ResumeHasilPemeriksaanForm;
use yii\base\View;
use yii\helpers\ArrayHelper;

class ResumeHasilPemeriksaanAction extends Action
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
        $this->model = new ResumeHasilPemeriksaanForm;
        $this->requestData = [
            'pendaftaran_id' => $request->get('id') != null ? DocoHelpers::decrypt($request->get('id')) : null,
            'pasien_id' => $request->get("pasien_id") != null ? DocoHelpers::decrypt($request->get('pasien_id')) : null,
        ];
        $this->dataPasien = Yii::$app->cache->get('data-pasien-' . $this->requestData['pasien_id']);

        if ($request->post()) {
            $this->model->attributes = $request->post("ResumeHasilPemeriksaanForm");
            return $this->saveProcessData();
        } else {
            $title = Yii::t('fe', 'Resume Hasil Pemeriksaan');
            $resultPhr = $this->getResultPhr();
            
            $hasilPemeriksaan = ArrayHelper::getValue($resultPhr, 'resume_pemeriksaan');
            $hasilPemeriksaan = $hasilPemeriksaan != null ? json_decode($hasilPemeriksaan, true ) : null;
            $this->model->attributes = $hasilPemeriksaan;   
            
            return $this->controller->renderAjax('partials_prima/_resume_hasil_pemeriksaan', [
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
            $resumePemeriksaan = $this->model->resume_hasil_pemeriksaan;
            $filterPemeriksaan = $this->model->tatalaksana_pemeriksaan;

            if(empty($resumePemeriksaan) && empty($filterPemeriksaan)) {
                return DocoHelpers::responseTemplate(422, 'error', [], ['message'=>'Silahkan isi salah satu data baik Resume Hasil Pemeriksaan atau Tatalaksana Hasil Pemeriksaan ']);
            }

            if (!$this->model->validate()) {
                $response = $this->model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }

            $this->model->pendaftaran_id = ArrayHelper::getValue($this->requestData, 'pendaftaran_id');
            $this->model->pasien_id = ArrayHelper::getValue($this->requestData, 'pasien_id');

            return $this->controller->guzzleExec(Yii::$app->docoRest->mcu, [
                'method' => 'POST',
                'url' => 'pemeriksaan/save-resume-hasil-pemeriksaan',
                'payload' => [
                    'query' => [
                        'pendaftaran_id' => $this->requestData['pendaftaran_id'],
                        'pasien_id' => $this->requestData['pasien_id'],
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
