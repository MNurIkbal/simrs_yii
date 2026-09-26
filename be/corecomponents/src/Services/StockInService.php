<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
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

class StockInService extends BaseService
{
    public function __construct()
    {
        $this->service = Yii::$app->docoRest->gudang;
    }

    public function stockIn($header_transaksi, $data)
    {
        $endpoint = 'stock/stock-in';

        return $this->post($endpoint,[
                'form_params' => [
                    'details' => $data
                ],
                'failed' => function($response, $status) use($data, $endpoint){
                    \Yii::error(
                        'Message : API STOCK IN GAGAL--||--Line : NULL --||--File : StockInService.php --||--API URL : '.$endpoint.'--||--Method : POST--||--Payload : ' . json_encode( $data ),
                        'server-error'
                    );
                    return DocoHelpers::response($response, $status);
                },
                'success' => function($response) use($data, $endpoint, $header_transaksi){
                    \Yii::error(
                        'Message : API STOCK IN SUKSES--||--Line : NULL --||--File : StockInService.php --||--API URL : '.$endpoint.'--||--Method : POST--||--Payload : ' . json_encode( $data ),
                        'server-error'
                    );

                    /**
                     * @todo:
                     * Integrasi Akunting:
                     * - Retur
                     * - Batal Resep
                     */

                    switch (strtolower($header_transaksi['type'])) {
                        case 'penerimaan-obat-manual':
                            IntegrasiAkunting::integratePenerimaanSupplier(
                                $header_transaksi['no_transaksi'],
                                DocoConstants::JENIS_OBAT
                            );
                            break;

                        case 'penerimaan-obat-dari-po':
                            IntegrasiAkunting::integratePenerimaanSupplier(
                                $header_transaksi['no_transaksi'],
                                DocoConstants::JENIS_OBAT
                            );
                            break;

                        case 'adjustment-masuk':
                            IntegrasiAkunting::integratePenerimaanSupplier(
                                $header_transaksi['no_transaksi'],
                                DocoConstants::ADJ.DocoConstants::JENIS_OBAT
                            );
                            break;

                        case 'stok-opname':
                            IntegrasiAkunting::integrateStokOpname(
                                $header_transaksi['no_transaksi']
                            );
                            break;

                        default:
                            break;
                    }

                    return DocoHelpers::response($response);
                }
            ]);
    }
}
