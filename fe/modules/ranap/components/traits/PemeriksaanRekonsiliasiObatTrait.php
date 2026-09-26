<?php

/**
 * @Author: Sunarko
 * @Date:   2018-07-10 14:18:39
 * @Last Modified by:
 * @Last Modified time:
 */

// Namespace
namespace app\modules\ranap\components\traits;

// Using Yii
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

// Using Guzzles
use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

// Using components
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

// Using model
use app\modules\ranap\models\InfoPasienRanapForm;
use app\modules\ranap\models\RekonsiliasiObatForm;
use app\modules\ranap\models\ObatAlkesForm;


trait PemeriksaanRekonsiliasiObatTrait 
{
    public function actionRekonsobat()
    {  
        $request = Yii::$app->request;
        $disabled = (!empty($this->_data_pasien['pasienpulang_id']) || $this->_data_pasien['is_stopakomodasi'] == true) ? true : false;
        $model = new RekonsiliasiObatForm;
        $model->scenario = 'input_obat';
        $model->is_alergi = $this->_data_pasien['r_alergiobat'] ? 2 : 1;

        $model->is_hamil = $this->_data_pasien['is_hamil'] == 1 ? 1 : 0;
        $model->sumber_informasi = $this->_data_pasien['sumber_info'] ? : 0;

        $pend_id = $this->_data_pasien['pendaftaran_id'];
        $admisi_id = $this->_data_pasien['pasienadmisi_id'];

        // echo '<pre>';
        // print_r($pend_id . ' ' . $admisi_id); exit;
        $model->pendaftaran_id = $pend_id;
        $model->pasienadmisi_id = $admisi_id;

        $user_identity = $this->_user_identity;
        $data_alergi = [
            '0' => Yii::t('fe', 'Tidak tahu'),
            '1' => Yii::t('fe', 'Tidak'),
            '2' => Yii::t('fe', 'Ya')
        ];
        $data_choice = [
            '0' => Yii::t('fe', 'Tidak'),
            '1' => Yii::t('fe', 'Ya'),
        ];
        $data_info = [
            '0' => Yii::t('fe', 'Pasien'), 
            '1' => Yii::t('fe', 'Keluarga'), 
            '2' => Yii::t('fe', 'Lain-lain'),
        ];
        $data_signa = [
            ['signa' => '3 x 1'],
            ['signa' => '2 x 1'],
            ['signa' => '2 butir sebelum makan'],
            ['signa' => '2 butir sebelum tidur'],
            ['signa' => '1 x 1'],
        ];

        $this->setDefaultCache($pend_id, $admisi_id);
        $datarekon = Yii::$app->cache->get($this->_cacheRekon);

        if ($datarekon) {
            $last = count($datarekon) - 1;
            $model->is_alergi = $datarekon[0]['is_alergi'];
            $model->obat_alergi = $datarekon[0]['obat_alergi'];
            $model->is_hamil = $datarekon[0]['is_hamil'] === null ? 0 : ($datarekon[0]['is_hamil'] == false ? 0 : 1);
            $model->informasi = $datarekon[0]['informasi'];
            $model->sumber_informasi = $datarekon[0]['sumber_informasi'];
        }
        // print_r($model->attributes);die;
        $hide = 'show()';
        $status_disabled = $this->getStatusPeriksa($this->_data_pasien['pendaftaran_id']);
        if($status_disabled == true){
            $hide = 'hide()';
        }else {
            $status_disabled = 'false';
        }
        
        return $this->renderAjax('rekonsiliasi-obat/__rekonsobat', get_defined_vars());
    }

    public function actionSaveRekonCache() {
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        $post = $request->post();

        $model = new RekonsiliasiObatForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $model->scenario = 'input_obat';
        $model->load($post);

        if ($model->validate()) {

            $response = $this->_restRanap->get('allow/get-one-obat-alkes?id=' . $model->obatalkes_id);
            $body = json_decode($response->getBody(), true);
            $model->nama_obat = $body['response']['obatalkes_nama'];

            $cacheRekonObat = $cache->get($this->_cacheRekon);

            if (!$cacheRekonObat) {
                $cache->set($this->_cacheRekon, []);
                $cacheRekonObat = [];
            }
            if (!isset($cacheRekonObat[$model->obatalkes_id])) {
                $cacheRekonObat[$model->obatalkes_id] = [];
            }

            $setCache = [
                // from hidden field
                'pendaftaran_id' => trim($model->pendaftaran_id),
                'pasienadmisi_id' => trim($model->pasienadmisi_id),
                'tgl_rekonsiliasi' => date('Y-m-d H:i:s'),
                // from head
                'obatalkes_id' => trim($model->obatalkes_id),
                'is_alergi' => trim($model->is_alergi),
                'obat_alergi' => trim($model->obat_alergi),
                'is_hamil' => trim($model->is_hamil),
                'sumber_informasi' => trim($model->sumber_informasi),
                'informasi' => trim($model->informasi),
                // from detail
                'obatalkes_id' => trim($model->obatalkes_id),
                'nama_obat' => trim($model->nama_obat),
                'qty_obat' => trim($model->qty_obat),
                'satuan_kecil' => trim($model->satuan_kecil),
                'rute_obat' => trim($model->rute_obat),
                'signa' => trim($model->signa),
                'waktu_pemberian' => trim($model->waktu_pemberian),
                'is_lanjut' => '',
                'catatan' => '',
                'dokter_id' => '',
                'pemberi_keputusan' => '',
                'qty_layak' => '',
                'qty_tidaklayak' => '',
                'terapi' => '',
                'signaterapi' => '',
                'rute_kelayakan' => '',
                'apoteker_id' => '',
                'tgl_kelayakan' => '',  
                'review_kelayakan' => '',
                //hidden
                'rekonsiliasiobat_id' => '',
            ];

            $cacheRekonObat[$model->obatalkes_id] = $setCache;
            $cache->set($this->_cacheRekon, $cacheRekonObat);
            
            $res['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil di tambah'
            ];
            return DocoHelpers::response($res);
        } else {
            $response = $model->errors;
            return DocoHelpers::response($response, 422, $formName);
        }
    }
    

    public function actionGetListRekonsiliasi()
    {
        $request = Yii::$app->request;

        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $cacheRekonObat = Yii::$app->cache->get($this->_cacheRekon);

        // echo '<pre>';
        // print_r($cacheRekonObat); exit;

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        if ($cacheRekonObat !== false) {
            $no = $request->get('start', 0);
            foreach ($cacheRekonObat as $key => $value) {
                $no++;
                // $primaryKey = DocoHelpers::encrypt($key);
                $data[] = [
                    'rowNum' => $no,
                    'pasienadmisi_id' => $value['pasienadmisi_id'],
                    'tgl_rekonsiliasi' => $value['tgl_rekonsiliasi'],
                    'obatalkes_id' => $value['obatalkes_id'],
                    'is_alergi' => $value['is_alergi'],
                    'obat_alergi' => $value['obat_alergi'],
                    'is_hamil' => $value['is_hamil'],
                    'sumber_informasi' => $value['sumber_informasi'],
                    'informasi' => $value['informasi'],
                    // 'nama_obat' => $value['nama_obat'],
                    'nama_obat' => $value['nama_obat'],
                    'qty_obat' => $value['qty_obat'],
                    'satuan_kecil' => $value['satuan_kecil'],
                    'rute_obat' => $value['rute_obat'],
                    'signa' => $value['signa'],
                    'waktu_pemberian' => $value['waktu_pemberian'],
                    'is_lanjut' => $value['is_lanjut'],
                    'catatan' => $value['catatan'],
                    'pemberi_keputusan' => $value['pemberi_keputusan'],
                    'dokter_id' => $value['dokter_id'],
                    'qty_layak' => $value['qty_layak'],
                    'qty_tidaklayak' => $value['qty_tidaklayak'],
                    'terapi' => $value['terapi'],
                    'signaterapi' => $value['signaterapi'],
                    'rute_kelayakan' => $value['rute_kelayakan'],
                    'apoteker_id' => $value['apoteker_id'],
                    'tgl_kelayakan' => $value['tgl_kelayakan'],
                    'review_kelayakan' => $value['review_kelayakan'],
                    'rekonsiliasiobat_id' => $value['rekonsiliasiobat_id'],
                ];
            }
            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);
        }

        return DocoHelpers::response($result);
    }


    public function actionSaveRekon() 
    {
        $request = Yii::$app->request;
        // i dont know why $this->_data_pasien is updated when hit this action

        $cache = Yii::$app->cache;
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $cacheRekonObat = $cache->get($this->_cacheRekon);


        try {
            $response = $this->_restRanap->post('rekonsiliasi-obat/create', [
                'form_params' => $cacheRekonObat
            ]);
            $response = json_decode($response->getBody(), true);

            if ($response['metadata']['status'] == 200){
                // unset session
                $this->setDefaultCache($request->post('pend_id'), $request->post('admisi_id'));
                
                return DocoHelpers::response($response);
            }else{
                return DocoHelpers::responseTemplate(
                    500, 
                    'Error', 
                    [], 
                    [
                        'title' => Yii::t('fe', 'Peringatan'), 
                        'text' => 'Terdapat kesalahan',
                        'message' => Yii::t('fe','Terjadi kesalahan').'!',
                    ]
                );
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionSaveKeputusanRekon() 
    {
        $request = Yii::$app->request;
        // i dont know why $this->_data_pasien is updated when hit this action

        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $data = $request->post('data');
        $posts = [];
        foreach ($data as $datum) {
            // return json_encode($datum['name']);
            if ($datum['name'] == '_csrf') continue;
            
            $name = explode('-', $datum['name']);
            $posts[$name[0]][$name[1]] = trim($datum['value']);
        }

        try {
            $response = $this->_restRanap->post('rekonsiliasi-obat/update-keputusan-rekon?pegawai_id='.$id_pegawai, [
                'form_params' => $posts
            ]);
            $response = json_decode($response->getBody(), true);

            if ($response['metadata']['status'] == 200){
                // unset session
                $this->setDefaultCache($request->post('pend_id'), $request->post('admisi_id'));
                
                return DocoHelpers::response($response);
            }else{
                return DocoHelpers::responseTemplate(
                    500, 
                    'Error', 
                    [], 
                    [
                        'title' => Yii::t('fe', 'Peringatan'), 
                        'text' => 'Terdapat kesalahan',
                        'message' => Yii::t('fe','Terjadi kesalahan').'!',
                    ]
                );
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionSaveKelayakanRekon() 
    {
        $request = Yii::$app->request;
        // i dont know why $this->_data_pasien is updated when hit this action

        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $data = $request->post('data');
        $posts = [];
        foreach ($data as $datum) {
            // return json_encode($datum['name']);
            if ($datum['name'] == '_csrf') continue;
            
            $name = explode('-', $datum['name']);
            $posts[$name[0]][$name[1]] = trim($datum['value']);
        }

        try {
            $response = $this->_restRanap->post('rekonsiliasi-obat/update-kelayakan-rekon?pegawai_id='.$id_pegawai, [
                'form_params' => $posts
            ]);
            $response = json_decode($response->getBody(), true);

            if ($response['metadata']['status'] == 200){
                // unset session
                $this->setDefaultCache($request->post('pend_id'), $request->post('admisi_id'));
                
                return DocoHelpers::response($response);
            }else{
                return DocoHelpers::responseTemplate(
                    500, 
                    'Error', 
                    [], 
                    [
                        'title' => Yii::t('fe', 'Peringatan'), 
                        'text' => 'Terdapat kesalahan',
                        'message' => Yii::t('fe','Terjadi kesalahan').'!',
                    ]
                );
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function setDefaultCache($pend_id, $admisi_id) {
        $cache = Yii::$app->cache;
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $cache->delete($this->_cacheRekon);

        $user_identity = $this->_user_identity;
        $kelPgw = $user_identity['kelompokpegawai_id'];

        // get list rekon obat
        $response = $this->_restRanap->get('allow/get-list-rekon-obat?pendaftaran_id=' . $pend_id . '&pasienadmisi_id=' . $admisi_id);
        $body = json_decode($response->getBody(), true);
        $listRekonObat = $body['response'];
        // return $listRekonObat;
        $list = [];

        $data_choice = [
            '0' => Yii::t('fe', 'Tidak'),
            '1' => Yii::t('fe', 'Ya'),
        ];

        $data_signa = [
            '3 x 1' => '3 x 1',
            '2 x 1' => '2 x 1',
            '2 butir sebelum makan' => '2 butir sebelum makan',
            '2 butir sebelum tidur' => '2 butir sebelum tidur',
            '1 x 1' => '1 x 1',
        ];

        foreach ($listRekonObat as $key => $each) {
            $is_lanjut = $each['is_lanjut'] === null ? '' : ($each['is_lanjut'] === true ? Yii::t('fe', 'Ya') : Yii::t('fe', 'Tidak'));
            $form_lanjut = Html::dropDownList(
                $each['rekonsiliasiobat_id'] . '-is_lanjut', 
                $each['is_lanjut'] === null ? '' : ($each['is_lanjut'] === false ? '0': '1'), 
                $data_choice,
                [
                    'class'=>'is_lanjut',
                    'data-rekon_id'=>$each['rekonsiliasiobat_id'],
                    'prompt'=>Yii::t('fe', '--Pilih--'),
                ]
            );

            $catatan = $each['catatan'];
            $form_catatan =  Html::textInput(
                $each['rekonsiliasiobat_id'] . '-catatan', 
                $each['catatan'],
                [
                    'style'=>'width:100px',
                    'data-rekon_id'=>$each['rekonsiliasiobat_id'],
                    'class'=> 'input-kelayakan'
                ]
            );

            $qty_layak = $each['qty_layak'];
            $form_qty_layak = '';
            if ($is_lanjut !== '') {
                $form_qty_layak =  Html::textInput(
                    $each['rekonsiliasiobat_id'] . '-qty_layak', 
                    $each['qty_layak'],
                    [
                        'style'=>'width:50px',
                        'data-rekon_id'=>$each['rekonsiliasiobat_id'],
                        'class'=>'docoNumberOnly input-kelayakan layak',
                    ]
                );
            }

            $qty_tidaklayak = $each['qty_tidaklayak'];
            $form_qty_tidaklayak = '';
            if ($is_lanjut !== '') {
                $form_qty_tidaklayak =  Html::textInput(
                    $each['rekonsiliasiobat_id'] . '-qty_tidaklayak', 
                    $each['qty_tidaklayak'],
                    [
                        'style'=>'width:50px',
                        'data-rekon_id'=>$each['rekonsiliasiobat_id'],
                        'class'=>'docoNumberOnly input-kelayakan tidaklayak'
                    ]
                );
            }

            $terapi = $each['terapi'];
            $form_terapi = '';
            if ($is_lanjut !== '') {
                $form_terapi =  Html::textInput(
                    $each['rekonsiliasiobat_id'] . '-terapi', 
                    $each['terapi'],
                    [
                        'style'=>'width:50px',
                        'data-rekon_id'=>$each['rekonsiliasiobat_id'],
                        // 'class'=> 'input-kelayakan'
                    ]
                );
            }

            $signaterapi = $each['signaterapi'] === null ? '' : $each['signaterapi'];
            $form_signaterapi = '';
            if ($is_lanjut !== '') {  
                $form_signaterapi = Html::dropDownList(
                    $each['rekonsiliasiobat_id'] . '-signaterapi', 
                    $each['signaterapi'] === null ? '' : $each['signaterapi'], 
                    $data_signa,
                    [
                        'data-rekon_id'=>$each['rekonsiliasiobat_id'],
                        'prompt'=>Yii::t('fe', '--Pilih--'),
                        'style'=>'width:70px',
                        // 'class' => 'input-kelayakan'
                    ]
                );
            }

            $rute_kelayakan = $each['rute_kelayakan'];
            $form_rute_kelayakan = '';
            if ($is_lanjut !== '') {  
                $form_rute_kelayakan =  Html::textInput(
                    $each['rekonsiliasiobat_id'] . '-rute_kelayakan', 
                    $each['rute_kelayakan'],
                    [
                        'style'=>'width:50px',
                        'data-rekon_id'=>$each['rekonsiliasiobat_id'],
                        // 'class'=> 'input-kelayakan'
                    ]
                );
            }
            $setCache = [
                // from hidden field
                'pendaftaran_id' => $each['pendaftaran_id'],
                'pasienadmisi_id' => $each['pasienadmisi_id'],
                'tgl_rekonsiliasi' => $each['tgl_rekonsiliasi'],
                // from head
                'obatalkes_id' => $each['obatalkes_id'],
                'is_alergi' => $each['is_alergi'],
                'obat_alergi' => $each['obat_alergi'],
                'is_hamil' => $each['is_hamil'],
                'sumber_informasi' => $each['sumber_informasi'],
                'informasi' => $each['informasi'],
                // from detail
                'obatalkes_id' => $each['obatalkes_id'],
                'nama_obat' => $each['nama_obat'],
                'qty_obat' => '<div class="qty-obat-' . $each['rekonsiliasiobat_id'] . '">' . $each['qty_obat'] . '</div>',
                'satuan_kecil' => $each['satuan_kecil'],
                'rute_obat' => $each['rute_obat'],
                'signa' => $each['signa'],
                'waktu_pemberian' => $each['waktu_pemberian'],
                /* MENGHILANGKAN VALIDASI PEGAWAI REKONS OBAT */
                // 'is_lanjut' => $is_lanjut == '' && $kelPgw == DocoConstants::KELOMPOK_MEDIS ? $form_lanjut : $is_lanjut,
                // 'catatan' => $is_lanjut == '' && $kelPgw == DocoConstants::KELOMPOK_MEDIS ? $form_catatan : $catatan,
                'is_lanjut' => $is_lanjut == '' ? $form_lanjut : $is_lanjut,
                'catatan' => $is_lanjut == '' ? $form_catatan : $catatan,
                'pemberi_keputusan' => $each['dokter_nama'] ? $each['dokter_nama'] . ' - ' . date('d-m-Y', strtotime($each['tgl_keputusan'])) : '',
                'dokter_id' => $each['dokter_id'],
                /* MENGHILANGKAN VALIDASI PEGAWAI REKONS OBAT */
                // 'qty_layak' => $each['tgl_kelayakan'] === null && $kelPgw == DocoConstants::KELOMPOK_KEFARMASIAN ? $form_qty_layak : $qty_layak,
                // 'qty_tidaklayak' => $each['tgl_kelayakan'] === null && $kelPgw == DocoConstants::KELOMPOK_KEFARMASIAN ? $form_qty_tidaklayak : $qty_tidaklayak,
                // 'terapi' => $each['tgl_kelayakan'] === null && $kelPgw == DocoConstants::KELOMPOK_KEFARMASIAN ? $form_terapi : $terapi,
                // 'signaterapi' => $each['tgl_kelayakan'] === null && $kelPgw == DocoConstants::KELOMPOK_KEFARMASIAN ? $form_signaterapi : $signaterapi,
                // 'rute_kelayakan' => $each['tgl_kelayakan'] === null && $kelPgw == DocoConstants::KELOMPOK_KEFARMASIAN ? $form_rute_kelayakan : $rute_kelayakan,
                'qty_layak' => $each['tgl_kelayakan'] === null ? $form_qty_layak : $qty_layak,
                'qty_tidaklayak' => $each['tgl_kelayakan'] === null ? $form_qty_tidaklayak : $qty_tidaklayak,
                'terapi' => $each['tgl_kelayakan'] === null ? $form_terapi : $terapi,
                'signaterapi' => $each['tgl_kelayakan'] === null ? $form_signaterapi : $signaterapi,
                'rute_kelayakan' => $each['tgl_kelayakan'] === null ? $form_rute_kelayakan : $rute_kelayakan,
                'apoteker_id' => $each['apoteker_id'],
                'tgl_kelayakan' => $each['tgl_kelayakan'],
                'review_kelayakan' => $each['apoteker_nama'] ? $each['apoteker_nama'] . ' - ' . ($each['tgl_kelayakan'] ? date('d-m-Y', strtotime($each['tgl_kelayakan'])) : '') : '',
                //hidden
                'rekonsiliasiobat_id' => $each['rekonsiliasiobat_id'],
            ];
            $list[] = $setCache;
        }

        $cache->set($this->_cacheRekon, $list);
    }

    public function actionAddNewObatRekon() {
        $model = new ObatAlkesForm();

        return $this->renderAjax('rekonsiliasi-obat/__modal_add_obat_rekon', get_defined_vars());
    }

    public function actionSaveObatRekon() {
        $request = Yii::$app->request;

        $model = new ObatAlkesForm();
        $model->load($request->post());

        try{
            if(!$model->validate()){
                $formName = substr(strrchr(get_class($model), "\\"), 1);
                $response = $model->getErrors();
                return DocoHelpers::response($response, 422, $formName);
            }
    
            $post = $request->post();
            $response = $this->_restRanap->post('rekonsiliasi-obat/save-obat-rekon',['form_params'=>$post]);
            $response = json_decode($response->getBody(), true);
    
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        } catch(Exception $e){
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        }
        
    }


    public function actionGetKode()
    {
        try{
            $response = $this->_restRanap->request('get', 'allow/get-last-kode-oa');
            $body = json_decode($response->getBody(), true);
            $nomor = $body['response'];
        } catch (RequestException $e) {
            $nomor = '';
        } catch (\Exception $e) {
            $nomor = '';
        }
        return $nomor;
    }

    public function actionExportPdfRekon()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $pendaftaran_id = $request->get('pendaftaran_id');
        $admisi_id = $request->get('admisi_id');
        $peg_id = Yii::$app->docoVars->user("id_pegawai");

        $path = Yii::getAlias("@download") . "/rekonsiliasi-obat.pdf";
        try {
            $response = $this->_restRanap->get('rekonsiliasi-obat/export-pdf?pendaftaran_id='.$pendaftaran_id.'&pasienadmisi_id='.$admisi_id.'&pegawai_id='.$peg_id,[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
            // return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            // var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            // var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
    
}