<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Stock;

use Yii;
use yii\base\Action;

use app\modules\v1\models\StokObatAlkes;
use SirsCore\businessLogic\StokObatAlkes as BLStokObatAlkes;
use app\modules\v1\businessLogic\BusinessLogicStockOut as BLStockOut;

class StockOutAction extends Action {
    public function run() {
        try{
            $request = Yii::$app->request;
            $details = $request->post('details',[]);
            $ruangan_id = $request->post('ruangan_id',0);
            $detailTrans = $detailTransWithExpire = [];
            foreach ($details as $detail) {
                if(isset($detail['tglkadaluarsa']) && !empty($detail['tglkadaluarsa'])){
                    $detailTransWithExpire[] = $detail;
                }else{
                    $detailTrans[] = [
                        'obatalkes_id' => $detail['obatalkes_id'],
                        'qty_satuanpakai' => $detail['qty'],
                        'satuankecil_id' => isset($detail['satuan_id']) ? $detail['satuan_id'] : null,
                        'obatalkespasien_id' => isset($detail['obatalkespasien_id']) ? $detail['obatalkespasien_id'] : null,
                        'adjusmenobatkeluar_id' => isset($detail['adjusmenobatkeluar_id']) ? $detail['adjusmenobatkeluar_id'] : null
                    ];
                }
            }

            if(count($detailTransWithExpire)>0){
                $cutByExpire = $this->cutStockByExpire($detailTransWithExpire,date('Y-m-d'),$ruangan_id);
            }

            if(count($detailTrans)>0){
                $cutByConfig = $this->cutStockByConfig($detailTrans,date('Y-m-d'),$ruangan_id);
            }

            return [
                'error' => false
            ];
        }catch(\Exception $e){
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    private function cutStockByConfig($detail,$tgl_transaksi,$ruangan_id)
    {
        $_POST['ruangan_id'] = $ruangan_id;
    	$tanggalBerlaku = date('Y-m-d');

        $konfig = Yii::$app->db->createCommand("
            SELECT metodeantrian FROM konfigfarmasi_k
            WHERE tglberlaku >= '{$tanggalBerlaku}'
            AND konfigfarmasi_aktif = true
            AND is_active = true
        ")->queryOne();

        // Mencari Metode dengan nilai default FEFO
        $currentMetode = BLStokObatAlkes::FEFO;
        if ($konfig) {
            $currentMetode = isset($konfig['metodeantrian'])
                                ? strtoupper($konfig['metodeantrian']) : BLStokObatAlkes::FEFO;
        }

        if ($currentMetode === BLStokObatAlkes::FEFO) {
           $res = BLStokObatAlkes::methodeFEFO($detail,$tgl_transaksi);
        } else {
           $res = BLStokObatAlkes::methodeFIFO($detail,$tgl_transaksi);
        }

        return $res;
    }

    private function cutStockByExpire($detail,$tgl_transaksi,$ruangan_id)
    {
        return BLStockOut::expire($detail,$tgl_transaksi,$ruangan_id);
    }
}