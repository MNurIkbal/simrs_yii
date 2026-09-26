<?php

/**
** @author yaya
** service : 
** - Apotek pemakaian-obat-alkes version 1
**/

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\models\PemakaianObatAlkesForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

class PemakaianObatAlkesController extends DocoController
{
    protected $_title = "Pemakaian Obat Alkes";
    protected $_module = '/apotek/pemakaian-obat-alkes/';
    protected $_restApotek; 
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek; 
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $model = new PemakaianObatAlkesForm;
        return $this->render('index', get_defined_vars());
    }

    public function actionGetListItem()
    {
        $request = Yii::$app->request;

        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $cacheObatAlkes = Yii::$app->cache->get("pemakaian-obat-{$instalasi_id}-{$ruangan_id}");
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $konvSatuan = Yii::$app->cache->get('konvert-satuan');
        if ($cacheObatAlkes !== false) {
            $no = $request->get('start',1);
            foreach ($cacheObatAlkes as $key => $value) {
                $no++;
                $satuanBesarId = $value['satuanbesar_id'];
                $satuanKecilId = $value['satuankecil_id'];
                $satuanBesar = isset($konvSatuan[$satuanBesarId][$satuanKecilId]) 
                                ? $konvSatuan[$satuanBesarId][$satuanKecilId] : 0;
                $hasilSatuanBesar = $satuanBesar != 0 ? round($value['permintaan']/$satuanBesar,2) : 0;
                $primaryKey = DocoHelpers::encrypt($key);
                $data[] = [
                    'rowNum' => $no,
                    'id' => $value['id'],
                    'nama_obat' => $value['nama_obat'],
                    'kode_obat' => $value['kode_obat'],
                    'qty_besar' => $value['jumlah_input'],
                    'satuan_besar' => $value['satuanbesar_nama'],
                    'qty_kecil' => $value['permintaan'],
                    'satuan_kecil' => $value['satuankecil_nama'],
                    'ket_obatpakai' => $value['ket_obatpakai'],
                    'aksi' => Html::button(
                        "<i class='fa fa-trash'></i>",[
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'action' => Url::to([$this->_module .'delete-list-item','id' => $primaryKey]),
                        ]
                    )
                ];
            }
            $result['data'] = $data;
            $result['recordsTotal'] = 1;
            $result['recordsFiltered'] = 1;
        }

        return DocoHelpers::response($result);
    }

    public function actionSetListItem()
    {
        $request = Yii::$app->request;
        Yii::error($request->post());
        $model = new PemakaianObatAlkesForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $setItem = [];
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");

        $model->load($request->post());
        $model->instalasi_id = $instalasi_id;
        $model->ruangan_id = $ruangan_id;
        if ($model->validate()) {
            $cacheObatAlkes = Yii::$app->cache->get("pemakaian-obat-{$instalasi_id}-{$ruangan_id}");
            $satuan = $model->satuan;
            $qty = $model->qty;
            $satuanKecil = $request->post('satuankecil_id');
            $satuanBesar = $request->post('satuanbesar_id');
            $nilai_konversi = $request->post('nilai_konversi');
            if ($cacheObatAlkes == false) {
                Yii::$app->cache->set("pemakaian-obat-{$instalasi_id}-{$ruangan_id}",[]);
                $cacheObatAlkes = [];
            }

            if (!isset($cacheObatAlkes[$model->obatalkes_id])) {
                $cacheObatAlkes[$model->obatalkes_id] = [];
            }

            $permintaan = isset($cacheObatAlkes[$model->obatalkes_id]['permintaan']) 
                            ? $cacheObatAlkes[$model->obatalkes_id]['permintaan'] : 0;
            $totalPermintaan = $permintaan + ($model->qty * $nilai_konversi);
            $hasilSatuanBesar = round($totalPermintaan/$nilai_konversi, 2);

            $setCache = [
                'id' => $request->post('id'),
                'text' => $request->post('text'),
                'kode_obat' => $request->post('kode_obat'),
                'nama_obat' => $request->post('nama_obat'),
                'satuankecil_id' => $request->post('satuankecil_id'),
                'satuankecil_nama' => $request->post('satuankecil_nama'),
                'satuanbesar_id' => $request->post('satuanbesar_id'),
                'satuanbesar_nama' => $model->satuan_text,
                'jumlah_input' => $hasilSatuanBesar,
                'permintaan' => $totalPermintaan,
                'ket_obatpakai'=> $model->ket_obatpakai,
                'harga_netto' => $request->post('harga_netto'),
                'tanggal_pemakaian' => $model->tanggal_pemakaian
            ];
            $cacheObatAlkes[$model->obatalkes_id] = $setCache;

            $cacheObatAlkes = Yii::$app->cache->set("pemakaian-obat-{$instalasi_id}-{$ruangan_id}",$cacheObatAlkes,3600);
            
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil di tambah'
            ];
            return DocoHelpers::response($response);
        } else {
            $response = $model->errors;
            return DocoHelpers::response($response,422,$formName);
        }
    }

    public function actionDeleteListItem($id = null)
    {
        $id = DocoHelpers::decrypt($id);
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $cacheObatAlkes = Yii::$app->cache->get("pemakaian-obat-{$instalasi_id}-{$ruangan_id}");
        if ($cacheObatAlkes !== false) {
            if (isset($cacheObatAlkes[$id])) {
                unset($cacheObatAlkes[$id]);
                Yii::$app->cache->set("pemakaian-obat-{$instalasi_id}-{$ruangan_id}",$cacheObatAlkes);
            }
        }
        $response['response'] = [
            'title' => 'Proses Berhasil !',
            'text' => 'Data berhasil dihapus'
        ];
        return DocoHelpers::response($response);
    }

    public function actionSearchObatAlkes()
    {
        $response = [];
        try {
            $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $request = Yii::$app->request;
            $result = $this->_restApotek->get('allow/list-stok-apotek',[
                            'query' => [
                                'instalasi_id' => $instalasi_id,
                                'ruangan_id' => $ruangan_id,
                                'term' => $request->get('term')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']['data']) ? $result['response']['data'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_kode'] . ' - ' . $value['obatalkes_namalain'],
                    'kode_obat' => $value['obatalkes_kode'],
                    'nama_obat' => $value['obatalkes_namalain'],
                    'stok' => $value['qty_tersedia'],
                    'satuankecil_id' => $value['satuankecil_id'],
                    'satuankecil_nama' => $value['satuankecil_nama'],
                    'satuanbesar_id' => $value['satuanbesar_id'],
                    'satuanbesar_nama' => $value['satuanbesar_nama'],
                    'satuan' => $value['satuan'],
                    'harga_netto' => $value['harganetto']
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

    public function actionSave()
    {
        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $cacheObatAlkes = Yii::$app->cache->get("pemakaian-obat-{$instalasi_id}-{$ruangan_id}");
        $response['response'] = [
            'text' => 'Obat alkes harus terisi, silahkan input obat alkes kembali',
            'title' => 'Proses Gagal !'
        ];
        $codeHttp = 422;
        if ($cacheObatAlkes) {
            try {
                $tanggal_pemakaian = Yii::$app->request->post("tanggal_pemakaian");
                $result = $this->_restApotek->post('pemakaian-obat-alkes/save',[
                    'form_params' => [
                        'instalasi_id' => $instalasi_id,
                        'tanggal_pemakaian' => date('Y-m-d H:i:s', strtotime($tanggal_pemakaian)),
                        'ruangan_id' => $ruangan_id,
                        'data' => json_encode($cacheObatAlkes)
                    ]
                ]);
                $result = json_decode($result->getBody(),true);
                if (ArrayHelper::getValue($result, 'metadata.status', 200) == 422) {
                    $response = ArrayHelper::getValue($result, 'response');
                    return DocoHelpers::responseTemplate(
                            422,
                            ArrayHelper::getValue($response, 'message'),
                            ArrayHelper::getValue($response, 'data'),
                            ['message' => 'Stok obat tidak mencukupi, silahkan melakukan penyesuain stok obat']
                        );
                }
                $response['response'] = $result;
                // $cacheSatuan = isset($result['response']) ? $result['response'] : [];
                // Yii::$app->cache->set('konvert-satuan',$cacheSatuan,3600);
                $codeHttp = 200;
                $response['response'] = [
                    'text' => 'Obat alkes berhasil disimpan',
                    'title' => 'Proses berhasil !',
                    'id' => DocoHelpers::encrypt($result['response'])
                ];
                Yii::$app->cache->set("pemakaian-obat-{$instalasi_id}-{$ruangan_id}",[]);
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
                $response['response']['text'] = 'Terjadi kesalahan pada sistem';
                $response['response']['message'] = $e->getMessage();
            }
        }
        return DocoHelpers::response($response,$codeHttp);
    }
    public function actionClearData()
    {
        try {
            $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $cacheObatAlkes = Yii::$app->cache->delete("pemakaian-obat-{$instalasi_id}-{$ruangan_id}");
            $response['response'] = [
                    'text' => 'Data ',
                    'title' => 'Proses berhasil !'
                ];
            return true;
        } catch (\Exception $e) {
            return DocoHelpers::response($e->getMessage());
        }
    }
    public function actionCetak($id)
    {
        $request = Yii::$app->request;
        try {
            $id = DocoHelpers::decrypt($id);
            $path = Yii::getAlias("@download") . "/cetak-pemakaian-obat-alkes.pdf";
            $response = $this->_restApotek->get('pemakaian-obat-alkes/cetak', [
                'save_to' => $path,
                'query' => ['id'=>$id]
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }
}