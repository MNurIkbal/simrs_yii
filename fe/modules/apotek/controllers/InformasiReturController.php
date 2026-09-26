<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi Retur Resep
 * @copyright 16 January 2018 aweutist
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\apotek\models\PendaftaranForm;
use app\components\DocoConstants;
use yii\helpers\ArrayHelper;

class InformasiReturController extends DocoController {
    public $_title = "Informasi Retur Resep";
    public $_restApotek;
    public $_restMaster;

    public function init() {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actions() {
        return [
            'export-excel'      => 'Doco\apotek\actions\InformasiRetur\ExportExcelAction',
            'get-data'          => 'Doco\apotek\actions\InformasiRetur\GetDataAction',
            'get-data-obat'     => 'Doco\apotek\actions\InformasiRetur\GetDataObatAction',
            'get-no-resep'      => 'Doco\apotek\actions\InformasiRetur\GetNoResepAction',
            'get-no-retur'      => 'Doco\apotek\actions\InformasiRetur\GetNoReturAction',
            'get-penjamin'      => 'Doco\apotek\actions\InformasiRetur\GetPenjaminAction',
            'index'             => 'Doco\apotek\actions\InformasiRetur\IndexAction',
            'get-log-activity'  => 'Doco\apotek\actions\InformasiRetur\GetLogActivityAction',
            'retur-resep'       => 'Doco\apotek\actions\InformasiRetur\ReturResepAction',
            'edit-retur-resep'  => 'Doco\apotek\actions\InformasiRetur\EditReturResepAction',
            'batal-retur'       => 'Doco\apotek\actions\InformasiRetur\BatalReturResepAction',
            'tambah'            => [
                'class'         => 'Doco\apotek\actions\InformasiRetur\DetailAction',
                'type'          => 'tambah',
                'title'         => 'Tambah Retur Obat/BMHP'
            ],
            'lihat'             => [
                'class'         => 'Doco\apotek\actions\InformasiRetur\DetailAction',
                'type'          => 'lihat',
                'title'         => 'Lihat Retur Obat/BMHP'
            ],
            'edit'             => [
                'class'         => 'Doco\apotek\actions\InformasiRetur\DetailAction',
                'type'          => 'edit',
                'title'         => 'Edit Retur Obat/BMHP'
            ],
        ];
    }

    public function getFilter($request) {
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        if(!isset($yiiRestfulParams['advanced-filter']['tgl_retur'])) {
            $yiiRestfulParams['advanced-filter']['tgl_retur'] = date('d-m-Y').' - '.date('d-m-Y');
        }
        return $yiiRestfulParams;
    }

    public function actionTambahRetur() {
        $title = 'Cari Pasien';
        $model = new PendaftaranForm;
        return $this->renderAjax('_modal_cari_pasien', get_defined_vars());
    }

    public function actionGetPencarianPasien(){
        try {
            $response = $this->guzzleExec(Yii::$app->docoRest->apotek, [
                'url' => 'inf-retur/get-pasien-retur',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'term' => Yii::$app->request->get('term', null)
                    ]
                ]
            ]);

            $list = [];
            foreach ($response as $data) {
                $detail = isset($data['nama_pasien']) ? $data['nama_pasien'] : !empty($data['no_pendaftaran']) ? $data['no_pendaftaran'] : $data['no_rekam_medik'];
                $pasien = isset($data['nama_pasien']) ? $data['nama_pasien'] : null ;
                $no_pendaftaran = isset($data['no_pendaftaran']) ? $data['no_pendaftaran'] : null ;
                $no_rekam_medik = isset($data['no_rekam_medik']) ? $data['no_rekam_medik'] : null ;
                $list[] = [
                    'id' => $no_pendaftaran,
                    'text' => $no_pendaftaran .' - '. $no_rekam_medik .' - '. $pasien
                ];
            }
            
            return DocoHelpers::response($list);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionGetDataPasienRetur() {
        $request = Yii::$app->request;
        $result = [];
        try {
            $response = $this->guzzleExec(Yii::$app->docoRest->apotek, [
                'url' => 'inf-retur/get-data-pasien-retur',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'identifier' => Yii::$app->request->get('identifier', null)
                    ]
                ]
            ]);
            
            $no = $request->get('start', 1);
            foreach ($response['detail'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['id_encrypt'] = $primaryKey;
                $value['ruanganakhir'] = !empty($value['ruanganakhir']) ? $value['ruanganakhir'] : '';
                $data[$key] = $value;
            }

            $result['data'] = $data;

            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionLogActivity($id) {
        $title = 'Log Activity';
        $transaksi_id = $id;
        return $this->renderAjax('_modal_log_activity', get_defined_vars());
    }

    public function actionVerifikasi() {
        $request = Yii::$app->request;
        $post = $request->post();
        $data_retur = $request->post('data_retur', []);
        $valid = false;
        $payload_retur = [];
        $pendaftaran_id = ArrayHelper::getValue($post, 'pendaftaran_id');
        $ruangan_retur = ArrayHelper::getValue($post, 'ruangan');
        $tanggal_retur = ArrayHelper::getValue($post, 'tanggal');
        $alasan_edit = ArrayHelper::getValue($post, 'alasan_edit');
        $returresep_id = ArrayHelper::getValue($post, 'returresep_id');
        $verif_type = ArrayHelper::getValue($post, 'verif_type');
        foreach ($data_retur as $key => $value) {
            $payload_retur['detail'][] = [
                'identifier' => $value['obatalkes_id'],
                'qty_retur' => isset($value['qty_retur']) ? $value['qty_retur'] : 0
            ];

            $value['qty_retur'] = isset($value['qty_retur']) ? $value['qty_retur']: 0;

            if ($value['qty_retur'] > 0) {
                $valid = true;
            } else {
                $racikan = filter_var($value['is_racikan'], FILTER_VALIDATE_BOOLEAN);
                if($racikan == false) {
                    return DocoHelpers::response([
                        'response' => [
                            'text' => 'Qty Retur tidak boleh kosong.',
                            'title' => 'Proses Gagal!',
                        ]
                    ], 422);
                }
            }
        }

        return $this->guzzleExec(Yii::$app->docoRest->apotek, [
            'url' => 'inf-retur/verifikasi',
            'method' => 'post',
            'payload' => [
                'form_params' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'ruangan_retur' => $ruangan_retur,
                    'tanggal_retur' => $tanggal_retur,
                    'data_retur' => $data_retur,
                    'returresep_id' => DocoHelpers::decrypt($returresep_id),
                    'verif_type' => $verif_type,
                    'alasan' => $alasan_edit,
                    'username' => $request->post('nama_pemakai'),
                    'pass' => $request->post('katakunci_pemakai')
                ]
            ],
            'returnResponse' => true
        ]);
    }
}