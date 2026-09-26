<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfFormulir;

use Yii;
use yii\base\Action;
use Doco\components\DocoHelpers;
use app\modules\v1\models\FormulirStokOpname;
use app\modules\v1\models\StokOpname;
use app\modules\v1\models\StokOpnameDetail;
use app\modules\v1\models\DetailFormulirStokOpnameView;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\businessLogic\FormulirStokOpname as BL_FSO;
use SirsCore\businessLogic\StokObatAlkes as BL_SOA;
use SirsCore\features\IntegrasiAkunting;
use app\components\ApotekComponent;

class CreateStokOpnameAction extends Action {
    public function run() {
        try {
            $request = Yii::$app->request;
            $data = $request->post();
            $is_stokawal = $data['is_stokawal'];

            if($is_stokawal){
                //temporary disabled feature SO stok awal
                //11 Juli 2020 MHKN
                $return = $this->stokAwal();
            } else {
                $return = $this->penyesuaian($data);
            }

            return $return;
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $this->controller->logError($e);
            return $this->responseJson(500,DocoMessages::KEY_ERR_CUSTOM,['text'=>$e->getMessage(), 'file' => $e->getFile()]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->controller->logError($e);
            return $this->responseJson(500,DocoMessages::KEY_ERR_CUSTOM,['text'=>$e->getMessage(), 'file' => $e->getFile()]);
        }
    }

    public function penyesuaian($data) {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        $stok_opname = StokOpname::find()
            ->where(['formulirstokopname_id' => $data['formulirstokopname_id']])
            ->asArray()
            ->one();

        if($stok_opname != null) {
            $result = $this->update($stok_opname, $data, $transaction);
        } else {
            $result = $this->create($data, $transaction);
        }

        return $result;
    }

    private function create($data, $transaction)
    {
        $data_stokopname = $data['data_stokopname'];
        $data_detailstokopname = isset($data['data_detailstokopname']) ? $data['data_detailstokopname'] : [];
        $ruangan_id = isset($data['ruangan_id']) ? $data['ruangan_id'] : null;
        $formulirstokopname_id = $data['formulirstokopname_id'];
        $formulir_so = FormulirStokOpname::findOne($formulirstokopname_id);

        $modelStokOpname = new StokOpname;
        $modelStokOpname->attributes = $data_stokopname;
        $modelStokOpname->tglstokopname = date('Y-m-d H:i:s');
        $modelStokOpname->formulirstokopname_id = $formulirstokopname_id;
        $modelStokOpname->ruangan_id = $formulir_so->ruangan_id;
        $modelStokOpname->ruanganinput_id = $ruangan_id;
        if ($modelStokOpname->save()) {
            $idParent = $modelStokOpname->stokopname_id;

            $modelStokOpname->is_verifikasi = false;
            $modelStokOpname->update();
            // prepare data detail SO
            $formstokopname_ids = array_keys($data_detailstokopname);
            $detail_formstokopname = DetailFormulirStokOpnameView::find()->where([
                                        'formulirstokopname_id' => $formulirstokopname_id
                                    ])->asArray()->all();
            $list_columns = [];

            foreach ($detail_formstokopname as $value) {
                $formstokopname_id = $value['formstokopname_id'];
                if(in_array($formstokopname_id, $formstokopname_ids)) {
                    $volume_fisik = $data_detailstokopname[$formstokopname_id]['volume_fisik'];
                    $kondisibarang = 9999;
                } else {
                    $volume_fisik = $value['stok_sistem'];
                    $kondisibarang = 9999;
                }

                $list_columns[] = [
                    'formstokopname_id' => $formstokopname_id,
                    'stokopname_id' => $modelStokOpname->stokopname_id,
                    'obatalkes_id' => $value['obatalkes_id'],
                    'hargasatuan' => $value['hargajual'],
                    'harganetto' => $value['harganetto'],
                    'tglkadaluarsa' => $value['tglkadaluarsa'],
                    'volume_sistem' => $value['stok_sistem'],
                    'volume_fisik' => $volume_fisik == "" ? null : floatval($volume_fisik),
                    'kondisibarang' => $kondisibarang,
                    'jumlahharga' => abs($value['hargajual'] * $volume_fisik),
                    'jumlahnetto' => abs($value['harganetto'] * $volume_fisik),
                    'jmlselisihstok' => abs($volume_fisik - $value['stok_sistem'])
                ];
            }

            // Insert SO detail
            StokOpnameDetail::batchInsert($list_columns, false);

            $konfig = KonfigFarmasi::find()->one();
            if(property_exists($konfig, 'is_verifstokopname') && $konfig->is_verifstokopname == FALSE){
                // update stokobatalkes_t
                BL_SOA::updateStokObatAlkes($idParent, $ruangan_id, false);
            }

            // manipulate formulirstokopname & formstokopname
            BL_FSO::executeFormulirStokOpname($formulirstokopname_id, $modelStokOpname->stokopname_id);
            $getSo = StokOpname::findOne($idParent);
            $noStok = isset($getSo['nostokopname']) ? $getSo['nostokopname'] : '';

            $return = [
                'title' => 'Proses Berhasil!',
                'text'  => 'Data Berhasil di simpan',
                'id'    => DocoHelpers::encrypt($idParent),
                'nomor' => $noStok
            ];
            $transaction->commit();
            IntegrasiAkunting::integrateStokOpname($noStok);
        } else {
            $errors = DocoHelpers::parseError($modelStokOpname->errors, 'StokOpnameForm');
            $return = [
                'data' => $errors,
                'message' => $errors,
                'status' => 422
            ];
            $transaction->rollBack();
        }

        return $return;
    }

    private function update($stok_opname, $data, $transaction) {
        try {
            $updateIndex = 0;
            $dataUpdate = $updateCondition = [];

            foreach ($data['data_detailstokopname'] as $key => $value) {
                $arrIndex[$updateIndex] = (int) $key;
                $arrStokRevisi[$updateIndex] = $value['stok_revisi'] == "" ? null : floatVal($value['stok_revisi']);
                
                $dataUpdate = ['revisi_stok' => $arrStokRevisi];
                $updateCondition = ['stokopnamedetail_id' => $arrIndex];

                $updateIndex++;
            }

            ApotekComponent::updateMultiple('stokopnamedetail_t', $dataUpdate, $updateCondition);
            $transaction->commit();

            $return = [
                'title' => 'Proses Berhasil!',
                'text'  => 'Data Berhasil di update',
                'id'    => DocoHelpers::encrypt($stok_opname['stokopname_id']),
                'nomor' => $stok_opname['nostokopname']
            ];

            return $return;
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch(\Exception $e){
            $transaction->rollBack();
            Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function stokAwal() 
    {
        \Yii::$app->response->statusCode = 500;
        $return = [
            'data' => [],
            'message' => 'Tidak Dapat Dilanjutkan',
            'text' => 'Tidak Dapat Dilanjutkan',
            'status' => 422
        ];
    }
}
