<?php

namespace Doco\penjaminasuransi\actions\TransaksiAlokasiPembayaran;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoDatatableHelper;
use app\components\DocoController;
use Doco\penjaminasuransi\models\PenerimaanPembayaranForm;
use Doco\penjaminasuransi\models\AlokasiPengajuanForm;
use Doco\penjaminasuransi\models\TransaksiAlokasiForm;
use Doco\penjaminasuransi\models\TransaksiAlokasiImportForm;

class GetDataAction extends BaseCurrentAction
{
    private function setJumlahBayarFromSession($noPembayaran)
    {
        $detailsImport = $this->getDetailsSession();
        if(empty($detailsImport)) return null;
        $key = array_search($noPembayaran, array_column($detailsImport, 'no_invoice'));
        if ($key === false) return null;
        return ArrayHelper::getValue($detailsImport, "$key.jumlah_bayar");
    }

    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = $request->get('id');
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $filter['id'] = DocoHelpers::decrypt($id);
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restPenjamin->get('transaksi-alokasi-pembayaran/get-data', [
                'form_params' => [],
                'query' => $filter
            ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $isImported = false;
                $noPembayaran = $value['no_pembayaran'];
                $pureJumlahBayar = $value['jumlah_bayar'];
                $jumlahBayarSession = $this->setJumlahBayarFromSession($noPembayaran);
                if ($jumlahBayarSession) {
                    $isImported = true;
                }
                $primaryKey = DocoHelpers::encrypt($value['no_pembayaran']);
                $rawData = $value;
                $rawData['rowNum'] = $no;
                $rawData['nosep'] = $value['nosep'] ? $value['nosep'] : '-';
                $html = '<div class="input-group">';
                $html .= '<span class="input-group-addon" title="Select date &amp; time">';
                $html .= '<span>Rp.</span>';
                $html .= '</span>';
                $html .= Html::textInput('jumlah_bayar[]', null, [
                    'class' => 'form-control doco-number text-right total-alokasi',
                    'disabled' => true
                ]);
                $html .= '</div>';
                $rawData['label_bayar'] = $html;
                $rawData['nama_pasien'] = $value['no_rekam_medik'] . ' - ' . $value['nama_pasien'];
                $rawData['tgl_pendaftaran'] = date('d-M-Y', strtotime($value['tgl_pendaftaran']));
                $rawData['tglpasienpulang'] = date('d-M-Y', strtotime($value['tglpasienpulang']));
                $rawData['total_tagihan'] = DocoHelpers::formatNumber((int)$value['total_tagihan']);
                $rawData['jumlah_telahbayar'] = DocoHelpers::formatNumber((int)$value['jumlah_telahbayar']);
                $rawData['jumlah_bayar'] = DocoHelpers::formatNumber((int)$pureJumlahBayar);
                $rawData['jumlah_sisapiutang'] = DocoHelpers::formatNumber((int) $value['jumlah_piutang'] - $pureJumlahBayar);
                $rawData['sisa_piutang'] = $value['jumlah_piutang'] - $pureJumlahBayar;
                $rawData['jumlah_piutang'] = DocoHelpers::formatNumber((int)$value['jumlah_piutang']);
                $rawData['piutang'] = $value['jumlah_piutang'];
                $rawData['bayar'] = $pureJumlahBayar;
                $rawData['no_pembayaran'] = $value['no_pembayaran'];
                $rawData['is_imported'] = $isImported;
                $rawData['jumlah_bayar_session'] = $jumlahBayarSession;
                $data[$key] = $rawData;
            }
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
