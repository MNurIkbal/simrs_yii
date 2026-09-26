<?php

/**
 * @Author: rizal
 * @Description:
 */

namespace Doco\gizi\controllers;

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


class InfPasienRanapController extends DocoController
{
    protected $_title = "Informasi pasien rawat inap";
    protected $_controller = '/ranap/inf-pasien-ranap';
    protected $_restGizi;
    protected $_pegawai_id;

    public function init()
    {
        
        parent::init();
        $this->_restGizi = Yii::$app->docoRest->gizi;
        $this->_pegawai_id = Yii::$app->docoVars->user('id_pegawai');
    }

    public function behaviors()
    {
      $behaviors = parent::behaviors();
      unset($behaviors['access']);
      unset($behaviors['verbs']);
      return $behaviors;
    }

    public function actionIndex()
    {
        try {
            $this->_title = "Informasi pasien rawat inap";
            $response = $this->_restGizi->get('allow/get-bundle-data');
            $resResponseBody = json_decode($response->getBody(), True)['response']['master'];
            $lookupResponseBody = json_decode($response->getBody(), True)['response']['lookup'];
            $lookupPerResponseBody = json_decode($response->getBody(), True)['response']['lookupKeperawatan'];
            $body = json_decode($response->getBody(), TRUE);

            $listDokter = isset($resResponseBody['dokter']) ? $resResponseBody['dokter'] : [];
            $listDokter = ArrayHelper::map($listDokter, 'pegawai_id', 'nama_pegawai');
            $listCaraBayar = isset($resResponseBody['carabayar']) ? $resResponseBody['carabayar'] : [];
            $listCaraBayar = ArrayHelper::map($listCaraBayar, 'carabayar_id', 'carabayar_nama');
            $listPenjamin = isset($resResponseBody['penjamin']) ? $resResponseBody['penjamin'] : [];
            $listPenjamin = ArrayHelper::map($listPenjamin, 'penjamin_id', 'penjamin_nama');
            $listRuangan = isset($resResponseBody['ruangan']) ? $resResponseBody['ruangan'] : [];
            $listRuangan = ArrayHelper::map($listRuangan, 'ruangan_id', 'ruangan_nama');
            $listKamar = isset($resResponseBody['kamar']) ? $resResponseBody['kamar'] : [];
            $listKamar = ArrayHelper::map($listKamar, 'kamarruangan_id', 'kamarruangan_nokamar');
            $listKelasPelayanan = isset($resResponseBody['kelaspelayanan']) ? $resResponseBody['kelaspelayanan'] : [];
            $listKelasPelayanan = ArrayHelper::map($listKelasPelayanan, 'kelaspelayanan_id', 'kelaspelayanan_nama');
            $listStatusRanap = isset($lookupResponseBody['status_ranap']) ? $lookupResponseBody['status_ranap'] : [];
            $listStatusRanap = ArrayHelper::map($listStatusRanap, 'lookup_id', 'lookup_name');
            $listStatusAsesmen = isset($lookupPerResponseBody['status_asmen_gizi']) ? $lookupPerResponseBody['status_asmen_gizi'] : [];
            $listStatusAsesmen = ArrayHelper::map($listStatusAsesmen, 'lookupkeperawatan_id', 'lookup_name');

            $konfig_print_gizi = ArrayHelper::getValue($body, 'response.konfig_print_gizi', false);

            return $this->render('index', get_defined_vars());
        } catch(RequestException $e){
            return DocoHelpers::response(['message' => $e->getMessage()],500);
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
        $result['recordsTotal'] = 0;
        $counter=0;

        try {
            $response = $this->_restGizi->get('inf-pasien-ranap/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);

            foreach ($body['response']["data"] as $key => $value) {
                $no++;
                $primary = json_encode($value['pendaftaran_id']);
                $value['primary'] = DocoHelpers::encrypt($primary);
                $value['rowNum'] = $no;
                $value['tgl_admisi'] = date('d-m-Y H:i:s', strtotime($value['tgl_admisi']));
                $value['info_pasien'] = $value['no_rekam_medik'] . '<br>' . $value['no_pendaftaran'] . '<br>' . $value['nama_pasien'];
                $value['info_carabayar'] = $value['carabayar_nama'] . '<br>' . $value['penjamin_nama'];
                $value['info_kamar'] = $value['ruangan_nama'] . '<br>' . $value['kamarruangan_nokamar'] . ' - ' . $value['no_tempattidur'];
                $value['info_kelas'] = $value['hak_kelas'] . ' / ' . $value['kelas_pelayanan'];
                $value['status_gizi'] = $value['skor'] >= 2 ? true : false;
                $value['jenis_diet'] = $value['jenisdiet_nama'];
                $value['tanggal_lahir'] = date('d-M-Y', strtotime($value['tanggal_lahir']));

                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionPrintLabelMakanan()
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/print-label-makanan.pdf";

        $id = $request->get('id', null);
        $jumlah = $request->get('jumlah', 1);
        $waktu = $request->get('waktu', null);
        $pendaftaran_ids =[];
        $ex_pendaftaran_id = explode(',',$id);
        
        foreach($ex_pendaftaran_id as $_pendaftaran_id){
            $pendaftaran_ids[] = DocoHelpers::decrypt($_pendaftaran_id);
        }
        $decryptPendaftaranIds = implode(',',$pendaftaran_ids);
        $post = [
            'pendaftaran_id' => $decryptPendaftaranIds,
            'jumlah' => $jumlah,
            'waktu' => $waktu,
        ];

        $urlReport = 'label-makan-gizi-per-pendaftaran';
        $path = !empty($optGuzzle['save_to']) ? $optGuzzle['save_to'] : null;

        return Yii::$app->report->exec($urlReport,[
            'queryParameter' => $post
        ]);
    }

    public function actionPilihJumlahLabelMakanan()
    {
        $title = 'Cetak Label Makanan';
        $request = Yii::$app->request;
        $id = $request->get('id');
        $with_jumlah = $request->get('with_jumlah');

        $res_waktu = Yii::$app->docoRest->master->get('lookup/get-lookup-by-type',
                    ['query' => ['type' => 'waktu']
            ]);
        $res_waktu = json_decode($res_waktu->getBody(), True);

        $waktu = ArrayHelper::map(ArrayHelper::getValue($res_waktu, 'response', []), 'lookup_id', 'lookup_value');
        ksort($waktu); // ordering array by asc


        return $this->renderPartial('_modal_jumlah_cetakan', get_defined_vars());
    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/informasi-pasien-ranap.pdf";
        try {
            $response = $this->_restGizi->get('inf-pasien-ranap/export-pdf?'.http_build_query($yiiRestfulParams),[
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

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filters = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/informasi-pasien-ranap.xlsx";
            $response = $this->_restGizi->get('inf-pasien-ranap/export-excel',[
                'query' => $filters,
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}