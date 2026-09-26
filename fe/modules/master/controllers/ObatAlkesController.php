<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-06-05 09:07:51
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-11-21 10:45:41
 */

namespace Doco\master\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoConstants;
use yii\helpers\Html;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use app\modules\master\models\ObatAlkesForm;
use app\components\DHtml;

class ObatAlkesController extends DocoController
{
    protected $_title = "Obat alkes";
    protected $_module = 'master/obat-alkes/';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    private function getAlloLoopAksi($listRequest = [])
    {
        try {
            $request = $this->_restMaster->get('allow/loop-aksi', ['form_params'=>$listRequest]);
            $body = json_decode($request->getBody(), true);
            $body = $body['response'];
        } catch (RequestException $e) {
             $body = [];
        }
        return $body;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $module = $this->_module;
        $listRequest = ['data_obat'=>'actionListJenisObat', 'data_ven'=>['actionGetLookup', 'ven']];
        $body = self::getAlloLoopAksi($listRequest);
        $data_obat = (isset($body['data_obat']) && count($body['data_obat']) > 0) ? ArrayHelper::map($body['data_obat'], 'jenisobatalkes_id', 'jenisobatalkes_nama') : [];
        $data_ven = (isset($body['data_ven']) && count($body['data_ven']) > 0) ? ArrayHelper::map($body['data_ven'], 'lookup_id', 'lookup_name') : [];
        return $this->render('index', get_defined_vars());
    }

    public function actionGetDataSupplier()
    {
        $request = Yii::$app->request;
        $q = $request->get('q');
        $data = [];
        
        $response = $this->guzzleExec($this->_restMaster, [
            'url' => 'allow/get-data-supplier',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'q' => $q
                ]
            ],
            'returnResponse' => true
        ]);
        
        if(!empty($response)){
            $response = isset($response['data']) ? $response['data'] : '';
            foreach ($response as $value) {
                $data[] = [
                    'id' => $value['supplier_id'].'_'.$value['supplier_nama'],
                    'text' => $value['supplier_nama']
                ];
            }

            return DocoHelpers::response([
                'result' => $data
            ]);
        }else{
            return $data;
        }
    }

    public function actionGetDataManufaktur()
    {
        $request = Yii::$app->request;
        $q = $request->get('q');
        $data = [];
        
        $response = $this->guzzleExec($this->_restMaster, [
            'url' => 'allow/get-data-manufaktur',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'q' => $q
                ]
            ],
            'returnResponse' => true
        ]);

        if(!empty($response)){
            $response = isset($response['data']) ? $response['data'] : '';
            foreach ($response as $value) {
                $data[] = [
                    'id' => $value['manufaktur_id'].'_'.$value['nama'],
                    'text' => $value['nama']
                ];
            }

            return DocoHelpers::response([
                'result' => $data
            ]);
        }else{
            return $data;
        }
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new ObatAlkesForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if($request->post()){
            $model->load($request->post());
            $post = $request->post($formName);
            try {
                if($model->validate()){
                    $response = $this->_restMaster->post('obat-alkes/create', ['form_params'=>$request->post()['ObatAlkesForm']]);
                    $body = json_decode($response->getBody(), true);
                    if(isset($body['response']['meta-status'])){
                        return DocoHelpers::response($body['response']['data'], $body['response']['meta-status'], 'ObatAlkesForm');
                    }
                    return DocoHelpers::response($body['response']);
                }else{
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            } catch (RequestException $e) {
                $result['response']['error'] = $e->getMessage();
                $result['response']['title'] = 'Error!';
                return DocoHelpers::response($result, 500);
            } catch (\Exception $e) {
                $result['error'] = $e->getMessage();
                $result['title'] = 'Error!';
                return DocoHelpers::response($result, 500);
            }

        }

        $title = $this->_title;
        $module = $this->_module;
        $homeUrl = '/master/obat-alkes';
        $disabled = false;
        $listRequest = [
            'data_obat'=>'actionListJenisObat',
            'data_ven'=>['actionGetLookup', 'ven'],
            'data_kategori'=>['actionGetLookup', 'obatalkes_kategori'],
            'data_groupinacbg' => 'actionListInaCbg',
            'data_kekuatan'=>['actionGetLookup', 'satuan_kekuatan'],
            'data_sk'=>'actionListSatuan',
            'data_ss'=>'actionListSatuan',
            'data_sb'=>'actionListSatuan',
            // 'data_satuan'=> 'actionGetDataSatuan',
            'data_penomoran'=>'actionGetLastKodeOa',
            'konfig_gudang'=>'actionGetKonfigGudang',
            'data_supplier'=>'actionGetListSupplier',
            'data_manufaktur'=>'actionGetListManufaktur',
            'data_zataktif' => 'actionGetListZatAktif',
            'data_ruteobat' => 'actionGetRuteObat',
            'data_atc_code' => 'actionGetAtcCode',
            'data_mims' => 'actionGetMims',
            'konfig' => 'actionGetKonfig',
        ];
        $body = self::getAlloLoopAksi($listRequest);
        $konfig_manufaktur = !empty($body['konfig']['is_mutiple_manufaktur']);
        $konfig_supplier = !empty($body['konfig']['is_multiple_supplier']);
        $satuan_kekuatan = (isset($body['data_kekuatan']) && count($body['data_kekuatan']) > 0) ? ArrayHelper::map($body['data_kekuatan'], 'lookup_id', 'lookup_name') : [];
        $jenis_oa = (isset($body['data_obat']) && count($body['data_obat']) > 0) ? ArrayHelper::map($body['data_obat'], 'jenisobatalkes_id', 'jenisobatalkes_nama') : [];
        $satuankecil = (isset($body['data_sk']) && count($body['data_sk']) > 0) ? ArrayHelper::map($body['data_sk'], 'satuanunit_id', 'satuanunit_nama') : [];
        $satuanbesar = (isset($body['data_sb']) && count($body['data_sb']) > 0) ? ArrayHelper::map($body['data_sb'], 'satuanunit_id', 'satuanunit_nama') : [];
        $satuansedang = (isset($body['data_ss']) && count($body['data_ss']) > 0) ? ArrayHelper::map($body['data_ss'], 'satuanunit_id', 'satuanunit_nama') : [];
        $kategorioa = (isset($body['data_kategori']) && count($body['data_kategori']) > 0) ? ArrayHelper::map($body['data_kategori'], 'lookup_id', 'lookup_name') : [];
        $groupinacbg = (isset($body['data_groupinacbg']) && count($body['data_groupinacbg']) > 0) ? ArrayHelper::map($body['data_groupinacbg'], 'groupinacbg_id', 'groupinacbg_nama') : [];
        $dataven = (isset($body['data_ven']) && count($body['data_ven']) > 0) ? ArrayHelper::map($body['data_ven'], 'lookup_id', 'lookup_name') : [];
        
        $dataSupplier = (isset($body['data_supplier']) && count($body['data_supplier']) > 0) ? ArrayHelper::map($body['data_supplier'], 'supplier_id', 'supplier_nama') : [];
        $dataSuppliers = (isset($body['data_supplier']) && count($body['data_supplier']) > 0) ? ArrayHelper::map($body['data_supplier'], 'supplier_id', 'supplier_nama') : [];
        $dataManufaktur = (isset($body['data_manufaktur']) && count($body['data_manufaktur']) > 0) ? ArrayHelper::map($body['data_manufaktur'], 'manufaktur_id', 'nama') : [];
        $dataManufakturs = (isset($body['data_manufaktur']) && count($body['data_manufaktur']) > 0) ? ArrayHelper::map($body['data_manufaktur'], 'manufaktur_id', 'nama') : [];
        $dataZatAktif = (isset($body['data_zataktif']) && count($body['data_zataktif']) > 0) ? ArrayHelper::map($body['data_zataktif'], 'zataktif_id', 'zataktif_nama') : [];
        $dataRuteObat = (isset($body['data_ruteobat']) && count($body['data_ruteobat']) > 0) ? ArrayHelper::map($body['data_ruteobat'], 'ruteobat_id', 'nama_rute') : [];
        $dataATCCode = (isset($body['data_atc_code']) && count($body['data_atc_code']) > 0) ? ArrayHelper::map($body['data_atc_code'], 'atccode_id', 'atccode') : [];
        $dataMIMS = (isset($body['data_mims']) && count($body['data_mims']) > 0) ? ArrayHelper::map($body['data_mims'][0], 'obatalkesmims_id', 'obatalkesmims_nama') : [];
        $dataSubMIMS = [];
        $masterMIMS = $body['data_mims'];
        $callbackSupplier = [];
        $callbackManufaktur = [];
        
        $nomor = !empty($body['data_penomoran']) ? $body['data_penomoran'] : 'A001';
        $model->obatalkes_kode = $nomor;
        $ppn_persen = !empty($body['konfig_gudang']['persenppn']) ? $body['konfig_gudang']['persenppn'] / 100 : 0;
        $persen_margin = !empty($body['konfig_gudang']['persenmargin']) ? $body['konfig_gudang']['persenmargin'] : 0;
        $model->harganetto = 0;
        $model->hargamaksimum = 0;
        $model->hargaminimum = 0;
        $model->hargaratarata = 0;
        $model->hargaterakhir = 0;
        $model->reorder = 1;
        $model->discount = 0;

        $kategoriobat = '';

        return $this->render('tambah', get_defined_vars());
    }

    public function actionDetail($id, $is_flag = 0){
        $id = DocoHelpers::decrypt($id);
        $homeUrl = ($is_flag == 1) ? '/gudang/inf-obat-alkes' : '/master/obat-alkes';
        $request = Yii::$app->request;
        $title = DHtml::getTitleMenu();
        $module = $this->_module;
        $model = new ObatAlkesForm;

        $kategoriobat = '-';
        if(!empty($id)){
            $kategoriobat = self::getKategoriObat($id);
        }
        $kategoriobat = '
        <div class="form-group highlight-addon">
            <label class="control-label col-sm-4">Kategori Obat : </label>
            <div class="col-sm-8">
                <div class="form-group">
                    <input type="text" class="form-control" name="ObatAlkesForm[kategoriobat]" value="'. $kategoriobat .'" readonly>
                </div>
            </div>
        </div>';

        if($request->post()){
            $model->load($request->post());
            $model->satuankecil_id = !empty($model->satuankecil_id) ? $model->satuankecil_id : $request->post('satuan_disable');
            if($model->validate()){
                return $this->guzzleExec($this->_restMaster, [
                    'url' => 'obat-alkes/edit',
                    'method' => 'put',
                    'payload' => [
                        'form_params' => $request->post()['ObatAlkesForm'],
                        'query' => [
                            'id' => $id
                        ]
                    ],
                    'returnResponse' => true
                ]);
            } else {
                return DocoHelpers::response($model->getErrors(), 422, 'ObatAlkesForm');
            }
        } else {
            $data_update = [];
            $dataSupplier = [];
            $oa_request = $this->guzzleExec($this->_restMaster, [
                'url' => 'obat-alkes/view',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'id' => $id
                    ]
                ]
            ]);

            $obatAlkes = ArrayHelper::getValue($oa_request, 'data', []);
            $defaultRuteobatId = ArrayHelper::getValue($oa_request, 'default_ruteobat_id', []);
            
            $data_update['ObatAlkesForm'] = $obatAlkes;
            $model->load($data_update);
            $model->ven = isset($obatAlkes['ven_id']) ? $obatAlkes['ven_id'] : '';
            $model->groupinacbg_id = isset($obatAlkes['groupinacbg_id']) ? $obatAlkes['groupinacbg_id'] : '';
            $model->on_ro = isset($obatAlkes['on_ro']) ? $obatAlkes['on_ro'] : 0;
            $model->on_po = isset($obatAlkes['on_po']) ? $obatAlkes['on_po'] : 0;
            $model->lead_time = isset($obatAlkes['lead_time']) ? $obatAlkes['lead_time'] : '';
            $model->avg_usage = isset($obatAlkes['avg_usage']) ? $obatAlkes['avg_usage'] : '';
            $model->min_order = isset($obatAlkes['min_order']) ? $obatAlkes['min_order'] : '';
            $model->max_order = isset($obatAlkes['max_order']) ? $obatAlkes['max_order'] : '';
            $model->hargaratarata = isset($obatAlkes['hargaratarata']) ? $obatAlkes['hargaratarata'] : '';
            $model->is_generik = isset($obatAlkes['is_generik']) ? ($obatAlkes['is_generik'] == false ? 0 : 1) : '';
            $model->is_oral = isset($obatAlkes['is_oral']) ? ($obatAlkes['is_oral'] == false ? 0 : 1) : '';
            $model->is_formularium = isset($obatAlkes['is_formularium']) ? ($obatAlkes['is_formularium'] == false ? 0 : 1) : '';
            $model->is_antibiotic = isset($obatAlkes['is_antibiotic']) ? ($obatAlkes['is_antibiotic'] == false ? 0 : 1) : '';
            $model->is_psycothropica = isset($obatAlkes['is_psycothropica']) ? ($obatAlkes['is_psycothropica'] == false ? 0 : 1) : '';
            $model->is_narcotic = isset($obatAlkes['is_narcotic']) ? ($obatAlkes['is_narcotic'] == false ? 0 : 1) : '';
            $model->ruteobat_id = isset($obatAlkes['ruteobat_id']) ? $obatAlkes['ruteobat_id'] : ($obatAlkes['is_oral'] ? $defaultRuteobatId : '');
            if(!empty($obatAlkes['supplier_ids'])) {
                $suppliers = json_decode($obatAlkes['supplier_ids'], true);
                foreach ($suppliers as $key => $value) {
                    $valId = $value['id'];
                    $valText = $value['text'];
                    $keySuppliers[] = $valId;
                    $callbackSupplier[] = [
                        'id' => $valId, 
                        'text' => $valText 
                    ];
                }
                $dataSuppliers = $keySuppliers;
            }else{
                $callbackSupplier = null;
            }
            if(!empty($obatAlkes['supplier_id'])){
                $dataSupplier = [$obatAlkes['supplier_id'] => $obatAlkes['supplier_nama']];
            }
            $model->kemasan_besar = isset($obatAlkes['kemasan_besar']) ? $obatAlkes['kemasan_besar'] : '';
            $model->kemasan_sedang = isset($obatAlkes['kemasan_sedang']) ? $obatAlkes['kemasan_sedang'] : '';
            $model->harga_beli = !empty($model->harga_beli) ? DocoHelpers::formatNumber($model->harga_beli) : 0;
            $model->harganetto = !empty($model->harganetto) ? DocoHelpers::formatNumber($model->harganetto) : 0;
            $model->hargamaksimum = !empty($model->hargamaksimum) ? DocoHelpers::formatNumber($model->hargamaksimum) : 0;
            $model->hargaminimum = !empty($model->hargaminimum) ? DocoHelpers::formatNumber($model->hargaminimum) : 0;
            $model->hargaratarata = !empty($model->hargaratarata) ? DocoHelpers::formatNumber($model->hargaratarata) : 0;
            $model->discount = !empty($model->discount) ? DocoHelpers::formatNumber($model->discount) : 0;
            $model->ppn_persen = !empty($model->ppn_persen) ? DocoHelpers::formatNumber($model->ppn_persen) : 0;
            $model->hargaterakhir = !empty($model->hargaterakhir) ? DocoHelpers::formatNumber($model->hargaterakhir) : 0;
            $model->reorder = isset($obatAlkes['reorder']) ? ($obatAlkes['reorder'] == false ? 0 : 1) : '';
            $model->is_consigment = isset($obatAlkes['is_consigment']) ? ($obatAlkes['is_consigment'] == false ? 0 : 1) : '';
            $model->is_produksi = isset($obatAlkes['is_produksi']) ? ($obatAlkes['is_produksi'] == false ? 0 : 1) : '';
            if(!empty($obatAlkes['manufacture_ids'])) {
                $manufakturs = json_decode($obatAlkes['manufacture_ids'], true);
                foreach ($manufakturs as $key => $value) {
                    $valId = $value['id'];
                    $valText = $value['text'];
                    $keyManufactures[] = $valId;
                    $callbackManufaktur[] = [
                        'id' => $valId, 
                        'text' => $valText 
                    ];
                }
                $dataManufakturs = $keyManufactures;
            }else{
                $callbackManufaktur = null;
            }
            if(!empty($obatAlkes['manufaktur_id'])){
                $dataManufaktur = [$obatAlkes['manufaktur_id'] => $obatAlkes['manufaktur_nama']];
            }
            $model->mims_id = !empty($obatAlkes['mims_id']) ? $obatAlkes['mims_id'] : 0;
            $model->obatalkesmims_id = !empty($obatAlkes['submims_id']) ? $obatAlkes['submims_id'] : '';

            $listRequest = [
                'data_obat'=>'actionListJenisObat',
                'data_ven'=>['actionGetLookup', 'ven'],
                'data_kategori'=>['actionGetLookup', 'obatalkes_kategori'],
                'data_kekuatan'=>['actionGetLookup', 'satuan_kekuatan'],
                'data_sk'=>['actionListSatuan', '0'],
                'data_ss'=>['actionListSatuan', '1'],
                'data_sb'=>['actionListSatuan', '2'],
                'data_penomoran'=>'actionGetLastKodeOa',
                'konfig_gudang'=>'actionGetKonfigGudang',
                'data_groupinacbg' => 'actionListInaCbg',
                'data_supplier'=>'actionGetListSupplier',
                'data_manufaktur'=>'actionGetListManufaktur',
                'data_zataktif' => 'actionGetListZatAktif',
                'data_ruteobat' => 'actionGetRuteObat',
                'data_atc_code' => 'actionGetAtcCode',
                'data_mims' => 'actionGetMims',
                'konfig' => 'actionGetKonfig',
            ];

            $body = self::getAlloLoopAksi($listRequest);
            $konfig_manufaktur = !empty($body['konfig']['is_mutiple_manufaktur']);
            $konfig_supplier = !empty($body['konfig']['is_multiple_supplier']);
            $satuan_kekuatan = (isset($body['data_kekuatan']) && count($body['data_kekuatan']) > 0) ? ArrayHelper::map($body['data_kekuatan'], 'lookup_id', 'lookup_name') : [];
            $jenis_oa = (isset($body['data_obat']) && count($body['data_obat']) > 0) ? ArrayHelper::map($body['data_obat'], 'jenisobatalkes_id', 'jenisobatalkes_nama') : [];
            $satuankecil = (isset($body['data_sk']) && count($body['data_sk']) > 0) ? ArrayHelper::map($body['data_sk'], 'satuanunit_id', 'satuanunit_nama') : [];
            $satuanbesar = (isset($body['data_sb']) && count($body['data_sb']) > 0) ? ArrayHelper::map($body['data_sb'], 'satuanunit_id', 'satuanunit_nama') : [];
            $satuansedang = (isset($body['data_ss']) && count($body['data_ss']) > 0) ? ArrayHelper::map($body['data_ss'], 'satuanunit_id', 'satuanunit_nama') : [];
            $kategorioa = (isset($body['data_kategori']) && count($body['data_kategori']) > 0) ? ArrayHelper::map($body['data_kategori'], 'lookup_id', 'lookup_name') : [];
            $dataven = (isset($body['data_ven']) && count($body['data_ven']) > 0) ? ArrayHelper::map($body['data_ven'], 'lookup_id', 'lookup_name') : [];
            $ppn_persen = !empty($body['konfig_gudang']['persenppn']) ? $body['konfig_gudang']['persenppn'] / 100 : 0;
            $dataSupplier = (isset($body['data_supplier']) && count($body['data_supplier']) > 0) ? ArrayHelper::map($body['data_supplier'], 'supplier_id', 'supplier_nama') : [];
            $dataSuppliers = (isset($body['data_supplier']) && count($body['data_supplier']) > 0) ? ArrayHelper::map($body['data_supplier'], 'supplier_id', 'supplier_nama') : [];
            $dataManufakturs = (isset($body['data_manufaktur']) && count($body['data_manufaktur']) > 0) ? ArrayHelper::map($body['data_manufaktur'], 'manufaktur_id', 'nama') : [];
            $dataManufaktur = (isset($body['data_manufaktur']) && count($body['data_manufaktur']) > 0) ? ArrayHelper::map($body['data_manufaktur'], 'manufaktur_id', 'nama') : [];
            $dataZatAktif = (isset($body['data_zataktif']) && count($body['data_zataktif']) > 0) ? ArrayHelper::map($body['data_zataktif'], 'zataktif_id', 'zataktif_nama') : [];
            $groupinacbg = (isset($body['data_groupinacbg']) && count($body['data_groupinacbg']) > 0) ? ArrayHelper::map($body['data_groupinacbg'], 'groupinacbg_id', 'groupinacbg_nama') : [];
            $dataRuteObat = (isset($body['data_ruteobat']) && count($body['data_ruteobat']) > 0) ? ArrayHelper::map($body['data_ruteobat'], 'ruteobat_id', 'nama_rute') : [];
            $dataATCCode = (isset($body['data_atc_code']) && count($body['data_atc_code']) > 0) ? ArrayHelper::map($body['data_atc_code'], 'atccode_id', 'atccode') : [];
            $dataMIMS = (isset($body['data_mims']) && count($body['data_mims']) > 0) ? ArrayHelper::map($body['data_mims'][0], 'obatalkesmims_id', 'obatalkesmims_nama') : [];
            $dataSubMIMS = (isset($body['data_mims']) && count($body['data_mims']) > 0) ? ArrayHelper::map($body['data_mims'][$model->mims_id], 'obatalkesmims_id', 'obatalkesmims_nama') : [];
            $masterMIMS = $body['data_mims'];
            $masterManufaktur = $body['data_manufaktur'];

            $cekSatuan = 1;
            $disabled = ($cekSatuan > 0) ? true : false;
            return $this->render('tambah', get_defined_vars());
        }
    }
    
    private function getKategoriObat($obatalkes_id)
    {
        $kategoriobat = $this->guzzleExec($this->_restMaster, [
            'url' => 'kategori-obat/get-kategori-by-obat-id',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'obat_id' => $obatalkes_id
                ]
            ]
        ]);
        
        // Format kategoriobat to display multiple categories separated by commas
        if (!empty($kategoriobat) && isset($kategoriobat['data'])) {
            $categories = $kategoriobat['data'];
            if (is_array($categories) && count($categories) > 0) {
                $categoryNames = [];
                foreach ($categories as $category) {
                    if (isset($category['restriction_obat_nama'])) {
                        $categoryNames[] = $category['restriction_obat_nama'];
                    }
                }
                $kategoriobat = implode(', ', $categoryNames);
            } else {
                $kategoriobat = '-';
            }
        } else {
            $kategoriobat = '-';
        }

        return $kategoriobat;
    }

    private function cekSatuan($obatalkes_id)
    {
        $response = $this->_restMaster->get('allow/cek-satuan', ['query'=> [
                'obatalkes_id' => $obatalkes_id,
            ]
        ]);

        $body = json_decode($response->getBody(), true);
        $body = $body['response'];

        return $body;
    }

    public function actionDelete($id){
        $request = Yii::$app->request;
        try {
            $id = DocoHelpers::decrypt($id);
            $response = $this->_restMaster->get('obat-alkes/delete', ['query'=>['id'=>$id]]);
            $body = json_decode($response->getBody(), true);
            $result = ['response'=>[
                            'title' => 'Proses Berhasil',
                        ]
                    ];
            return DocoHelpers::response(['message'=>'Data berhasil dihapus']);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result,500);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result,500);
        }
    }
    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restMaster->get('obat-alkes/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['obatalkes_id']);
                unset($value['obatalkes_id']);
                $value['primary'] = $primaryKey;
                $value['obatalkes_nama'] = $value['obatalkes_kode'] ." - ". $value['obatalkes_nama'];
                $value['rowNum'] = $no;
                $value['is_active'] = $this::switchStatus($value['is_active'], $primaryKey);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
    public function actionGetObat()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $data = [];
            try {
                $response = $this->_restMaster->request('POST', 'obat-alkes/data-obat',[
                                'form_params'=>['term'=>$_GET['q']['term']],
                            ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id'=>$value['obatalkes_nama'],'text'=>$value['obatalkes_nama']];
                }
                $total = count($body['response']);
                $data = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            } catch (RequestException $e) {
                $data = [];
            } catch (\Exception $e) {
                $data = [];
            }
            return DocoHelpers::response($data);
        }
    }
    public function actionGetSupplier()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $data = [];
            try {
                $response = $this->_restMaster->request('POST', 'obat-alkes/data-supplier',[
                                'form_params'=>['term'=>$_GET['q']['term']],
                            ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id'=>$value['supplier_id'],'text'=>$value['supplier_nama']];
                }
                $total = count($body['response']);
                $data = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            } catch (RequestException $e) {
                $data = [];
            } catch (\Exception $e) {
                $data = [];
            }
            return DocoHelpers::response($data);
        }
    }
    public function actionGetKode()
    {
        try{
            $response = $this->_restMaster->request('get', 'allow/get-last-kode-oa');
            $body = json_decode($response->getBody(), true);
            $nomor = $body['response'];
        } catch (RequestException $e) {
            $nomor = '';
        } catch (\Exception $e) {
            $nomor = '';
        }
        return $nomor;
    }

    public function actionCheckTransaction()
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($request->get('id'));
        try {
            $response = $this->_restMaster->get('obat-alkes/check-transaction?id='.$id,
                            ['form_params' => []
                        ]);

            $body = json_decode($response->getBody(), true);
            $resResponse = $body['response']['title'];
            $resmetadata = $body['metadata']['status'];
            return $resmetadata;

        } catch (\Exception $e) {
            return 422;
        }
    }

    public function actionChangeStatus($id, $status)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->put('obat-alkes/update?id='.$id, [
                'form_params' => ["is_active" => $status]
            ]);

            $resJson = json_decode($response->getBody(),false);
            if($resJson->metadata->status == "200") {
                $data = [
                    'title' => \Yii::t('fe', 'Proses berhasil')." !",
                    'text' => \Yii::t('fe', "Status berhasil diubah.")
                ];
            } else {
                $data = [
                    'title' => \Yii::t('fe', 'Proses gagal')." !",
                    'text' => \Yii::t('fe', $resJson->response->message)
                ];
            }
            return DocoHelpers::responseTemplate(
                $resJson->metadata->status,
                $resJson->response->message,
                [],
                $data
            );
        } catch (RequestException $e) {
            $data = [
                'title' => \Yii::t('fe', 'Proses gagal ')." !",
                'text' => \Yii::t('fe', "Status tidak berhasil dubah.")
            ];
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(),
                json_decode($e->getResponse()->getBody()->getContents())->message,
                [],
                $data
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionGetDataSelect2()
    {
        return $this->guzzleExec($this->_restMaster, [
            'url' => 'obat-alkes/get-data-select2',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }

    protected static function switchStatus($status, $id, $classname = 'change-status', $paramOn = 'Aktif', $paramOff = 'Tidak&nbsp;aktif')
    {
        $str = Html::checkbox('noname', "$status", [
            'class' => 'switch ' . $classname,
            'label' => false,
            'checked' => $status == 1,
            'data-id' => $id,
            'data-on-color' => 'success',
            'data-off-color' => 'danger',
            'data-size' => 'mini',
            'data-on-text' => \Yii::t('fe', $paramOn),
            'data-off-text' => \Yii::t('fe', $paramOff),
        ]);
        return $str;
    }
}
