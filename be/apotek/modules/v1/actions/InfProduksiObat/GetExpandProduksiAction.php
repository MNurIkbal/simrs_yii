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
use app\modules\v1\models\InfoProduksiObatAlkesDetailView;
use app\modules\v1\models\InfoProduksiObatAlkesView;
use app\modules\v1\models\PemesananProduksiObat;

class GetExpandProduksiAction extends Action {
    public function run()
    {
        try {
            $request = Yii::$app->request;
            $id = $request->post('id', null);
            $model = new InfoProduksiObatAlkesBahanBakuView;
            $query = $model::find();
            $bahan_baku = $query->where(['produksiobatalkesdetail_id' => $id])->asArray()->all();
            $detailProduksi = InfoProduksiObatAlkesDetailView::find()->where(['produksiobatalkesdetail_id' => $id])->asArray()->one();
            $produksiobatalkesId = $detailProduksi['produksiobatalkes_id'];
            $produksi = InfoProduksiObatAlkesView::find()->where(['produksiobatalkes_id' => $produksiobatalkesId])->asArray()->one();
            $ruanganId = isset($produksi['ruangan_pemesanan_id']) ? $produksi['ruangan_pemesanan_id'] : null;

            // cek ketersediaan obat
            $listObatId = ArrayHelper::getColumn($bahan_baku,'obatalkes_id');
            $listObatAlkesToString = implode(",", $listObatId);
            $getStok = $this->cekKetersediaan($ruanganId,$listObatAlkesToString,$produksiobatalkesId);

            return [
                'bahan_baku' => $bahan_baku,
                'ketersediaan' => $getStok
            ];
        } catch (\Yii\db\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e){
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function cekKetersediaan($ruangan,$listObatAlkesToString,$produksiobatalkesId) {
        $Ketersediaan =  Yii::$app->db->createCommand("
                SELECT infoproduksiobatalkesbahanbaku_v.obatalkes_id,
                sum(infoproduksiobatalkesbahanbaku_v.qty_obat),
                CASE
                    WHEN ketersediaanobat.qty_tersedia < sum(infoproduksiobatalkesbahanbaku_v.qty_obat) THEN '#f4baba'::text
                    ELSE '#ffff'
                END AS warna,
                ketersediaanobat.qty_tersedia
                from infoproduksiobatalkesbahanbaku_v
                JOIN (select * from fgetketersediaanobat({$ruangan},'{$listObatAlkesToString}') )ketersediaanobat on ketersediaanobat.obatalkes_id = infoproduksiobatalkesbahanbaku_v.obatalkes_id
                WHERE produksiobatalkes_id = $produksiobatalkesId
                GROUP BY infoproduksiobatalkesbahanbaku_v.obatalkes_id,ketersediaanobat.qty_tersedia
            ")->queryAll();

        return $Ketersediaan;
    }
}