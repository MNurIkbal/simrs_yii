<?php

namespace app\components\Traits;

use Yii;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use app\modules\ranap\models\RujukanPulangForm;

trait RujukanPasienTrait
{

	public $_restGeneralRanap;

	public function actionFormPasienRujuk() 
    {
        $request = Yii::$app->request;
        $pasienadmisi_id = $request->get('pasienadmisi_id');
        $pendaftaran_id = DocoHelpers::decrypt($request->get('id'));
        $data_pasien = $this->_data_pasien;
        $reqData = $this->_restGeneralRanap->get('inf-pasien-rujukan-ranap/get-data',[
            'query' => [
                'pasienadmisi_id' => $pasienadmisi_id,
                'pendaftaran_id' => $pendaftaran_id,
                'intalasi' => $this->_instalasi_id
            ]
        ]);
        $bodyData = json_decode($reqData->getBody(), true);
        
        $dataRujukan = $bodyData['response']['dataRujukan'];
        $diagnosaAwal = $bodyData['response']['diagnosaAwal'];
        $diagnosaKeluar = $bodyData['response']['diagnosaKeluar'];
        $asesmen = $bodyData['response']['asesmen'];
        $intruksiPenunjang = $bodyData['response']['intruksiPenunjang'];
        $intruksiTindakan = $bodyData['response']['intruksiTindakan'];
        $intruksiTerapi = $bodyData['response']['intruksiTerapi'];
        $pegawai = !empty($bodyData['response']['list-pegawai']) ? $bodyData['response']['list-pegawai'] : [];
        $listDpjp = !empty($bodyData['response']['list-dpjp-bpjs']) ? $bodyData['response']['list-dpjp-bpjs'] : [];
        $listDpjp = ArrayHelper::map($listDpjp, 'kode', 'nama');
        $listDiagnosa = !empty($bodyData['response']['list-diagnosa']) ? $bodyData['response']['list-diagnosa'] : [];
        $listDiagnosa = ArrayHelper::map($listDiagnosa, 'kode', 'nama');
        $rujukanPulangForm = new RujukanPulangForm;
        if (!empty($dataRujukan)) {
            $rujukanPulangForm->attributes = $dataRujukan;
        } else {    
            $rujukanPulangForm->pendaftaran_id = $pendaftaran_id;
            $rujukanPulangForm->pasienadmisi_id = $pasienadmisi_id;
            $rujukanPulangForm->diagnosa_masuk = !empty($diagnosaAwal) ? $diagnosaAwal['diagnosa_awal'] : null;
            $rujukanPulangForm->diagnosa_keluar = !empty($diagnosaKeluar) ? $diagnosaKeluar['diagnosa_keluar'] : null;
            $rujukanPulangForm->anamnesis_keluhan_utama = !empty($asesmen) ? $asesmen['keluhan_utama'] : null;
            $rujukanPulangForm->riwayat_penyakit_sekarang = !empty($asesmen) ? $asesmen['r_penyakitsekarang'] : null;
            $r_penyakitdahulu = [];
            if (!empty($asesmen['r_penyakitdahulu'])) {
                $r_penyakitdahulu = json_decode($asesmen['r_penyakitdahulu']);
                $rujukanPulangForm->riwayat_penyakit_dahulu = end($r_penyakitdahulu)->penyakit;
            }
            $rujukanPulangForm->anamnesis_kesadaran = !empty($asesmen) ? $asesmen['jumlah_gcs'] : null;
            $rujukanPulangForm->anamnesis_tensi = !empty($asesmen) ? $asesmen['tensi'] : null;
            $rujukanPulangForm->anamnesis_suhu = !empty($asesmen) ? $asesmen['suhu'] : null;
            $rujukanPulangForm->anamnesis_nadi = !empty($asesmen) ? $asesmen['nadi'] : null;
            $rujukanPulangForm->anamnesis_pernafasan = !empty($asesmen) ? $asesmen['pernapasan'] : null;
        }

        $rujukanPulangForm->pegawai_nama = !empty($data_pasien) ? $data_pasien['nama_pegawai'] : null;

        if(isset($data_pasien['pegawai_kode_bpjs']) && !empty($data_pasien['pegawai_kode_bpjs'])) {
            if(isset($listDpjp[$data_pasien['pegawai_kode_bpjs']])) {
                $rujukanPulangForm->pegawai_kode_bpjs = $data_pasien['pegawai_kode_bpjs'];
            }
        } else {
            $kode = array_search($rujukanPulangForm->pegawai_nama, $listDpjp);
            if($kode !== false) {
                $rujukanPulangForm->pegawai_kode_bpjs = $kode;
            }
        }

        $pununjang = !empty($intruksiPenunjang) ? $intruksiPenunjang : [];
        $tindakan = !empty($intruksiTindakan) ? $intruksiTindakan : [];
        $terapi = !empty($intruksiTerapi) ? $intruksiTerapi : [];
        // var_dump($pununjang, $pasienadmisi_id, $pendaftaran_id);exit();
        $penunjangValue = !empty($dataRujukan) ? $dataRujukan['pemeriksaan_penunjang'] : $this->getDataInstruksi($pununjang);
        $tindakanValue =  !empty($dataRujukan) ? $dataRujukan['tindakan_medis'] : $this->getDataInstruksi($tindakan);
        $terapiValue =  !empty($dataRujukan) ? $dataRujukan['tindakan_terapi'] : $this->getDataInstruksi($terapi);
        $rujukanPulangForm->tanggal_rujukan = !empty($dataRujukan) ? date('d-m-Y', strtotime($dataRujukan['tanggal_rujukan'])) : null;
        $jam = !empty($dataRujukan) ? date('H:i', strtotime($dataRujukan['tanggal_rujukan'])) : null;
        
        return $this->renderAjax('//rujukan/_form_rujukan_pasien', [
            'rujukanPulangForm' => $rujukanPulangForm,
            'pununjang' => $pununjang,
            'tindakan' => $tindakan,
            'terapi' => $terapi,
            'pegawai' => $pegawai,
            'penunjangValue' => $penunjangValue,
            'tindakanValue' => $tindakanValue,
            'terapiValue' => $terapiValue,
            'jam' => $jam,
            'listDpjp' => $listDpjp,
            'listDiagnosa' => $listDiagnosa,
        ]);
    }

    public function actionSaveRujukan()
    {
        $request = Yii::$app->request;
        $model = new RujukanPulangForm;
        $model->load($request->post());
        $model->attributes = $request->post();

        $jam = !empty($request->post('jam_rujukan')) ? $request->post('jam_rujukan') : null;
        $model->tanggal_rujukan = !empty($model->tanggal_rujukan) ? date('Y-m-d H:i:s', strtotime("$model->tanggal_rujukan $jam")) : null;
        if ($model->validate()) {
            $response = $this->_restGeneralRanap->post('inf-pasien-rujukan-ranap/save', [
                'form_params' => $model->attributes,
            ]);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response, false, true);
        } else {
            return DocoHelpers::response($model->errors, 422, 'RujukanPulangForm');
        }
    }

    public function actionCetakRujukan()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = DocoHelpers::decrypt($request->get('id'));
        $path = Yii::getAlias("@download") . "/rujukan-pasien-".$pendaftaran_id.".pdf";
        try {
            $response = $this->_restGeneralRanap->get('inf-pasien-rujukan-ranap/print-pdf-rujukan', [
                'save_to' => $path,
                'query' => [
                        'pendaftaran_id' => $pendaftaran_id,
                        'data_pasien' => $this->_data_pasien
                    ],
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            // var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function getDataInstruksi($data) {
        $result = null;
        foreach ($data as $key => $value) {
            $result .= nl2br(date('d-m-Y H:i', strtotime($value['tgl_instruksi'])). " / " .$value['instruksi']."\n");
            $result = str_replace("<br />", ' ',$result);
        }
        return $result;
    }

    public function actionGetDataObatPrb() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $payload['pendaftaran_id'] = DocoHelpers::decrypt(Yii::$app->request->get('pendaftaran_id'));
        $response = $this->guzzleExec($this->_restGeneralRanap, [
            'url' => 'inf-pasien-rujukan-ranap/get-data-obat-prb',
            'method' => 'GET',
            'payload' => [
                'query' => $payload,
            ]
        ]);
        return $response;
    }
}