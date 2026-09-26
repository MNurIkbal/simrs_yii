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
use GuzzleHttp\Exception\RequestException;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class GetDataInformasiResepturAction extends Action {
    protected $_status_resep =  [
        346 => "Belum Proses",
        347 => "Dalam Proses",
        432 => "Batal Reseptur",
        660 => "Diserahkan"
    ];

    public function run() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = Yii::$app->docoRest->apotek->get('inf-reseptur/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $reseptur_param = null;
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = empty($value["reseptur_id"]) ? $value["resep_id"] : $value["reseptur_id"];
                $terbilang = !empty($value['no_antrian']) ? DocoHelpers::convertAntrian($value['no_antrian']) : '';
                if (!empty($value['no_antrian'])) {
                    $value['no_antrian'] = '<button type="button" class="btn btn-info btn-labeled btn-xs panggil" data-jenisresep="'.$value['status_racikan'].'" data-antrianid="'.$value['antrian_id'].'" data-noantrian="'.$value['no_antrian'].'" data-nama="'.$value['nama_pasien'].'" data-parent="" data-antrian="'.$terbilang.'"><b><i class="fa fa-volume-up"></i></b>' .$value['no_antrian'].'</button>';
                } else {
                    $value['no_antrian'] = "-";
                }

                $no_pendaftaran = empty($value['no_pendaftaran']) ? "-" : $value['no_pendaftaran'];
                $no_sep = empty($value['nosep_bpjs']) ? "-" : $value['nosep_bpjs'];

                $value['no_pendaftaran'] = $no_pendaftaran . ' / ' . $no_sep;
                $value['tgl_resep_dibuat'] = date('d M Y H:i:s', strtotime($value['tgl_resep_dibuat']));
                $value['nomor'] = empty($value['no_resep']) ? $value['no_reseptur'] : $value['no_resep'];
                $value['no_resep'] = empty($value['no_resep']) ? "-" : $value['no_resep'];
                $value['noresep'] = $value['no_resep'];
                $value['jenispenjualan'] = 1;
                $value['status_bayar'] = $value['status_bayar'] == DocoConstants::STAT_BAYAR_LUNAS ? "Sudah Bayar" : "Belum Lunas";
                $value['no_reseptur'] = empty($value['no_reseptur']) ? "-" : $value['no_reseptur'];
                $enc_pk = DocoHelpers::encrypt($primaryKey);
                $enc_noresep = DocoHelpers::encrypt($value['nomor']);
                $reseptur_id = empty($value["reseptur_id"]) ? null : $value["reseptur_id"];
                $status_reseptur_id = empty($value["status_reseptur_id"]) ? null : $value["status_reseptur_id"];
                $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                        'class' => 'btn btn-sm btn-success',
                        'data-source'=>"/apotek/informasi-reseptur/expand?id=".$enc_pk."&noresep=".$enc_noresep."&reseptur_id=".$reseptur_id."&status_reseptur_id=".$status_reseptur_id,
                        'onclick'=> 'docoHelper.detail(this)'
                ]);
                $value['instalasi_reseptur'] = ArrayHelper::getValue($value, 'instalasi_reseptur', ''). ' - ' .ArrayHelper::getValue($value, 'ruangan_reseptur', ''); 
                $value['primary'] = $enc_pk;
                $value['secondary'] = $enc_noresep;
                $value['rowNum'] = $no;
                $value['pendaftaran_id_encrypted'] = DocoHelpers::encrypt($value['pendaftaran_id']);
                $primaries = [
                    'resep_id' => isset($value['resep_id']) ? $value['resep_id'] : null,
                    'reseptur_id' => isset($value['reseptur_id']) ? $value['reseptur_id'] : null,
                    'penjualanresep_id' => isset($value['penjualanresep_id']) ? $value['penjualanresep_id'] : null,
                    'pasien_id' => isset($value['pasien_id']) ? $value['pasien_id'] : null,
                    'pendaftaran_id' => isset($value['pendaftaran_id']) ? $value['pendaftaran_id'] : null,
                ];
                $value['primes'] = DocoHelpers::encrypt(json_encode($primaries));
                $data[$key] = $value;
            }

            $session = Yii::$app->session;
            $session->set('list-data-info-reseptur', $data);
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}
