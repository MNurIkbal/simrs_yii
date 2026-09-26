<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-21 10:25:59
 */

namespace app\modules\igd\components\traits;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

trait ReturObatTrait 
{
    /* Fungsi retur obat */
    public function actionReturObat($id)
    {
        try {
            $pendaftaran_id = DocoHelpers::decrypt($id);
            $data_pasien = $this->_data_pasien;
            $pegawai_id = $this->_pegawai_id;
            $ruangan_id = $this->_ruangan_id;
            $disablePermintaanRetur = false;

            $request = $this->_restIgd->get('retur-obat/get-data-riwayat-permintaan-retur?id='.$pendaftaran_id, [
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

            return $this->renderAjax('retur-obat/form', get_defined_vars());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
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

            $request = $this->_restIgd->get('retur-obat/get-data-riwayat-permintaan-retur?id='.$pendaftaran_id.'&'.http_build_query($yiiRestfulParams), [
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
                $request = $this->_restIgd->get('retur-obat/get-data-retur-obat-untuk-ubah?id='.$pendaftaran_id.'&permintaanretur_id='.$permintaanretur_id.'&'.http_build_query($yiiRestfulParams), [
                    'form_params' => []
                ]);
                $response = json_decode($request->getBody(), true);
                $response = $response['response'];
                $dataReturObat = $response["dataReturObat"];
                $additionalField = $response["additionalField"];
            } else {
                $request = $this->_restIgd->get('retur-obat/get-data-retur-obat?id='.$pendaftaran_id.'&'.http_build_query($yiiRestfulParams), [
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

    /* Fungsi untuk mencetak riwayat retur obat */
    public function actionCetakRiwayatRetur($id)
    {
        try {
            $path = Yii::getAlias("@download") . "/retur-obat.pdf";

            $request = $this->_restIgd->get('retur-obat/cetak-riwayat-retur?id='.$id.'&pegawai_id='.$this->_pegawai_id.'&ruangan_id='.$this->_ruangan_id, [
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
        try {
            $path = Yii::getAlias("@download") . "/detail-retur-obat.pdf";

            $request = $this->_restIgd->get('retur-obat/cetak-detail-retur?id='.$id.'&permintaanretur_id='.$permintaanretur_id.'&pegawai_id='.$this->_pegawai_id.'&ruangan_id='.$this->_ruangan_id, [
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
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
                $request = $this->_restIgd->get('retur-obat/get-data-detail-retur-obat?id='.$permintaanretur_id, [
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
                    if ($value['qty_retur'] > 0) {
                        $post['permintaanReturDetail'][] = $value;
                    }
                }
            }

            $post['PermintaanReturForm'] = $data;

            if ($simpan == true && (isset($post['permintaanReturDetail']) && !empty($post['permintaanReturDetail']))) {
                $post['permintaanRetur'] = $data;

                $request = $this->_restIgd->post('retur-obat/simpan-permintaan-retur', [
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
                    422 ,
                    Yii::t('fe', 'Semua data yang mandatory harus diisi!')
                );
            }
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    /* Fungsi untuk menghapus permintaan retur */
    public function actionHapusPermintaanRetur()
    {

        try {
            $params = Yii::$app->request->get();
            $id = DocoHelpers::decrypt($params['permintaanretur_id']);

            $request = $this->_restIgd->post('retur-obat/hapus-permintaan-retur', [
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
}