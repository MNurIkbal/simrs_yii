<?php

namespace Doco\fisioterapi\actions\LaporanPasienFisioterapiRanap;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetDataAction extends BaseCurrentAction
{
    public function run()
    {
        $helper = new DocoHelpers();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = $this->getParamsFiltered();
        $no = $request->get('start', 1);
        try {
            $response = $helper->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'method' => 'GET',
                'url' => 'laporan-pasien-fisioterapi-ranap/get-data',
                'payload' => [
                    'query' => $filter
                ]
            ]);
            $dataResponse = ArrayHelper::getValue($response, 'data');
            $data = [];
            foreach ($dataResponse as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $tglPendaftaran = ArrayHelper::getValue($value, 'tgl_pendaftaran');
                if ($tglPendaftaran) {
                    $tglPendaftaran = date("d-m-Y H:i:s", strtotime($tglPendaftaran));
                }
                $tglRealisasi = ArrayHelper::getValue($value, 'tgl_realisasi');
                if ($tglRealisasi) {
                    $tglRealisasi = date("d-m-Y H:i:s", strtotime($tglRealisasi));
                }
                $tglPenjadwalanAwal = ArrayHelper::getValue($value, 'tgl_penjadwalan_awal');
                if ($tglPenjadwalanAwal) {
                    $tglPenjadwalanAwal = date("d-m-Y H:i:s", strtotime($tglPenjadwalanAwal));
                }
                $tglPenjadwalanAkhir = ArrayHelper::getValue($value, 'tgl_penjadwalan_akhir');
                if ($tglPenjadwalanAkhir) {
                    $tglPenjadwalanAkhir = date("d-m-Y H:i:s", strtotime($tglPenjadwalanAkhir));
                }
                $value['tgl_pendaftaran'] = $tglPendaftaran;
                $value['tgl_realisasi'] = $tglRealisasi;
                $value['tgl_penjadwalan_awal'] = $tglPenjadwalanAwal;
                $value['tgl_penjadwalan_akhir'] = $tglPenjadwalanAkhir;
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = ArrayHelper::getValue($response, '_meta.totalCount');
            $result['recordsFiltered'] = ArrayHelper::getValue($response, '_meta.totalCount');
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
