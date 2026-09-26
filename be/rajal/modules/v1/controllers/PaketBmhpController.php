<?php
/*
 * @Author: metafiliana 
 * @Date: 2018-01-23 13:25:08 
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-02-19 13:13:08
 * @Description: 
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\PaketBmhp;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\PaketBmhpView;

class PaketBmhpController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PaketBmhp';
    protected $_title = "Paket BMHP";

    public function verbs()
    {
        $verbs = parent::verbs();

        // additional/ override verbs
        $verbs['list-obat-alkes'] = ["GET", "POST"];
        $verbs['update'] = ["POST"];


        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        // unset default action
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        

        return $actions;
    }

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;

            // additional column group from relations
            $_GET['expand'] = $request->get('expand', 'daftartindakan_m,obatalkes_m');

            $model = new PaketBmhp;
            $query = $model::find()
                ->joinWith(['daftartindakan'])
                ->joinWith(['obatalkes']);

            // manual filter, for unsupported feature in advancedFilter 
            if(isset($_GET['advanced-filter'])){
                $filter = $_GET['advanced-filter'];

                if(isset($filter['daftartindakan_m.daftartindakan_nama'])){
                    $query->andFilterWhere(['ILIKE', 'daftartindakan_m.daftartindakan_nama', $filter['daftartindakan_m.daftartindakan_nama']]);
                }
                if(isset($filter['obatalkes_m.obatalkes_nama'])){
                    $query->andFilterWhere(['ILIKE', 'obatalkes_m.obatalkes_nama', $filter['obatalkes_m.obatalkes_nama']]);
                }
            }

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
        // $model = new PaketBmhp;
        // $query = $this->getData();
        // $query = DocoRestActiveFilter::advancedFilter($model, $query);
        // return $query->asArray()->all();
    }

    /**
    *
    * @see Fungsi get list data obat alkes untuk ajax request
    * @return array
    *
    */
    public function actionListObatAlkes()
    {
        try {
            $request = Yii::$app->request;
            $find = $this->getObatAlkes();

            $data = $find->asArray()->all();

            return [
                'data' => $data
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    *
    * @see Fungsi get list data daftar tindakan untuk ajax request
    * @return array
    *
    */
    public function actionListDaftarTindakan()
    {
        try {
            $request = Yii::$app->request;
            $find = $this->getDaftarTindakan();

            $data = $find->asArray()->all();

            return [
                'data' => $data
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    *
    * @see override action create
    * @return array, return message
    *
    */
    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            
            if ($data_post = $request->post()) {
                // init data
                $daftartindakan_id = $data_post["daftartindakan_id"];

                // get data exist
                $data_before = PaketBmhp::find()
                    ->select("obatalkes_id")
                    ->where([
                        "daftartindakan_id" => $daftartindakan_id,
                        "is_deleted" => 0,
                        ])->asArray()->all();

                // get data post
                $data_all = array_merge($data_before, $data_post["obatalkes_id"]);

                foreach ($data_all as $key => $value) {
                    // data di db = data di post
                    if (in_array($value, $data_before) && in_array($value, $data_post["obatalkes_id"])){
                        continue;
                    }

                    // data di db gaada di data post (ganti data)
                    if (in_array($value, $data_before) && !in_array($value, $data_post["obatalkes_id"])){
                        $modelPaketBmhp = PaketBmhp::find()->where([
                            "daftartindakan_id" => $daftartindakan_id,
                            "obatalkes_id" => $value,
                            "is_deleted" => 0,
                            ])->one();
                            $modelPaketBmhp->delete();
                            
                            continue;
                        }
                        
                    // data di db gaada di data post ada (data baru)
                    if (!in_array($value, $data_before) && in_array($value, $data_post["obatalkes_id"])){
                        $modelPaketBmhp = new PaketBmhp;
                        $modelPaketBmhp->daftartindakan_id = $daftartindakan_id;
                        $modelPaketBmhp->obatalkes_id = $value;
                        $modelPaketBmhp->save(false);

                        continue;
                    }
                }
                return ['message' => 'Data Berhasil di simpan'];
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    *
    * @see override action delete
    * @param $id = daftartindakan_id integer
    * @return array, return message
    *
    */
    public function actionDelete($id)
    {
        $data = $this->getData($id)->all();

        try{
            foreach ($data as $key => $value) {
                $value->delete();
            }

            return [
                'message' => 'sukses_hapus',
            ];
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    *
    * @see override action view
    * @param $id = daftartindakan_id integer
    * @return array, return message
    *
    */
    public function actionView($id)
    {
        try{
            $model = $this->getData($id)->asArray()->all();
            $temp_obatalkes = [];
            $data["daftartindakan_id"] = $id;
            foreach ($model as $key => $value) {
                $temp_obatalkes[] = $value["obatalkes_id"];
            }
            $data["obatalkes_id"] = $temp_obatalkes;

            if ($model && $data){
                return $data;
            }else{
                return [
                    'data' => 'data tidak ditemukan',
                    'status' => 422
                ];
            }
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    *
    * @see get data paket bmhp
    * @param $id = daftartindakan_id integer
    * @return array, return message
    *
    */
    private function getData($id = null)
    {
        $condition = [];
        $sql = "
                SELECT
                    paketbmhp_m.paketbmhp_id, 
                    paketbmhp_m.daftartindakan_id, 
                    paketbmhp_m.obatalkes_id, 
                    obatalkes_m.obatalkes_namalain as nama_obatalkes, 
                    daftartindakan_m.daftartindakan_nama as nama_daftartindakan 
                FROM
                    paketbmhp_m
                JOIN obatalkes_m ON obatalkes_m.obatalkes_id = paketbmhp_m.obatalkes_id
                JOIN daftartindakan_m ON daftartindakan_m.daftartindakan_id = paketbmhp_m.daftartindakan_id
                WHERE paketbmhp_m.is_deleted = FALSE
            ";

        // filter
        if ($id){
            $sql .= " AND paketbmhp_m.daftartindakan_id = :daftartindakan_id";
            $condition[':daftartindakan_id'] = $id;
        }

        $result = PaketBmhp::findBySql($sql, $condition);
        // var_dump($result->createCommand()->getRawSql());die; //dumping raw sql

        return $result;
    }

    /**
    *
    * @see Fungsi get data obat alkes
    * @return array, activeQueryRecords
    *
    */
    private function getObatAlkes()
    {
        $data = ObatAlkes::find()->select([
                "obatalkes_id",
                "obatalkes_namalain",
            ]);
        
        return $data;
    }

    /**
    *
    * @see Fungsi get data daftar tindakan
    * @return array, activeQueryRecords
    *
    */
    private function getDaftarTindakan()
    {
        $data = DaftarTindakan::find()->select([
                "daftartindakan_id",
                "daftartindakan_nama",
            ]);
        
        return $data;
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;

        try {
            $model = new PaketBmhpView;
            $query = $model::find(true);

            // manual filter, for unsupported feature in advancedFilter 
            if(isset($_GET['advanced-filter'])){
                $filter = $_GET['advanced-filter'];

                if(isset($filter['daftartindakan_m.daftartindakan_nama'])){
                    $query->andFilterWhere(['ILIKE', 'daftartindakan_nama', $filter['daftartindakan_m.daftartindakan_nama']]);
                }
                if(isset($filter['obatalkes_m.obatalkes_nama'])){
                    $query->andFilterWhere(['ILIKE', 'obatalkes_namalain', $filter['obatalkes_m.obatalkes_nama']]);
                }
            }

            $result = $query->all();
            $header = [];
            $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, array(
                    "uploadPath" => "./uploads"),[],[],true);

            $filePath->save('php://output');
            die;
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionExportPdf 
    * @attribute #table# => table data
    **/
    public function actionExportPdf()
    {
        try {
            $model = new PaketBmhpView;
            $query = $model::find(true);

            // manual filter, for unsupported feature in advancedFilter 
            if(isset($_GET['advanced-filter'])){
                $filter = $_GET['advanced-filter'];

                if(isset($filter['daftartindakan_m.daftartindakan_nama'])){
                    $query->andFilterWhere(['ILIKE', 'daftartindakan_nama', $filter['daftartindakan_m.daftartindakan_nama']]);
                }
                if(isset($filter['obatalkes_m.obatalkes_nama'])){
                    $query->andFilterWhere(['ILIKE', 'obatalkes_namalain', $filter['obatalkes_m.obatalkes_nama']]);
                }
            }

            $query->orderBy(['daftartindakan_nama' => SORT_ASC]);
            $data = $query->asArray()->all();
            $header = [];

            $print = new DocoPrint();
            $print->attributes = [
                '#table#' => $this->renderPartial('index',[
                    'title'=> $this->_title,
                    'header'=> $header,
                    'data' => $data,
                ]),
            ];
            $print->Output();

            // return true;
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}