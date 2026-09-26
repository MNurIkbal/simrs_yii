<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-21 13:35:07
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-04-04 16:18:37
 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;

use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use app\modules\v1\models\KelompokPegawai;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\RuanganPemakai;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\payload\PayloadForm;

class PegawaiRuanganController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\RuanganPegawai';
    public $instalasi_sysadmin = DocoConstants::INSTALASI_SYS_ADMIN;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }

    private function Model(){
        $model = new PegawaiView;
        return $model::find();
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $instalasi_id = $request->get('instalasi_id', Yii::$app->jwt->instalasi_id);
        $model = new PegawaiView;

        if ($instalasi_id == $this->instalasi_sysadmin) {
            $query = $model::find();
        }else{
            $query = $model::find()->where(['instalasi_id' => $instalasi_id]);
        }

        $query->orderBy([
            "ruangan_id" => SORT_ASC,
            "nama_pegawai" => SORT_ASC,
        ]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionGetListRuangan()
    {
        $request = Yii::$app->request;
        $user_id = $request->get('user_id', null);
        $instalasi_id = $request->get('instalasi_id', null);
        $payload = new PayloadForm;
        $payload->instalasi_id = $instalasi_id;
        if (!$payload->validate()) {
            return [
                'status' => 422,
                'data' => $payload->errors
            ];
        }

        $andWhere = null;
        if(!is_null($instalasi_id)){
            if ($instalasi_id != $this->instalasi_sysadmin) {
                $andWhere = " AND instalasi_id = '{$instalasi_id}'";
            }
        }
        $query = "
            SELECT ruangan_id, ruangan_nama
            FROM ruangan_m 
            WHERE is_deleted = false
        ".$andWhere."
        ORDER BY ruangan_nama ASC
        ";
        $data = Yii::$app->db->createCommand($query)->queryAll();

        return ['data'=>$data];
    }

    // public function actionGetListRuangan()
    // {
    //     $request = Yii::$app->request;
    //     $user_id = $_GET['user_id'];
    //     $instalasi_id = $request->get('instalasi_id', null);

    //     $andWhere = null;
    //     if(!is_null($instalasi_id)){
    //         if ($instalasi_id != $this->instalasi_sysadmin) {
    //             $andWhere = " AND rm.instalasi_id = '{$instalasi_id}'";
    //             $andWhere .= " AND rpk.loginpemakai_id = '{$user_id}'";
    //         }
    //     }
    //     $query = "
    //         SELECT rpk.ruangan_id, rm.ruangan_nama
    //         FROM ruanganpemakai_k rpk
    //         LEFT JOIN ruangan_m rm ON rm.ruangan_id = rpk.ruangan_id
    //         WHERE rpk.is_deleted = false
    //     ".$andWhere."
    //     ORDER BY rm.ruangan_nama ASC
    //     ";
    //     $data = Yii::$app->db->createCommand($query)->queryAll();

    //     return ['data'=>$data];
    // }

    public function actionGetListInstalasi()
    {
        $user_id = $_GET['user_id'];
        $query = "
            SELECT im.instalasi_id, im.instalasi_nama
            FROM ruanganpemakai_k rpk
            LEFT JOIN ruangan_m rm ON rm.ruangan_id = rpk.ruangan_id
            LEFT JOIN instalasi_m im ON im.instalasi_id = rm.instalasi_id
            WHERE rpk.is_deleted = false AND rpk.loginpemakai_id = '".$user_id."'
            GROUP BY (im.instalasi_id)
            ORDER BY im.instalasi_nama ASC
        ";

        $data = Yii::$app->db->createCommand($query)->queryAll();
        $kelompokPegawai = KelompokPegawai::find()->asArray()->all();

        return ['instalasi' => $data, 'kelompok_pegawai' => $kelompokPegawai];
    }

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $user_id = $post['user_id'];
        $postData = $post['PegawaiRuanganForm'];
        $query = RuanganPegawai::find()->where(['ruangan_id'=>$postData['ruangan_id'],'pegawai_id'=>$postData['pegawai_id']])->one();

        if(empty($query) || is_null($query)){
            $model = new RuanganPegawai;
            $model->ruangan_id = $postData['ruangan_id'];
            $model->pegawai_id = $postData['pegawai_id'];
            if($model->save()){
                $result = [
                    'status' => 200,
                    'title' => 'Simpan Data Berhasil',
                    'text' => 'Data Berhasil di simpan',
                ];
            }
            else {
                $result = [
                    'status' => 422,
                    'data' => $model->errors,
                ];
            }
        }
        else {
            if (!$query->is_active) {
                $query->is_active = true;
                if ($query->save()) {
                    $result = [
                        'status' => 200,
                        'title' => 'Simpan Data Berhasil',
                        'text' => 'Data Berhasil di aktifkan kembali',
                    ];
                } else {
                    $result = [
                        'status' => 422,
                        'data' => $query->errors,
                    ];
                }
            } else {
                $result['title'] = 'Proses Gagal';
                $result['status'] = 422;
                $result['text'] = "Pegawai sudah terdaftar di ruangan ini.";
            }
        }

        return $result;
    }

    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $user_id = $post['user_id'];
        $latest = $post['latest'];
        $post = $post['PegawaiRuanganForm'];
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $pegawai_ruangan = RuanganPegawai::find()->where([
                "pegawai_id" => $latest["pegawai_id"],
                "ruangan_id" => $latest["ruangan_id"],
            ])->one();

            $pegawai_ruangan_new = null;
            if ($latest["ruangan_id"] != $post["ruangan_id"]) {
                $pegawai_ruangan_new = RuanganPegawai::find()->where([
                    "pegawai_id" => $post["pegawai_id"],
                    "ruangan_id" => $post["ruangan_id"],
                ])->one();
            }
            if ($pegawai_ruangan_new) {
                if (!$pegawai_ruangan_new->is_active) {
                    $pegawai_ruangan->is_active = false;
                    $pegawai_ruangan->update();

                    $pegawai_ruangan_new->is_active = $post["is_active"] == "1" ? true : false;
                    $pegawai_ruangan_new->update();
                } else {
                    throw new \Exception('Pegawai sudah terdaftar di ruangan ini.');
                }
            } else {
                $pegawai_ruangan->ruangan_id = $post["ruangan_id"];
                $pegawai_ruangan->pegawai_id = $post["pegawai_id"];
                $pegawai_ruangan->is_active = $post["is_active"] == "1" ? true : false;
                if (!$pegawai_ruangan->update()) {
                    throw new \Exception(json_encode($pegawai_ruangan->errors));
                }
            }
            $transaction->commit();
            return [
                "status" => 200,
                "text" => "Pegawai Ruangan berhasil diperbarui"
            ];
        } catch (\Exception $e) {
            $transaction->rollback();
            return [
                "status" => 422,
                "text" => "Pegawai Ruangan gagal diperbarui"
            ];
        }

    }

    public function actionDataKelompokpegawai()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->getDataKelompokpegawai();
        $result->select(['kelompokpegawai_id','kelompokpegawai_nama']);
        if(!empty($post['term'])){
            $term = $post['term'];
            $result->where(['like', 'LOWER(kelompokpegawai_nama)', $term]);
        }
        return $result->asArray()->all();
    }
    public function getDataKelompokpegawai()
    {
        $data = KelompokPegawai::find();
        return $data;
    }

    public function actionGetRuanganNama($id)
    {
        $query = "
            SELECT * from ruangan_m
            WHERE ruangan_id = '".$id."'
        ";

        $data = Yii::$app->db->createCommand($query)->queryOne();
        return $data;
    }

    public function actionDataPegawai()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->getDataPegawai();
        $result->select(['nomorindukpegawai', 'nama_pegawai']);
        if(!empty($post['term'])){
            $term = $post['term'];
            $result->where(['like', 'LOWER(nama_pegawai)', $term]);
            $result->orWhere(['like', 'nomorindukpegawai', $term]);
        }
        return $result->asArray()->all();
    }
    public function getDataPegawai()
    {
        $data = Pegawai::find();
        return $data;

    }
    public function actionDelete($id)
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $id = explode("-", $id);
            $pegawai_id = $id[0];
            $ruangan_id = $id[1];
            $delete_doc = ( new RuanganPegawai)->delete([
                "pegawai_id" => $pegawai_id,
                "ruangan_id" => $ruangan_id,
            ]);
            $transaction->commit();
            return [
                "status" => 200,
                "text" => "Pegawai Ruangan berhasil dihapus",
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                "status" => 422,
                "text" => "Proses gagal",
                "e" => $e->getMessage()
            ];
        }
    }

    protected $_title = 'laporan pegawai ruangan';

    public function actionExportExcel()
    {
        try {
            $data = $this->getPdfExcelData();

            $tempData = [];

            if (!empty($data)) {
                foreach ($data as $index => $value) {
                    $value["status"] = ($value["status"] == true) ? Yii::t('app', 'Aktif') : Yii::t('app', 'Tidak Aktif');

                    $row = [
                        "Ruangan" => $value["ruangan_nama"],
                        "Nama Pegawai" => $value["nama_pegawai"],
                        "Kelompok Pegawai" => $value["kelompokpegawai_nama"],
                        "Status" => $value["status"],
                    ];

                    $tempData[] = $row;
                }
            }

            // Declare header
            $header = [
                "Tanggal Unduh" => date("d-M-Y H:m:s")
            ];

            $filePath = DocoHelpers::exportExcel(
                strtoupper($this->_title),
                $tempData,
                $header,
                [],
                [],
                [],
                true
            );


            $filePath->save('php://output');
            die();
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
    * @controller actionExportPdf
    * @attribute #table_pemakaian# => table
    **/

    public function actionExportPdf()
    {
        $data = $this->getPdfExcelData();

        // Creating an array as per the need for the table
        $array = [];
        foreach ($data as $value) {
            if(!isset($array[$value['ruangan_id']])){
                $array[$value['ruangan_id']] = [];
            }
            $array[$value['ruangan_id']][] = $value;
        }

        $filter = [
            'Ruangan nama'=> isset($_GET['advanced-filter']['ruangan_nama']) ? $_GET['advanced-filter']['ruangan_nama'] : '-',
            'Nama pegawai'=> isset($_GET['advanced-filter']['nama_pegawai']) ? $_GET['advanced-filter']['nama_pegawai'] : '-',
            'Kelompok Pegawai'=> isset($_GET['advanced-filter']['kelompokpegawai_nama']) ? $_GET['advanced-filter']['kelompokpegawai_nama'] : '-',
            'Status'=> isset($_GET['advanced-filter']['is_active']) ? ($_GET['advanced-filter']['is_active'] == 0) ? 'Aktif' : 'Tidak Aktif' : '-' ,
        ];

        $print = new DocoPrint();
        $print->attributes = [
            '#table_pemakaian#' => $this->renderPartial('index',[
                'filter'=> $filter,
                'detail' => $array,
            ]),
        ];
        $print->Output();
    }

    // Get kelompok pegawai by id
    public function actionGetKelompokPegawaiById($id)
    {
        // Try catch
        try {
            // Get model
            $model = KelompokPegawai::findOne($id);

            // Return
            return $model;
        } catch (\yii\db\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetDetailPegawaiRuangan($pegawai_id, $ruangan_id)
    {

        try {
            $pegawai_ruangan = PegawaiView::find()->where([
                'pegawai_id' => $pegawai_id,
                'ruangan_id' => $ruangan_id
            ])->one();

            $user_id = Yii::$app->jwt->instalasi_id;
            $where = "";
            // if (!is_null($user_id)) {
            //     $where = " AND rpk.loginpemakai_id = '".$user_id."'";
            // }
            $query = "
                SELECT im.instalasi_id, im.instalasi_nama
                FROM ruanganpemakai_k rpk
                LEFT JOIN ruangan_m rm ON rm.ruangan_id = rpk.ruangan_id
                LEFT JOIN instalasi_m im ON im.instalasi_id = rm.instalasi_id
                WHERE rpk.is_deleted = false {$where}
                GROUP BY im.instalasi_id
                ORDER BY im.instalasi_nama
            ";

            $data = Yii::$app->db->createCommand($query)->queryAll();
            $kelompokPegawai = KelompokPegawai::find()->asArray()->all();
            $ruangan = Ruangan::find()->select(["ruangan_id", "ruangan_nama"])
                ->where(["is_deleted" => false, "is_active" => true])
                ->orderBy(["ruangan_nama" => SORT_ASC])
                ->asArray()->all();

            $list_instalasi = ArrayHelper::map($data, "instalasi_id", "instalasi_nama");
            $kelompokPegawai = ArrayHelper::map($kelompokPegawai, "kelompokpegawai_id", "kelompokpegawai_nama");

            return [
                'pegawai_ruangan' => $pegawai_ruangan,
                'instalasi' => $list_instalasi,
                'kelompok_pegawai' => $kelompokPegawai,
            ];
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actionGetIndexData()
    {
        $request = Yii::$app->request;
        try {
            $instalasi_id =  $request->get("instalasi_id", null);
            $user_id =  $request->get("user_id", null);
            $where_ruangan = "";
            if ($instalasi_id != $this->instalasi_sysadmin && !is_null($instalasi_id)) {
                $where_ruangan .= " AND rm.instalasi_id = '{$instalasi_id}'";
            }

            if (!is_null($user_id)) {
                $where_ruangan .= "AND rpk.loginpemakai_id = '{$user_id}'";
            }

            $query = "
                SELECT rpk.ruangan_id, rm.ruangan_nama
                FROM ruanganpemakai_k rpk
                LEFT JOIN ruangan_m rm ON rm.ruangan_id = rpk.ruangan_id
                WHERE rpk.is_deleted = false
            ".$where_ruangan."
                ORDER BY rm.ruangan_nama ASC
            ";
            $ruangan = Yii::$app->db->createCommand($query)->queryAll();
            $ruangan = ArrayHelper::map($ruangan, "ruangan_id", "ruangan_nama");

            $kelompok_pegawai = KelompokPegawai::find()->all();
            $kelompok_pegawai = ArrayHelper::map($kelompok_pegawai, "kelompokpegawai_id", "kelompokpegawai_nama");

            return [
                "ruangan" => $ruangan,
                "kelompok_pegawai" => $kelompok_pegawai,
            ];

            // $ruangan =
        } catch (Exception $e) {

        }
    }

    public function getPdfExcelData()
    {
        $request = Yii::$app->request;
        $where = '';
        $instalasi_id = $request->get('instalasi_id', Yii::$app->jwt->instalasi_id);

       if ($instalasi_id == $this->instalasi_sysadmin) {
            $where .= "";
       }else{
           $where .= " AND r.instalasi_id = ".$instalasi_id;
       }

        $order = ' order by r.ruangan_id ASC ,p.nama_pegawai ASC';

        if(isset($_GET['advanced-filter'])){
            $filter = $_GET['advanced-filter'];
            if(isset($filter['ruangan_id'])){
                $where .= " and pr.ruangan_id = '".$filter['ruangan_id']."'";
            }
            if(isset($filter['nama_pegawai'])){
                $where .= " and p.nama_pegawai ILIKE '%".$filter['nama_pegawai']."%'";
            }
            if(isset($filter['kelompokpegawai_id'])){
                $where .= " and kp.kelompokpegawai_id = '".$filter['kelompokpegawai_id']."'";
            }
            if(isset($filter['is_active'])){
                $status = ($filter['is_active'] == 1) ? 'true' : 'false';
                $where .= " and pr.is_active = {$status}";
            }
        }

        $sql = "
        SELECT pr.pegawai_id,pr.ruangan_id, p.nama_pegawai, kp.kelompokpegawai_nama, pr.is_active as status, r.ruangan_nama,p.kelompokpegawai_id,r.instalasi_id
        FROM ruanganpegawai_mp pr
        RIGHT JOIN pegawai_m p ON pr.pegawai_id = p.pegawai_id
        RIGHT JOIN ruangan_m r ON r.ruangan_id = pr.ruangan_id
        RIGHT JOIN kelompokpegawai_m kp ON kp.kelompokpegawai_id = p.kelompokpegawai_id
        WHERE pr.is_deleted = false AND p.is_active = true AND p.is_deleted = false
         ".$where.$order;

        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }
}
