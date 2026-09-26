<?php

namespace app\modules\v1\controllers;

use Yii;

use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\PindahKamar;
use app\modules\v1\models\Pendaftaran;

use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\models\bpjs\BpjsAplicare;
use yii\helpers\ArrayHelper;

class PindahKamarController extends DocoActiveController
{
    /**
     * @todo Variable yang mendefinisikan model class
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public $modelClass = 'app\modules\v1\models\PindahKamar';

    /**
     * @todo Fungsi custom verbs
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function verbs()
    {
        $verbs = parent::verbs();

        return $verbs;
    }

    /**
     * @todo Fungsi custom actions
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actions()
    {
        $actions = parent::actions();

        return $actions;
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Fungsi untuk mendapatkan data options
     * @return array
     */
    public function actionGetDataOptions()
    {
        $restSerconn = Yii::$app->serconn->guzzle();
        $headers = Yii::$app->request->headers;
        $params['authorization'] = $headers['authorization'];
        $params['x-owner'] = $headers['x-owner'];
        $params['jeniskasuspenyakit'] = json_encode(['JenisKasusPenyakit', null, 'jeniskasuspenyakit_nama']);
        $params['kelaspelayanan'] = json_encode(['KelasPelayanan', null, 'kelaspelayanan_nama']);
        $params['warnatempattidur'] = json_encode(['WarnaTempatTidur', null, 'kamarruangan_jenis']);

        $request = $restSerconn->post('on/pindahkamar/getoptions', [
            'body' => json_encode($params)
        ]);
        $response = json_decode($request->getBody(), true);

        return $response['Results'][0]['data'];
    }

    /**
     * @todo Fungsi untuk mendapatkan data pasien
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @return array
     */
    public function actionGetDataPasienSerconn()
    {
        $restSerconn = Yii::$app->serconn->guzzle();
        $request = Yii::$app->request->get();
        $headers = Yii::$app->request->headers;
        $params['authorization'] = $headers['authorization'];
        $params['x-owner'] = $headers['x-owner'];

        if (!empty($request)) {
            foreach ($request as $key => $param) {
                if ($param != '') {
                    $params[$key] = $param;
                }
            }
        }

        $request = $restSerconn->post('on/pindahkamar/getdatapasienranap', [
            'body' => json_encode($params)
        ]);
        $response = json_decode($request->getBody(), true);

        return $response['Results'][0]['data'];
    }

    /**
     * @todo Fungsi untuk mendapatkan data pasien
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @return array
     */
    public function actionGetDataPasien()
    {
        try {
            $results = array();
            $request = Yii::$app->request->get();
            $model = new InfoPasienRiView;
            $data = $model::find()->where(['is', 'pasienpulang_id', null]);
            $pendaftaran_id = null;

            if (!empty($request)) {
                foreach ($request as $key => $value) {
                    if ($value != '') {
                        if ($key == "tanggal_lahir") {
                            # code...
                            $value = date("Y-m-d", strtotime($value));
                        }

                        if($key == "no_pendaftaran") {
                            $getPendaftaran = Pendaftaran::getPendaftaranByNomorPendaftaran($value);
                            $pendaftaran_id = ArrayHelper::getValue($getPendaftaran, 'pendaftaran_id');
                        }

                        if(
                            $key == "no_rekam_medik" && !isset($key['no_pendaftaran'])
                        ) {
                            $getPendaftaran = Pendaftaran::getPendaftaranByNoRekamMedik($value, true);
                            $pendaftaran_id = ArrayHelper::getValue($getPendaftaran, 'pendaftaran_id');
                        }

                        $data->andWhere([$key => $value]);
                    }
                }
            }
            
            if(!is_null($pendaftaran_id)) {
                $data->andWhere(['=', 'pendaftaran_id', $pendaftaran_id]);
            }

            $data = $data->one();

            if (empty($data)) {
                return [];
            }

            $pindahKamar = PindahKamar::find()
            ->where(['pendaftaran_id' => $data->pendaftaran_id])
            ->orderBy(['pindahkamar_id' => SORT_DESC])
            ->one();
            
            if (!empty($pindahKamar)) {
                if ($pindahKamar->is_pasientitipan == true && $pindahKamar->is_stoptitipan == false) {
                    if ($pindahKamar->is_pasientitipan) {
                        $data->kelas_ditagihkan_id = $pindahKamar->kelas_ditagihkan_id;
                        $data->kelas_ditagihkan_nama = $pindahKamar->kelasDitagihkan->kelaspelayanan_nama;
                        $data->ruangan_titipan_id = $pindahKamar->ruanganTitipan->ruangan_id;
                        $data->ruangan_titipan_nama = $pindahKamar->ruanganTitipan->ruangan_nama;
                        $data->kamar_titipan_id = $pindahKamar->kamarTitipan->kamarruangan_id;
                        $data->kamar_titipan_nama = $pindahKamar->kamarTitipan->kamarruangan_nokamar;
                    }
                } else {
                    $data->kelas_ditagihkan_id = null;
                    $data->kelas_ditagihkan_nama = null;
                    $data->ruangan_titipan_id = null;
                    $data->ruangan_titipan_nama = null;
                    $data->kamar_titipan_id = null;
                    $data->kamar_titipan_nama = null;
                }
            } else {
                if ($data->is_stoptitipan == true) {
                    $data->kelas_ditagihkan_id = null;
                    $data->kelas_ditagihkan_nama = null;
                    $data->ruangan_titipan_id = null;
                    $data->ruangan_titipan_nama = null;
                    $data->kamar_titipan_id = null;
                    $data->kamar_titipan_nama = null;
                }
            }

            return $data;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Fungsi untuk menyimpan transaksi pindah kamar
     * @return array
     */
    public function actionSimpanPindahKamar()
    {
        $restSerconn = Yii::$app->serconn->guzzle();
        $post = Yii::$app->request->post();
        $headers = Yii::$app->request->headers;
        $post['authorization'] = $headers['authorization'];
        $post['x-owner'] = $headers['x-owner'];
        $payloadAplcare = [ArrayHelper::getValue($post, 'kamarruangan_id'), ArrayHelper::getValue($post, 'old_kamarruangan_id')];
        unset($post['old_kamarruangan_id']);

        $request = $restSerconn->post('on/pindahkamar/simpanpindahkamar', [
            'body' => json_encode($post)
        ]);
        $response = json_decode($request->getBody(), true);
        foreach ($payloadAplcare as $kamarruangan_id) {
            (new BpjsAplicare())->createOrUpdateAplicare($kamarruangan_id, DocoConstants::TYPE_UPDATE_APLICARE);
        }

        return $response['Results'][0]['data'];
    }
}
