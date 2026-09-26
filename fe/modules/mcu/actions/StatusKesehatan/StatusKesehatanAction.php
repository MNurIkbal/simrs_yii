<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatam
 * Powered by Sirs
 */

namespace Doco\mcu\actions\StatusKesehatan;

use app\modules\mcu\models\StatusKesehatanForm;
use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use yii\base\View;
use yii\helpers\ArrayHelper;

class StatusKesehatanAction extends Action {
    protected $model;
    protected $requestData;
    protected $dataPasien;

    public function run() {
        $request = Yii::$app->request;
        $this->model = new StatusKesehatanForm;
        $pasien_id = $request->get('pasien_id') != null ? DocoHelpers::decrypt($request->get('pasien_id')) : null;
        $this->dataPasien = Yii::$app->cache->get('data-pasien-' . $pasien_id);
        $this->requestData = [
            'pasien_id' => $pasien_id,
            'pendaftaran_id' => $request->get('id') != null ? DocoHelpers::decrypt($request->get('id')) : null,
            'ruangan_id' => ArrayHelper::getValue($this->dataPasien, 'ruangan_id'),
            'pegawai_id' => ArrayHelper::getValue($this->dataPasien, 'pegawai_id'),  
        ];
        
        if($post = $request->post()) {
            return $this->saveData($post);
        } else {
            return $this->renderPage();
        }
    }

    private function renderPage() {
        $title = Yii::t('fe', 'Status Kesehatan');
        $this->model->nama = ArrayHelper::getValue($this->dataPasien, 'nama_pasien');
        $this->model->no_rm = ArrayHelper::getValue($this->dataPasien, 'no_rekam_medik');
        $this->model->usia = str_replace('umur ', '', DocoHelpers::getUmur(ArrayHelper::getValue($this->dataPasien, 'tanggal_lahir')));
        $data_kelaikan = $this->controller->guzzleExec(Yii::$app->docoRest->mcu, [
            'url' => 'allow/get-lookup-by',
            'payload' => [
                'query' => [
                    'column' => 'lookup_type',
                    'value' => 'status_kesehatan'
                ]
            ]
        ]);
        $data_kelaikan = ArrayHelper::map($data_kelaikan, 'lookup_id', 'lookup_value');
        $get_status_kesehatan = $this->controller->guzzleExec(Yii::$app->docoRest->mcu, [
            'url' => 'pemeriksaan/get-status-kesehatan',
            'payload' => [
                'query' => [
                    'id' => $this->requestData['pendaftaran_id']
                ]
            ]
        ]);

        if(!empty($get_status_kesehatan)) {
            $additional_data = json_decode(ArrayHelper::getValue($get_status_kesehatan, 'additional_data'));
            $this->model->attributes = $get_status_kesehatan;
            $this->model->usia = !empty($this->model->usia) ? $this->model->usia : ArrayHelper::getValue($additional_data, 'usia');
            $this->model->bagian = ArrayHelper::getValue($additional_data, 'bagian');
            $this->model->evaluasi = ArrayHelper::getValue($additional_data, 'evaluasi');
        }

        return $this->controller->renderAjax('partials_prima/_status_kesehatan', [
            'pendaftaran_id' => $this->requestData['pendaftaran_id'],
            'pasien_id' => $this->requestData['pasien_id'],
            'title' => $title,
            'model' => $this->model,
            'data_pasien' => $this->dataPasien,
            'data_kelaikan' => $data_kelaikan,
        ]);
    }

    private function saveData($post) {
        $formName = substr(strrchr(get_class($this->model), "\\"), 1);
        $data = $post['StatusKesehatanForm'];
        $this->model->attributes = $data;
        $this->model->tgl_periksa = date('Y-m-d', strtotime($data['tgl_periksa']));
        $this->model->pendaftaran_id = $this->requestData['pendaftaran_id'];
        $this->model->pasien_id = $this->requestData['pasien_id'];
        $this->model->ruanganterakhir_id = $this->requestData['ruangan_id'];
        $this->model->pegawai_id = $this->requestData['pegawai_id'];
        if(!$this->model->validate()) {
            $response = $this->model->errors;
            return DocoHelpers::response($response, 422, $formName);
        }

        return $this->controller->guzzleExec(Yii::$app->docoRest->mcu, [
            'method' => 'post',
            'url' => 'pemeriksaan/save-status-kesehatan',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => DocoHelpers::encrypt($this->requestData['pendaftaran_id'])
                ],
                'form_params' => $this->model->attributes
            ],
            'returnResponse' => true
        ]);
    }
}