<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-24 15:46:52
 */

namespace app\modules\ranap\components\traits;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\modules\ranap\models\PemberianObatForm;
use app\modules\ranap\models\PermintaanReturForm;

trait PemberianObatTrait 
{
    /* Fungsi pemberian obat */
    public function actionPemberianObat()
    {
        try {
            $pemberianObatForm = new PemberianObatForm;
            $dataPasien = $this->_data_pasien;
            $jenisObat = [];
            $listStokObatPasien = [];
            $text_diagnosa = null;
            $disabled = (!empty($this->_data_pasien['pasienpulang_id']) || $this->_data_pasien['is_stopakomodasi'] == true) ? true : false;
            $listData = $this->getListDataPemberianObat();
            $listDiagnosa = ArrayHelper::map($listData['listDiagnosa'], 'diagnosa_id', 'diagnosa_nama');
            $listEfek = ArrayHelper::map($listData['listEfek'], 'lookup_id', 'lookup_name');
            $listKeterangan = ArrayHelper::map($listData['listKeterangan'], 'lookup_id', 'lookup_name');
            $listPegawai = ArrayHelper::map($listData['listPegawai'], 'pegawai_id', 'nama_pegawai');
            $listStokObatPasienDetail = $listData['listStokObatPasienDetail'];
            $listPemberianObat = $listData['listPemberianObat'];
            $diagnosaCpptDokter = $listData['diagnosaCpptDokter'];
            $listJenisObatRiwayat = $listData['listJenisObatRiwayat'];
            $tabelPemberianObat = $listData['listStokObatPasien'];
            $lastReseptur = $listData['lastReseptur'];

            $pemberianObatForm->pendaftaran_id = $dataPasien['pendaftaran_id'];
            $pemberianObatForm->pasienadmisi_id = $dataPasien['pasienadmisi_id'];
            $pemberianObatForm->dokterdpjp_id = $dataPasien['dokter_admisi_id'];

            if (!empty($listPemberianObat)) {
                foreach ($listPemberianObat as $value) {
                    $pemberianObatForm->is_hamil = $value['is_hamil'] == true ? 1 : 0;
                    $pemberianObatForm->is_alergi = $value['is_alergi'] == true ? 1 : 0;
                    $pemberianObatForm->berat_badan = $value['berat_badan'];
                    $pemberianObatForm->tinggi_badan = $value['tinggi_badan'];
                    $pemberianObatForm->luas_tubuh = $value['luas_tubuh'];
                    $pemberianObatForm->diagnosa_id = $value['diagnosa_id'];
                    $dataDiag = json_decode($value['diagnosa_nama'], true);
                    // $text_diagnosa = $dataDiag['text'];
                    if (isset($dataDiag['text'])) {
                        $tmpDiag = '';
                        $idDiag = isset($dataDiag['id']) ? $dataDiag['id'] : '';
                        $tmpDiag = isset($dataDiag['text']) ? $dataDiag['text'] : '';
                        $text_diagnosa = !empty($idDiag) ? $idDiag. '_' .$tmpDiag : $tmpDiag;
                    }else{
                        $tmpDiag = '';
                        $valDiag = json_decode($diagnosaCpptDokter['a_diag_utama'], true);
                        $idDiag = isset($valDiag['id']) ? $valDiag['id'] : '';
                        $tmpDiag = isset($valDiag['text']) ? $valDiag['text'] : '';
                        $text_diagnosa = !empty($idDiag) ? $idDiag. '_' .$tmpDiag : $tmpDiag;
                    }
                }
            } elseif (!empty($lastReseptur)) {
                foreach ($lastReseptur as $value) {
                    $pemberianObatForm->is_hamil = $value['is_hamil'] == true ? 1 : 0;
                    $pemberianObatForm->berat_badan = $value['berat_badan'];
                    $pemberianObatForm->tinggi_badan = $value['tinggi_badan'];
                    $pemberianObatForm->luas_tubuh = $value['luas_tubuh'];
                    $pemberianObatForm->diagnosa_id = $value['diagnosa_id'];
                    $dataDiag = json_decode($value['diagnosa_nama'], true);
                    // $text_diagnosa = $dataDiag['text'];
                    if (isset($dataDiag['text'])) {
                        $tmpDiag = '';
                        $idDiag = isset($dataDiag['id']) ? $dataDiag['id'] : '';
                        $tmpDiag = isset($dataDiag['text']) ? $dataDiag['text'] : '';
                        $text_diagnosa = !empty($idDiag) ? $idDiag. '_' .$tmpDiag : $tmpDiag;
                    }else{
                        $tmpDiag = '';
                        $valDiag = json_decode($diagnosaCpptDokter['a_diag_utama'], true);
                        $idDiag = isset($valDiag['id']) ? $valDiag['id'] : '';
                        $tmpDiag = isset($valDiag['text']) ? $valDiag['text'] : '';
                        $text_diagnosa = !empty($idDiag) ? $idDiag. '_' .$tmpDiag : $tmpDiag;
                    }
                }
            }else{
                $tmpDiag = '';
                $valDiag = json_decode($diagnosaCpptDokter['a_diag_utama'], true);
                $idDiag = isset($valDiag['id']) ? $valDiag['id'] : '';
                $tmpDiag = isset($valDiag['text']) ? $valDiag['text'] : '';
                $text_diagnosa = !empty($idDiag) ? $idDiag. '_' .$tmpDiag : $tmpDiag;
            }

            if (!empty($listStokObatPasienDetail)) {
                foreach ($listStokObatPasienDetail as $key => $value) {
                    if ($value['jenisobatalkes_id'] != '') {
                        $jenisObat[$value['jenisobatalkes_id']] = $value['jenisobatalkes_nama'];
                    } else {
                        $jenisObat[3] = 'OBAT LAIN';
                    }

                    $listStokObatPasien[$value['jenisobatalkes_id']] = [];
                }
            }

            if (!empty($listStokObatPasien)) {
                foreach ($listStokObatPasien as $key => $value) {
                    if (!empty($listStokObatPasienDetail)) {
                        foreach ($listStokObatPasienDetail as $index => $content) {
                            if ($content['jenisobatalkes_id'] == $key) {
                                $listStokObatPasien[$key][] = $content;
                            }
                        }
                    }
                }
            }

            if (!empty($listStokObatPasien)) {
                foreach ($listStokObatPasien as $key => $value) {
                    if (!empty($listStokObatPasien[$key])) {
                        $listStokObatPasien[$key] = ArrayHelper::map($listStokObatPasien[$key], 'nomor', 'nomor');
                    }
                }
            }

            $pendaftaran_id = DocoHelpers::decrypt($this->_pendaftaran_id);

            $hide = 'show()';
            $status_disabled = $this->getStatusPeriksa($pendaftaran_id);
            if($status_disabled == true){
                $hide = 'hide()';
            }else {
                $status_disabled = 'false';
            }

            return $this->renderAjax('pemberian-obat/index', [
                'pendaftaran_id' => $this->_pendaftaran_id,
                'pegawai_id' => $this->_pegawai_id,
                'dataPasien' => $this->_data_pasien,
                'pemberianObatForm' => $pemberianObatForm,
                'listDiagnosa' => $listDiagnosa,
                'listEfek' => $listEfek,
                'listKeterangan' => $listKeterangan,
                'listPegawai' => $listPegawai,
                'listStokObatPasien' => $listStokObatPasien,
                'listJenisObatRiwayat' => $listJenisObatRiwayat,
                'jenisObat' => $jenisObat,
                'status_disabled' => $status_disabled,
                'hide' => $hide,
                'text_diagnosa' => $text_diagnosa,
                'tmpDiag' => $tmpDiag,
                'disabled' => $disabled
            ]);         
        } catch (RequestException $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    /* Fungsi permintaan retur */
    public function actionPermintaanRetur()
    {
        try {
            $permintaanReturForm = new PermintaanReturForm;
            $dataPasien = $this->_data_pasien;
            $disablePermintaanRetur = false;

            $request = $this->_restRanap->get('pemberian-obat/get-data-riwayat-permintaan-retur?id='.$dataPasien['pendaftaran_id'], [
                'form_params' => []
            ]);
            $response = json_decode($request->getBody(), true);
            $response = $response['response'];
            $riwayatPermintaanRetur = $response["riwayatPermintaanRetur"];

            if (!empty($riwayatPermintaanRetur)) {
                foreach ($riwayatPermintaanRetur as $value) {
                    if ($value['status'] == 'Belum di Proses Farmasi') {
                        $disablePermintaanRetur = true;
                    }
                }
            }
            $pendaftaran_id = DocoHelpers::decrypt($this->_pendaftaran_id);

            $hide = 'show()';
            $status_disabled = $this->getStatusPeriksa($pendaftaran_id);
            if($status_disabled == true){
                $hide = 'hide()';
            }else {
                $status_disabled = 'false';
            }

            return $this->renderAjax('pemberian-obat/form_retur_obat', [
                'pendaftaran_id' => $this->_pendaftaran_id,
                'pegawai_id' => $this->_pegawai_id,
                'ruangan_id' => $this->_id_ruangan,
                'dataPasien' => $this->_data_pasien,
                'permintaanReturForm' => $permintaanReturForm,
                'disablePermintaanRetur' => $disablePermintaanRetur,
                'status_disabled' => $status_disabled,
                'hide' => $hide,
            ]);         
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    /* Fungsi untuk mendapatkan riwayat pemberian obat */
    public function actionGetDataRiwayatPemberianObat()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params->get());
            $pendaftaran_id = DocoHelpers::decrypt($params->get('id'));
            $jenisobat_id = $params->get('jenisobat_id');
            $draw = $params->get('draw', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            if (isset($yiiRestfulParams['order'])) {
                unset($yiiRestfulParams['order']);
            }

            if (isset($yiiRestfulParams['q'])) {
                unset($yiiRestfulParams['q']);
            }

            if (isset($yiiRestfulParams['filters'])) {
                unset($yiiRestfulParams['filters']);
            }

            $request = $this->_restRanap->get('pemberian-obat/get-data-riwayat-pemberian-obat?id='.$pendaftaran_id.'&'.http_build_query($yiiRestfulParams), [
                'form_params' => []
            ]);
            $response = json_decode($request->getBody(), true);
            $response = $response['response'];
            $riwayatPemberianObat = $response["riwayatPemberianObat"];
            $listEfek = ArrayHelper::map($response['listEfek'], 'lookup_id', 'lookup_name');
            $listKeterangan = ArrayHelper::map($response['listKeterangan'], 'lookup_id', 'lookup_name');

            $count = 0;
            if (!empty($riwayatPemberianObat)) {
                foreach ($riwayatPemberianObat as $key => $value) {
                    if ($value['jenisobat_id'] == null) {
                        if ($jenisobat_id == 3) {
                            $data[$count]['primary'] = DocoHelpers::encrypt($value['pemberianobat_id']);
                            $data[$count]['no_res_rekon'] = $value['no_res_rekon'];
                            $data[$count]['nama_obat'] = $value['nama_obat'];
                            $data[$count]['signa_obat'] = $value['signa_obat'];
                            $data[$count]['jumlah'] = $value['jumlah'];
                            $data[$count]['dokter'] = $value['dokter'];
                            $data[$count]['wkt_pemberian'] = date('d-m-Y h:i:s', strtotime($value['wkt_pemberian']));
                            $data[$count]['pemberi1'] = $value['pemberi1'];
                            $data[$count]['pemberi2'] = $value['pemberi2'];
                            $data[$count]['efek'] = isset($listEfek[$value['efek']]) ? $listEfek[$value['efek']] : '-';
                            $data[$count]['keterangan'] = isset($listKeterangan[$value['keterangan']]) ? $listKeterangan[$value['keterangan']] : '-';
                            $count++;
                        }
                    } else {
                        if ($jenisobat_id == $value['jenisobat_id']) {
                            $data[$count]['primary'] = DocoHelpers::encrypt($value['pemberianobat_id']);
                            $data[$count]['no_res_rekon'] = $value['no_res_rekon'];
                            $data[$count]['nama_obat'] = $value['nama_obat'];
                            $data[$count]['signa_obat'] = $value['signa_obat'];
                            $data[$count]['jumlah'] = $value['jumlah'];
                            $data[$count]['dokter'] = $value['dokter'];
                            $data[$count]['wkt_pemberian'] = date('d-m-Y h:i:s', strtotime($value['wkt_pemberian']));
                            $data[$count]['pemberi1'] = $value['pemberi1'];
                            $data[$count]['pemberi2'] = $value['pemberi2'];
                            $data[$count]['efek'] = isset($listEfek[$value['efek']]) ? $listEfek[$value['efek']] : '-';
                            $data[$count]['keterangan'] = isset($listKeterangan[$value['keterangan']]) ? $listKeterangan[$value['keterangan']] : '-';
                            $count++;
                        }
                    }
                }

                $result['data'] = $data;
                $result['recordsTotal'] = count($data);
                $result['recordsFiltered'] = count($data);
            }

            return $result;    
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    /* Fungsi untuk mendapatkan data riwayat permintaan retur */
    public function actionGetDataRiwayatPermintaanRetur()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params->get());
            $pendaftaran_id = DocoHelpers::decrypt($params->get('id'));
            $draw = $params->get('draw', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            if (isset($yiiRestfulParams['order'])) {
                unset($yiiRestfulParams['order']);
            }

            if (isset($yiiRestfulParams['q'])) {
                unset($yiiRestfulParams['q']);
            }

            if (isset($yiiRestfulParams['filters'])) {
                unset($yiiRestfulParams['filters']);
            }

            $request = $this->_restRanap->get('pemberian-obat/get-data-riwayat-permintaan-retur?id='.$pendaftaran_id.'&'.http_build_query($yiiRestfulParams), [
                'form_params' => []
            ]);
            $response = json_decode($request->getBody(), true);
            $response = $response['response'];
            $riwayatPermintaanRetur = $response["riwayatPermintaanRetur"];

            $count = 0;
            $no = $params->get('start', 1);
            if (!empty($riwayatPermintaanRetur)) {
                foreach ($riwayatPermintaanRetur as $key => $value) {
                    $no++;
                    $data[$count]['no'] = $no;
                    $data[$count]['primary'] = DocoHelpers::encrypt($value['permintaanretur_id']);
                    $data[$count]['no_permintaanretur'] = $value['no_permintaanretur'];
                    $data[$count]['tgl_permintaanretur'] = $value['tgl_permintaanretur'];
                    $data[$count]['ruangan_nama'] = $value['ruangan_nama'];
                    $data[$count]['status'] = $value['status'];
                    $data[$count]['nama_pegawai'] = $value['nama_pegawai'];
                    $count++;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = count($data);
                $result['recordsFiltered'] = count($data);
            }

            return $result;    
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    /* Fungsi untuk mendapatkan data retur obat */
    public function actionGetDataReturObat()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params->get());
            $pendaftaran_id = DocoHelpers::decrypt($params->get('id'));
            $permintaanretur_id = DocoHelpers::decrypt($params->get('permintaanretur_id'));
            $draw = $params->get('draw', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            if (isset($yiiRestfulParams['order'])) {
                unset($yiiRestfulParams['order']);
            }

            if (isset($yiiRestfulParams['q'])) {
                unset($yiiRestfulParams['q']);
            }

            if (isset($yiiRestfulParams['filters'])) {
                unset($yiiRestfulParams['filters']);
            }

            if ($permintaanretur_id) {
                $request = $this->_restRanap->get('pemberian-obat/get-data-retur-obat-untuk-ubah?id='.$pendaftaran_id.'&permintaanretur_id='.$permintaanretur_id.'&'.http_build_query($yiiRestfulParams), [
                    'form_params' => []
                ]);
                $response = json_decode($request->getBody(), true);
                $response = $response['response'];
                $dataReturObat = $response["dataReturObat"];
                $additionalField = $response["additionalField"];
            } else {
                $request = $this->_restRanap->get('pemberian-obat/get-data-retur-obat?id='.$pendaftaran_id.'&'.http_build_query($yiiRestfulParams), [
                    'form_params' => []
                ]);
                $response = json_decode($request->getBody(), true);
                $response = $response['response'];
                $dataReturObat = $response["dataReturObat"];
                $additionalField = [];
            }

            if (!empty($dataReturObat)) {
                foreach ($dataReturObat as $key => $value) {
                    if (isset($additionalField[$key])) {
                        $dataReturObat[$key]['jumlah_retur'] = $additionalField[$key]['jumlah_retur'];
                        $dataReturObat[$key]['alasan'] = $additionalField[$key]['alasan'];
                        $dataReturObat[$key]['permintaanreturdetail_id'] = $additionalField[$key]['permintaanreturdetail_id'];
                    } else {
                        $dataReturObat[$key]['jumlah_retur'] = 0;
                        $dataReturObat[$key]['alasan'] = '';
                        $dataReturObat[$key]['permintaanreturdetail_id'] = '';
                    }
                }
            }

            $count = 0;
            $no = $params->get('start', 1);
            if (!empty($dataReturObat)) {
                foreach ($dataReturObat as $key => $value) {
                    if ($value['is_retur'] == false && $value['stok_retur'] > 0) {
                        $no++;
                        $data[$count]['no'] = $no.
                        Html::hiddenInput('permintaanreturdetail_id', $value['permintaanreturdetail_id'], [
                            'class' => 'permintaanreturdetail_id',
                            'readonly' => 'readonly'
                        ]).
                        Html::hiddenInput('obatalkes_id', $value['obatalkes_id'], [
                            'class' => 'obatalkes_id',
                            'readonly' => 'readonly'
                        ]).
                        Html::hiddenInput('obatalkespasien_id', $value['obatalkespasien_id'], [
                            'class' => 'obatalkespasien_id',
                            'readonly' => 'readonly'
                        ]).
                        Html::hiddenInput('signa', $value['signa'], [
                            'class' => 'signa',
                            'readonly' => 'readonly'
                        ])
                        .Html::hiddenInput('harga_satuan', $value['hargasatuan_oa'], [
                            'class' => 'harga_satuan',
                            'readonly' => 'readonly'
                        ])
                        .Html::hiddenInput('harga_jumlah', ((float)$value['jumlah_retur'] * (float)$value['hargasatuan_oa']), [
                            'class' => 'harga_jumlah'
                        ]);
                        $data[$count]['noresep'] = $value['noresep'];
                        $data[$count]['nama_obat'] = $value['nama_obat'];
                        $data[$count]['signa'] = $value['signa'];
                        $data[$count]['stok_retur'] = Html::tag('span', $value['stok_retur'], ['class' => 'stok_retur']);
                        $data[$count]['harga_satuan'] = $value['hargasatuan_oa'];
                        $data[$count]['harga_jumlah'] = Html::tag('span', ((float)$value['jumlah_retur'] * (float)$value['hargasatuan_oa']), ['class' => 'harga_jual']);
                        $data[$count]['jumlah_retur'] = Html::textInput('qty_retur', $value['jumlah_retur'], ['class' => 'form-control input-sm docoNumberOnly jumlah_retur', 'onchange' => 'onQtyResepturChange(this)']);
                        $data[$count]['alasan'] = Html::textInput('alasan', $value['alasan'], ['class' => 'form-control input-sm']);
                        $count++;
                    }
                }

                $result['data'] = $data;
                $result['recordsTotal'] = count($data);
                $result['recordsFiltered'] = count($data);
            }

            return $result;    
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    /* Fungsi untuk mendapatkan data detail permintaan retur */
    public function actionGetDataDetailPermintaanRetur()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params = Yii::$app->request;
            $permintaanretur_id = DocoHelpers::decrypt($params->get('permintaanretur_id'));
            $draw = $params->get('draw', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            if ($permintaanretur_id) {
                $request = $this->_restRanap->get('pemberian-obat/get-data-detail-retur-obat?id='.$permintaanretur_id, [
                    'form_params' => []
                ]);
                $response = json_decode($request->getBody(), true);
                $response = $response['response'];
                $dataReturObat = $response["dataReturObat"];

                $count = 0;
                $no = $params->get('start', 1);
                if (!empty($dataReturObat)) {
                    foreach ($dataReturObat as $key => $value) {
                        $no++;
                        $data[$count]['no'] = $no;
                        $data[$count]['obatalkes_nama'] = $value['obatalkes_nama'];
                        $data[$count]['signa_nama'] = $value['signa_nama'];
                        $data[$count]['qty_retur'] = $value['qty_retur'];
                        $data[$count]['alasan'] = $value['alasan'];
                        $data[$count]['qty_approve'] = $value['qty_approve'];
                        $data[$count]['alasan_retur'] = $value['alasan_retur'];
                        $data[$count]['harga_satuan'] = $value['hargasatuan'];
                        $data[$count]['total'] = $value['total'];
                        $data[$count]['tgl_approve'] = $value['tgl_approve'];
                        $count++;
                    }

                    $result['data'] = $data;
                    $result['recordsTotal'] = count($data);
                    $result['recordsFiltered'] = count($data);
                }
            }

            return $result;    
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    /* Fungsi get list obat */
    public function actionGetListObat() {
        try {
            $params = Yii::$app->request->get();
            $out = [];

            if (isset($params['id']) && isset($params['no_res_rekon']) && isset($params['jenisobat_id'])) {
                $pendaftaran_id = $params['id'];
                $no_resep = $params['no_res_rekon'];
                $jenisobat_id = $params['jenisobat_id'];
                $data = [];

                $request = $this->_restRanap->get('pemberian-obat/get-list-obat?id='.$pendaftaran_id, [
                    'form_params' => []
                ]);
                $response = json_decode($request->getBody(), true);
                $listObat = $response['response'];

                if (!empty($listObat)) {
                    $count = 0;
                    foreach ($listObat as $value) {
                        if ($jenisobat_id == 'OBAT LAIN') {
                            if ($no_resep == $value['nomor']) {
                                if ($jenisobat_id == $value['jenisobatalkes_nama'] || $value['jenisobatalkes_nama'] == null) {
                                    $out[$count]['id'] = $value['obatalkes_id'];
                                    $out[$count]['name'] = $value['nama_obat'];
                                    $out[$count]['dipakai'] = $value['stok_dipakai'];
                                    $out[$count]['sisa'] = $value['stok_sisa'];
                                    $out[$count]['stokobatpasien_id'] = $value['stokobatpasien_id'];

                                    $count++;
                                }
                            }
                        } else {
                            if ($no_resep == $value['nomor']) {
                                if ($jenisobat_id == $value['jenisobatalkes_nama']) {
                                    $out[$count]['id'] = $value['obatalkes_id'];
                                    $out[$count]['name'] = $value['nama_obat'];
                                    $out[$count]['dipakai'] = $value['stok_dipakai'];
                                    $out[$count]['sisa'] = $value['stok_sisa'];
                                    $out[$count]['stokobatpasien_id'] = $value['stokobatpasien_id'];

                                    $count++;
                                }
                            }
                        }
                    }
                }

                echo json_encode($out);
                return;
            }

            echo json_encode('');
            return;
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    /* Fungsi get stok obat pasien */
    public function actionGetStokObatPasien() {
        try {
            $params = Yii::$app->request->get();

            if ($params['id'] != '' && $params['obatalkes_id'] != '' && $params['nomor'] != '') {
                $request = $this->_restRanap->get('pemberian-obat/get-stok-obat-pasien?id='.$params['id'].'&obatalkes_id='.$params['obatalkes_id'].'&nomor='.$params['nomor'], [
                    'form_params' => []
                ]);
                $response = json_decode($request->getBody(), true);

                echo json_encode($response['response']);
                return;
            }

            echo json_encode('');
            return;
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    /* Fungsi simpan pemberian obat */
    public function actionSimpanPemberianObat() 
    {
        try {
            $post = [];
            // $save = false;
            $post['pemberianObat'] = Yii::$app->request->post('PemberianObatForm');
            $post['pemberianObatDetail'] = Yii::$app->request->post('PemberianObatDetailForm');

            if (!empty($post['pemberianObatDetail'])) {
                foreach ($post['pemberianObatDetail'] as $key => $value) {
                    if (isset($value['obatalkes_id']) && $value['obatalkes_id'] != '') {
                        $save = true;
                    } else {
                        unset($post['pemberianObatDetail'][$key]);
                    }
                }
            }

            $request = $this->_restRanap->post('pemberian-obat/simpan-pemberian-obat', [
                'form_params' => $post
            ]);
            
            $response = json_decode($request->getBody(), true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],500);
        } catch (\Exception $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],500);
        }
    }

    /* Fungsi simpan retur obat */
    public function actionSimpanPermintaanRetur() {
        try {
            $post = [];
            $data = [];
            $detail = [];
            $simpan = true;
            $permintaanRetur = Yii::$app->request->post('PermintaanReturForm');
            $permintaanReturDetail = Yii::$app->request->post('PermintaanReturDetailForm');

            $count = 0;
            if (!empty($permintaanRetur)) {
                foreach ($permintaanRetur as $value) {
                    $data[$value['name']] = $value['value'];
                }
            }

            unset($data['_csrf']);
            $data['permintaanretur_id'] = $data['permintaanretur_id'] != '' ? DocoHelpers::decrypt($data['permintaanretur_id']) : $data['permintaanretur_id'];
            $data['tgl_permintaanretur'] = date('Y-m-d h:i:s', strtotime('NOW'));

            $count = -1;
            if (!empty($permintaanReturDetail)) {
                foreach ($permintaanReturDetail as $key => $value) {
                    if ($key % 8 == 0) {
                        $count++;
                    }
                    $detail[$count][$value['name']] = $value['value'];
                }
            }

            if (!empty($detail)) {
                foreach ($detail as $value) {
                    if ($value['qty_retur'] == 0) continue;
                    
                    if ($value['qty_retur'] > 0 && $value['alasan'] == '') {
                        $simpan = false;
                    } else {
                        $post['permintaanReturDetail'][] = $value;
                    }
                }
            }

            $post['PermintaanReturForm'] = $data;

            if ($simpan == true) {
                if (isset($post['permintaanReturDetail'])) {
                    $post['permintaanRetur'] = $data;

                    $request = $this->_restRanap->post('pemberian-obat/simpan-permintaan-retur', [
                        'form_params' => $post
                    ]);
                    $response = json_decode($request->getBody(), true);

                    if ($response['metadata']['status'] == 200) {
                        return DocoHelpers::response($response);
                    } else {
                        return DocoHelpers::responseTemplate(
                            500,
                            'Error',
                            [],
                            [
                                'title' => Yii::t('fe', 'Peringatan'),
                                'text' => 'Terdapat kesalahan',
                                'message' => Yii::t('fe', 'Terjadi kesalahan').'!',
                            ]
                        );
                    }
                } else {
                    return DocoHelpers::responseTemplate(
                        200,
                        Yii::t('fe', 'Tidak ada data yang disimpan!')
                    );
                }
            } else {
                return DocoHelpers::responseTemplate(
                    422,
                    Yii::t('fe', 'Semua data yang mandatory harus diisi!')
                );
            }
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    /* Cetak pemberian obat */
    public function actionCetakPemberianObat($id)
    {
        try {
            $path = Yii::getAlias("@download") . "/pemberian-obat.pdf";

            $request = $this->_restRanap->get('pemberian-obat/cetak-pemberian-obat?id='.$id.'&pegawai_id='.$this->_pegawai_id.'&ruangan_id='.$this->_id_ruangan, [
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /* Fungsi untuk mencetak riwayat retur obat */
    public function actionCetakRiwayatRetur($id)
    {
        try {
            $path = Yii::getAlias("@download") . "/retur-obat.pdf";

            $request = $this->_restRanap->get('pemberian-obat/cetak-riwayat-retur?id='.$id.'&pegawai_id='.$this->_pegawai_id.'&ruangan_id='.$this->_id_ruangan, [
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /* Fungsi untuk mencetak detail retur obat */
    public function actionCetakDetailRetur($id, $permintaanretur_id)
    {
        // try {
            $path = Yii::getAlias("@download") . "/detail-retur-obat.pdf";

            $request = $this->_restRanap->get('pemberian-obat/cetak-detail-retur?id='.$id.'&permintaanretur_id='.$permintaanretur_id.'&pegawai_id='.$this->_pegawai_id.'&ruangan_id='.$this->_id_ruangan, [
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        // } catch (RequestException $e) {
        //     throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        // } catch (\Exception $e) {
        //     throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        // }
    }

    /* Fungsi untuk menghapus permintaan retur */
    public function actionHapusPermintaanRetur()
    {

        try {
            $params = Yii::$app->request->get();
            $id = DocoHelpers::decrypt($params['permintaanretur_id']);

            $request = $this->_restRanap->post('pemberian-obat/hapus-permintaan-retur', [
                'form_params' => ['id' => $id]
            ]);
            $response = json_decode($request->getBody(), true);

            return json_encode($response['response']);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /* Fungsi get list data pemberian obat */
    private function getListDataPemberianObat()
    {
        try {
            $request = $this->_restRanap->get('pemberian-obat/get-list-data?id='.$this->_pendaftaran_id.'&ruangan_id='.$this->_id_ruangan, ['form_params' => []]);
            $response = json_decode($request->getBody(), true);

            return $response['response'];
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }
}
?>