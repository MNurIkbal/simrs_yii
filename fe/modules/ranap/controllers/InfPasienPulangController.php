<?php

/**
 * @Author: iqbal@docotel.com
 * @Date:   2018-08-15 16:59:18
 * @Description:
 */

namespace Doco\ranap\controllers;

use app\components\DHtml;
use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\Services\SuratKematianService;

use app\modules\ranap\models\PasienBatalPulangForm;

class InfPasienPulangController extends DocoController
{
    protected $_title = "Informasi Pasien Pulang Rawat Jalan";
    protected $_module = '/ranap/inf-pasien-pulang';
    protected $_controller = '/ranap/inf-pasien-pulang';
    protected $_restRanap;
    protected $_restMaster;
    protected $_ruangan_id;
    protected $_instalasi_id;
    protected $_pegawai_id;

    public function init(){
        parent::init();
        $this->_restRanap = Yii::$app->docoRest->ranap;
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_ruangan_id = !empty(Yii::$app->docoVars->workspace('ruangan_id')) ? (int)Yii::$app->docoVars->workspace('ruangan_id') : 0 ;
        $this->_instalasi_id = !empty(Yii::$app->docoVars->workspace('instalasi_id')) ? (int)Yii::$app->docoVars->workspace('instalasi_id') : 0;
        $this->_pegawai_id = !empty(Yii::$app->docoVars->workspace('id_pegawai')) ? (int)Yii::$app->docoVars->workspace('id_pegawai') : 0;
    }

    public function behaviors(){
      $behaviors = parent::behaviors();
      unset($behaviors['access']);
      unset($behaviors['verbs']);
      return $behaviors;
    }

    public function actionIndex(){
         try {
            $title = !empty(DHtml::getTitleMenu()) ? DHtml::getTitleMenu().' Rawat Inap' : 'Informasi Pasien Pulang Rawat Inap';
            $userIdentity = Yii::$app->session->get('user_identity');
            $response = $this->_restRanap->get('allow/get-filter-pasien-pulang?instalasi_id='.$this->_instalasi_id.'&ruangan_id='.$this->_ruangan_id,['form_params'=> [] ]);
            $body = json_decode($response->getBody(), TRUE);
            $body = $body['response'];

            $dataRuangan = empty($body['dataRuangan']) ? [] : $body['dataRuangan'];
            $dataJenisKelamin = empty($body['dataJenisKelamin']) ? [] : $body['dataJenisKelamin'];
            $dataPenjamin = empty($body['dataPenjamin']) ? [] : $body['dataPenjamin'];
            $dataDokter = empty($body['dataDokter']) ? [] : $body['dataDokter'];
            $dataCaraKeluar = empty($body['dataCaraKeluar']) ? [] : $body['dataCaraKeluar'];
            $dataKelasPelayanan = empty($body['dataKelasPelayanan']) ? [] : $body['dataKelasPelayanan'];
            $dataJenisKasusPenyakit = empty($body['dataJenisKasusPenyakit']) ? [] : $body['dataJenisKasusPenyakit'];
            $dataCaraBayar = empty($body['dataCaraBayar']) ? [] : $body['dataCaraBayar'];
            $dataKondisiKeluar = empty($body['dataKondisiKeluar']) ? [] : $body['dataKondisiKeluar'];

            $status_belum_lunas = DocoConstants::STAT_BAYAR_BLM_LUNAS;
            $status_pulang = DocoConstants::CARA_KELUAR_PULANG;
            $result = [
                'dataPenjamin' => $dataPenjamin,
                'dataRuangan' => $dataRuangan,
                'dataJenisKelamin' => $dataJenisKelamin,
                'dataDokter' => $dataDokter,
                'dataCaraKeluar' => $dataCaraKeluar,
                'dataKelasPelayanan' => $dataKelasPelayanan,
                'dataJenisKasusPenyakit' => $dataJenisKasusPenyakit,
                'dataCaraBayar' => $dataCaraBayar,
                'dataKondisiKeluar' => $dataKondisiKeluar,
            ];
            $instalasiId = $this->_instalasi_id;
            $instalasiRanap = DocoConstants::INSTALASI_ID_RI;
            $role_pengguna = Yii::$app->session->get('akses_menu');
            $hide_button_cetak = isset($role_pengguna['/kasir/inf-pasien-belum-bayar']) ? '' : 'hidden';
            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        // echo "<pre>";var_dump($this->_ruangan_id);die();
        $draw = $request->get('draw', 1);
        try {
            $response = $this->_restRanap->get('inf-pasien-pulang/index?ruangan_id='.$this->_ruangan_id . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            $no = $request->get('start', 1);
            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $primary = json_encode($value['pendaftaran_id']);
                $value['primary'] = DocoHelpers::encrypt($primary);
                $value['rowNum'] = $no;
                $value['tgl_admisi'] = date('d M Y H:i:s', strtotime($value['tgl_admisi']));
                $value['tglpasienpulang'] = !empty($value['tglpasienpulang']) ? date('d M Y H:i:s', strtotime($value['tglpasienpulang'])) : null;
                $lamaRawat = isset($value['lama_rawat']) ? $value['lama_rawat'] : '';
                if(empty($lamaRawat)) {
                    $lamaRawat = $this->getLamaRawat($value['tgl_admisi'], $value['tglpasienpulang']);
                }
                $value['lama_rawat'] = $lamaRawat .' Hari';
                // $value['lama_rawat'] = ($value['lama_rawat'] == 0) ? 1 : $value['lama_rawat'];
                $value['carabayar_penjamin'] = 'Cara Bayar : '.$value['carabayar_nama'].'<br>Penjamin : '.$value['penjamin_nama'];
                $cara_kondisipulang = (is_null($value['pasienpulang_id']) && $value['is_stopakomodasi'] == true) ? 'STOP AKOMODASI' : $value['carakeluar_nama'] .' - '. $value['kondisikeluar_nama'];
                $value['cara_kondisipulang'] = $cara_kondisipulang;
                // $value['lama_rawat'] = $value['lama_rawat'] .' Hari';
                $value['tgl_admisi'] = 'Masuk : '.$value['tgl_admisi'].'<br>Pulang : '.$value['tglpasienpulang'];
                $value['no_rekam_medik_old'] = strtoupper($value['nama_pasien']).' ('.substr($value['jns_kelamin'], 0, 1).')<br>No. Registrasi : '.$value['no_pendaftaran'].'<br>No. RM : '.$value['no_rekam_medik'];
                $value['no_telepon_pasien'] = isset($value['no_telepon_pasien']) ?  $value['no_telepon_pasien'] : ' - ';
                $tempKelas = $value['kelaspelayanan_nama'];
                $value['kelaspelayanan_nama'] = 'Kelas Layanan : '.$tempKelas.'<br> Kelas Tagihan : -';
                $value['no_rekam_medik'] = $value['no_rekam_medik'];

                if (!array_key_exists('kelas_ditagihkan_nama_pk', $value)) {
                    $value['kelas_ditagihkan_nama_pk'] = '-';
                }

                if (!array_key_exists('kelas_ditagihkan_nama', $value)) {
                    $value['kelas_ditagihkan_nama'] = '-';
                }

                if (isset($value['pindahkamar_id']) && $value['pindahkamar_id']) {
                    if ($value['pk_is_stoptitipan'] == false) {
                        if ($value['pk_is_pasientitipan']) {
                            $value['kelaspelayanan_nama'] = 'Kelas Layanan : '.$tempKelas.'<br> Kelas Tagihan : '.$value['pk_kelas_ditagihkan_nama'];
                        }
                    }
                } else {
                    if ($value['is_stoptitipan'] == false) {
                        if ($value['is_pasientitipan']) {
                            $value['kelaspelayanan_nama'] = 'Kelas Layanan : '.$tempKelas.'<br> Kelas Tagihan : '.$value['kelas_ditagihkan_nama'];
                        }
                    }
                }

                $value['ruangan_nama'] = 'Kasus Penyakit : '.$value['jeniskasuspenyakit_nama'].'<br>Ruangan : '.$value['ruangan_nama'].'<br>Lama Rawat : '.$lamaRawat .' Hari';

                $value['rowNum'] = $no;
                $value['konfirmasi'] = $this->setKonfirmasi($value['status_konfirmasi']);
                $value['instalasi_id'] = DocoConstants::INSTALASI_ID_RI;
                $value['ruangan_id'] = null;
                // dump($value['konfirmasi']);die;
                $data[$key] = $value;
            }
            $return = [
                'data' => $data,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
    public function actionBatalPulang()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $pendaftaran_id = DocoHelpers::decrypt($get['pendaftaran_id']);
            $getDetailInfoPasienPulang = $this->GetDetailInfoPasienPulangRI($pendaftaran_id);
            $title = 'Pembatalan Pulang Pasien Rawat Inap';
            $model = new PasienBatalPulangForm;

            $response = $this->_restRanap->request('GET', 'allow/data-kamar', [
                'query' => []
            ]);
            $bundleData = json_decode($response->getBody(),TRUE);
            $data = ArrayHelper::getValue($bundleData,'response.data');
            $dataJenisKasusPenyakit = ArrayHelper::map($data,'jeniskasuspenyakit_id','jeniskasuspenyakit_nama');
            $dataKelasPelayanan = ArrayHelper::map($data,'kelaspelayanan_id','kelaspelayanan_nama');
            $dataRuangan = ArrayHelper::map($data,'ruangan_id','ruangan_nama');
            $dataKamar = ArrayHelper::map($data,'kamarruangan_id','kamarruangan_nokamar');

            return $this->renderAjax('_batal_pulang',get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function GetDetailInfoPasienPulangRI($pendaftaran_id = null)
    {
        try {
             $response = $this->_restRanap->request('GET', 'inf-pasien-pulang/data-info-pasien-pulang', [
                            'query' => ['pendaftaran_id' => $pendaftaran_id]
                        ]);
            $body = json_decode($response->getBody(),TRUE);
            $return = $body['response'];

            return $return;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }


    public function actionSaveBatalPulang()
    {
        try {
            $request = Yii::$app->request;
            $model = new PasienBatalPulangForm;
            $post = $request->post('PasienBatalPulangForm');
            $model->attributes = $post;
            // $model->load($request->post());
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            if($model->validate()){
                if ($post) {
                    $response = $this->_restRanap->post('inf-pasien-pulang/batal-pulang', [
                        'form_params' => $post
                    ]);
                    $body = json_decode($response->getBody(), True);
                    return json_encode($body);
                    // return DocoHelpers::response($body);
                }
            }else{
                $response = $model->getErrors();
                return DocoHelpers::response($response,422,'PasienBatalPulangForm');
            }
        } catch (RequestException $e) {
           return DocoHelpers::responseTemplate(500, $e->getMessage());

        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionPeriksa($id){
        try {
            // echo "<pre>";var_dump($id);die();
            $response = $this->_restRanap->request('GET', 'inf-pasien-ranap/update-status-periksa',
                [
                    'query' => ['id' => $id]
                ]
            );
            return $this->redirect(['pemeriksaan-rawat-inap/periksa?id='.$id]);
        } catch (RequestException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function actionExportRincianTagihanPdf($pendaftaran_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        // return $request;
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $path = Yii::getAlias("@download") . "/rincian-tagihan-pasien-ranap.pdf";
        try {
            $response = $this->_restRanap->get('inf-pasien-ranap/export-rincian-tagihan-pdf?pendaftaran_id=' . $pendaftaran_id , [
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/inf-pasien-pulang.pdf";
        try {
            $response = $this->_restRanap->get('inf-pasien-pulang/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/informasi-pasien-pulang.xlsx";
            $response = $this->_restRanap->get('inf-pasien-pulang/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);

        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportPdfSuratKematian($pendaftaran_id)
    {
        $request = Yii::$app->request;
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $path = Yii::getAlias("@download") . "/surat_kematian.pdf";
        $nama_usercetak = Yii::$app->session->get('user_identity')['nama'];
        $id_usercetak = Yii::$app->session->get('user_identity')['id_pegawai'];

        try {
            return DocoHelpers::previewPdf((new SuratKematianService)->execute($pendaftaran_id, $this->_ruangan_id));
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionKonfirmasi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $pendaftaran_id = $request->post('pendaftaran_id', null);
        $response = $this->_restRanap->post('inf-pasien-pulang/konfirmasi', [
            'form_params' => [
                'pendaftaran_id' => $pendaftaran_id,
                'instalasi_id' => $this->_instalasi_id
            ]
        ]);
        $response = json_decode($response->getBody(), True);
        return DocoHelpers::response($response);
    }

    private function setKonfirmasi($status_konfirmasi)
    {
        $isKonfirmasi = false;
        if(!empty($status_konfirmasi)) {
            if(!empty($status_konfirmasi)) {
                foreach ($status_konfirmasi as $key => $value) {
                    $instalasiId = isset($value['instalasi_id']) ? $value['instalasi_id'] : null;
                    if($instalasiId == $this->_instalasi_id) {
                        $isKonfirmasi = true;
                    }
                }
            }
        }

        return $isKonfirmasi;
    }

    private function getLamaRawat($startDate, $endDate) {
        $earlier = new \DateTime($startDate);
        $later = new \DateTime($endDate);
        $abs_diff = $later->diff($earlier)->format("%a");
        return ($abs_diff == 0) ? 1 : $abs_diff;
    }
}
