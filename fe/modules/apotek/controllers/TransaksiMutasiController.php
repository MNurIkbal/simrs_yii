<?php

/**
 * @author Randy Vianda Putra
 * @todo Transaksi Mutasi
 * @copyright 15 January 2018 aweutist
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\apotek\models\MutasiObatRuanganForm;
use app\modules\apotek\models\InfoPemesananObatAlkesForm;
use app\modules\apotek\models\DetailMutasiObatAlkesForm;

class TransaksiMutasiController extends DocoController
{

    protected $_title = "Mutasi Obat Alkes";
    protected $_module = '/apotek/transaksi-mutasi';
    protected $_restApotek;
    protected $allowAction = [
        '*'
    ];
    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    public function actionObatAlkes($nopemesanan = null)
    {
        try {
            $title = $this->_title ;
            $model = new MutasiObatRuanganForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $pemesanan = $this->_restApotek->get('transaksi-mutasi/get-pemesanan?nopemesanan='.$nopemesanan);
            $pemesanan = json_decode($pemesanan->getBody(), True);
            $data = $pemesanan['response']['pemesanan'];
            $tglpesan = isset($data['tglpemesanan']) ? date('Y-m-d H:i:s',strtotime($data['tglpemesanan'])) : date('Y-m-d H:i:s');
            $pesanobatalkes_id = $data['pesanobatalkes_id'];
            $request = Yii::$app->request;

            if ($request->post()) {
                $post = $request->post('MutasiObatRuanganForm');
                $model->attributes = $post;
                $model->pesanobatalkes_id = $data['pesanobatalkes_id'];
                $model->tglmutasioa = date('Y-m-d H:i:s');
                $model->ruanganasal_id = $data['ruangan_id'];
                $model->ruangantujuan_id = $data['ruangan_pemesan_id'];
                $model->pegawaimenyetujui_id = Yii::$app->docoVars->user('id_pegawai');
                $model->nopemesanan = $nopemesanan;
                $model->status_mutasi = 401;
                $model->totalharganettomutasi = 0;
                $model->totalhargajual = 0;

                $mDetail = new DetailMutasiObatAlkesForm;
                $detail = $request->post('DetailMutasiObat',[]);
                if(!$mDetail->check($detail)){
                    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'response'=>[
                            'data'=> DocoHelpers::parseError($mDetail->errors,'DetailMutasiObat')
                        ]
                    ];
                }

                if ($model->validate()) {
                    $response = $this->_restApotek->post('transaksi-mutasi/create', [
                        'form_params' => [
                            'MutasiObatRuangan'=>$model,
                            // 'DetailMutasiObat' =>(new DetailMutasiObatAlkesForm)->clearNull($detail)
                            'DetailMutasiObat' =>$detail
                        ],
                        'query' => ['pesanobatalkes_id' => $pesanobatalkes_id]
                    ]);

                    $result = json_decode($response->getBody(), true);
                    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    \Yii::$app->response->statusCode = $result['metadata']['status'];
                    if($result['metadata']['status'] == 422){
                        $result['response']['text'] = $result['response']['message'];
                    }
                    return $result;
                } else {
                    return DocoHelpers::response($model->errors,422,$formName);
                }
            } else {
                // $ruangan_id = is_int($ruangan_id) ? $ruangan_id : 0;
                $api = $this->_restApotek->get('transaksi-mutasi/generate-api?ruangan_id='.$ruangan_id);
                $api = json_decode($api->getBody(), true);
                $mutasiobatruangan_id = !empty($data['mutasiobatruangan_id']) ? $data['mutasiobatruangan_id'] : null;
                $curlMutasi = $this->_restApotek->get('transaksi-mutasi/get-mutasi-by-pesan',['query'=>['pesan_id'=>$pesanobatalkes_id]]);
                $mutasi = json_decode($curlMutasi->getBody(),true);
                if(isset($mutasi['response']['mutasi'])){
                    if(isset($mutasi['response']['mutasi']['tglmutasioa'])){
                        $model->tglmutasioa = $mutasi['response']['mutasi']['tglmutasioa'];
                    }
                    if(isset($mutasi['response']['mutasi']['pegawaimengetahui_id'])){
                        $model->pegawaimengetahui_id = $mutasi['response']['mutasi']['pegawaimengetahui_id'];
                    }
                }

                return $this->render('obat-alkes',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetDataDetail($nopemesanan = null)
    {
        try {
            $post = Yii::$app->request->post();
            $row = [];

            $response = $this->_restApotek->request('POST', 'transaksi-mutasi/get-pemesanan-detail', [
                'query' => ['nopemesanan' => $nopemesanan],
                'form_params' => $post
            ]);

            $body = json_decode($response->getBody(),TRUE);


            $no = Yii::$app->request->post('start', 0);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $nilai_konversi = ($value['nilai_konversi'] > 0) ? $value['nilai_konversi'] : 1;
                $value['nama_obat'] = $value['obatalkes_namalain'];
                $stok_pengirim = $value['stok_pengirim'] / $nilai_konversi;
                $value['stok_pengirim'] = DocoHelpers::formatNumber($stok_pengirim > 0 ? $stok_pengirim : 0);
                $stok_pemesan = $value['stok_pemesan'] / $nilai_konversi;
                $value['stok_pemesan'] = DocoHelpers::formatNumber($stok_pemesan > 0 ? $stok_pemesan : 0);
                $value['jumlah_pesan_form'] = Html::input('number',
                    "",
                    is_null($value['jumlah_mutasi']) ? $value['qty_besar'] : $value['jumlah_mutasi'] / ($nilai_konversi > 0 ? $nilai_konversi : 1),
                    [
                        'class'=>'form-control qty-kirim',
                        'data-konversi' => $nilai_konversi,
                        'data-id' => $value['pesanobatdetail_id'],
                    ]
                ).Html::input('hidden',
                    'DetailMutasiObat['.$value['pesanobatdetail_id'].']',
                    is_null($value['jumlah_mutasi']) ? $value['qty_besar'] * $nilai_konversi : $value['jumlah_mutasi'],
                    [
                        'class'=>'hidden-control-'.$value['pesanobatdetail_id'],
                    ]
                );

                $value['rowNum'] = $no;
                $row[$key] = $value;
            }

            $return = [
                'data' => $row,
                'draw' => Yii::$app->request->post('draw'),
                'recordsTotal' => $body['response']['count'],
                'recordsFiltered' => $body['response']['count']
            ];

            if($return) {
                return DocoHelpers::response($return);
            } else {
                return DocoHelpers::response($return);
            }
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
        catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionListPegawai()
    {
        try {
            $title = 'Pegawai';
            return $this->renderPartial('list-pegawai', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetDataPegawai()
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
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('transaksi-mutasi/list-pegawai?'.http_build_query($yiiRestfulParams), [
                'form_params' => []]);

            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-id' => $value['pegawai_id'],
                    'data-value' => $value['nama_pegawai'],
                    'title' => \Yii::t('fe', 'Pilih'),
                ]);

                $value['rowNum'] = $no;
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

    public function actionCetakPdf($id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/transaksi-mutasi.pdf";
        try {
            $response = $this->_restApotek->get('transaksi-mutasi/cetak-pdf?id=' . $id,[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::downloadPdf($response, $path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}
