<?php
/**
 * @author : Budi
 * @description : Controller Laporan
 * @date : 17 Januari 2018
 */

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\modules\rm\models\PasienForm;
use app\modules\rm\models\LaporanPemakaianBarangForm;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class LaporanController extends DocoController
{
	protected $_restRm;
	protected $_module = '/rm/laporan/';

	public function init()
    {
        parent::init();

        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionPemakaianBarang()
    {
    	$title = Yii::t('fe', 'Laporan Pemakaian Barang');
        $url_popup = $this->_module.'popup?tipe=pemakaian_barang';

        return $this->render('pemakaian_barang', get_defined_vars());
    }

    public function actionGetData($tipe = NULL)
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
            // $response = $this->_restMaster->get('cara-bayar/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            // $body = json_decode($response->getBody(), True);
            $body = [];
            $no = $request->get('start',1);
            // foreach ($body as $key => $value) {
                $no++;

                $row = [];
                // $primaryKey = DocoHelpers::encrypt($value['carabayar_id']);
                // unset($value['carabayar_id']);

                if($tipe == 'pemakaian_barang') {
                    $value['tgl_pemakaianbarang'] = '12-02-2018';
                    $value['nama_pegawai'] = 'Mang Atang';
                    $value['barang_nama'] = 'Spidol';
                    $value['jumlah_pakai'] = '2';
                    $value['barang_satuan'] = 'Buah';
                } elseif($tipe == 'sirs') {
                    $value['jenis_laporan'] = 'Foto Tanpa Bahan Kontras';
                    $value['jumlah'] = '664';
                } elseif($tipe == 'penyakit') {
                    $value['diagnosa'] = 'Demam Typhoid';
                    $value['jumlah'] = '200';
                } elseif($tipe == 'morbiditas') {
                    $value['no_dtd'] = '001';
                    $value['no_daftar'] = 'A00';
                    $value['golongan_sebab_penyakit'] = 'Kolera';

                    $data_pasien_hidup = [
                        'col_1' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_2' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_3' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_4' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_5' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_6' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_7' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_8' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_9' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                    ];

                    $data_pasien_keluar = [
                        'col_1_data_pasien_keluar' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_2_data_pasien_keluar' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                    ];

                    $data_pasien_keluar_hidup = [
                        'col_1_data_pasien_keluar_hidup' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                    ];

                    foreach ($data_pasien_hidup as $key => $value2) {
                        $value[$key] = $value2;
                    }

                    foreach ($data_pasien_keluar as $key3 => $value3) {
                        $value[$key3] = $value3;
                    }

                    foreach ($data_pasien_keluar_hidup as $key4 => $value4) {
                        $value[$key4] = $value4;
                    }
                } elseif($tipe == 'moralitas') {
                    $value['no_dtd'] = '001';
                    $value['no_daftar'] = 'A00';
                    $value['golongan_sebab_penyakit'] = 'Kolera';

                    $data_pasien_hidup = [
                        'col_1' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_2' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_3' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_4' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_5' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_6' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_7' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_8' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_9' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                    ];

                    $data_pasien_keluar = [
                        'col_1_data_pasien_keluar' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                        'col_2_data_pasien_keluar' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                    ];

                    $data_pasien_keluar_mati = [
                        'col_1_data_pasien_keluar_mati' => [
                            'L' => 0,
                            'P' => 1,
                        ],
                    ];

                    foreach ($data_pasien_hidup as $key => $value2) {
                        $value[$key] = $value2;
                    }

                    foreach ($data_pasien_keluar as $key3 => $value3) {
                        $value[$key3] = $value3;
                    }

                    foreach ($data_pasien_keluar_mati as $key4 => $value4) {
                        $value[$key4] = $value4;
                    }
                }

                $value['rowNum'] = 1;
                // $data[$key] = $value;
                $data[] = $value;
            // }

            $result['data'] = $data;
            // $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            // $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            $result['recordsTotal'] = 1;
            $result['recordsFiltered'] = 1;
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionPopup($tipe = NULL)
    {
    	if($tipe == 'pemakaian_barang') {
            $title = Yii::t('fe', 'Nama Barang');
            $path = 'popup_pemakaian_barang';
        }
        elseif($tipe == 'penyakit') {
            $title = Yii::t('fe', 'Diagnosa');
            $path = 'popup_diagnosa';
        }
        // elseif($tipe == 'stok_opname') {
        //     $title = Yii::t('fe', 'Nomor Stok Opname');
        //     $path = 'popup_stok_opname';
        // }

        return $this->renderPartial('popup/'.$path, get_defined_vars());
    }

    public function actionGetDataPopup($tipe = NULL)
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
            // $response = $this->_restMaster->get('cara-bayar/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            // $body = json_decode($response->getBody(), True);
            $body = [];
            $no = $request->get('start',1);
            // foreach ($body as $key => $value) {
                $no++;
                // $primaryKey = DocoHelpers::encrypt($value['carabayar_id']);
                // unset($value['carabayar_id']);

                if($tipe == 'pemakaian_barang') {
                    $value['barang_nama'] = 'Spidol';
                    $value['barang_kode'] = 'SP001';

                    $value['aksi'] = Html::a('<i class="fa fa-lg fa-check-square-o"></i>', ['#'], [
                        'class' => 'btn btn-success btn-xs select-barang',
                        'data-barang' => 1,
                        'data-tooltip' => 'tooltip',
                        'title' => Yii::t('fe', 'Pilih'),
                    ]);
                } elseif($tipe == 'penyakit') {
                    $value['kode_diagnosa'] = 'A01.0';
                    $value['nama_diagnosa'] = 'Typhoid fever';

                    $value['aksi'] = Html::a('<i class="fa fa-lg fa-check-square-o"></i>', ['#'], [
                        'class' => 'btn btn-success btn-xs select-diagnosa',
                        'data-diagnosa' => 1,
                        'data-tooltip' => 'tooltip',
                        'title' => Yii::t('fe', 'Pilih'),
                    ]);
                }

                $value['rowNum'] = 1;
                // $data[$key] = $value;
                $data[] = $value;
            // }

            $result['data'] = $data;
            // $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            // $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            $result['recordsTotal'] = 1;
            $result['recordsFiltered'] = 1;
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionSirs()
    {
        $title = Yii::t('fe', 'Laporan SIRS');

        return $this->render('sirs', get_defined_vars());
    }

    public function action10BesarPenyakit()
    {
        $title = Yii::t('fe', 'Laporan 10 Besar Penyakit');
        $url_popup = $this->_module.'popup?tipe=penyakit';

        return $this->render('penyakit', get_defined_vars());
    }
    public function actionMorbiditas()
    {
        $title = Yii::t('fe', 'Laporan Morbiditas');
        $url_popup = $this->_module.'popup?tipe=penyakit';

        return $this->render('morbiditas', get_defined_vars());
    }

    public function actionMoralitas()
    {
        $title = Yii::t('fe', 'Laporan Mortalitas');
        $url_popup = $this->_module.'popup?tipe=penyakit';

        return $this->render('moralitas', get_defined_vars());
    }
}
