<?php


namespace Doco\gudang\actions\LaporanStockInventoryBarang;

use Yii;
use yii\base\Action;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\Traits\ControllerHelperTrait;

class GetListDataAction extends Action
{
    use ControllerHelperTrait;
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

        $filter_tgl = ArrayHelper::getValue($filter,'advanced-filter.tanggal_inventory');
        if(is_null($filter_tgl)){
            $filter['advanced-filter']['tanggal_inventory'] = date('d-M-Y');
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $result['is_disabled'] = false;

        $response = $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => 'lap-stock-inventory-barang/get-list-data',
            'method' => 'GET',
            'payload' => [
                'query' => $filter
            ]
        ]);

        $no = $request->get('start',1);
        foreach ($response['data'] as $key => $value) {
            $no++;
            $value['rowNum'] = $no;
            $value['harga_konversi'] = DocoHelpers::rupiahDisplay(ArrayHelper::getValue($value,'harga_konversi',null));
            $value['harga_netto'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value,'harga_netto',null));
            $value['harga'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value,'harga',null));
            $value['total_harga'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value,'total_harga',null));
            $value['nilai_konv'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value,'nilai_konv',null));
            $value['qty_satuankecil'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value,'qty_satuankecil',null));
            $value['qty_satuanbesar'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value,'qty_satuanbesar',null));
            $value['baseprice_kecil'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value,'baseprice_kecil',null));
            $value['baseprice_besar'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value,'baseprice_besar',null));
            $value['total_satuankecil'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value,'total_satuankecil',null));
            $value['total_satuanbesar'] = DocoHelpers::formatNumber(ArrayHelper::getValue($value,'total_satuanbesar',null));
            $value['tanggal_inventory'] = isset($value['tanggal_inventory']) ? date('Y-m-d', strtotime($value['tanggal_inventory'])) : '';
            $data[$key] = $value;
        }

        $result['data'] = $data;
        $result['recordsTotal'] = $response['_meta']['totalCount'];
        $result['recordsFiltered'] = $response['_meta']['totalCount'];
        return $result;

    }
}
