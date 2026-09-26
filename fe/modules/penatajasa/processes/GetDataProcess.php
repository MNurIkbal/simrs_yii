<?php

/**
 * @author : Dede (dede.herdiana@sirs.co.id)
 * Powered by Sirs
 */

namespace app\modules\penatajasa\processes;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

class GetDataProcess extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
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
        $pendaftaran_id = $request->get('pendaftaran_id',null);
        $pendaftaranID = DocoHelpers::encrypt($pendaftaran_id);
        $yiiRestfulParams['id'] = $pendaftaran_id;
        // try {
            $rest = Yii::$app->docoRest->penatajasa->get('informasi-pasien/get-new-list-tindakan?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($rest->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primary = DocoHelpers::encrypt($key);
                $isPenataJasa = !empty($value['is_penatajasa']) ? $value['is_penatajasa'] : false;
                $tindakanPelayanan = !empty($value['tindakanpelayanan_id']) ? DocoHelpers::encrypt($value['tindakanpelayanan_id']) : null;
                $obatalkespasien_id = !empty($value['obatalkespasien_id']) ? DocoHelpers::encrypt($value['obatalkespasien_id']) : null;
                $oaPasien = !empty($value['obatalkespasien_id']) ? DocoHelpers::encrypt($value['obatalkespasien_id']) : null;
                $noRegis = !empty($value['no_pendaftaran']) ? DocoHelpers::encrypt($value['no_pendaftaran']) : null;
                $value['primary'] = $primary;
                $value['rowNum'] = $no;
                $value['tgl_tindakan'] = DocoHelpers::convDateTime($value['tgl_tindakan'],false,true);
                $value['tarif_satuan'] = DocoHelpers::formatNumber($value['tarif_satuan']);
                $value['tarifcyto_tindakan'] = DocoHelpers::formatNumber($value['tarifcyto_tindakan']);
                $value['tarifpenyulit_tindakan'] = DocoHelpers::formatNumber($value['tarifpenyulit_tindakan']);
                $value['tarif_tindakan'] = DocoHelpers::formatNumber($value['tarif_tindakan']);
                $value['instalasi_ruangan_nama'] = $value['instalasi_nama'].'<br>'.$value['ruangan_nama'];
                if ($value['obatalkes_id']) {
                    $value['qty_tindakan'] = $value['qty_oa']. ' ' .$value['satuanobat_nama'];
                } else {
                    $value['qty_tindakan'] = $value['qty_tindakan'];
                }

                $value['aksi'] = '';
                if ($isPenataJasa) {
                    if ($tindakanPelayanan) {
                        $value['aksi'] .= '<button class="btn btn-danger" data-toggle="modal" 
                        data-target="#modal_backdrop" data-width="40%" action="'.Url::to([
                                'delete-data-tindakan', 
                                'id' => $tindakanPelayanan,
                                'no_regis' => $noRegis,
                                'no_masukpenunjang' => $value['no_masukpenunjang'],
                            ]).'"><i class="fa fa-trash fa-xs"></i></button>';
                    } else {
                        $value['aksi'] .= '<button class="btn btn-danger" data-toggle="modal" 
                        data-target="#modal_backdrop" data-width="40%" action="'.Url::to([
                                'delete-data-bmhp', 
                                'id' => $oaPasien,
                                'no_regis' => $noRegis,
                            ]).'"><i class="fa fa-trash fa-xs"></i></button>';
                    }
                    if (!empty($value['tindakanpelayanan_id'])) {
                        // Saat ini hide tombol BMHP karena sudah diakomodir di tombol tambah di atas tabel
                        //
                        // $value['aksi'] .= '&nbsp;&nbsp;<button class="btn btn-success" data-toggle="modal" data-target="#modal_backdrop" data-width="60%" action="'.Url::to([
                        //          'tambah-bmhp-tambahan', 
                        //          'id' => $value['tindakanpelayanan_id'], 
                        //          'pendaftaran_id' => $pendaftaran_id
                        //       ]).'" title="Tambah BMHP"><i class="fa fa-plus fa-xs"></i></button>';
                    }
                } else {
                    // $value['aksi'] .= '<i class="fa fa-lock"></i>';
                    if ($tindakanPelayanan) {
                        $value['aksi'] .= '<button class="btn btn-danger" data-toggle="modal" 
                        data-target="#modal_backdrop" action="'.Url::to([
                                'delete-data-tindakan', 
                                'id' => $tindakanPelayanan,
                                'no_regis' => $noRegis,
                                'no_masukpenunjang' => $value['no_masukpenunjang'],
                            ]).'"><i class="fa fa-trash fa-xs"></i></button>';

                        $value['aksi'] .= '&nbsp;&nbsp;<button class="btn btn-primary" data-toggle="modal" data-target="#modal_backdrop" data-width="60%" action="'.Url::to([
                                'edit-tanggal-pelayanan', 
                                'id' => $tindakanPelayanan, 
                                'pendaftaran_id' => $pendaftaran_id
                            ]).'" title="Edit Tanggal Pelayanan"><i class="fa fa-pencil fa-xs"></i></button>';
                    }
                    else {
                        $value['aksi'] .= '<button class="btn btn-danger" data-toggle="modal" 
                        data-target="#modal_backdrop" action="'.Url::to([
                                'delete-data-bmhp', 
                                'id' => $obatalkespasien_id,
                                'no_regis' => $noRegis,
                            ]).'"><i class="fa fa-trash fa-xs"></i></button>';
                    }
                }

                $data[$key] = $value;
            }
        // } catch (\RequestException $e) {
        //         $data = [];
        // } catch (\Exception $e) {
            
            
        //     Yii::error([
        //         "message" => $e->getMessage(),
        //     ]);
        //     $data = [];
        // }

        $result['data'] = $data;
        $result['recordsTotal'] = count($data);
        $result['recordsFiltered'] = count($data);
        return $result;
    }
}