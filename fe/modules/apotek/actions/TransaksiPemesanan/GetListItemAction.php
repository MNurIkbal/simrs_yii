<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\TransaksiPemesanan;

use Yii;
use yii\base\Action;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class GetListItemAction extends Action {
    protected $_module = '/apotek/transaksi-pemesanan';

    public function run() {
        $request = Yii::$app->request;

        $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");
        $cacheObatAlkes = Yii::$app->cache->get("pemesanan-obat-" . $id_pegawai . $ruangan_id);
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = [];
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $konvSatuan = Yii::$app->cache->get('konvert-satuan');
        if ($cacheObatAlkes !== false) {
            $no = $request->get('start', 0);
            foreach ($cacheObatAlkes as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($key);
                $data[] = [
                    'rowNum' => $no,
                    'nama_obat' => $value['text'],
                    'qty_besar' => $value['qty_besar']." ".$value['satuanbesar_nama'],
                    'satuan_besar' => $value['satuanbesar_nama'],
                    'qty_kecil' => $value['qty_kecil']." ".$value['satuankecil_nama'],
                    'satuan_kecil' => $value['satuankecil_nama'],
                    'aksi' => Html::button(
                        "<i class='fa fa-trash'></i>",
                        [
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'action' => Url::to([$this->_module . '/delete-cache', 'id' => $primaryKey]),
                        ]
                    )
                ];
            }
            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);
        }

        return DocoHelpers::response($result);
    }
}