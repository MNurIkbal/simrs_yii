<?php

namespace Doco\pendaftaran\controllers;

use Yii;
use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\pendaftaran\models\PasienForm;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;
use yii\web\Response;

class PembuatanNomorRekamMedikController extends DocoController
{
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
    public function actionCreate()
    {
            $title = Yii::t('fe', 'Pembuatan Nomor Rekam Medik');
            $request = Yii::$app->request;
            $model = new PasienForm;
            $model->scenario = 'pendaftaran-rajal';
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $post = $request->post();
            $dataOptions = $this->getDataOptions();

            if (!empty($post)) {
                $model->load($post);

                if (array_key_exists(0, $model->jenisidentitas) && $model->jenisidentitas[0] == '') {
                    $model->jenisidentitas = null;
                }

                if (array_key_exists(0, $model->no_identitas_pasien) && $model->no_identitas_pasien[0] == '') {
                    $model->no_identitas_pasien = null;
                }

                if ($model->validate()) {

                    return $this->helper->guzzleExec($this->restPendaftaran, [
                        'url' => 'pembuatan-nomor-rekam-medik/save',
                        'payload' => [
                            'form_params' => $model->attributes
                        ],
                        'returnResponse' => true
                    ]);
                    
                } else {
                    $errors = DocoHelpers::parseError($model->errors, $formName);

                    return DocoHelpers::response([
                        'response' => [
                            'data' => $errors
                        ]
                    ],422);
                }
            }

            return $this->render('create', [
                'title' => $title,
                'model' => $model,
                'dataOptions' => $dataOptions
            ]);
    }

    /**
     * @todo Fungsi untuk mendapatkan options data pembuatan nomor rekam medik
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected function getDataOptions() {
        $restPendaftaran = $this->restPendaftaran->get('pembuatan-nomor-rekam-medik/get-data-options');
        $response = json_decode($restPendaftaran->getBody(), true);
        $response = $response['response'];

        return $response;
    }
}