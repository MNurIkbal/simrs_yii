<?php

/**
 * @Author: Sunarko
 * @Date:   2018-07-13 11:44:21
 * @Last Modified by:
 * @Last Modified time:
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\VisiteDokterView;
use app\modules\v1\models\InfoTarifRs;
use app\modules\v1\models\TindakanKomponen;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\Cppt;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;

class TraVisiteDokterController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\VisiteDokterView';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;

        $model = new VisiteDokterView;
        $query = $model::find();
        
        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_cppt'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_cppt']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_cppt']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        $query->andWhere(['between', 'tgl_cppt', $start, $end]);
        $query->andWhere(['is_visitedokter' => false]);

        if ($_GET['ruangan_id']) {
            $ruangan_id = $_GET['ruangan_id'];
            $query->andWhere(['ruangan_id' => $ruangan_id]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
        
    }

    public function actionGetJenisVisite()
    {
        try{
            $request = Yii::$app->request;
            $id = $request->get('id');
            
            $modelV = new VisiteDokterView;
            $queryV = $modelV::find()->where(['cppt_id'=>$id])->asArray()->one();

            $kelaspelayanan_id = $queryV['kelaspelayanan_id'];
            $ruangan_id = $queryV['ruangan_id'];
            $penjamin_id = $queryV['penjamin_id'];
            $komponentarif_id = 6;
            $kelompoktindakan_id = 32;
            
            $model = new InfoTarifRs;
            $query = $model::find();
            $query->andWhere(['komponentarif_id' => $komponentarif_id]);
            $query->andWhere(['penjamin_id' => $penjamin_id]);
            $query->andWhere(['kelaspelayanan_id' => $kelaspelayanan_id]);
            $query->andWhere(['kelompoktindakan_id' => $kelompoktindakan_id]);
            $query->andWhere(['ruangan_id' => $ruangan_id]);
            $result = $query->asArray()->all();

            return ['data' =>$result];
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionOnvisiteDokter()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        $request = Yii::$app->request;
        $modelPelayanan = new TindakanPelayanan;
        $modelKomponen = new TindakanKomponen;
        $modelInfo = new InfoTarifRs;

        $data_tindakankomponen = [];
        try {
            $id = $request->get('id');
            $daftartindakan_id = $request->post('daftartindakan_id');
            $dokterpenanggungjawab_id = $request->post('pegawai_id');
            $modelCppt = Cppt::findOne($id);
            //get data pasien di visite
            $dataVisite = VisiteDokterView::find()->where(['cppt_id'=>$id])->asArray()->one();
            //get data info tarif 
            $kelaspelayanan_id = $dataVisite['kelaspelayanan_id'];
            $ruangan_id = $dataVisite['ruangan_id'];
            $penjamin_id = $dataVisite['penjamin_id'];
            $komponentarif_id = 6;
            $kelompoktindakan_id = 32;
            $dataInfo = InfoTarifRs::find()->where([
                'ruangan_id'=>$ruangan_id,
                'komponentarif_id' => $komponentarif_id,
                'penjamin_id' => $penjamin_id,
                'kelaspelayanan_id' => $kelaspelayanan_id,
                'kelompoktindakan_id' => $kelompoktindakan_id,
            ])->asArray()->one();
            //set data tindakan pelayanan
            $modelPelayanan->kelaspelayanan_id = $kelaspelayanan_id;
            $modelPelayanan->pasien_id = $dataVisite['pasien_id'];
            $modelPelayanan->instalasi_id = $dataInfo['instalasi_id']; 
            $modelPelayanan->carabayar_id = $dataVisite['carabayar_id'];
            $modelPelayanan->pasienadmisi_id = $dataVisite['pasienadmisi_id'];
            $modelPelayanan->pendaftaran_id = $dataVisite['pendaftaran_id'];
            $modelPelayanan->jeniskasuspenyakit_id = $dataVisite['jeniskasuspenyakit_id'];
            $modelPelayanan->ruangan_id = $ruangan_id;
            $modelPelayanan->penjamin_id = $penjamin_id;
            $modelPelayanan->tgl_tindakan = $dataVisite['tgl_cppt'];
            $modelPelayanan->tarif_satuan = $dataInfo['harga_tariftindakan'];
            $modelPelayanan->qty_tindakan = 1;
            $modelPelayanan->tarif_tindakan = $modelPelayanan->tarif_satuan*$modelPelayanan->qty_tindakan;
            $modelPelayanan->tarifcyto_tindakan = "0";
            $modelPelayanan->cyto_tindakan = false;
            $modelPelayanan->discount_tindakan = "0";
            $modelPelayanan->satuan_tindakan = DocoConstants::VAR_ST;
            $modelPelayanan->daftartindakan_id = $daftartindakan_id;
            $modelPelayanan->dokterpenanggungjawab_id = $dokterpenanggungjawab_id;
            //insert ke tindakan pelayanan_t
            if (!$modelPelayanan->save()) {
                $transaction->rollBack();
                $errors = DocoHelpers::parseError($modelPelayanan->errors,'TindakanPelayanan');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
            //set data and update status cppt sudah visite dokter
            $modelCppt['is_visitedokter'] = true;
            $modelCppt['tindakanvisite_id'] = $modelPelayanan['tindakanpelayanan_id'];
            // print_r($modelCppt->attributes); die; 
            if (!$modelCppt->save()) {
                $transaction->rollBack();
                $errors = DocoHelpers::parseError($modelCppt->errors,'Cppt');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
            //insert tindakankomponen_t
            $query = InfoTarifRs::find()->where([
                'ruangan_id'=>$ruangan_id,
                'penjamin_id' => $penjamin_id,
                'kelaspelayanan_id' => $kelaspelayanan_id,
                'kelompoktindakan_id' => $kelompoktindakan_id,
            ]);
            $query->andWhere(['not in', 'komponentarif_id', ['6']]);
            $query = DocoRestActiveFilter::advancedFilter($modelInfo, $query);
            
            $dataInfotarif = $query->asArray()->all();
            // return $dataInfotarif;
            $counter = 0;
            foreach ($dataInfotarif as $key => $value) {
                $data_tindakankomponen[$counter]['tindakanpelayanan_id'] = $value['daftartindakan_id'];
                $data_tindakankomponen[$counter]['komponentarif_id'] = $value['komponentarif_id'];
                $data_tindakankomponen[$counter]['tarif_kompsatuan'] = $value['harga_tariftindakan'];
                $data_tindakankomponen[$counter]['tarif_tindakankomp'] = $value['harga_tariftindakan']*1;
                $data_tindakankomponen[$counter]['tarifcyto_tindakankomp'] = 0;
                $data_tindakankomponen[$counter]['subsidiasuransikomp'] = 0;
                $data_tindakankomponen[$counter]['subsidipemerintahkomp'] = 0;
                $data_tindakankomponen[$counter]['subsidirumahsakitkomp'] = 0;
                $data_tindakankomponen[$counter]['iurbiayakomp'] = 0;
                $counter++;
            }
            $model = TindakanKomponen::batchInsert($data_tindakankomponen);
            $transaction->commit();
            return ['message' => 'Data Berhasil di simpan'];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

}