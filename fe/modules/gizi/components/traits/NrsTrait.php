<?php

/**
 * @Author: Aris Munandar
 */

namespace app\modules\gizi\components\traits;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use app\modules\gizi\models\NrsForm;

trait NrsTrait
{
    /**
     * This function will return new form of pagt
     *
     * @return Json
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionNrs()
    {
        $skriningAwal = [
            'is_imt' => 'Apakah IMT < 20.5 atau LLA < 25 cm untuk wanita dan LLA < 26.3 cm untuk Pria',
            'is_berat_badan' => 'Apakah pasien kehilangan BB dalam 3 bulan terakhir?',
            'is_asupan_makan' => 'Apakah asupan makanan menurun 1 minggu terakhir?',
            'is_penyakit_berat' => 'Apakah pasien dengan penyakit berat? (ICU)',
        ];

        $response = $this->guzzleExec($this->_restGizi, [
            'url' => 'nrs',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $this->helper->decrypt(Yii::$app->request->get('id'))
                ]
            ]
        ]);
        extract($response);
        $lookup = ArrayHelper::index($lookup, null, function($val){
                return strtolower($val['lookup_value']);
            }); // grouping lookup by anak dan dewasa
        $skriningNrs = ArrayHelper::index($skriningNrs, null, 'jenisskrining_id');

        $skriningLanjut = [];
        if(isset($lookup['dewasa'])){
            foreach ($lookup['dewasa'] as $jenisskrining) {
                $skriningLanjut[] = [
                    "id" => $jenisskrining['lookup_id'],
                    "nama" => $jenisskrining['lookup_name'],
                    "data" => $skriningNrs[$jenisskrining['lookup_id']],
                ];
            }
        }

        $skrining_nrs_anak = [];
        if(isset($lookup['anak'])){
            foreach ($lookup['anak'] as $key => $jenis_skrining_anak) {
                array_push($skrining_nrs_anak, [
                    'id' => $jenis_skrining_anak['lookup_id'],
                    'nama' => $jenis_skrining_anak['lookup_name'],
                    'data' => $skriningNrs[$jenis_skrining_anak['lookup_id']],
                ]);
            }
        }

        $model = new NrsForm;
        if(!empty($data)) {
            $keterangan = reset($data);
            unset($keterangan['skriningnrs_id']);
            unset($keterangan['skor']);
            $model->skrining = ArrayHelper::map($data,'skriningnrs_id', 'skor', function($val){
                                    return DocoConstants::KATEGORI_NRS[($val['is_anak'] ? 1 : 0)];
                                });

            if(!$keterangan['is_anak']){ // SEt IMT, DLL untuk dewasa
                foreach ($keterangan as $key => $value) {
                    $keterangan[$key] = $value ? 1 : 0;
                }
                $model->keterangan = $keterangan;
            }


            $model->kategori = DocoConstants::KATEGORI_NRS[($keterangan['is_anak'] ? 1 : 0)];
        }
        $kesimpulan = ArrayHelper::index($kesimpulan, null, function($val){
                        return DocoConstants::KATEGORI_NRS[($val['is_anak'] ? 1 : 0)];
                    });

        $data_pasien = $this->_data_pasien;
        $umur = explode('tahun', strtolower($data_pasien['umur']));
        $umur = trim($umur[0]);
        return $this->renderAjax('nrs/__form', compact('model', 'skriningAwal', 'skriningLanjut', 'kesimpulan', 'umur', 'skrining_nrs_anak'));
    }

    public function actionSaveNrs()
    {
        $request = Yii::$app->request;
        $model = new NrsForm;
        $model->load($request->post());
        $keterangan = $model->keterangan;
        $skrining = $model->skrining ? $model->skrining[$model->kategori] : [];
        $details = [];
        foreach ($skrining as $skriningnrs_id => $skor) {
            $details[] = [
                'skriningnrs_id' => $skriningnrs_id,
                'skor' => $skor,
            ];
        }
        $data = [
            'pendaftaran_id' => $this->helper->decrypt(Yii::$app->request->get('id')),
            'tgl_asesmen' => date('Y-m-d H:i:s'),
            'is_imt' => $model->kategori == 0 ? $keterangan['is_imt'] : null,
            'is_berat_badan' => $model->kategori == 0 ? $keterangan['is_berat_badan'] : null,
            'is_asupan_makan' => $model->kategori == 0 ? $keterangan['is_asupan_makan'] : null,
            'is_penyakit_berat' => $model->kategori == 0 ? $keterangan['is_penyakit_berat'] : null,
            'is_anak' => $model->kategori ? array_search($model->kategori, DocoConstants::KATEGORI_NRS) : 0,
            'details' => $details,
        ];

        return $this->guzzleExec($this->_restGizi, [
            'url' => 'nrs/save',
            'method' => 'POST',
            'payload' => [
                'form_params' => $data,
            ],
            'returnResponse' => true,
        ]);
    }
}
