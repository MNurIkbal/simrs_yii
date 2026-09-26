<?php

namespace app\modules\v1\actions\InfProduksiObat;

use Yii;
use yii\base\Action;
use Doco\components\DocoSpout;
use Doco\components\DocoHelpers;
use yii\data\ActiveDataProvider;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\InfoProduksiObatAlkesBahanBakuView;
use app\modules\v1\models\InfoProduksiObatAlkesView;
use app\modules\v1\models\PemesananProduksiObat;

class CekKetersediaanAction extends Action {
    public function run()
    {
        try {
            $request = Yii::$app->request;
            $produksiobatalkesId = $request->get('id', null);
            $bahan_baku = InfoProduksiObatAlkesBahanBakuView::find()->where(['produksiobatalkes_id' => $produksiobatalkesId])->asArray()->all();
            $produksi = InfoProduksiObatAlkesView::find()->where(['produksiobatalkes_id' => $produksiobatalkesId])->asArray()->one();
            $ruanganId = isset($produksi['ruangan_pemesanan_id']) ? $produksi['ruangan_pemesanan_id'] : null;
            // cek ketersediaan obat
            $listObatId = ArrayHelper::getColumn($bahan_baku,'obatalkes_id');
            $listObatAlkesToString = implode(",", $listObatId);
            $getStok = $this->cekKetersediaan($ruanganId,$listObatAlkesToString,$produksiobatalkesId);
            $countKetersediaan = 0;
            $detail = [];
            foreach($getStok  as $value){
                if($value['ketersediaan'] == true){
                    $countKetersediaan ++;
                }
                
                foreach($bahan_baku as $bahan){
                    if($bahan['obatalkes_id'] == $value['obatalkes_id'] && $value['ketersediaan'] == true){
                        $detail[] = $bahan['produksiobatalkesdetail_id'];
                    }
                }
            }
            $detailKetersediaan = $detail;

            return [
                'ketersediaan' => $getStok,
                'obatTidakTersedia' => $countKetersediaan,
                'detailKetersediaan' => $detailKetersediaan
            ];
        } catch (\Yii\db\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e){
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function cekKetersediaan($ruangan,$listObatAlkesToString,$produksiobatalkesId) {
            $Ketersediaan =  Yii::$app->db->createCommand("
                SELECT
                    infoproduksiobatalkesbahanbaku_v.obatalkes_id,
                    infoproduksiobatalkesbahanbaku_v.produksiobatalkes_id,
                    SUM ( infoproduksiobatalkesbahanbaku_v.qty_obat ) as qty_transaksi,
                CASE
                    WHEN ketersediaanobat.qty_tersedia < SUM ( infoproduksiobatalkesbahanbaku_v.qty_obat ) THEN
                    '#f4baba' :: TEXT ELSE'#ffff'
                    END AS warna,
                CASE
                    WHEN ketersediaanobat.qty_tersedia < SUM ( infoproduksiobatalkesbahanbaku_v.qty_obat ) THEN
                    TRUE ELSE FALSE 
                    END AS ketersediaan,
                    ketersediaanobat.qty_tersedia
                FROM
                    infoproduksiobatalkesbahanbaku_v
                    JOIN ( SELECT * FROM fgetketersediaanobat ({$ruangan},'{$listObatAlkesToString}')) ketersediaanobat ON ketersediaanobat.obatalkes_id = infoproduksiobatalkesbahanbaku_v.obatalkes_id 
                WHERE
                    produksiobatalkes_id = $produksiobatalkesId
                GROUP BY
                    infoproduksiobatalkesbahanbaku_v.obatalkes_id,
                    ketersediaanobat.qty_tersedia,
                infoproduksiobatalkesbahanbaku_v.produksiobatalkes_id
            ")->queryAll();

        return $Ketersediaan;
    }
}