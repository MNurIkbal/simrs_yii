<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi Formulir So
 * @copyright 17 January 2018 aweutist
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use Doco\apotek\models\InformasiForm;
use Doco\apotek\models\StokOpnameForm;

class InformasiFormulirController extends DocoController
{

    protected $_title = "Informasi Formulir Stok Opname";
    protected $_module = '/apotek/informasi-formulir';
    protected $_restApotek;
    protected $_restMaster;
    protected $_ruangan_id;
    protected $_ruangan_nama;
    protected $allowAction = ['*'];

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $this->_ruangan_nama = Yii::$app->docoVars->workspace("ruangan_name");
    }

    public function actionStokOpname()
    {
        $title = $this->_title;
        $model = new InformasiForm;
        $instalasi = [];
        $ruangan = [];
        $default_url = Url::home().(Yii::$app->controller->module->id."/".Yii::$app->controller->id);

        return $this->render('stok-opname', get_defined_vars());
    }

    public function actionDetail($id)
    {
        $request = Yii::$app->request;
        $id_decrypt = DocoHelpers::decrypt($id);
        $title = 'Stok Opname Obat';
        $subtitle = Yii::t('fe', 'Transaksi Stok Opname');
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $ruangan_nama = Yii::$app->docoVars->workspace("ruangan_name");
        $get = $this->guzzleExec($this->_restApotek, [
            'url' => 'inf-stok-opname/get-info-detail',
            'payload' => [
                'query' => [
                    'id' => $id_decrypt,
                ]
            ]
        ]);
        $data = $get['data'];
        $default_url = Url::home().("apotek/informasi-stok-opname/inf-stok-formulir-opname");
        $response = $this->guzzleExec($this->_restApotek, [
            'url' => 'inf-stok-opname/data-formulir-detail',
            'payload' => [
                'query' => [
                    'id' => $id_decrypt,
                ]
            ]
        ]);
        
        $model = new StokOpnameForm;
        $get_konfig = $this->_restApotek->get('allow/konfig-farmasi');
        $data_konfig = json_decode($get_konfig->getBody(), true);
        $filling_rule = ArrayHelper::getValue($data_konfig,'response.is_fulfilledso');
        $disabledfilling_rule = ArrayHelper::getValue($data_konfig,'response.is_disabledfulfilled_so');
        $is_fulfilled = $filling_rule;
        $is_disabledfulfilled = $disabledfilling_rule;
        $model->detail = isset($response['data']) ? $response['data'] : [];
        
        return $this->render('detail', get_defined_vars());
    }

    public function actionSave($id)
    {
        $model = new StokOpnameForm;
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $post = $request->post('StokOpnameForm');
        $model->attributes = $post;
        $model->formulirstokopname_id = $id;
        $model->ruangan_id = $request->get('ruangan_id', Yii::$app->docoVars->workspace("ruangan_id"));
        $inputanSO = $request->post('inputan_so', []);
        $inputHasilSO = $obatBaru = [];
        if(count($inputanSO)) {
            foreach ($inputanSO as $key => $value) {
                $value = json_decode($value, true);
                if(isset($value['is_newso']) && $value['is_newso']) {
                    array_push($obatBaru, $value);
                } else {
                    $key = !isset($value['stokopnamedetail_id']) && !empty($value['stokopnamedetail_id']) ? $value['stokopnamedetail_id'] : $value['formstokopname_id'];
                    $inputHasilSO[$key] = [
                        'formstokopname_id' => ArrayHelper::getValue($value, 'formstokopname_id'),
                        'stokopnamedetail_id' => ArrayHelper::getValue($value, 'stokopnamedetail_id'),
                        'stok_fisik' => ArrayHelper::getValue($value, 'stok_fisik', ''),
                        'revisi_stok' => ArrayHelper::getValue($value, 'revisi_stok', '')
                    ];
                }
            }
        }

        $model->inputan_so = $inputHasilSO;
        $model->list_obat_baru = $obatBaru;
        if ($model->validate()) {
            return $this->guzzleExec($this->_restApotek, [
                'url' => 'inf-stok-opname/save',
                'method' => 'POST', 
                'payload' => [
                    'form_params' => $model->attributes
                ],
                'returnResponse' => true
            ]);
        } else {
            $response = $model->errors;
        }
        return DocoHelpers::response($response, 422, 'StokOpnameForm');
    }
    
    public function actionGetDataStok()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = $this->_ruangan_id;
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('inf-stok-opname/get-data-stok?ruangan_id='. $this->_ruangan_id .'&'. http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['formulirstokopname_id']);
                unset($value['formulirstokopname_id']);

                $value['primary'] = $primaryKey;
                $value['tglformulir'] = date('d-M-Y', strtotime($value['tglformulir']));
                $value['periode_stok'] = date('d-M-Y', strtotime($value['periode_awal'])) ." / ". date('d-M-Y', strtotime($value['periode_akhir']));
                $value['rowNum'] = $no;
                $value['totalharga'] = DocoHelpers::formatNumber($value['totalharga']);
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch(RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        } catch(\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    // $id = formulirstokopname_id
    public function actionGetDataDetail($type = 1)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $id = DocoHelpers::decrypt($request->get('id'));

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('inf-stok-opname/data-formulir-detail', ['query' => ['id'=>$id]]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            $is_formulir = $body['response']['is_formulir'];

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['formstokopname_id']);

                $value['rakobat_nama'] = !is_null($value['rakobat_nama']) ? $value['rakobat_nama'] : 'Tanpa Rak';
                $value['laci'] = !is_null($value['laci']) ? $value['laci'] : 'Tanpa Rak';
                $value['obatalkes_kode'] = ArrayHelper::getValue($value, 'obatalkes_kode', '-');
                $value['obatalkes_namalain'] = is_null($value['obatalkes_namalain']) ? $value['obatalkes_nama'] : $value['obatalkes_namalain'];
                $value['uom'] = ArrayHelper::getValue($value, 'uom', '-');
                $value['satuanunit_nama'] = ArrayHelper::getValue($value, 'satuanunit_nama', '-');
                $value['stok_sistem'] = ArrayHelper::getValue($value, 'stok_sistem', 0);
                $value['stok_in'] = isset($value['stok_in']) ? $value['stok_in'] : 0;
                $value['stok_out'] = isset($value['stok_out']) ? $value['stok_out'] : 0;
                $value['stok_saatini'] = ArrayHelper::getValue($value, 'stok_saatini', 0);
                $value['stoksistem'] = DocoHelpers::formatNumber($value['stok_sistem']);
                
                if($type == 1){
                    $value['stok_fisik'] = Html::textInput('stok_fisik_'. $primaryKey, null, ['class' => 'form-control input-xs docoNumberOnly stok_fisik_edit', 'disabled'=>'disabled']);
                    $value['kondisi'] = Html::textInput('kondisi_'. $primaryKey, null, ['class' => 'form-control input-xs', 'disabled'=>'disabled']);
                }else{
                    $value['stok_so'] = isset($value['stok_sistem']) ? $value['stok_sistem'] : 0;
                    $value['stok_fisik'] = $is_formulir ? '' : $value['stok_fisik'];
                    $value['stok_revisi'] = $is_formulir ? '' : $value['stok_revisi'];
                    $value['selisih'] = Html::tag('p', Html::encode(''), ['class' => 'selisih-stok']);
                    $value['selisih_revisi'] = Html::tag('p', Html::encode(''), ['class' => 'selisih-revisi']);
                    
                    $value['stokfisik'] = Html::textInput('listdata['.$value['formstokopname_id'].']["stok_fisik"]', $value['stok_fisik'], [
                        'class' => 'form-control input-xs doco-decimal stok_fisik_edit disabled',
                        'tabindex'=>$no,
                        'data-key'=> $value['formstokopname_id'],
                        'data-stoksistem'=>$value['stok_saatini'],
                        'disabled' => !$is_formulir,
                        'onkeypress' => "return event.charCode >= 48 && event.charCode <= 57"
                    ]);

                    $value['stokrevisi'] = Html::textInput('listdata['.$value['formstokopname_id'].']["stok_revisi"]', $value['stok_revisi'], [
                        'class' => 'form-control input-xs doco-decimal stok_revisi_edit',
                        'tabindex'=>$no,
                        'data-key'=> $value['formstokopname_id'],
                        'data-stoksistem'=>$value['stok_saatini'],
                        'disabled' => $is_formulir,
                        'onkeypress' => "return event.charCode >= 48 && event.charCode <= 57"
                    ]);
                }
                
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }
            
            $result['data'] = $data;
            $result['draw'] = $request->post('draw');
            $result['recordsTotal'] = $body['response']['count'];
            $result['recordsFiltered'] = $body['response']['count'];
            
            return $result;
        } catch(RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        } catch(\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    private function getListData()
    {
        try {
            $request = Yii::$app->request;
            $get = $this->_restApotek->get('inf-stok-opname/get-list-data');
            $body = json_decode($get->getBody(), true);
            $response = $body['response'];

            $data_kondisiobat = $response['data_kondisiobat'] ? $response['data_kondisiobat'] : [];
            $data_jenisstokopname = $response['data_jenisstokopname'] ? $response['data_jenisstokopname'] : [];

            return [
                'data_kondisiobat' => $data_kondisiobat,
                'data_jenisstokopname' => $data_jenisstokopname,
            ];
        } catch(RequestException $e){
            return [
                'data_kondisiobat' => [],
                'data_jenisstokopname' => [],
            ];
        } catch(\Exception $e){
            return [
                'data_kondisiobat' => [],
                'data_jenisstokopname' => [],
            ];
        }
    }

    private function getListRak()
    {
        try{
            $request = Yii::$app->request;
            $get = $this->_restApotek->get('allow/get-rakobat');
            $body = json_decode($get->getbody(), true);
            $response = $body['response'];

            return $response;
        } catch(RequestException $e){
            return [];
        } catch(\Exception $e){
            return [];
        }
    }

    // $id = formulirstokopname_id
    private function prepareSimpanStokOpname($id, $data, $list_data)
    {
        try {
            if (!$data){
                return ['status' => 500, 'message' => Yii::t('fe', 'Data kosong')];
            }

            $modelStokOpname = new StokOpnameForm;
            $modelStokOpname->load($data);
            $modelStokOpname['formulirstokopname_id'] = (int)$data['StokOpnameForm']['formulirstokopname_id'];

            // check validasi stok opname
            if (!$modelStokOpname->validate()){
                return ['status' => 422, 'message' => $modelStokOpname->getErrors()];
            }else{
                //data prepare
                $id = DocoHelpers::decrypt($id);
                $is_stokawal = FALSE;

                // define data SO
                $data_stokopname = $data['StokOpnameForm'];

                // define data detail formulirstokopname
                $response = $this->_restApotek->get('inf-stok-opname/get-data-formulir?id='. $id);
                $body = json_decode($response->getBody(), true);
                $data_formulirstokopname = $body['response']['data'];

                // assign data StokOpname
                $data_stokopname_insert = [];
                $data_stokopname_insert['ruangan_id'] = $data_formulirstokopname['ruangan_id'];
                $data_stokopname_insert['formulirstokopname_id'] = DocoHelpers::decrypt($data_stokopname['formulirstokopname_id']);
                $data_stokopname_insert['tglstokopname'] = date('Y-m-d H:i:s');
                $data_stokopname_insert['jenisstokopname'] = $data_stokopname['jenisstokopname'];
                $data_stokopname_insert['totalharga_fisik'] = $data_stokopname['totalharga_fisik'];
                $data_stokopname_insert['totalharga_sistem'] = $data_stokopname['totalharga_sistem'];
                $data_stokopname_insert['totalharga_sistem'] = $data_stokopname['totalharga_sistem'];
                    // define stok awal / penyesuaian
                $indicator_jenisso = ArrayHelper::map($list_data['data_jenisstokopname'], 'lookup_name', 'lookup_kode');
                if ($indicator_jenisso[$data_stokopname['jenisstokopname']] == DocoConstants::LU_KD_JSO_SA){
                    $data_stokopname_insert['is_stokawal'] = TRUE;
                    $is_stokawal = TRUE;
                }

                // define data detail formulirstokopname
                $response = $this->_restApotek->get('inf-stok-opname/data-formulir-detail', ['query' => ['id'=>$id]]);
                $body = json_decode($response->getBody(), true);
                $data_detailformulirstokopname = $body['response']['data'];

                if ((!empty($data_stokopname)) && (!empty($data_detailformulirstokopname))){
                    // clear data, only get edited detail formulirstokopname
                    unset($data['StokOpnameForm']);
                    if (isset($data['_csrf'])){
                        unset($data['_csrf']);
                    }

                    // define data edited detail formulirstokopname
                    $data_editdetail = $data;

                    // define variable tambahan untuk find id stok di backend
                    $data_obatalkes = [];
                    $ruangan_id = $data_formulirstokopname['ruangan_id'];

                    // assign / compare detail formulirstokopname ~ edited detail formulirstokopname
                    $data_detailstokopname_insert = [];
                    foreach ($data_detailformulirstokopname as $key => $value) {
                        $data_detailstokopname_temp = [];
                        $id_formstokopname = DocoHelpers::encrypt($value['formstokopname_id']);

                        $data_detailstokopname_temp['formstokopname_id'] = $value['formstokopname_id'];
                        $data_detailstokopname_temp['stokopname_id'] = "";
                        $data_detailstokopname_temp['obatalkes_id'] = $value['obatalkes_id'];
                        $data_detailstokopname_temp['volume_fisik'] = (isset($data_editdetail['stok_fisik_'. $id_formstokopname])) ? $data_editdetail['stok_fisik_'. $id_formstokopname] : $value['stok_sistem'];
                        $data_detailstokopname_temp['volume_sistem'] = $value['stok_sistem'];
                        $data_detailstokopname_temp['hargasatuan'] = $value['harganetto'];
                        $data_detailstokopname_temp['jumlahharga'] = $data_detailstokopname_temp['volume_fisik'] * $value['harganetto'];
                        $data_detailstokopname_temp['tglkadaluarsa'] = $value['tglkadaluarsa'];
                        $data_detailstokopname_temp['kondisibarang'] = (isset($data_editdetail['kondisi_'. $id_formstokopname])) ? $data_editdetail['kondisi_'. $id_formstokopname] : "";
                        $data_detailstokopname_temp['jmlselisihstok'] = abs($data_detailstokopname_temp['volume_fisik'] - $data_detailstokopname_temp['volume_sistem']);
                        $data_detailstokopname_temp['stokobatalkes_id'] = $value['stokobatalkes_id'];
                        array_push($data_detailstokopname_insert, $data_detailstokopname_temp);

                        // insert list obatalkes_id
                        $data_obatalkes_temp = [];
                        $data_obatalkes_temp['stokobatalkes_id'] = $value['stokobatalkes_id'];
                        $data_obatalkes_temp['stok_fisik'] = $data_detailstokopname_temp['volume_fisik'];
                        $data_obatalkes_temp['stok_sistem'] = $value['stok_sistem'];
                        $data_obatalkes_temp['nobatch'] = isset($value['no_batch']) ? $value['no_batch]'] : null;
                        $data_obatalkes_temp['harganetto'] = $value['harganetto'];
                        array_push($data_obatalkes, $data_obatalkes_temp);
                    }

                    $data_send = [
                        'data_stokopname' => $data_stokopname_insert,
                        'data_detailstokopname' => $data_detailstokopname_insert,
                        'data_obatalkes' => $data_obatalkes,
                        'ruangan_id' => $ruangan_id,
                        'is_stokawal' => $is_stokawal,
                        'formulirstokopname_id' => $id,
                    ];
                    return $data_send;
                    $response = $this->_restApotek->post('inf-stok-opname/create-stok-opname', [
                        'form_params' => $data_send
                    ]);
                    $response = json_decode($response->getBody(), true);

                    if ($response['metadata']['status'] == 200){
                        return ['status' => 200, 'message' => 'OK'];
                    }else{
                        return [
                            'status' => 500,
                            'message' => @$response['response']['message'],
                        ];
                    }
                }
            }
        } catch (RequestException $e) {
            // var_dump(json_decode($e->getResponse()->getBody(), true));die;
            return [
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }

    // $id = formulirstokopname_id
    public function actionView()
    {
        try{
            $request = Yii::$app->request;
            $title = Yii::t('fe', 'Informasi Formulir stok opname');
            $subtitle = Yii::t('fe', 'Lihat Formulir Stok Opname');
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $ruangan_nama = Yii::$app->docoVars->workspace("ruangan_name");
            $modelStokOpname = new StokOpnameForm;
            $id = $request->get('id');
            $id_decrypt = DocoHelpers::decrypt($id);
            $default_url = Url::home().("apotek/informasi-stok-opname/inf-stok-formulir-opname");

            $get = $this->_restApotek->get('inf-stok-opname/get-info-detail?id='. $id_decrypt);
            $body = json_decode($get->getBody(), true);
            $response = $body['response'];
            $data = $response['data'];

            return $this->render('view', get_defined_vars());
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restApotek->delete('inf-stok-opname/delete-formulir?id=' . $id);
            $response = json_decode($response->getBody(), true);

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(),
                json_decode($e->getResponse()->getBody()->getContents())->message,
                []
            );
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionGetStokObat() {
        $request = Yii::$app->request;
        $ruanganId = $request->get('ruangan_id', Yii::$app->docoVars->workspace("ruangan_id"));
        $response = $this->guzzleExec($this->_restMaster, [
            'url' => 'obat-alkes/get-stok',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'obatalkes_id' => $request->get('obatalkes_id', null),
                    'ruangan_id' => $ruanganId
                ],
            ],
            'returnResponse' => true    
        ]);
        return $response;
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;

        $id = $request->get('id');
        $id_decrypt = DocoHelpers::decrypt($id);
        $nama_ruangan = $this->_ruangan_nama;
        $path = Yii::getAlias("@download") . "/formulirso-{$nama_ruangan}-{$id}.pdf";
        try {
            $response = $this->_restApotek->get('inf-stok-opname/pdf-formulir-stok-opname?id='. $id_decrypt, [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
    public function actionCetakTransaksi()
    {
        $request = Yii::$app->request;

        $id = $request->get('id');
        $id_decrypt = DocoHelpers::decrypt($id);
        $nama_ruangan = $this->_ruangan_nama;

        $path = Yii::getAlias("@download") . "/transaksi-so-{$nama_ruangan}-{$id}.pdf";
        try {
            $response = $this->_restApotek->get('inf-stok-opname/pdf-transaksi-stok-opname?id='. $id_decrypt, [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

}
