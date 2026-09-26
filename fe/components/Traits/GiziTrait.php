<?php

namespace app\components\Traits;

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
use Doco\components\DocoMessages;
use app\components\Traits\Form\DietPasienForm;

trait GiziTrait
{
    /**
     * @var String $type
     * @author ilham.pramono@sirs.co.id
     */
    public $type;

    /**
     * @var String $serviceRest
     * @author ilham.pramono@sirs.co.id
     */
    public $serviceRest;

    /**
     * @var String $mainUrl
     * @author ilham.pramono@sirs.co.id
     */
    public $mainUrl;

    /**
     * @var String $frontendUrl
     * @author ilham.pramono@sirs.co.id
     */
    public $frontendUrl;

    /**
     * @var String $backendUrl
     * @author  ilham.pramono@sirs.co.id
     */
    public $backendUrl;

    /**
     * @var String $saveEndpoint
     * @author  ilham.pramono@sirs.co.id
     */
    public $saveEndpoint;

    /**
     * @var String $verifikasiEndpoint
     */
    public $verifikasiEndpoint;

    private function ServiceByType()
    {
        $serviceRest = null;
        switch ($this->type) {
            case 'RJ':
                $serviceRest = Yii::$app->docoRest->rajal;
                $this->mainUrl = 'pemeriksaan';
                $this->frontendUrl = '/rajal/' . $this->mainUrl;
                $this->backendUrl = 'tra-pemeriksaan';
                $this->saveEndpoint = $this->backendUrl . '/save-diet-pasien';
                $this->verifikasiEndpoint = $this->backendUrl . '/verifikasi-skrining-gizi';
                break;
            case 'RI':
                $serviceRest = Yii::$app->docoRest->ranap;
                $this->mainUrl = 'pemeriksaan-rawat-inap';
                $this->frontendUrl = '/ranap/' . $this->mainUrl;
                $this->backendUrl = $this->mainUrl;
                $this->saveEndpoint = $this->mainUrl . '/save-diet-pasien';
                $this->verifikasiEndpoint = $this->backendUrl . '/verifikasi-skrining-gizi';
                break;
            case 'RD':
                $serviceRest = Yii::$app->docoRest->igd;
                $this->mainUrl = 'pemeriksaan-igd';
                $this->frontendUrl = '/igd/' . $this->mainUrl;
                $this->backendUrl = 'pemeriksaan-igd';
                $this->saveEndpoint = $this->backendUrl . '/save-diet-pasien';
                $this->verifikasiEndpoint = $this->backendUrl . '/verifikasi-skrining-gizi';
                break;
        }
        if (empty($serviceRest)) {
            return $this->responseJson(400, 'Service REST Gizi not set');
        }
        $this->serviceRest = $serviceRest;
    }

    public function actionFormModalDietPasien($id)
    {
        try {
            $this->ServiceByType();
            $pendaftaran_id = DocoHelpers::decrypt($id);
            $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
            $ruangan_asal = Yii::$app->docoVars->workspace("ruangan_id");

            $model = new DietPasienForm;
            $model->pendaftaran_id = $pendaftaran_id;
            $pegawai_id = $this->_pegawai_id;
            $pasien_admisi = isset($this->_pasienadmisi_id) ? $this->_pasienadmisi_id : '';

            $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelaspelayanan_nama', '-');
            $isTitipan = ArrayHelper::getValue($this->_data_pasien, 'is_pasientitipan', false);
            if ($isTitipan) {
                $kelasTagihan = ArrayHelper::getValue($this->_data_pasien, 'kelas_ditagihkan_nama', '-');
            }

            $infoPasien = [
                'nama_pasien' => ArrayHelper::getValue($this->_data_pasien, 'nama_pasien', '-'),
                'penjamin_nama' => ArrayHelper::getValue($this->_data_pasien, 'penjamin_nama', '-'),
                'kelaspelayanan_nama' => $kelasTagihan,
            ];

            $_url = $this->frontendUrl ;
            if (Yii::$app->request->post()) {
                $request = Yii::$app->request->post('DietPasienForm');
                $model->attributes = $request;

                if ($model->validate()) {
                    $post = [
                        'pendaftaran_id' => $model->pendaftaran_id,
                        'catatan_diet' => $model->catatan_diet,
                        'peg_pemesan_id' => $pegawai_id,
                        'instalasi_id' => $instalasi_id,
                        'pasienadmisi_id' => $pasien_admisi,
                        'ruangan_asal' => $ruangan_asal
                    ];

                    return $this->guzzleExec($this->serviceRest, [
                        'method' => 'POST',
                        'url' => $this->saveEndpoint,
                        'payload' => [
                            'form_params' => $post,
                        ],
                        'returnResponse' => true
                    ]);
                } else {
                    $formName = substr(strrchr(get_class($model), "\\"), 1);
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
            }

            return $this->renderAjax('//cppt/gizi/__modal_diet_pasien', get_defined_vars());
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionGetDataDietPasien($id)
    {
        $this->ServiceByType();
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $payload = DocoDatatableHelper::advancedFilterParam();
        $payload['id'] = $pendaftaran_id;
        $data = [];
        $result = [];
        $result['data'] = $data;

        $result['recordsTotal'] = 0;
        $no = 0;
        $res = $this->helper->guzzleExec($this->serviceRest, [
            'method' => 'get',
            'url' => $this->backendUrl . '/get-data-diet-pasien',
            'payload' => [
                'query' => $payload,
            ],
        ]);
        foreach ($res['data'] as $key => $value) {
            $no++;
            $res['data'][$key]['tgl_permintaanmakan'] = date('d/M/Y H:i:s', strtotime($value['tgl_permintaanmakan']));
            $res['data'][$key]['catatan_diet'] = $value['catatan_diet'];
            $res['data'][$key]['pegawai_nama'] = $value['nama_pegawai'];
            $res['data'][$key]['rowNum'] = $no;
        }
        return DocoHelpers::response($res);
    }

    public function actionVerifikasiSkriningGizi($id)
    {
        $this->ServiceByType();
        $pendaftaran_id = $this->helper->decrypt($id);

        return $this->guzzleExec($this->serviceRest, [
            'url' => $this->verifikasiEndpoint,
            'method' => 'POST',
            'payload' => [
                'form_params' => compact('pendaftaran_id')
            ],
            'returnResponse' => true
        ]);
    }
}
