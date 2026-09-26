<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\Services;

use Yii;
use yii\helpers\Url;
use Doco\Services\BaseService;
use Doco\components\DocoConstants;
use SirsCore\features\IntegrasiAkunting;
use Doco\components\DocoHelpers;

class StockService extends BaseService {
    public function __construct() {
        $this->service = Yii::$app->docoRest->gudang;
    }

    /**
    * @param:
    * $header_transaksi: object, untuk keperluan integrasi akunting
    * $data: array, detail obat yang ditransaksikan
    */
    public function in($header_transaksi, $data) {
        return $this->stock("in", $header_transaksi, $data);
    }

    public function out($header_transaksi, $data) {
        return $this->stock("out", $header_transaksi, $data);
    }

    public function stock($type, $header_transaksi, $data) {
        $endpoint = 'stock/stock-'.$type;
        $form_params = [
            'details' => $data
        ];

        if($type == "out") {
            $form_params['ruangan_id'] = $header_transaksi['ruangan_id'];
        }

        return $this->post($endpoint, [
            'form_params' => $form_params,
            'failed' => function($response, $status) use($type, $data, $endpoint){
                \Yii::error(
                    'Message : API STOCK '.strtoupper($type).' GAGAL--||--Line : NULL --||--File : StockService.php --||--API URL : '.$endpoint.'--||--Method : POST--||--Payload : ' . json_encode( $data ),
                    'server-error'
                );
                return DocoHelpers::response($response, $status);
            },
            'success' => function($response) use($type, $data, $endpoint, $header_transaksi){
                \Yii::error(
                    'Message : API STOCK '.strtoupper($type).' SUKSES--||--Line : NULL --||--File : StockService.php --||--API URL : '.$endpoint.'--||--Method : POST--||--Payload : ' . json_encode( $data ),
                    'server-error'
                );

                $this->integrasiAkunting($header_transaksi);

                return DocoHelpers::response($response);
            }
        ]);
    }

    /**
     * @todo:
     * Integrasi Akunting:
     * - Retur
     * - Batal Resep
     */
    public function integrasiAkunting($header_transaksi) {
        switch (strtolower($header_transaksi['type'])) {
            case 'penerimaan-obat':
                IntegrasiAkunting::integratePenerimaanSupplier(
                    $header_transaksi['no_transaksi'],
                    DocoConstants::JENIS_OBAT
                );
                break;

            case 'adjustment':
                /**
                * @param:
                * no_transaksi = no_adjustment
                * DocoConstants::ADJ.DocoConstants::JENIS_OBAT
                */
                IntegrasiAkunting::integratePenerimaanSupplier(
                    $header_transaksi['no_transaksi'],
                    DocoConstants::ADJ.DocoConstants::JENIS_OBAT);
                break;

            case 'serahkan-obat':
                /**
                * @param:
                * no_transaksi = no_resep
                * DocoConstants::ADJ.DocoConstants::JENIS_OBAT
                */
                IntegrasiAkunting::integrateByNoResep($header_transaksi['no_transaksi']);
                break;

            case 'tindakan-bmhp':
                /**
                * @param:
                * no_transaksi = no_pendaftaran
                * instalasi_id
                */
                IntegrasiAkunting::integrateTindakanBmhp(
                    $header_transaksi['no_transaksi'],
                    $header_transaksi['instalasi_id']);
                break;

            case 'stok-opname':
                /**
                * @param:
                * no_transaksi = nostokopname
                */
                IntegrasiAkunting::integrateStokOpname(
                    $header_transaksi['no_transaksi']);
                break;

            default:
                break;
        }
    }
}
