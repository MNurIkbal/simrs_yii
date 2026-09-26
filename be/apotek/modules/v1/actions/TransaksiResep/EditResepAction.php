<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\TransaksiResep;

use Yii;
use yii\base\Action;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use GuzzleHttp\Exception\RequestException;

use app\modules\v1\entities\Resep;
use app\modules\v1\entities\ResepturDetail;
use app\modules\v1\entities\PenjualanResep;
use app\modules\v1\entities\ObatAlkesPasien;
use app\modules\v1\models\InfoResepView;
use yii\helpers\ArrayHelper;

class EditResepAction extends Action {
    public function run($id) {
        $inputListObat = Yii::$app->request->post('listObat',[]);
        $inputHeader = [
            'biaya_administrasi'    => Yii::$app->request->post('biayaadministrasi'),
            'biayaadministrasi'     => Yii::$app->request->post('biayaadministrasi'),
            'totharganetto'         => Yii::$app->request->post('totalharga_netto'),
            'totalhargajual'        => Yii::$app->request->post('totalharga_jual'),
            'ruangan_id'            => Yii::$app->request->post('ruangan_id'),
        ];

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $info_resep = Yii::$app->db->createCommand(
                "SELECT status_reseptur, status_bayar
                FROM penjualanresep_t
                WHERE penjualanresep_id =:penjualanresep_id"
            )->bindValue(':penjualanresep_id',$id)->queryOne();
            
            $status_reseptur = ArrayHelper::getValue($info_resep, 'status_reseptur');
            $status_bayar = ArrayHelper::getValue($info_resep, 'status_bayar');

            if ($status_reseptur == DocoConstants::RESEPTUR_DISERAHKAN || $status_bayar == DocoConstants::LUNAS || $status_reseptur == DocoConstants::RESEPTUR_DIBATALKAN) {
                \Yii::$app->response->statusCode = 422;
                return ['data'=>['status' => 'sudah_diserahkan'],'message'=>'Gagal edit reseptur, reseptur sudah dibayar, diserahkan atau dibatalkan.'];
            }

            $resep = (new Resep)->loadById($id);
            $updateObat = $resep->deleteOrAddDetail($inputListObat, $inputHeader);
            $resep->updateTagihan($id, $inputHeader);

            if(\Yii::$app->response->statusCode == 200) {
                $transaction->commit();
                \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
                return ['data'=>[],'message'=>'Berhasil'];
            } else {
                return ['data'=>[],'message'=>'Gagal edit resep'];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}