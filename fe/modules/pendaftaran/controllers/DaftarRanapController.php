<?php
/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pendaftaran\controllers;

use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\api\models\BpjsForm;
use app\modules\pendaftaran\components\traits\PendaftaranTrait;
use app\modules\pendaftaran\models\KunjunganForm;
use app\modules\pendaftaran\models\PasienAdmisiForm;
use app\modules\pendaftaran\models\PasienForm;
use app\modules\pendaftaran\models\TipePasienForm;
use app\modules\pendaftaran\models\RujukanForm;
use app\modules\pendaftaran\models\AsuransiForm;
use app\modules\pendaftaran\models\BpjsNewForm;
use app\modules\pendaftaran\models\PendaftaranForm;
use app\modules\pendaftaran\models\PjpasienForm;
use app\modules\pendaftaran\models\MultiCarabayarForm;

use GuzzleHttp\Exception\RequestException;
use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;

class DaftarRanapController extends DocoController
{
    protected $_title = 'Pendaftaran Rawat Inap';
    protected $_module = '/pendaftaran/daftar-ranap';
    protected $allowAction = ['*'];
    protected $_restMaster;
    protected $_restPendaftaran;
    protected $_id_carabayar_bpjs;

    protected $_instalasi_id_ri = DocoConstants::INSTALASI_ID_RI;
    use PendaftaranTrait;

    public function init()
    {
        parent::init();
        $this->pendaftaranTipe = 'ranap';
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;

        $carabayarRequest = $this->_restMaster->get('cara-bayar/allow-get-carabayar-bpjs');
        $body = json_decode($carabayarRequest->getBody(), true);
        $carabayar = $body['response'];

        $this->_id_carabayar_bpjs = $carabayar['carabayar_id'];
    }

    public function actionIndex($id_booking = null, $pendaftaran_id = null)
    {
        return Yii::$app->docoPlugin->execute($this,'daftar_ranap_index');
    }

    /**
     * @author Rizal
     * @since 2018-01-26 15:39:31
     * @param string id encrypted
     * @return
     * @desc
     */
    public function actionUpdate($id)
    {
        // Init
        $id = DocoHelpers::decrypt($id);
        $status = $this->_status;
        $options = $this->_options;
        $request = Yii::$app->request;
        $model = new PendaftaranForm;
        $model->scenario = 'update';
        $modelPasienAdmisi = new PasienAdmisiForm;
        $modelBpjs = new BpjsForm;
        $title = \Yii::t('fe', 'Ubah') . ' ' . \Yii::t('fe', $this->_title);

        // $formName = substr(strrchr(get_class($model), "\\"), 1);

        if ($request->post()) {
            $post = $request->post();
            $model->attributes = $post['PendaftaranForm'];
            $modelBpjs->attributes = $post['BpjsForm'];
            $modelPasienAdmisi = $post['PasienAdmisiForm'];

            if ($model->validate()) {
                if ($model->carabayar_id == $this->_id_carabayar_bpjs) {
                    $modelBpjs->no_rekam_medik = $model->no_rekam_medik;
                    if ($modelBpjs->validate()) {
                        $response = $this->_restPendaftaran->post('bpjs/create', [
                            'form_params' => $modelBpjs->attributes,
                        ]);
                        $responseBpjs = json_decode($response->getBody(), true);
                        $model->bpjs_id = $responseBpjs['response']['bpjs_id'];
                    } else {
                        $errors = DocoHelpers::parseError($modelBpjs->errors, 'BpjsForm');
                        return DocoHelpers::responseTemplate(422, 'Error', $errors);
                    }
                }
                $response = $this->_restPendaftaran->put('tra-pasien-rawat-inap/update?id=' . $id, [
                    'form_params' => $post, //$model->attributes
                ]);

                $session = Yii::$app->session;
                $session->set('trans_update_success', Yii::t('fe', 'Data rawat inap berhasil diubah.'));
                return $this->redirect('/pendaftaran/informasi-pasien/ranap');
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'PendaftaranForm');
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }

        } else {
            $response = $this->_restPendaftaran->get('tra-pasien-rawat-inap/view?id=' . $id);
            $body = json_decode($response->getBody(), true);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            $model->pendaftaran_id = $attributes['pendaftaran_id'];

            $response = $this->_restPendaftaran->get('tra-pasien-admisi/view?id=' . $model->pendaftaran_id);
            $body = json_decode($response->getBody(), true);
            $attributes = $body['response'];
            $modelPasienAdmisi->attributes = $attributes;

            $dokterRequest = $this->_restMaster->get('pegawai/allow-list-dokter-rajal');
            $body = json_decode($dokterRequest->getBody(), true);
            $dokterList = $body['response'];

            $penyakitRequest = $this->_restMaster->get('jenis-kasus-penyakit/allow-list-penyakit');
            $body = json_decode($penyakitRequest->getBody(), true);
            $penyakitList = $body['response'];

            $carabayarRequest = $this->_restMaster->get('cara-bayar/list-cara-bayar?default=0');
            $body = json_decode($carabayarRequest->getBody(), true);
            $carabayarList = $body['response'];

            $penjaminRequest = $this->_restMaster->get('penjamin/list-penjamin?carabayar_id=' . $model->carabayar_id);
            $body = json_decode($penjaminRequest->getBody(), true);
            $penjaminList = $body['response'];

            return $this->render('form_update', get_defined_vars());
        }
    }

    public function actionGetPasien()
    {
        $no_rekam_medik = Yii::$app->request->post('no_rekam_medik');

        try {
            $response = $this->_restPendaftaran->get('daftar-ranap/get-pasien?no_rekam_medik=' . $no_rekam_medik);
            return DocoHelpers::responseJsonString($response->getBody(), '');

        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), '');
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan data kunjungan pasien
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataKunjunganPasien()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $pasien_id = $request->get('pasien_id', null);
            $draw = $request->get('draw', 1);
            $no = $request->get('start', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            if ($pasien_id) {
                $restPendaftaran = $this->_restPendaftaran->get('pendaftaran-ranap/get-data-kunjungan-pasien?pasien_id=' . $pasien_id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
                $body = json_decode($restPendaftaran->getBody(), true);
                $data = $body['response']['data'];
                if (!empty($data)) {
                    foreach ($data as $key => $value) {
                        $no++;
                        $value['tgl_pendaftaran'] = $value['tgl_pendaftaran'] != '' ? DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_pendaftaran'])), false, false) : '';
                        unset($value['pasien_id']);
                        $data[$key] = $value;
                    }

                    $result['data'] = $data;
                    $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                    $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
                } else {
                    $result['data'] = $data;
                    $result['recordsTotal'] = 0;
                    $result['recordsFiltered'] = 0;
                }
            }

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
}
