<?php

namespace app\modules\v1\businessLogic;

/**
** @author yaya
**/

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\FormulirStokOpname as FormulirSO;
use app\modules\v1\models\FormStokOpname;
use app\modules\v1\models\FormulirStokOpname as Model;
use app\modules\v1\models\FormStokOpnameR;
use app\modules\v1\models\FormulirStokOpname as ModelsFormulirStokOpname;
use app\modules\v1\models\StokOpnameDetail;
use Doco\components\BatchUpdate;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class FormulirStokOpname
{

    /**
    * @var $data harus array [data asal dari infostokobatdetail_v yang sudah di filter]
    * @return array
    **/
    public static function excecute(array $data)
    {
        $request = Yii::$app->request;
        $totalVolume = $totalHarga = 0;
        $insertDetail = [];
        $namaRuangan = '';
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            foreach ($data as $value) {
                $stokSistem = $value['stok_sistem'];
                $hargaNetto = $value['harganetto'] ? $value['harganetto'] : 0;
                $totalVolume += $stokSistem;
                $totalHarga += $stokSistem * $hargaNetto;
                $namaRuangan = $value['ruangan_nama'];
                $row = [
                    'obatalkes_id' => $value['obatalkes_id'],
                    'formulirstokopname_id' => '',
                    'volume_stok' => $value['stok_sistem'],
                    'ruangan_id' => $value['ruangan_id'],
                    'nobatch' => isset($value['nobatch']) ? $value['nobatch'] : null,
                    'stokobatalkes_id' => @$value['id_stok'],
                    'tglkadaluarsa' => isset($value['tglkadaluarsa']) ? $value['tglkadaluarsa'] : null
                ];
                $insertDetail[] = $row;
            }
            if (!$insertDetail) {
                return [
                    'text' => 'Data tidak ada',
                    'status' => 422
                ];
            }
            $parent = [
                'tglformulir' => date('Y-m-d H:i:s'),
                'totalvolume' => $totalVolume,
                'totalharga' => $totalHarga,
                'ruangan_id' => $request->get('ruangan_id')
            ];
            $stokOpname = new Model;
            $stokOpname->attributes = $parent;
            
            if ($stokOpname->save()) {
                $idParent = $stokOpname->formulirstokopname_id;
                foreach ($insertDetail as $key => $value) {
                    $insertDetail[$key]['formulirstokopname_id'] = $idParent;
                }
                $result = FormStokOpname::batchInsert($insertDetail, false);
                $transaction->commit();
                $getModel = Model::find()->where([
                    'formulirstokopname_id' => $idParent
                ])->one();
                $periode = isset($_GET['periode_stok']) ? $_GET['periode_stok'] : '';
                return [
                    'id_parent' => DocoHelpers::encrypt($idParent),
                    'periode' => $periode,
                    'ruangan_nama' => $namaRuangan,
                    'no_formulir' => $getModel->noformulir,
                    'messages' => 'success',
                    'data' => $data
                ];
            } else {
                return [
                    'data' => $stokOpname->errors,
                    'status' => 422
                ];
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'messages' => $e->getMessage(),
                'status' => 500
            ];
        }
    }

    /**
     *
     * @author : metafiliana
     * fungsi update formulir SO, insert temp detail formulir SO, delete detail formulir SO, insert detail formulir SO
     * @param $formulirstokopname_id -> formulirstokopname_id yang akan di olah
     * @param $stokopname_id -> stokopname_id yang akan di olah
     *
     */
    public static function executeFormulirStokOpname($formulirstokopname_id, $stokopname_id)
    {
        if ((!$formulirstokopname_id) || (!$stokopname_id)) return true;

        // update formulir stokopname
        Yii::$app->db->createCommand("
            UPDATE formulirstokopname_t SET stokopname_id = {$stokopname_id} 
            WHERE formulirstokopname_id = {$formulirstokopname_id} 
            AND is_deleted = false
        ")->execute();

        // select detail so
        $data_sodetail = Yii::$app->db->createCommand("
            SELECT 
                stokopnamedetail_id,
                formstokopname_id
            FROM 
                stokopnamedetail_t
            WHERE 
                stokopname_id = {$stokopname_id} AND
                is_deleted = FALSE
        ")->queryAll();

        // select detail formulir
        $data_formulirdetail = Yii::$app->db->createCommand("
            SELECT 
                formstokopname_id,
                stokopnamedetail_id,
                obatalkes_id,
                formulirstokopname_id,
                volume_stok,
                periodestok_id,
                ruangan_id,
                nobatch
            FROM 
                formstokopname_t
            WHERE 
                formulirstokopname_id = {$formulirstokopname_id} AND
                is_deleted = FALSE
        ")->queryAll();

        // prepare data formstokopname
        $idDelete = [];
        foreach ($data_formulirdetail as $k_fd => $v_fd) {
            foreach ($data_sodetail as $k_sod => $v_sod) {
                if ($v_sod['formstokopname_id'] == $v_fd['formstokopname_id']){
                    $data_formulirdetail[$k_fd]['stokopnamedetail_id'] = $v_sod['stokopnamedetail_id'];
                    $idDelete[] = $v_sod['formstokopname_id'];
                }
            }
        }

        if ($idDelete){
            // insert formstokopname temp -- formstokopname_r
            FormStokOpnameR::batchInsert($data_formulirdetail, false);

            // hard delete formstokopname
            $inCondition = "(" . implode(",", $idDelete) . ")";
            Yii::$app->db->createCommand("
                DELETE FROM formstokopname_t
                WHERE formstokopname_id IN {$inCondition};
            ")->execute();

            // insert new formstokopname_t
            FormStokOpname::batchInsert($data_formulirdetail, false);
        }

        return true;
    }

    public static function updateFormulirStokOpname($formulirstokopname_id, $stokopname_id) {
        $updateFormulir = FormulirSO::updateAll(
            ['stokopname_id' => $stokopname_id],
            ['and',
                ['formulirstokopname_id' => $formulirstokopname_id],
                ['is_deleted' => false]
            ]
        );
        if(!$updateFormulir) throw new \Exception("Gagal update formulirstokopname_t");

        (new BatchUpdate(FormStokOpname::tableName(), function($query) use ($formulirstokopname_id, $stokopname_id) {
            // $list = FormStokOpname::find()->where(['formulirstokopname_id' => $formulirstokopname_id])->asArray()->all();
            $list = StokOpnameDetail::find()->where(['stokopname_id' => $stokopname_id])->asArray()->all();
            $list = ArrayHelper::index($list, 'formstokopname_id');
            $listKey = [];
            foreach ($list as $key => $value) {
                $query->set([
                    'stokopnamedetail_id' => $value['stokopnamedetail_id']
                ], "formstokopname_id = {$key}", $key);
                array_push($listKey, $key);
            }
            if(count($listKey) > 0){
                $id = implode(", ", $listKey);
                $query->where("formstokopname_id in({$id})");
            }
        }
        ))->execute();
    }
}
