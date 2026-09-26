<?php

/**
 * @author : Dede Herdiana (dede.herdiana@sirs.co.id)
 * Powered by Sirs
 */

namespace app\extensions\penatajasa;

use Yii;
use yii\web\Response;
use app\components\DocoController;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

class GetDataMHG extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $session = Yii::$app->session;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $pendaftaran_id = $request->get('pendaftaran_id',null);
        $pendaftaranID = DocoHelpers::encrypt($pendaftaran_id);
        try {
            $draw = $request->get('draw', 1);
            $data = [];
            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $rest = Yii::$app->docoRest->penatajasa->get('informasi-pasien/get-list-tindakan', [
                'query' => [
                    'id' => $pendaftaran_id
                ],
            ]);
            $body = json_decode($rest->getBody(), true);
            $responData = $body['response'];
            $no = $request->get('start', 1);
            $konfigSystem = $this->getKonfigSystem();
            $konfigKelompokTindakan = !empty($konfigSystem['konfig_kelompok_tindakan']) ? $konfigSystem['konfig_kelompok_tindakan'] : [];
            $newKonfigKelompok = [];
            if(!empty($konfigKelompokTindakan)) {
                $konfigKelompokTindakan = json_decode($konfigKelompokTindakan, true);
                $konfigKelompokTindakan = ArrayHelper::getValue($konfigKelompokTindakan, 'konfig_kelompok_tindakan', []);
                foreach ($konfigKelompokTindakan as $key => $value) {
                    if(is_array($value)) {
                        foreach ($value as $k => $val) {
                            $newKonfigKelompok[] = $k;
                        }
                    }
                }
            }
            
            foreach ($responData as $key => $value) {
                $no++;
                $primary = DocoHelpers::encrypt($key);
                $isPenataJasa = !empty($value['is_penatajasa']) ? $value['is_penatajasa'] : false;
                $tindakanPelayanan = !empty($value['tindakanpelayanan_id']) ? DocoHelpers::encrypt($value['tindakanpelayanan_id']) : null;
                $obatalkespasien_id = !empty($value['obatalkespasien_id']) ? DocoHelpers::encrypt($value['obatalkespasien_id']) : null;
                $kelompokTindakanId = !empty($value['kelompoktindakan_id']) ? $value['kelompoktindakan_id'] : null;
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
                $additionalData = ArrayHelper::getValue($value, 'additional_data', []);
                $remarks = null;
                $isRemarks = false;
                if(!empty($additionalData)) {
                    $additionalData = json_decode($additionalData, true);
                    if(isset($additionalData['remarks'])) {
                        $remarks = $additionalData['remarks'];
                    }
                }
                
                if(!empty($kelompokTindakanId) && !empty($newKonfigKelompok)) {
                    if(in_array($kelompokTindakanId, $newKonfigKelompok)) {
                        $isRemarks = true;
                    }
                }

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
						/**
						 * Khusus untuk KN tombol bmhp dimunculkan lagi
						 * updated at 08/12/2021
						 */
                        
                        $value['aksi'] .= '&nbsp;&nbsp;<button class="btn btn-success" data-toggle="modal" data-target="#modal_backdrop" data-width="60%" action="'.Url::to([
                                 'tambah-bmhp-tambahan', 
                                 'id' => $value['tindakanpelayanan_id'], 
                                 'pendaftaran_id' => $pendaftaran_id
                              ]).'" title="Tambah BMHP"><i class="fa fa-plus fa-xs"></i></button>';
                    }
                    if ($isRemarks) {
                        $daftarTindakanNama = ArrayHelper::getValue($value, 'daftartindakan_nama');
                        $kelompokTindakanNama = ArrayHelper::getValue($value, 'kelompok');
                        $value['daftartindakan_nama'] = $daftarTindakanNama.' - '.$kelompokTindakanNama.' - '.$remarks;
                        $value['aksi'] .= '&nbsp;&nbsp;<button class="btn btn-primary" data-toggle="modal" data-target="#modal_backdrop" data-width="90%" action="'.Url::to([
                            'edit-tindakan', 
                            'id' => $tindakanPelayanan, 
                            'pendaftaran_id' => $pendaftaranID
                        ]).'" title="Edit Tindakan"><i class="fa fa-pencil fa-xs"></i></button>';
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

                $data[] = $value;
            }
        } catch (\RequestException $e) {
                $data = [];
        } catch (\Exception $e) {
            
            
            Yii::error([
                "message" => $e->getMessage(),
            ]);
            $data = [];
        }

        $result['data'] = $data;
        $result['recordsTotal'] = count($data);
        $result['recordsFiltered'] = count($data);
        return $result;
    }

    private function getKonfigSystem()
    {
      $response = Yii::$app->docoRest->penatajasa->get('informasi-pasien/get-konfig-system',[
        'query' => []
      ]);
      return json_decode($response->getBody(),true)['response'];
    }
}