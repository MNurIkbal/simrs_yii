<?php

namespace Doco\mcu\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;   
use GuzzleHttp\Exception\RequestException;
use app\modules\mcu\models\PendaftaranForm;
use app\modules\mcu\components\traits\PenunjangTrait;
use app\modules\mcu\components\traits\KesimpulanTrait;
use app\modules\mcu\components\traits\RiwayatPenyakitTrait;
use app\modules\mcu\components\traits\PemeriksaanFisikTrait;
use app\modules\mcu\components\traits\PemeriksaanMcuTrait;
use app\modules\mcu\models\PemeriksaanMcuForm;
use app\modules\mcu\models\TemplateForm;
use app\modules\mcu\models\ResumeHasilPemeriksaanForm;
use app\components\Pelayanan\PelayananHelpers;

class PemeriksaanController extends DocoController
{
    protected $_title = "Pemeriksaan Pasien MCU";
    protected $_module = '/mcu/pemeriksaan/';
    protected $_restMcu;
    protected $_id_ruangan;
    protected $allowAction = ['*'];

    public $_pendaftaran_id;
    public $_jenis_kelamin;

    use PenunjangTrait;
    use KesimpulanTrait;
    // use RiwayatPenyakitTrait;
    use PemeriksaanFisikTrait;
    use PemeriksaanMcuTrait;
    
    public function init()
    {
        parent::init();
        $this->_restMcu = Yii::$app->docoRest->mcu;
        $this->_id_ruangan = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
        $this->_pendaftaran_id = DocoHelpers::decrypt(Yii::$app->request->get('id'));
        // $response = $this->_restMcu->get('allow/get-pasien?id=' . $this->_pendaftaran_id);
        // $response = json_decode($response->getBody(), true);
        // $response = $response["response"]["data"];
        $this->_jenis_kelamin = !empty($response['jenis_kelamin']) ? $response['jenis_kelamin'] : null;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actions()
    {
        return [
            'riwayat-penyakit' => 'Doco\mcu\actions\PemeriksaanMcu\RiwayatPenyakitAction',
            'pemeriksaan-fisik' => 'Doco\mcu\actions\PemeriksaanFisik\PemeriksaanFisikAction',
            'status-kesehatan' => 'Doco\mcu\actions\StatusKesehatan\StatusKesehatanAction',
            'print-status-kesehatan' => 'Doco\mcu\actions\StatusKesehatan\PrintStatusKesehatanAction',
            'hasil-pemeriksaan-phr' => 'Doco\mcu\actions\HasilPemeriksaanPhr\HasilPemeriksaanAction',
            'resume-hasil-pemeriksaan' => 'Doco\mcu\actions\ResumeHasilPemeriksaan\ResumeHasilPemeriksaanAction', // Prima Only

        ];
    }
    
    public function actionConfirmPeriksa($pendaftaran_id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($pendaftaran_id);
        $title = Yii::t('fe', 'Mulai pemeriksaan');
        $sub_title = $this->_title;
        $list_data = $this->getListDataApi();
        $data_pegawai = $list_data["data_pegawai"];
        $modelPendaftaran = new PendaftaranForm;
        $modelPendaftaran->scenario = 'set-dokter-mcu';
        $form_name = substr(strrchr(get_class($modelPendaftaran), "\\"), 1);
        $is_disabled = 'true';
        try{
            $response = $this->_restMcu->get('allow/get-pasien?id='.$id);
            $response = json_decode($response->getBody(), true);
            $data_pasien = $response["response"]["data"];
            if ($modelPendaftaran->load($data_pasien, '')) {
                $modelPendaftaran->pegawai_id = $data_pasien['pegawai_id'];
                if ($request->post()){
                    $data = $request->post('PendaftaranForm');
                    $modelPendaftaran->pegawai_id = $data["pegawai_id"];
                    $modelPendaftaran->tgl_masukperiksa = $data["tgl_masukperiksa"];
                    $modelPendaftaran->pendaftaran_id = $id;
                    if($modelPendaftaran->validate()) {
                        $response = $this->_restMcu->post('pemeriksaan/ubah-dokter', [
                            'form_params' => $modelPendaftaran->attributes
                        ]);
                        $response = json_decode($response->getBody(), true);
                        $redirect = Url::to(['/mcu/pemeriksaan/periksa', 'id' => $pendaftaran_id]);
                        return json_encode(['url'=>$redirect]);
                        $this->redirect(['pemeriksaan/periksa?id='.$pendaftaran_id]);
                    }
                    else {
                        return DocoHelpers::response($modelPendaftaran->errors, 422, $form_name);
                    }
                    
                }else{
                    return $this->renderAjax('_confirm_periksa', get_defined_vars());
                }
            }
            else {
                return DocoHelpers::responseTemplate(
                    500,
                    Yii::t('fe', 'Kegagalan Pada Sistem'),
                    [],
                    ['title' => Yii::t('fe', 'Gagal Mengubah Dokter'), 'text' => Yii::t('fe', 'Data tidak ditemukan.')]
                );
            }
        } catch (\Exception $e){
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (RequestException $e){
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    private function getListDataApi()
    {
        try {
            $request = Yii::$app->request;
            $response = $this->_restMcu->get('allow/get-list-data?id_ruangan='.$this->_id_ruangan);
            $body = json_decode($response->getBody(),TRUE);

            $data_statusperiksa = empty($body['response']['data-statusperiksa']) ? [] : $body['response']['data-statusperiksa'];
            $data_pegawai = empty($body['response']['data-pegawai']) ? [] : $body['response']['data-pegawai'];
            $data_penjamin = empty($body['response']['data-penjamin']) ? [] : $body['response']['data-penjamin'];
            $format_mcu = empty($body['response']['format_mcu']) ? [] : $body['response']['format_mcu'];
            $result = [
                'data_statusperiksa' => $data_statusperiksa,
                'data_pegawai' => $data_pegawai,
                'data_penjamin' => $data_penjamin,
                'format_mcu' => $format_mcu,
            ];

            return $result;
        } catch (RequestException $e) {
            return [
                'data_statusperiksa' => [],
                'data_pegawai' => [],
                'data_penjamin' => [],
            ];
        } catch (\Exception $e) {
            return [
                'data_statusperiksa' => [],
                'data_pegawai' => [],
                'data_penjamin' => [],
            ];
        }
    }

    public function actionPeriksa($id)
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Pemeriksaan Pasien');
        $sub_title = $this->_title;
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $statepulang = $request->get('state', null);
        
        $response = $this->_restMcu->get('allow/get-pasien?id=' . $pendaftaran_id);
        $response = json_decode($response->getBody(), true);
        $response = $response["response"]["data"];
        if(!empty($response)) {
            Yii::$app->cache->set('data-pasien-' . $response['pasien_id'], $response, DocoConstants::EXPIRED_CACHE);
        }

        $response['umur'] = DocoHelpers::getUmur($response['tanggal_lahir']);
        $response['poliklinik'] = $response['ruangan_nama'];
        $response['nama_pegawai'] = $response['nama_dokter'];
        $response['stat_ranap'] = $response['status_periksa'];
        $pasien_id = isset($response['pasien_id']) ? $response['pasien_id'] : null;

        $config = $this->checkConfigPrima();
        $data = $this->_restMcu->get('pemeriksaan/data-mcu-prima?id=' . $pendaftaran_id);
        $data = json_decode($data->getBody(), true);
        $data = $data["response"];
        $dataFisik = isset($data['pemeriksaanFisik']['pemeriksaan_fisik']) ? json_decode($data['pemeriksaanFisik']['pemeriksaan_fisik'], true) : [];
        $dataRiwayat = isset($data['riwayat']['additional_data']) ? json_decode($data['riwayat']['additional_data'], true) : [];
        $dataResume = isset($data['resume']['resume_pemeriksaan']) ? json_decode($data['resume']['resume_pemeriksaan'], true) : [];
        $is_disabled = true;
        if(isset($dataRiwayat['pasien_phr']) || isset($dataFisik['keadaan_umum']) || isset($dataResume['resume_hasil_pemeriksaan'])){
            $is_disabled = false;
        }
        $list_data = $this->getListDataApi();
        $data_penjamin = isset($list_data['data_penjamin']) ? $list_data['data_penjamin'] : null;
        $data_pegawai = isset($list_data['data_pegawai']) ? $list_data['data_pegawai'] : null;
        $data_statusperiksa = isset($list_data['data_statusperiksa']) ? $list_data['data_statusperiksa'] : null;
        $format_mcu = isset($list_data['format_mcu']) ? $list_data['format_mcu'] : null;
        $data_diagnosa = isset($list_data['data_diagnosa']) ? $list_data['data_diagnosa'] : null;
        $pasien_id = (!empty($response['pasien_id'])) ? DocoHelpers::encrypt($response['pasien_id']) : null;
        $pegawai_id = (!empty($response['pegawai_id'])) ? DocoHelpers::encrypt($response['pegawai_id']) : null;
        $kelaspelayanan_id = (!empty($response['kelaspelayanan_id'])) ? DocoHelpers::encrypt($response['kelaspelayanan_id']) : null;
        $ruangan_id = (!empty($response['ruangan_id'])) ? DocoHelpers::encrypt($response['ruangan_id']) : null;
        $encrytedPendaftaranId = $id;

        return $this->render('periksa', get_defined_vars());
    }

    public function actionGetHasilTd($pendaftaran_id, $nilai = '0/0')
    {
        $request = Yii::$app->request;
        $data_td = DocoConstants::TD_HASIL;
        if(count($data_td) < 1) {
            $response = Yii::$app->docoRest->mcu->get('pemeriksaan/get-data-klasifikasi-tekanan-darah');
            $body = json_decode($response->getBody(), true);
            $data_td = $body['response'];
        }
        $nilai = explode('/', $nilai);
        $nilai_systolic = (int)$nilai[0];
        $nilai_diastolic = (int)$nilai[1];
        $systolic = '';
        $diastolic = '';
        $urutan_systolic = 0;
        $urutan_diastolic = 0;
        $hasil = '';
        $klasifikasitekanandarah_id = 0;

        if (!empty($data_td)) {
            foreach ($data_td as $key => $value) {
                if ($nilai_systolic >= $value['sistolik_min'] && $nilai_systolic <= $value['sistolik_max']) {
                    $systolic = $value['klasifikasitekanadarah'];
                    $urutan_systolic = $value['urutan'];
                    $klasifikasitekanandarah_id = $value['klasifikasitekanadarah_id'];
                }

                if ($nilai_diastolic >= $value['diastolik_min'] && $nilai_diastolic <= $value['diastolik_max']) {
                    $diastolic = $value['klasifikasitekanadarah'];
                    $urutan_diastolic = $value['urutan'];
                    $klasifikasitekanandarah_id = $value['klasifikasitekanadarah_id'];
                }
            }
        }

        if ($urutan_systolic > $urutan_diastolic) {
            $hasil = $systolic;
            $klasifikasitekanandarah_id = $klasifikasitekanandarah_id;
        } else if ($urutan_systolic < $urutan_diastolic) {
            $hasil = $diastolic;
            $klasifikasitekanandarah_id = $klasifikasitekanandarah_id;
        } else {
            $hasil = $systolic;
            $klasifikasitekanandarah_id = $klasifikasitekanandarah_id;
        }

        $result = [
                    'hasil' => $hasil,
                    'klasifikasitekanandarah_id' => $klasifikasitekanandarah_id
                ];
        return DocoHelpers::response($result);
    }

    public function actionPemeriksaanMcu()
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Pemeriksaan MCU');
        $pendaftaran_id = DocoHelpers::decrypt($request->get('id', null));
        $pasien_id = DocoHelpers::decrypt($request->get('pasien_id', null));
        $ruangan_id = PelayananHelpers::decryptId($request->get('ruangan_id', null));
        $data = $this->setAttributeData($pendaftaran_id, $ruangan_id);
        if(empty($data['data']) || $ruangan_id == "") {
            return DocoHelpers::responseTemplate(
                404,
                Yii::t('fe', 'Kegagalan Pada Sistem'),
                [],
                ['title' => Yii::t('fe', 'Terjadi Kesalahan'), 'text' => Yii::t('fe', 'Halaman tidak tersedia (masih dalam pengembangan).')]
            );
        }
        $dataAttr = $data['data'];
        $model = $data['model'];
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $model->attributes = $dataAttr;
        $path = $data['path'];
        $id = isset($dataAttr['pemeriksaanspesialismcu_id']) ? DocoHelpers::encrypt($dataAttr['pemeriksaanspesialismcu_id']) : null;
        if($request->post()) {
            $model->load($request->post());
            $model->pendaftaran_id = $pendaftaran_id;
            $model->pasien_id = $pasien_id;
            $model->ruangan_id = $ruangan_id;
            if ($model->validate()) {
                $request = $this->_restMcu->post('pemeriksaan/save-pemeriksaan-mcu', [
                    'form_params' => [
                        'data' => $model->attributes,
                        'formName' => $formName
                    ],
                ]);
                $response = json_decode($request->getBody(), true);
                return DocoHelpers::response($response);
            }else{
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        else {
          if(empty($path)) {
              return DocoHelpers::responseTemplate(
                  404,
                  Yii::t('fe', 'Kegagalan Pada Sistem'),
                  [],
                  ['title' => Yii::t('fe', 'Terjadi Kesalahan'), 'text' => Yii::t('fe', 'Halaman tidak ditemukan.')]
              );
          }
          return $this->renderAjax($path, [
            'pendaftaran_id' => $pendaftaran_id,
            'id' => $id,
            'title' => $title,
            'model' => $model,
          ]);
        }
    }

    public function actionSaveAnatomi()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;

        try {
            $data_anatomi = $request->post('data_anatomi');
            $pendaftaran_id = $request->post('pendaftaran_id');
            $pasien_id = $request->post('pasien_id');
            $pemeriksaanfisikmcu_id = $request->post('pemeriksaanfisikmcu_id');

            $send_data = [
                'data_anatomi' => $data_anatomi,
                'pendaftaran_id' => $pendaftaran_id,
                'pasien_id' => DocoHelpers::decrypt($pasien_id),
                'pemeriksaanfisikmcu_id' => $pemeriksaanfisikmcu_id,
            ];

            $response = $this->_restMcu->post('pemeriksaan/save-periksatubuh', [
                'form_params' => $send_data
            ]);
            $response = json_decode($response->getBody(), true);

            return [
                'status' => 200,
                'message' => $response,
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }

    // Export pdf
    public function actionExportPdfPeriksaFisik($pendaftaran_id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($pendaftaran_id);
        $path = Yii::getAlias("@download") . "/pemeriksaan_fisik_" . uniqid() . ".pdf";
        try {
            $response = $this->_restMcu->get('pemeriksaan/export-pdf-periksa-fisik?pendaftaran_id=' . $id, [
                'save_to' => $path
            ]);

            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path, null, true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
    // halaman template mcu
    public function actionModalTemplate($id,$type)
    {
        $model          = new TemplateForm;
        $title          = Yii::t('fe', 'Template');
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $type           = $type;
        return $this->renderAjax('__modal_template', get_defined_vars());
    }

    /**
     * Check config for RS Prima.
     */
    private function checkConfigPrima()
    {
        $response = Yii::$app->docoRest->mcu->get('allow/get-konfig-mcu-prima');
        $response = json_decode($response->getBody(), true);
        $response = $response["response"];
        $konfig = ArrayHelper::getValue($response, 'konfig');

        return $konfig;
    }

    // hapus template
    public function actionHapusTemplate($id){
        $response = $this->guzzleExec(Yii::$app->docoRest->mcu, [
            'url' => 'pemeriksaan/hapus-template',
            'payload' => [
                'query' => [
                    'id' => $id
                ]
            ]
        ]);
        return DocoHelpers::response($response);
    }

    // pilih template
    public function actionPilihTemplate($id,$pendaftaran_id){
        $response = $this->guzzleExec(Yii::$app->docoRest->mcu, [
            'url' => 'pemeriksaan/pilih-template',
            'payload' => [
                'query' => [
                    'id' => $id,
                    'pendaftaran_id' => $pendaftaran_id
                ]
            ]
        ]);
        
        return DocoHelpers::response($response);
    }

    // simpan template
    public function actionSimpanTemplate($pendaftaran_id, $type, $judul){
        $request = Yii::$app->request;
        $post = $request->post();

        $response = $this->guzzleExec(Yii::$app->docoRest->mcu, [
            'url' => 'pemeriksaan/save-template',
            'payload' => [
                'form_params' => $post,
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'type' => $type,
                    'judul' => $judul,
                ]
            ],
            'returnResponse' => true,
        ]);
            
        return $response;
    }

    // list template
    public function actionListTemplate($type) {
        $request = Yii::$app->request;
        $response = $this->guzzleExec(Yii::$app->docoRest->mcu, [
            'url' => 'pemeriksaan/get-data-template',
            'payload' => [
                'query' => [
                    'type' => $type,
                    'term' => $request->get('term'),
                ]
            ]
        ]);

        $data[] = ['id' => '', 'text' => '-- Pilih Template --'];
        foreach ($response as $value) {
            $data[] = ['id' => $value['template_id'], 'text' => $value['temp_nama']];
        }

        return DocoHelpers::response($data);
    }
}