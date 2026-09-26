<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\TransaksiResep;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;

use SirsCore\features\FeatureTindakanBmhp;
use app\modules\v1\businessLogic\ValidasiStok;
use app\modules\v1\entities\PenjualanResep;
use app\modules\v1\entities\ObatAlkesPasien;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\entities\Resep;
use Doco\components\DocoConstants;

class ApproveResepAction extends Action {
    public function run($id) {
        $inputListObat = Yii::$app->request->post('listObat',[]);
        $inputHeader = [
            'biaya_administrasi' => Yii::$app->request->post('biayaadministrasi'),
            'biayaadministrasi' => Yii::$app->request->post('biayaadministrasi'),
            'totharganetto' => Yii::$app->request->post('totalharga_netto'),
            'totalhargajual' => Yii::$app->request->post('totalharga_jual'),
            'ruangan_id' => Yii::$app->request->post('ruangan_id'),
            'status_reseptur' => DocoConstants::RESEPTUR_SUDAH_DIPROSES,
            'tgl_approve' => date('Y-m-d H:i:s'),
            'pegawai_approve_id' => Yii::$app->user->identity->pegawai_id
        ];
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $resep = (new Resep)->loadById($id);
            $resep->deleteOrAddDetail($inputListObat, $inputHeader);
            $resep->updateTagihan($id, $inputHeader);

            $obatAlkesPasien = (new ObatAlkesPasien);
            $payload = $obatAlkesPasien->getObatAlkesPasien($id);
            $potongStok = FeatureTindakanBmhp::stokObatAlkes($payload, false);
            ValidasiStok::serahkan($payload);
            
            if(is_array($potongStok)) {
                \Yii::$app->response->statusCode = 422;
                $obat = ObatAlkes::find()->select(['obatalkes_id', 'obatalkes_nama'])->where(['obatalkes_id' => current($potongStok)])->one();
                $obat_nama = ArrayHelper::getValue($obat,'obatalkes_nama');
                $transaction->rollBack();
                return [
                    'status' => 422,
                    'message' => 'Gagal potong stok obat',
                    'text' => "Stok obat {$obat_nama} tidak mencukupi",
                    'list_obat_tidak_cukup' => $potongStok
                ];
            }

            if(!$potongStok){
                \Yii::$app->response->statusCode = 422;
                $transaction->rollBack();
                return [
                    'status' => 422,
                    'message' => 'Gagal potong stok obat',
                    'text' => 'Stok obat tidak mencukupi',
                    'data' => $potongStok
                ];
            }

            if(\Yii::$app->response->statusCode == 200) {
                \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
                $transaction->commit();
                return ['data'=>[],'message'=>'Berhasil approve resep'];
            } else {
                return ['data'=>[],'message'=>'Gagal approve reseptur'];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
        } catch (\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 422;
            return [
                'text' => $e->getMessage(),
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ];
        }
    }
}