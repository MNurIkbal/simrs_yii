<?php
namespace Doco\gudang\controllers;
/**
* @author yaya
*/

use Yii;
use yii\web\Response;

use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use Doco\gudang\components\access\BatalPemesananBarangAccess as BatalPemesanan;
// use yii\web\UploadedFile;

use app\modules\gudang\models\FormMutasiBarang;
// use Doco\gudang\models\UploadHasilForm;

class InfPemesananBarangController extends DocoController
{
    public $_title = "Informasi Pemesanan Barang Masuk";
    protected $_module = '/gudang/inf-pemesanan-barang/';
    protected $_restGudang;
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $module = $this->_module;
        try {
            $response = $this->_restGudang->get('informasi-pemesanan-barang-keluar/get-fillter',[]);
            $response = json_decode($response->getBody(),true);
            $instalasi = $response['response']['instalasi'];
            $ruangan = $response['response']['ruangan'];
            $nopemesanan = $response['response']['nopemesanan'];
            $status = $response['response']['statusdistribusi'];
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        } catch (\Exception $e) {
            Yii::info($e->getMessage());
        }

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter['advanced-filter']['ruangantujuan_id'] = $ruangan_id;
        if(isset($filter['advanced-filter']['instalasi_ruangan_pemesan'])) {
            $filter['advanced-filter']['ruanganpemesan_id'] = $filter['advanced-filter']['instalasi_ruangan_pemesan'];
        }
        $draw = $request->get('draw',1);
        $data = [];

        try {
            $response = $this->_restGudang->get('inf-pemesanan-barang/get-data', [
                'query' => http_build_query($filter)
            ]);
            $body = json_decode($response->getBody(), true);
            $list = $body["response"]["data"];
            $no = $request->get('start',1);

            foreach ($list as $index => $row) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($row['pesanbarang_id']);
                $value['primary'] = $primaryKey;
                $value["tgl_pesanbarang"] = isset($row["tgl_pesanbarang"]) ? date("d-M-Y", strtotime($row["tgl_pesanbarang"])) : "-";
                $value["tgl_mutasibarang"] = isset($row["tgl_mutasibarang"]) ? date("d-M-Y", strtotime($row["tgl_mutasibarang"])) : "-";
                $value["tglterima"] = isset($row["tglterima"]) ? date("d-M-Y", strtotime($row["tglterima"])) : "-";
                $value["no_pemesanan"] = $row["no_pemesanan"];
                $value["instalasi_pemesan"] = $row["instalasi_pemesan"];
                $value["ruangan_pemesan"] = $row["ruangan_pemesan"];
                $value['instalasi_ruangan_pemesan'] = $value['instalasi_pemesan'] . ' - ' . $value['ruangan_pemesan'];
                $value["status_pengiriman"] = $row["status_pengiriman"];
                $value["reference"] = isset($row["reference"]) ? $row["reference"] : "-";
                $value["rowNum"] = $no;
                $data[$index] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetRuangan($assign_id = "")
    {
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?instalasi_id='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restGudang->get('allow/get-ruangan'. $params);
            $body = json_decode($response->getBody(), true);

            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $value['ruangan_id'],
                    'name' => $value['ruangan_nama']
                ];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'messages' => $e->getMessage()
            ],422);
        }

    }

    public function actionLihat($id)
    {
        $module = $this->_module;
        $title = $this->_title;
        $data = (object) [];

        try {
            $response = $this->_restGudang->get('inf-pemesanan-barang/get-header', [
                'query' => array('id' => DocoHelpers::decrypt($id))
            ]);

            $response = json_decode($response->getBody(), true);

            foreach ($response['response'] as $key => $value) {
                if(in_array($key, ['tgl_pesanbarang', 'tgl_mutasibarang']) && !empty($value)) {
                    $response['response'][$key] = date('d M Y',strtotime($value));
                }

                if(empty($value)) {
                    $response['response'][$key] = '-';
                }
            }

            $data = (object) $response['response'];
            $primary = $id;
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
        } catch (\Exception $e) {
            Yii::info($e->getMessage());
        }

        $btn_toolbar = [
            'back',
            'pdf' =>[
                'type' => 'button',
                'attributes' => [
                    'data-target' => $module.'cetak-pemesanan?id='.$id,
                    'data-options' => 'link',
                    'target' => '_blank'
                ]
            ]
        ];
        
        if((new BatalPemesanan)->check($data->statuspesan, DocoConstants::BATAL_PESAN_TUJUAN)){
            $btn_toolbar['batal'] = [
                'type' => 'button',
                'title' => 'Batal Pesan',
                'icon' => 'fa fa-close',
                'attributes' => [
                    'data-options' => 'click',
                    'id' => 'batal-pemesanan',
                    'data-url' => $module.'batal-pemesanan?id='.$id
                ]
            ];
        }

        return $this->render('detail', get_defined_vars());
    }

    public function actionBatalPemesanan($id) {
        $id = DocoHelpers::decrypt($id);
        $url = 'inf-pemesanan-barang/batal-pemesanan';
        return (new BatalPemesanan)->batal($id, $url);
    }

    public function actionGetDetail($id) 
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
            $response = $this->_restGudang->get('inf-pemesanan-barang/get-detail?id=' . DocoHelpers::decrypt($id) . '&' . http_build_query($yiiRestfulParams), ['form_params' => []]);

            $body = json_decode($response->getBody(), true);
            $list = $body["response"]["data"];
            $currentPage = $body['response']['_meta']['currentPage'];
            $perPage = $body['response']['_meta']['perPage'];
            $rowNum = 1;

            if($currentPage > 1) {
                $rowNum = $perPage * ($currentPage - 1);
                $rowNum++;
            }

            foreach ($list as $index => $row) {
                $value["barang_nama"] = $row["barang_nama"];
                $value["qty_besar"] = $row["qty_besar"];
                $value["satuan_besar"] = $row["satuan_besar"] == null ? "-" : $row["satuan_besar"];
                $value["jumlah_mutasi"] = $row["jumlah_mutasi"];
                $value["satuan_kirim"] = $row["satuan_kirim"] == null ? "-" : $row["satuan_kirim"];
                $value["rowNum"] = $rowNum++;
                $data[$index] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionCetakPemesanan($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/gudang-informasi-pemesanan-barang.pdf";
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restGudang->get('inf-pemesanan-barang/cetak-pemesanan', [
                "query" => [
                    "id" => $id
                ],
                'save_to' => $path
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionMutasi($id)
    {
        $title = "Mutasi Barang";
        $module = $this->_module;

        $model = new FormMutasiBarang;
        $id = DocoHelpers::decrypt($id);
        $query = [
            "id" => $id
        ];

        try {
            $response = $this->_restGudang->get('inf-pemesanan-barang/get-data-pemesanan', [
                'query' => $query
            ]);
            $body = json_decode($response->getBody(), true);
            $header = $body["response"]["data_header"];
            $detail = $body["response"]["data_detail"];
        } catch (\Exception $e) {

        }

        return $this->render('mutasi', get_defined_vars());
    }

    public function actionSearchPegawai()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $term = $request->get('term');
            $result = $this->_restMaster->get('allow/list-pegawai',[
                'query' => [
                    'term' => $term
                ]
            ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }

        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    public function actionProsesMutasi($id)
    {
        $request = Yii::$app->request;
        $model = new FormMutasiBarang;
        $model->load($request->post());
        $model->mutasi_detail = $request->post('mutasi_detail');
        try {
            if ($model->validate()) {
                $response = $this->_restGudang->post("inf-pemesanan-barang/mutasi", [
                    "form_params" => $model->attributes
                ]);

                $body = json_decode($response->getBody(), true);
            }else{
                return DocoHelpers::response([
                    "response" => [
                        "data" => $model->errors,
                    ]
                ],422, "FormMutasiBarang");
            }
            return DocoHelpers::response($body,200);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result, 422);
        }
    }

    public function actionCetakMutasi($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/gudang-informasi-pemesanan-barang-masuk.pdf";
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restGudang->get('inf-pemesanan-barang/cetak-mutasi', [
                "query" => [
                    "id" => $id
                ],
                'save_to' => $path
            ]);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }

    }

}
?>
