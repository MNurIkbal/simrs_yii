<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class GetDataObatAction extends Action {
    public function run() {
        $id = DocoHelpers::decrypt($_GET['id']);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $get = $request->get();
        $noresep = DocoHelpers::decrypt($get['noresep']);
        $yiiRestfulParams['advanced-filter']['noresep'] = DocoHelpers::decrypt($get['noresep']);
        $yiiRestfulParams['status_reseptur'] = $get['status_reseptur'];
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        unset($yiiRestfulParams['page']);
        unset($yiiRestfulParams['per-page']);

        try {
            $response = Yii::$app->docoRest->apotek->get('inf-reseptur/data-obat?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $data_obat = $body['response']['data'];
            Yii::$app->cache->set('data-obat-' . $noresep, $data_obat, 3600);
            $no = $request->get('start',1);
            $subtotalharga = $biayaadministrasi = $totaltagihan = 0;
            foreach ($data_obat as $key => $value) {
                if($get['status_reseptur'] == DocoConstants::STATUS_RESEPTUR_BATAL){
                    $namaRc = !empty($value['racikan_nama']) ? $value['racikan_nama'] : "";
                } else {
                    $namaRc = !empty($value['nama_racikan']) ? $value['nama_racikan'] : "";
                }
                $qtRc = !empty($value['qty_racikan']) ? $value['qty_racikan'] : "";
                $stRcNama = !empty($value['satuan_racikan_nama']) ? $value['satuan_racikan_nama'] : "";
                $namaRcJmlRc = $namaRc . " " . $qtRc . " " .$stRcNama;
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['obatalkes_id']);
                unset($value['obatalkes_id']);
                $jenisRacikan = !empty($value['rke']) ? Yii::t('fe', 'Racikan') : Yii::t('fe', 'Non Racikan');
                $value['jenis_racikan'] = $value['racikan_nama'];
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['rke'] = isset($value['rke']) ? $value['rke'] ." - ". $namaRcJmlRc : "-";
                $value['etiket'] = isset($value['etiket']) ? strip_tags($value['etiket']) : '';
                $value['qty'] = isset($value['det_transaksi']) ? $value['det_transaksi'] : $value['qty_transaksi'];
                $total = $value['totalharga_jual'];
                $value['totaltagihan'] = DocoHelpers::formatNumber($total);
                $value['hargajual_satuan'] = DocoHelpers::formatNumber($value['hargajual_satuan']);
                $value['is_kronis_raw'] = isset($value['is_kronis']) ? $value['is_kronis'] : false;
                $value['is_kronis'] = isset($value['is_kronis']) ? $value['is_kronis'] == true ? 'Ya' : 'Tidak' : 'Tidak';
                $data[$key] = $value;
                $subtotalharga += $total;
            }

            $result['subtotalobat'] = DocoHelpers::formatNumber($subtotalharga);
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = 'x-'.$e->getMessage();
            return $result;
        }
    }
}
