<?php 
/**
 * @author : Budi
 * @description : Controller RM Informasi Pasien
 * @date : 16 Januari 2018 
 */

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\modules\rm\models\PasienForm;
use app\modules\rm\models\DokRekamMedisForm;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class InformasiController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/informasi/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Informasi Pasien');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionPasien()
    {
    	$title = $this->_title;
        $url_popup = $this->_module.'popup?tipe=pasien';
        
        return $this->render('pasien', get_defined_vars());
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
                // $primaryKey = DocoHelpers::encrypt($value['carabayar_id']);
                // unset($value['carabayar_id']);

                if($tipe == 'pasien') {
                    $value['tanggal_rekam_medik'] = '12-02-2018';
                    $value['no_rekam_medik'] = '234598';
                    $value['nama_pasien'] = 'Dadang Conelo';
                    $value['jenis_kelamin'] = 'Laki-Laki';
                    $value['alamat'] = 'Soreang';
                    $value['tanggal_lahir'] = '03-02-1992';
                    $value['umur'] = '26 Tahun';
                    $value['nama_ibu_kandung'] = 'Maemunah';
                    $value['aksi'] = Html::a(
                        '<i class="fa fa-pencil" aria-hidden="true"></i>', '/rm/informasi/edit?tipe=pasien', [
                            'class' => 'btn btn-dark-turquise btn-xs',
                            'data-popup' => "tooltip",
                            'title' => \Yii::t('fe', 'Edit'),
                        ]
                    );
                } elseif($tipe == 'kunjungan') {
                    $value['tanggal_pendaftaran'] = '12-02-2018';
                    $value['no_pendaftaran'] = 'RJ123456789';
                    $value['no_rekam_medik'] = '234598';
                    $value['nama_pasien'] = 'Dadang Conelo';
                    $value['jenis_kelamin'] = 'Laki-Laki';
                    $value['cara_bayar'] = 'BPJS';
                    $value['penjamin'] = 'BPJS';
                    $value['jenis_kasus_penyakit'] = 'Umum';
                    $value['instalasi'] = 'Rawat Jalan';
                    $value['ruangan'] = 'Klinik Umum';
                    $value['dokter_pj'] = 'Dr. Budi';
                } elseif($tipe == 'dokumen') {
                    $value['tglrekammedis'] = '12-02-2018';
                    $value['lokasirak_id'] = 'I';
                    $value['subrak_id'] = '02';
                    $value['no_rekam_medik'] = '234598';
                    $value['nama_pasien'] = 'Dadang Conelo';
                    $value['warnadokrm_id'] = 'Merah';
                    $value['aksi'] = Html::a(
                        '<i class="fa fa-pencil" aria-hidden="true"></i>', '/rm/informasi/edit?tipe=dokumen', [
                            'class' => 'btn btn-dark-turquise btn-xs',
                            'data-popup' => "tooltip",
                            'title' => \Yii::t('fe', 'Edit'),
                        ]
                    );
                } elseif($tipe == 'stok_opname') {
                    $value['tglstokopname'] = '12-02-2018';
                    $value['nostokopname'] = 'STK201809120001';
                    $value['totalharga'] = '12000';
                    $value['totalnetto'] = '12000';
                    $value['selisih'] = '0';
                } elseif($tipe == 'pemakaian_barang') {
                    // $primaryKey = DocoHelpers::encrypt($value['pemakaianbarang_id']);
                    $primaryKey = 1;
                    $value['tgl_pemakaianbarang'] = '12-02-2018';
                    $value['nama_pegawai'] = 'Mang Atang';
                    $value['barang_nama'] = 'Spidol';
                    $value['jumlah_pakai'] = '2';
                    $value['barang_satuan'] = 'Buah';
                    $value['aksi'] = Html::a(
                    '<i class="fa fa-trash" aria-hidden="true"></i>', '#', [
                            'class' => 'btn btn-danger btn-xs data-delete',
                            'action' => Url::home().$this->_module.'delete-pemakaian-barang?id='.$primaryKey,
                            'data-popup' => "tooltip",
                            'title' => \Yii::t('fe', 'Hapus'),
                        ]
                    );
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
    	if($tipe == 'pasien') {
            $title = Yii::t('fe', 'No Rekam Medik Pasien');
            $path = 'popup_rekam_medik';
        } elseif($tipe == 'dokter') {
            $title = Yii::t('fe', 'Dokter');
            $path = 'popup_dokter';
        } elseif($tipe == 'stok_opname') {
            $title = Yii::t('fe', 'Nomor Stok Opname');
            $path = 'popup_stok_opname';
        } elseif($tipe == 'pemakaian_barang') {
            $title = Yii::t('fe', 'Nama Barang');
            $path = 'popup_pemakaian_barang';
        }

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

                if($tipe == 'pasien') {
                    $value['no_rekam_medik'] = '234598';
                    $value['nama_pasien'] = 'Dadang Conelo';
                    $value['tanggal_lahir'] = '03-02-1992';

                    $value['aksi'] = Html::a('<i class="fa fa-lg fa-check-square-o"></i>', ['#'], [
                        'class' => 'btn btn-success btn-xs select-pasien',
                        'data-pasien' => 1,
                        'data-tooltip' => 'tooltip',
                        'title' => Yii::t('fe', 'Pilih'),
                    ]);
                } elseif($tipe == 'kunjungan') {
                    $value['nomorindukpegawai'] = '201701234';
                    $value['nama_pegawai'] = 'Rizki';
                    $value['jabatan_id'] = 'Dokter Kandungan';

                    $value['aksi'] = Html::a('<i class="fa fa-lg fa-check-square-o"></i>', ['#'], [
                        'class' => 'btn btn-success btn-xs select-dokter',
                        'data-dokter' => 1,
                        'data-tooltip' => 'tooltip',
                        'title' => Yii::t('fe', 'Pilih'),
                    ]);
                } elseif($tipe == 'stok_opname') {
                    $value['tglstokopname'] = '12-09-2017';
                    $value['nostokopname'] = 'R20709123';
                    $value['aksi'] = Html::a('<i class="fa fa-lg fa-check-square-o"></i>', ['#'], [
                        'class' => 'btn btn-success btn-xs select-nostokopname',
                        'data-nostokopname' => 1,
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

    public function actionEdit($id = NULL, $tipe = NULL)
    {
    	if($tipe == 'pasien') {
            $title = Yii::t('fe', 'Edit Pasien');
            $model = new PasienForm();
            $path = 'form_pasien';
        } elseif($tipe == 'dokumen') {
            $title = Yii::t('fe', 'Edit Dokumen Rekam Medik');
            $model = new DokRekamMedisForm();
            $path = 'form_dokumen';
        }

    	$url_popup = $this->_module.'popup?tipe=pasien';

    	return $this->render($path, get_defined_vars());
    }

    public function actionKunjunganPasien()
    {
        $title = Yii::t('fe', 'Informasi Kunjungan Pasien Rumah Sakit');
        $url_popup = $this->_module.'popup?tipe=dokter';
        
        return $this->render('kunjungan', get_defined_vars());
    }

    public function actionDokumenRm()
    {
        $title = Yii::t('fe', 'Informasi Dokumen Rekam Medik');
        $url_popup = $this->_module.'popup?tipe=pasien';
        
        return $this->render('dokumen', get_defined_vars());
    }

    public function actionStokOpname()
    {
        $title = Yii::t('fe', 'Informasi Stok Opname');
        $url_popup = $this->_module.'popup?tipe=stok_opname';
        
        return $this->render('stok_opname', get_defined_vars());
    }

    public function actionPemakaianBarang()
    {
        $title = Yii::t('fe', 'Informasi Pemakaian Barang');
        $url_popup = $this->_module.'popup?tipe=pemakaian_barang';
        
        return $this->render('pemakaian_barang', get_defined_vars());
    }
}