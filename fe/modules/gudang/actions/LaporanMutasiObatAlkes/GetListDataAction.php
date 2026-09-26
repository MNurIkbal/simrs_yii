<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions\LaporanMutasiObatAlkes;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetListDataAction extends Action
{
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        $filter['advanced-filter']['tgl_pengiriman_awal'] = date('Y-m-d 00:00:00');
        $filter['advanced-filter']['tgl_pengiriman_akhir'] = date('Y-m-d 23:59:00');

        if(isset($filter['advanced-filter']['tgl_pengiriman'])) {
            $explode = explode(' - ', $filter['advanced-filter']['tgl_pengiriman']);

            $filter['advanced-filter']['tgl_pengiriman_awal'] = date('Y-m-d H:i:s', strtotime($explode[0] . '00:00:00'));
            $filter['advanced-filter']['tgl_pengiriman_akhir'] = date('Y-m-d H:i:s', strtotime($explode[1] . '23:59:59'));

            unset($filter['advanced-filter']['tgl_pengiriman']);
        }

        if(isset($filter['advanced-filter']['ruangan_pengirim'])) {
            $filter['advanced-filter']['ruanganpengirim_id'] = $filter['advanced-filter']['ruangan_pengirim'];

            unset($filter['advanced-filter']['ruangan_pengirim']);
        }

        if(isset($filter['advanced-filter']['ruangan_penerima'])) {
            $filter['advanced-filter']['ruanganpenerima_id'] = $filter['advanced-filter']['ruangan_penerima'];

            unset($filter['advanced-filter']['ruangan_penerima']);
        }

        if(isset($filter['advanced-filter']['jenisobatalkes_nama'])) {
            $filter['advanced-filter']['jenisobatalkes_id'] = $filter['advanced-filter']['jenisobatalkes_nama'];

            unset($filter['advanced-filter']['jenisobatalkes_nama']);
        }

        if(isset($filter['advanced-filter']['nama_obat'])) {
            $filter['advanced-filter']['obatalkes_id'] = $filter['advanced-filter']['nama_obat'];

            unset($filter['advanced-filter']['nama_obat']);
        }

        if($ruangan_id != DocoConstants::GUDANG_FARMASI) {
            $filter['advanced-filter']['ruanganpengirim_id'] = $ruangan_id;
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $result['is_disabled'] = false;

        try {
            $response = Yii::$app->docoRest->gudang->get('lap-mutasi-obat-alkes/get-list-data', [
                'query' => $filter
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $uom = $value['uom_input'] == 'null' ? $value['uom_input'] : $value['uom_input_terima'];
                $no++;
                $value['rowNum'] = $no;
                $value['tgl_pemesanan']  = Self::convDate($value['tgl_pemesanan']);
                $value['tgl_pengiriman'] = Self::convDate($value['tgl_pengiriman']);
                $value['tgl_penerimaan'] = Self::convDate($value['tgl_penerimaan']);
                $value['nama_obat'] = $value['nama_obat'];
                $value['qty_kirim'] = $value['qty_input_kirim'] . ' ' . $uom;
                $value['harga_total'] = DocoHelpers::formatNumber($value['harga_total']);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['message'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['message'] = $e->getMessage();
            return $result;
        }
    }

    private function convDate ($date) {
        $result = '-';

        if($date != '-' && $date != 'null') {
            $result = date('d/m/Y', strtotime($date));
        }

        return $result;
    }
}
