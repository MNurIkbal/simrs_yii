<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-09 13:28:54
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-07 10:51:34
 */

namespace app\modules\bedah\components\traits;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use yii\base\DynamicModel;

use Doco\bedah\models\IntraOperasiForm;
use Doco\bedah\models\IntraPegawaiOperasiForm;
use Doco\bedah\models\IntraItemOperasiForm;
use Doco\bedah\models\IntraPenggunaanCairanForm;
use Doco\bedah\models\IntraAlatDitubuhForm;
use Doco\bedah\models\IntraPemeriksaanPelengkapForm;
use Doco\bedah\models\IntraKonsulTindakanForm;
use Doco\bedah\models\IntraPenggunaanBmhpForm;
use Doco\bedah\models\IntraTindakanLuarBedahForm;

trait IntraOperasiTrait
{
    public function actionIntraOperasi($id)
    {
        $request = Yii::$app->request;
        $penunjangId = $request->get('id', null);
        $model = new IntraOperasiForm;
        $unique = '';
        $options = [];
        $opsi['set_instrumen'] = [];
        $opsi['penunjang_khusus'] = [];
        $opsi['penerima'] = [];
        $data = $options = $infopasien = $infopasiendetail = [];
        $cache = Yii::$app->session;
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        if ($request->post()) {
            $post = $request->post();
            $model->load($post);
            $ruangan_id = !empty($model->ruangan_id) ? $model->ruangan_id : $ruangan_id;
            $unique = DocoHelpers::encrypt($model->inpostoperasi_id) . '-' . DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
            
            /** cleansing dan recek validasi dokter operator per tindakan */
            $cleansingPegawaiOperasi = $this->cleansingPegawaiOperasi($unique);
            $validDokter = !empty($cleansingPegawaiOperasi['isDokterOperator']) ? $cleansingPegawaiOperasi['isDokterOperator'] : false;
            $msgError = !empty($cleansingPegawaiOperasi['msgError']) ? $cleansingPegawaiOperasi['msgError'] : "";
            if(!$validDokter){
                $response = '';
                /** Penambahan kondisi ketika belum memilih dokter */
                if($msgError == '') {
                    $response = [
                        'response' => [
                            'title' => 'Proses Gagal.',
                            'text' => 'List dan Detail Operasi belum di isi!',
                            'status' => 422,
                            'is_nulldokter' => true
                        ]
                    ];
                }else{
                    $response = [
                        'response' => [
                            'title' => 'Proses Gagal.',
                            'text' => $msgError,
                            'status' => 422,
                        ]
                    ];
                }
                return DocoHelpers::response($response, 422, false);
            }

            $validateBmhp = $this->validateBmhp($unique);
            $is_available = !empty($validateBmhp['is_available']) ? $validateBmhp['is_available'] : false;
            $msgError = !empty($validateBmhp['msgError']) ? $validateBmhp['msgError'] : "";
            if(!$is_available){
                $response = [
                    'response' => [
                        'title' => 'Proses Gagal.',
                        'text' => $msgError,
                        'status' => 422,
                    ]
                ];
                return DocoHelpers::response($response, 422, false);
            }

            $additionalCache = $this->cacheIn([
                'pegawaioperasi-' . $unique,
                'itemoperasi-' . $unique,
                'penggunaancairan-' . $unique,
                'alatditubuh-' . $unique,
                'pemeriksaanpelengkap-' . $unique,
                'konsultindakan-' . $unique,
                'penggunaanbmhp-' . $unique,
                'tindakanluarbedah-' . $unique,
                'instrumen-' . $unique,
            ]);

            foreach( $additionalCache as $key => $value){
                if(empty($additionalCache[$key])){
                    $additionalCache[$key] = $cache->get($key);
                    $additionalCacheDeleted['deleted-'.$key] = $cache->get('deleted-'.$key);
                }
            }
            /** Penyesuaian pegawai operasi */
            $tmpPegawaiOperasi = $additionalCache['pegawaioperasi-' . $unique];
            $pegawaiOperasi = [];
            $cacheItemOperasi = $additionalCache['itemoperasi-' . $unique];
            $listItemOperasi = [];
            if(!empty($cacheItemOperasi)) {
                foreach ($cacheItemOperasi as $key => $value) {
                    $daftarTindakanId = ArrayHelper::getValue($value, 'daftartindakan_id');
                    if(!empty($daftarTindakanId)) {
                        $listItemOperasi[$daftarTindakanId] = $value;
                    }
                }
            }
            if(!empty($tmpPegawaiOperasi)){
                foreach($tmpPegawaiOperasi as $key => $row){
                    $daftarTindakanId = $key;
                    $kegiatanOperasiId = isset($listItemOperasi[$daftarTindakanId]['kegiatanoperasi_id']) ? $listItemOperasi[$daftarTindakanId]['kegiatanoperasi_id'] : null;
                    $kegiatanOperasiNama = isset($listItemOperasi[$daftarTindakanId]['kegiatanoperasi_nama']) ? $listItemOperasi[$daftarTindakanId]['kegiatanoperasi_nama'] : null;
                    $golonganOperasiId = isset($listItemOperasi[$daftarTindakanId]['golonganoperasi_id']) ? $listItemOperasi[$daftarTindakanId]['golonganoperasi_id'] : null;
                    $golonganOperasiNama = isset($listItemOperasi[$daftarTindakanId]['golonganoperasi_nama']) ? $listItemOperasi[$daftarTindakanId]['golonganoperasi_nama'] : null;
                    foreach($row as $val){
                        $val['kegiatanoperasi_id'] = $kegiatanOperasiId;
                        $val['kegiatanoperasi_nama'] = $kegiatanOperasiNama;
                        $val['golonganoperasi_id'] = $golonganOperasiId;
                        $val['golonganoperasi_nama'] = $golonganOperasiNama;
                        $pegawaiOperasi[] = $val;
                    }
                }
            }

            $additional = [
                'pegawaioperasi' => $pegawaiOperasi,
                'itemoperasi' => $additionalCache['itemoperasi-' . $unique],
                'penggunaancairan' => $additionalCache['penggunaancairan-' . $unique],
                'alatditubuh' => $additionalCache['alatditubuh-' . $unique],
                'pemeriksaanpelengkap' => $additionalCache['pemeriksaanpelengkap-' . $unique],
                'konsultindakan' => $additionalCache['konsultindakan-' . $unique],
                'penggunaanbmhp' => $additionalCache['penggunaanbmhp-' . $unique],
                'tindakanluarbedah' => $additionalCache['tindakanluarbedah-' . $unique],
                'instrumen' => $additionalCache['instrumen-' . $unique],
                //tampung deleted cache
                'deleted-pegawaioperasi' => $additionalCacheDeleted['deleted-pegawaioperasi-' . $unique],
                'deleted-itemoperasi' => $additionalCacheDeleted['deleted-itemoperasi-' . $unique],
                'deleted-penggunaancairan' => $additionalCacheDeleted['deleted-penggunaancairan-' . $unique],
                'deleted-alatditubuh' => $additionalCacheDeleted['deleted-alatditubuh-' . $unique],
                'deleted-pemeriksaanpelengkap' => $additionalCacheDeleted['deleted-pemeriksaanpelengkap-' . $unique],
                'deleted-konsultindakan' => $additionalCacheDeleted['deleted-konsultindakan-' . $unique],
                'deleted-penggunaanbmhp' => $additionalCacheDeleted['deleted-penggunaanbmhp-' . $unique],
                'deleted-tindakanluarbedah' => $additionalCacheDeleted['deleted-tindakanluarbedah-' . $unique],
                'deleted-instrumen' => $additionalCacheDeleted['deleted-instrumen-' . $unique],
                
            ];
            
            if ($model->validate()) {
                try {
                    $response = $this->_restBedah->post('intra-operasi/save', [
                        'form_params' => [
                            'data' => $model->attributes,
                            'additional' => $additional,
                            'ruangan_id' => $ruangan_id
                        ]
                    ]);
                    $response = json_decode($response->getBody(), true);
                    $response['status'] = 200;
                    return DocoHelpers::response($response, false);
                } catch (\GuzzleHttp\Exception\ClientException $e) {
                    $httpStatusCode = $e->getResponse()->getStatusCode();
                    if ($httpStatusCode >= 400 && $httpStatusCode <= 499) {
                        $responseError = json_decode($e->getResponse()->getBody(), true)['response'];
                        return $this->responseJson($httpStatusCode, isset($responseError['message']) ? $responseError['message'] : '');
                        // Client error
                    } else {
                        Yii::error([
                            "Message" => $e->getMessage(),
                            "Line" => $e->getLine(),
                            "File" => $e->getFile()
                        ]);
                        throw new \Exception("Something went wrong on API");
                    }
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        try {
            $listRequest = ['keadaan_kulit', 'posisi_operasi', 'pencucian_operasi', 'posisi_elektroda'];
            $response = $this->_restBedah->get(
                'allow/pack-intra',
                [
                    'query' => ['id' => $id],
                    'form_params' => $listRequest
                ]
            );
            $body = json_decode($response->getBody(), true);
            $data = $body['response']['data'];
            $options = $body['response']['lookup'];
            $infopasien = $body['response']['infopasien']['data'];
            $ruangan_id = isset($infopasien['ruangan_id']) ? $infopasien['ruangan_id'] : $ruangan_id;
            $infopasiendetail = $body['response']['infopasien']['data']['detailOperasi'];
            $timoperasi = $body['response']['timoperasi'];
            $ruangan_id = !empty($infopasien['ruangan_id']) ? $infopasien['ruangan_id'] : null;
            $instalasi_id = !empty($infopasien['instalasiasal_id']) ? $infopasien['instalasiasal_id'] : null;
            $model->attributes = $data;
            $model->ruangan_id = $ruangan_id;
            $model->is_surgicalsavety = ($model->is_surgicalsavety == true) ? 1 : 0;
            $model->is_diathermy = ($model->is_diathermy == true) ? 1 : 0;
            $model->is_diathermy = ($model->is_diathermy == true) ? 1 : 0;
            $model->is_jaringantubuh = ($model->is_jaringantubuh == true) ? 1 : 0;
            $model->is_diserahkan = ($model->is_diserahkan == true) ? 1 : 0;
            $opsi['set_instrumen'] = (!empty($data['setInstrumen'])) ? $data['setInstrumen'] : [];
            $opsi['penunjang_khusus'] = (!empty($data['setPenunjang'])) ? $data['setPenunjang'] : [];
            $opsi['penerima'] = (!empty($data['pegawaiPenerima'])) ? $data['pegawaiPenerima'] : [];
            $unique = DocoHelpers::encrypt($model->inpostoperasi_id) . '-' . DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
            $cache->set('pegawaioperasi-' . $unique,[]);
            $cache->set('itemoperasi-' . $unique,[]);
            $cache->set('penggunaancairan-' . $unique,[]);
            $cache->set('alatditubuh-' . $unique,[]);
            $cache->set('pemeriksaanpelengkap-' . $unique,[]);
            $cache->set('konsultindakan-' . $unique,[]);
            $cache->set('penggunaanbmhp-' . $unique,[]);
            $cache->set('instrumen-' . $unique,[]);
            if (!$cache->get('pegawaioperasi-' . $unique)) {
                $cache->set('pegawaioperasi-' . $unique, $timoperasi);
            }
            if (!$cache->get('itemoperasi-' . $unique)) {
                if (count($infopasiendetail) > 0) {
                    Yii::$app->session->set('itemoperasi-' . $unique, []);
                    $arrItemOperasi = [];
                    $resItemOperasi = [];
                    $bmhp = [];
                    $daftartindakanId = [];
                    foreach ($infopasiendetail as $key => $value) {
                        $arrItemOperasi['inpostoperasi_id'] = $model->inpostoperasi_id;
                        $arrItemOperasi['pasienmasukpenunjang_id'] = $model->pasienmasukpenunjang_id;
                        $arrItemOperasi['daftartindakan_id'] = $value['daftartindakan_id'];
                        $arrItemOperasi['daftartindakan_nama'] = $value['daftartindakan_nama'];
                        $arrItemOperasi['operasi_nama'] = $value['daftartindakan_nama'];
                        $arrItemOperasi['pegawai_nama'] = $value['nama_dokter'];
                        $arrItemOperasi['pegawai_input'] = $value['created_by'];
                        $arrItemOperasi['is_cyto'] = $value['is_cyto'];
                        $arrItemOperasi['is_penyulit'] = $value['is_penyulit'];
                        $arrItemOperasi['penyulit'] = $value['is_penyulit'];
                        $arrItemOperasi['tarif_satuan'] = $value['tarif_satuan'];
                        $arrItemOperasi['tarif_tindakan'] = $value['tarif_tindakan'];
                        $arrItemOperasi['tarif_cyto'] = empty($value['tarifcyto_tindakan']) ? 0 : $value['tarifcyto_tindakan'];
                        $arrItemOperasi['cyto'] = ($value['is_cyto']) ? '✓' : '';
                        $arrItemOperasi['operasi_id'] = $value['operasi_id'];
                        $arrItemOperasi['jenis_luka'] = '';
                        $arrItemOperasi['jenis_luka_nama'] = '';
                        $arrItemOperasi['golonganoperasi_id'] = $value['golonganoperasi_id'];
                        $arrItemOperasi['golonganoperasi_nama'] = $value['golonganoperasi_nama'];
                        $arrItemOperasi['jenisanastesi_id'] = '';
                        $arrItemOperasi['jenisanastesi_nama'] = '';
                        $arrItemOperasi['default'] = 1;
                        $arrItemOperasi['pegawai_id'] = $value['pegawai_id'];
                        $arrItemOperasi['kegiatanoperasi_id'] = isset($value['kegiatanoperasi_id']) ? $value['kegiatanoperasi_id'] : null;
                        $arrItemOperasi['kegiatanoperasi_nama'] = isset($value['kegiatanoperasi_nama']) ? $value['kegiatanoperasi_nama'] : '-';
                        $resItemOperasi[] = $arrItemOperasi;
                        if (!in_array($value['daftartindakan_id'], $daftartindakanId)) {
                            $daftartindakanId[] = $value['daftartindakan_id'];
                        }
                    }
                    $this->saveToCache('itemoperasi-' . $unique, $resItemOperasi);
                    try {
                        $pegawai_input = Yii::$app->session->get('user_identity');
                        $bmhpResponse = $this->_restBedah->get('allow/compare-bmhp-ruangan', ['form_params' => ['daftartindakan_id' => $daftartindakanId, 'ruangan_id' => $ruangan_id]]);
                        $bmhpRes = json_decode($bmhpResponse->getBody(), true);
                        foreach ($bmhpRes['response'] as $key => $value) {
                            $newData = [];
                            $newData['pasienmasukpenunjang_id'] = $model->pasienmasukpenunjang_id;
                            $newData['inpostoperasi_id'] = $model->inpostoperasi_id;
                            $newData['daftartindakan_id'] = $value['daftartindakan_id'];
                            $newData['obatalkes_id'] = $value['obatalkes_id'];
                            $newData['obatalkes_nama'] = $value['obatalkes_nama'];
                            $newData['persediaan'] = !empty($value['qty_tersedia']) ? $value['qty_tersedia'] : 0;
                            $newData['tambahan'] = 0;
                            $newData['terpakai'] = 0;
                            $newData['sisa'] = $value['persediaan'];
                            $newData['ditagihkan'] = '';
                            $newData['is_ditagihkan'] = '0';
                            $newData['is_available'] = ($value['is_available']) ? 1 : 0;
                            $newData['msg'] = $value['msg'];
                            $newData['pegawai_input'] = !empty($pegawai_input['nama_pegawai']) ? $pegawai_input['nama_pegawai'] : '';
                            $bmhp[] = $newData;
                        }
                    } catch (\RequestException $e) {
                        $bmhp = [];
                    }
                    // $this->saveToCache('penggunaanbmhp-' . $unique, $bmhp);
                }
            }

            $penjaminId = ArrayHelper::getValue($infopasien, 'penjamin_id');
            $kelasPelayananId = ArrayHelper::getValue($infopasien, 'kelaspelayanan_id');
        } catch (\Exception $e) {
            $model->attributes = [];
        }

        $show_kegiatan_golongan_operasi = $this->getShowKegiatanGolonganOperasi();
        return $this->renderAjax('detail-partial/_intraoperasi', get_defined_vars());
    }
    public function actionIntraTambahPegawai()
    {
        $cache = Yii::$app->session;
        $model = new IntraPegawaiOperasiForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('fe', 'Tambah anggota tim operasi');
        $request = Yii::$app->request;
        $dataTimOperasi = $this->getDataTimOperasi();
        $timOperasi = json_encode($dataTimOperasi);
        if ($request->post()) {
            $post = $request->post();
            $model->load($post);
            if(empty($model->kegiatanoperasi_id) && empty($model->golonganoperasi_id)) {
                if(!empty($model->daftartindakan_id)) { 
                    $dataTindakanOperasi = $this->getTindakanOperasi($model->daftartindakan_id);
                    $dataTindakanOperasi = ArrayHelper::getValue($dataTindakanOperasi, 'data', []);
                    $model->kegiatanoperasi_id = ArrayHelper::getValue($dataTindakanOperasi, 'kegiatanoperasi_id');
                    $model->golonganoperasi_id = ArrayHelper::getValue($dataTindakanOperasi, 'golonganoperasi_id');
                }
            }
            $prosentase = 0;
            $slug_posisi = '';
            if(!empty($dataTimOperasi)) {
                foreach ($dataTimOperasi as $key => $value) {
                    if($value['id'] == $model->posisi_tim) {
                        $prosentase = isset($value['prosentase']) ? $value['prosentase'] : 0;
                        $slug_posisi = isset($value['slug']) ? $value['slug'] : '';
                    }
                }
            }
            $model->prosentase = $prosentase;
            $model->slug_posisi = $slug_posisi;

            if(empty($model->daftartindakan_id)){
                $customError = [
                    'daftartindakan_id' => [
                        'Tindakan Belum dipilih.',
                    ],
                ];
                $customFormName = substr(strrchr(get_class(new IntraItemOperasiForm), "\\"), 1);
                $errors = DocoHelpers::parseError($customError, $customFormName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }

            if ($model->validate()) {
                $inpostid = DocoHelpers::encrypt($model->inpostoperasi_id);
                $pasienpenunjangid = DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
                $cacheName = 'pegawaioperasi-' . $inpostid . '-' . $pasienpenunjangid;
                $data = $model->attributes;
                $pegawai_input = Yii::$app->session->get('user_identity');
                $data['pegawai_input'] = $pegawai_input['nama_pegawai'];
                $cacheData = $cache->get($cacheName);
                $cacheData[$model->daftartindakan_id][]= $data;
                return DocoHelpers::response([
                    'cacheSet' => $cache->set($cacheName, $cacheData),
                    'meta' => ['message'=>'Berhasil']
                ]);
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        $options = [];
        return $this->renderAjax('detail-partial/intraoperasi/__pegawaioperasi', get_defined_vars());
    }

    public function actionIntraUpdatePegawai(){
        $cache = Yii::$app->session;
        $request = Yii::$app->request;
        $data = $request->post('payload',[]);
        $cacheName = $request->post('cacheName','');
        $key = $request->post('key',null);
        $daftartindakan_id = $request->post('daftartindakan_id',null);
        $update = false;
        $status = 422;
        
        $pegawai_input = Yii::$app->session->get('user_identity');
        $data['pegawai_input'] = !empty($pegawai_input['nama_pegawai']) ? $pegawai_input['nama_pegawai'] : '';
        if(!empty($data) && !empty($cacheName) && isset($key) && !empty($daftartindakan_id)){
            $update = $this->updateCachePegawaiOperasi($cacheName, $data, $key, $daftartindakan_id);
        }

        if($update){
            $status = 200;
            $response = [
                'response' => [
                    'title' => 'Sukses',
                    'text' => 'Berhasil mengupdate data.',
                    'status' => $status,
                ]
            ];
        }else{
            $status = 422;
            $response = [
                'response' => [
                    'title' => 'Gagal',
                    'text' => 'Gagal mengupdate data.',
                    'status' => $status,
                ]
            ];
        }
        return DocoHelpers::response($response, $status, false);
    }

    public function actionIntraTambahItemOperasi($key = null, $unique = null)
    {
      return Yii::$app->docoPlugin->execute($this, 'item_operasi');
    }
    
    public function actionIntraTambahPenggunaanCairan()
    {
        $model = new IntraPenggunaanCairanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('fe', 'Tambah penggunaan cairan');
        $request = Yii::$app->request;
        if ($request->post()) {
            $post = $request->post();
            $model->load($post);
            if ($model->validate()) {
                $inpostid = DocoHelpers::encrypt($model->inpostoperasi_id);
                $pasienpenunjangid = DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
                $cacheName = 'penggunaancairan-' . $inpostid . '-' . $pasienpenunjangid;
                $data = $model->attributes;
                $pegawai_input = Yii::$app->session->get('user_identity');
                $data['pegawai_input'] = $pegawai_input['nama_pegawai'];
                return DocoHelpers::response($this->saveToCache($cacheName, $data));
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        $options = [];

        return $this->renderAjax('detail-partial/intraoperasi/_penggunaancairan', get_defined_vars());
    }
    public function actionIntraTambahPenggunaanBmhp($ruangan_id, $key = null, $unique = null)
    {
        $model = new IntraPenggunaanBmhpForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('fe', 'Tambah penggunaan bmhp');
        $request = Yii::$app->request;
        $cache = Yii::$app->session;
        $opsi = '';
        if ($request->post()) {
            $post = $request->post();
            $model->load($post);
            if ($model->validate()) {
                $inpostid = DocoHelpers::encrypt($model->inpostoperasi_id);
                $pasienpenunjangid = DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
                $cacheName = 'penggunaanbmhp-' . $inpostid . '-' . $pasienpenunjangid;
                $data = $model->attributes;
                $postObatId = $model->obatalkes_id;
                $postObatNama = $model->obatalkes_nama;
                $cache = Yii::$app->session;
                $cacheData = $cache->get($cacheName);
                
                $listBmhp = [];
                if(!empty($cacheData)) {
                    foreach ($cacheData as $value) {
                        $obatalkesId = isset($value['obatalkes_id']) ? $value['obatalkes_id'] : null;
                        if(!empty($obatalkesId)) {
                            $listBmhp[$obatalkesId] = $value;
                        }
                    }
                }
                $isValid = true;
                $msgError = '';
                if(($key == '' || $key == null) && isset($listBmhp[$postObatId])) {
                    $isValid = false;
                    $msgError = 'BMHP <strong><i>'.$postObatNama. ' </i></strong> sudah ditambahkan. Silahkan cek inputan.';
                }
                if(!$isValid) {
                    $response = [
                        'response' => [
                            'title' => 'Proses Gagal.',
                            'text' => $msgError,
                            'status' => 422,
                        ]
                    ];
                    return DocoHelpers::response($response, 422, false);
                }
                $pegawai_input = Yii::$app->session->get('user_identity');
                $data['pegawai_input'] = $pegawai_input['nama_pegawai'];
                if ($key != '') {
                    return DocoHelpers::response($this->updateCache($cacheName, $data, $key));
                }
                return DocoHelpers::response($this->saveToCache($cacheName, $data));
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        $options = [];
        $konfig = $this->getKonfigDefaultInputBmhp();
        if(isset($konfig['default_bmhpbedah_ditagihkan'])) {
            $model->is_ditagihkan = $konfig['default_bmhpbedah_ditagihkan'];
            if($konfig['default_bmhpbedah_ditagihkan'] == true) {
                $model->ditagihkan = '✓';
            }
        }
        if ($key != '') {
            $data = $cache->get($unique);
            $model->attributes = $data[$key];
            $opsi = json_encode(['id' => $data[$key]['obatalkes_id'], 'name' => $data[$key]['obatalkes_nama']]);
        }
        $instalasiId = $request->get('instalasi_id');
        $kelasPelayananId = $request->get('kelaspelayanan_id');
        $penjaminId = $request->get('penjamin_id');
        return $this->renderAjax('detail-partial/intraoperasi/_penggunaanbmhp', get_defined_vars());
    }
    public function actionIntraTambahPemeriksaanPelengkap()
    {
        $model = new IntraPemeriksaanPelengkapForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('fe', 'Tambah pemeriksaan pelengkap');
        $request = Yii::$app->request;
        $options = [];
        $listRuangan = $listInstalasi = [];
        if ($request->post()) {
            $post = $request->post();
            $model->load($post);
            if ($model->validate()) {
                $model->tarif_cyto = 0;
                $model->additional_data = json_encode([
                    'tariftindakan_id' => $model->tariftindakan_id
                ]);
                $inpostid = DocoHelpers::encrypt($model->inpostoperasi_id);
                $pasienpenunjangid = DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
                $cacheName = 'pemeriksaanpelengkap-' . $inpostid . '-' . $pasienpenunjangid;
                $data = $model->attributes;
                $pegawai_input = Yii::$app->session->get('user_identity');
                $data['pegawai_input'] = $pegawai_input['nama_pegawai'];
                return DocoHelpers::response($this->saveToCache($cacheName, $data));
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restBedah->get('allow/get-ruangan-pelengkap');
            $body = json_decode($response->getBody(), true);
            $bodyResponse = isset($body['response']) ? $body['response'] : [];
            $listInstalasi = isset($bodyResponse['instalasi']) 
                    ? ArrayHelper::map($bodyResponse['instalasi'],'instalasi_id', 'instalasi_nama')
                    : [];
            $listRuangan = isset($bodyResponse['ruangan']) 
                    ? ArrayHelper::map($bodyResponse['ruangan'], 'ruangan_id', 'ruangan_nama')
                    : [];
            $model->instalasi_id = isset($bodyResponse['default_instalasi']) ? $bodyResponse['default_instalasi'] : null;
        }

        return $this->renderAjax('detail-partial/intraoperasi/_pemeriksaanpelengkap', get_defined_vars());
    }
    public function actionIntraTambahAlatDitubuh()
    {
        $model = new IntraAlatDitubuhForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('fe', 'Tambah alat yang sengaja ditinggal dalam tubuh');
        $request = Yii::$app->request;
        if ($request->post()) {
            $post = $request->post();
            $model->load($post);
            if ($model->validate()) {
                $inpostid = DocoHelpers::encrypt($model->inpostoperasi_id);
                $pasienpenunjangid = DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
                $cacheName = 'alatditubuh-' . $inpostid . '-' . $pasienpenunjangid;
                $data = $model->attributes;
                $pegawai_input = Yii::$app->session->get('user_identity');
                $data['pegawai_input'] = $pegawai_input['nama_pegawai'];
                return DocoHelpers::response($this->saveToCache($cacheName, $data));
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        $options = [];

        return $this->renderAjax('detail-partial/intraoperasi/_alatditubuh', get_defined_vars());
    }
    public function actionIntraTambahKonsulTindakan()
    {
        $model = new IntraKonsulTindakanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('fe', 'Tambah konsultasi tindakan');
        $request = Yii::$app->request;
        if ($request->post()) {
            $post = $request->post();
            $model->load($post);
            if ($model->validate()) {
                $inpostid = DocoHelpers::encrypt($model->inpostoperasi_id);
                $pasienpenunjangid = DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
                $cacheName = 'konsultindakan-' . $inpostid . '-' . $pasienpenunjangid;
                $data = $model->attributes;
                $pegawai_input = Yii::$app->session->get('user_identity');
                $data['pegawai_input'] = $pegawai_input['nama_pegawai'];
                return DocoHelpers::response($this->saveToCache($cacheName, $data));
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        $options = [];

        return $this->renderAjax('detail-partial/intraoperasi/_konsultindakan', get_defined_vars());
    }

    /**
     * This function save payload to cache
     * 
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionTambahTindakanLuarBedah()
    {
        $model = new IntraTindakanLuarBedahForm;
        $model->attributes = Yii::$app->request->post();
        if ($model->validate()) {
            $cacheName = 'tindakanluarbedah-' . $model->keyUnique;
            $existingCache = Yii::$app->session->get($cacheName, []);
            if ($existingCache) {
                foreach ($existingCache as $eachCache) {
                    if (isset($eachCache['tindakanluarbedah_id']) && $eachCache['tindakanluarbedah_id'] == $model->tindakanluarbedah_id) {
                        return $this->responseJson(400, 'Tindakan sudah diinput.');
                    }
                }
            }
            $data = $model->attributes;
            $pegawai_input = Yii::$app->session->get('user_identity');
            $data['pegawai_input'] = $pegawai_input['nama_pegawai'];
            unset($data['keyUnique']);
            $this->saveToCache($cacheName, $data);
            return $this->responseJson(200, 'Data berhasil disimpan');
        } else {
            return $this->responseJson(422, 'Silakan cek kembali data yang disubmit.', $model->errors);
        }
    }
    /**
     * set instrumen for intra operasi
     * 
     * @param String var
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */

    public function actionAddInstrumen()
    {
        $post = Yii::$app->request->post();
        $customValidation = DynamicModel::validateData($post, [
            [
                ['persediaan', 'obatalkes_id', 'inpostoperasi_id', 'pasienmasukpenunjang_id'], 'required',
            ],
            [
                'terpakai', 'integer', 'min' => 0, 'max' => ($post['persediaan'] + (isset($post['tambahan']) && !empty($post['tambahan']) ? $post['tambahan'] : 0)), 'message' => 'Terpakai tidak boleh lebih dari persediaan'
            ]
        ]);
        if ($customValidation->hasErrors()) {
            return $this->helper->response([
                'error' => $customValidation->getErrors()
            ], 422);
        }
        $inpostId = $this->helper->encrypt($post['inpostoperasi_id']);
        $pasienMasukPenunjangId = $this->helper->encrypt($post['pasienmasukpenunjang_id']);

        $cacheName = 'instrumen-' . $inpostId . '-' . $pasienMasukPenunjangId;
        $getCache = Yii::$app->session->get($cacheName);
        $pegawai_input = Yii::$app->session->get('user_identity');
        $post['pegawai_input'] = $pegawai_input['nama_pegawai'];
        if ($getCache) {
            $updateValue = false;
            $hasError = [];
            foreach ($getCache as $index => $item) {
                if ($item['obatalkes_id'] == $post['obatalkes_id']) {
                    $updateValue = true;
                    $tambahan = $item['tambahan'] + $post['tambahan'];
                    $terpakai = ($item['terpakai'] + $post['terpakai']);
                    $sisa = ($post['persediaan'] + $tambahan) - $terpakai;
                    if ($terpakai <= ($post['persediaan'] + $tambahan)) {
                        $getCache[$index]['persediaan'] = $post['persediaan'];
                        $getCache[$index]['tambahan'] = $tambahan;
                        $getCache[$index]['terpakai'] = $terpakai;
                        $getCache[$index]['sisa'] = $sisa;
                    } else {
                        $hasError = [
                            'terpakai' => [
                                'Instrumen sudah pernah diinput dan lebih dari persediaan!'
                            ]
                        ];
                    }
                }
            }
            if ($hasError) {
                return $this->helper->response([
                    'error' => $hasError
                ], 422);
            }
            if ($updateValue) {
                return Yii::$app->session->set($cacheName, $getCache);
            }
        }
        return $this->helper->response($this->saveToCache($cacheName, $post));
    }

    // update-prosentase-anestesi
    /**
     * This function set prosentase to cache
     * 
     * @param String $inpostoperasi_id
     * @param String $pasienmasukpenunjang_id
     * @param String $pegawai_id
     * @param String $pegawai_id
     * @param String $value
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionUpdateProsentaseAnestesi($inpostoperasi_id, $pasienmasukpenunjang_id)
    {
        $pegawai_id = Yii::$app->request->post('pegawai_id', null);
        $value = Yii::$app->request->post('value', 0);
        $unique = DocoHelpers::encrypt($inpostoperasi_id) . '-' . DocoHelpers::encrypt($pasienmasukpenunjang_id);
        $bucketCache = Yii::$app->session->get('pegawaioperasi-' . $unique);
        $newBucketCache = [];
        $validEmployee = false;
        foreach ($bucketCache as $eachCache) {
            if ($eachCache['slug_posisi'] == 'dokter-anastesi' && $eachCache['pegawai_id'] == $pegawai_id) {
                $eachCache['prosentase'] = $value;
                $validEmployee = true;
            }
            $newBucketCache[] = $eachCache;
        }
        if ($validEmployee) {
            Yii::$app->session->set('pegawaioperasi-' . $unique, $newBucketCache);
            return $this->responseJson(200, 'Prosentase berhasil diperbarui');
        } else {
            return $this->responseJson(400, 'Pegawai tidak ditemukan');
        }
    }

    private function getDataTimOperasi()
    {
        $response = $this->_restBedah->get('allow/get-tim-operasi');
        $body = json_decode($response->getBody(), true);

        return $body['response'];
    }

    /**
     * created : 1 Oktober 2021
     * Fungsi ini untuk memastikan tidak ada pegawai operasi yang tidak memiliki tindakan, lalu ikut tersimpan ke intraoperasi_t, sehingga di halaman verifikasi tidak muncul tindakan yang tidak diinputkan, karena yang dimasukkan ke verifikasi hasil join ke table tindakanoperasi_t
     * case : tambah tindakan operasi, pilih tindakan operasi, tambah pegawai operasi, lalu klik kembali.
     * story: tambah tindakan operasi mapping ke pegawai US1212
     */
    private function cleansingPegawaiOperasi($unique){
        $cache = Yii::$app->session;
        $cacheNamePo = 'pegawaioperasi-' . $unique;
        $cacheItemOperasi = $cache->get('itemoperasi-' . $unique);
        $cachePegawaiOperasi = $cache->get($cacheNamePo);
        $isDokterOperator = false;
        $msgError = "";
        $arrItemOperasi = $arrPegawaiOperasi = [];
        foreach($cacheItemOperasi as $key => $val){
            $isDokterOperator = false;
            $daftartindakanId = !empty($val['daftartindakan_id']) ? $val['daftartindakan_id'] : null;
            $daftartindakan_nama = !empty($val['daftartindakan_nama']) ? $val['daftartindakan_nama'] : '';
            $arrItemOperasi[]= $daftartindakanId;
            if(!empty($cachePegawaiOperasi[$daftartindakanId])){
                foreach ($cachePegawaiOperasi[$daftartindakanId] as $key => $value) {
                    if($value["posisi_tim"] == DocoConstants::TIM_DOKTER_BEDAH){
                        $isDokterOperator = true;
                    }
                }
            }else{
                $isDokterOperator = false;
                $msgError = "Tindakan " . $daftartindakan_nama . " belum memiliki tim operasi.";
                break;
            }
            if(!$isDokterOperator){
                $msgError = "Tindakan " . $daftartindakan_nama . " belum memiliki dokter operator.";
                break;
            }
        }

        /** cleansing data */
        foreach( $cachePegawaiOperasi as $key => $val ){
            if(!in_array($key, $arrItemOperasi)){
                $this->unsetCachePegawaiOperasi(null, $cacheNamePo, $key); 
            }
        }

        $result = [
            'isDokterOperator' => $isDokterOperator,
            'msgError' => $msgError,
        ];
        return $result;
    }

    /**
     * created : 11 Oktober 2021
     * Fungsi ini digunakan untuk memvalidasi obat yang diinputkan daritindakan dan stoknya kurang
     */
    private function validateBmhp($unique){
        $cache = Yii::$app->session;
        $cacheName = 'penggunaanbmhp-' . $unique;
        $cacheData = $cache->get($cacheName);
        $is_available = true;
        $msgError = "";
        $arrItemOperasi = [];
        if($cacheData){
            foreach($cacheData as $key => $val){
                $obatalkes_nama = !empty($val['obatalkes_nama']) ? $val['obatalkes_nama'] : '';
                $persediaan = !empty($val['persediaan']) ? $val['persediaan'] : 0;
                $terpakai = !empty($val['terpakai']) ? $val['terpakai'] : 0;
                if($persediaan < $terpakai){
                    $is_available = false;
                    $msgError = "Obat " . $obatalkes_nama . " tidak tersedia.";
                    break;
                }
            }
        }

        $result = [
            'is_available' => $is_available,
            'msgError' => $msgError,
        ];
        return $result;
    }

    private function getTindakanOperasi($daftarTindakanId)
    {
        return $this->guzzleExec($this->_restBedah, [
            'url' => 'allow/get-tindakan-operasi',
            'payload' => [
                'query' => [
                    'daftartindakan_id' => $daftarTindakanId,
                ]
            ],
            'returnResponse' => true
        ]);
    }


    private function getKonfigDefaultInputBmhp()
    {
        return $this->guzzleExec($this->_restBedah, [
            'url' => 'allow/get-konfig-default-input-bmhp',
            'payload' => [
                'query' => []
            ],
        ]);
    }
}
