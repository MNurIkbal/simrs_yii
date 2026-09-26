<?php

namespace Doco\pendaftaran\controllers;

use Yii;
use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\pendaftaran\models\PindahKamarForm;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class PindahKamarController extends DocoController
{
    protected $allowAction = [
        '*',
    ];
    
    /**
     * @todo Variable rest API pendaftaran
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected $restPendaftaran;

    /**
     * @todo Fungsi inisialisasi controller pindah kamar
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function init()
    {
        parent::init();
        $this->restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    /**
     * @todo Fungsi custom behaviors
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function behaviors()
    {
      $behaviors = parent::behaviors();

      unset($behaviors['access']);
      unset($behaviors['verbs']);

      return $behaviors;
    }

    /**
     * @todo Fungsi untuk menampilkan halaman index
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionIndex($no_pendaftaran = '', $no_rm = '')
    {
        try {
            $title = Yii::t('fe', 'Pindah kamar');
            $model = new PindahKamarForm;
            $hidden = '';
            
            if($no_pendaftaran != ''){
                $no_pendaftaran = DocoHelpers::decrypt($no_pendaftaran);
                $hidden = 'hidden';
            }

            if($no_rm != ''){
                $no_rm = DocoHelpers::decrypt($no_rm);
            }
            
            $dataMaster = $this->getDataOptions();

            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan options data pindah kamar
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected function getDataOptions() {
        $restPendaftaran = $this->restPendaftaran->get('pindah-kamar/get-data-options');
        $response = json_decode($restPendaftaran->getBody(), true);
        $response = $response['response'];
        return $response;
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Fungsi untuk mencari data pasien
     * @param int $no_pendaftaran
     * @param int $no_rekam_medik
     * @return array
     */
    public function actionGetDataPasien($no_pendaftaran = null, $no_rekam_medik = null) {
        $restPendaftaran = $this->restPendaftaran->get('pindah-kamar/get-data-pasien', [
            'query' => [
                'no_pendaftaran' => $no_pendaftaran,
                'no_rekam_medik' => $no_rekam_medik
            ]
        ]);
        $response = json_decode($restPendaftaran->getBody(), true);
        $response = $response['response'];

        return json_encode($response);
    }

    /**
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @todo Fungsi untuk simpan transaksi pindah kamar
     * @return array
     */
    public function actionSimpanPindahKamar()
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $model = new PindahKamarForm;
        $model->load($request->post());
        $pendaftaran_id = DocoHelpers::encrypt($model->pendaftaran_id);
        $model->tgl_pindahkamar = date('Y-m-d H:i:s');
        $model->is_pasientitipan = false;
        $model->old_kamarruangan_id = ArrayHelper::getValue($request->post(), 'old_kamarruangan_id');

        if ($model->validate()) {
            if(!empty($model->kelas_ditagihkan_id) && !empty($model->kamar_titipan_id) && !empty($model->ruangan_titipan_id)) {
                $model->is_pasientitipan = true;
            }
            $req = $this->restPendaftaran->post('pindah-kamar/simpan-pindah-kamar', [
                'form_params' => $model->attributes
            ]);
            $response = json_decode($req->getBody(), true);
            $res_status = $response['metadata']['status'];
            $response = $response['response'];            
            if ($res_status == 200) {
                $removeSession = $this->removeSession($model->pendaftaran_id);
                $data_dashboard['reload'] = 1;
                $mode = Yii::$app->params->mode;
                Yii::$app->redis->executeCommand('PUBLISH', [
                    'channel' => 'display-dashboard-kamar-'.$mode,
                    'message' => json_encode(['data' => $data_dashboard])
                ]);
            }
            return DocoHelpers::response($response);
        } else {
            $errors = DocoHelpers::parseError($model->errors, 'PindahKamarForm');

            return DocoHelpers::response([
                'response' => [
                    'data' => $errors
                ]
            ], 422);
        }
    }

    private function removeSession($id)
    {
        $session = Yii::$app->session;
        $pendId = DocoHelpers::encrypt($id);
        

        if (Yii::$app->cache->get('pasien-pendaftaran-id-'. $pendId)) {
            Yii::$app->cache->delete('pasien-pendaftaran-id-'. $pendId);
        }

        if($session['ranap-list-data-allow-'.$pendId]){
            $session->remove('ranap-list-data-allow-'.$pendId);
        }

        if($session['tindakan']){
            $session->remove('tindakan');
        }

        if($session['paket']){
            $session->remove('paket');
        }

        return true;
    }
}
