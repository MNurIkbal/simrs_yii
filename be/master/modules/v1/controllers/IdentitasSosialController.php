<?php
// Author : Ardi Pratama

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\Pendidikan;
use app\modules\v1\models\PendidikanKualifikasi;
use app\modules\v1\models\Pekerjaan;
use app\modules\v1\models\Suku;
use app\modules\v1\payload\MasterForm;
use Doco\components\DocoPrint;

class IdentitasSosialController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    /**
     * @author: Arief Saputra
     * @Description: Pendidikan
     * @update : Iqbal@docotel.com
     * @dateUpdate : 08-10-2018
    **/
    /*Identitas Sosial Pendidikan   ------------ Start ------------  */

    public function actionGetPendidikan()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'pendidikan_m');
        
        $advancedFilters = $request->get('advanced-filter', []);
        $model = new Pendidikan;
        $query = $model::find()->joinWith(['indexing']);
        $query->andWhere(['pendidikan_m.is_deleted' => false]);
        if(isset($advancedFilters)){
      
            if (isset($advancedFilters['pendidikan_nama']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(pendidikan_nama)', strtolower($advancedFilters['pendidikan_nama']) ]);
            }

            if (isset($advancedFilters['pendidikan_namalainnya']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(pendidikan_namalainnya)', strtolower($advancedFilters['pendidikan_namalainnya']) ]);
            }

            if (isset($advancedFilters['indexing_nama']) ) {
                $query->andWhere(['pendidikan_m.indexing_id' =>  $advancedFilters['indexing_nama'] ]);
            }

            if (isset($advancedFilters['status']) ) {
                $query->andFilterWhere(['pendidikan_m.is_active' =>  $advancedFilters['status'] ]);
            }

        }
        $query->orderby(['pendidikan_m.pendidikan_urutan'=> SORT_DESC]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionCreatePendidikan()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $model = new Pendidikan;
            $model->attributes = $post;
            if($model->validate() && $model->save()){
                return ['message' => 'Data Berhasil di simpan'];

            }else{
                $errors = DocoHelpers::parseError($model->errors, 'PendidikanForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
            
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionViewPendidikan($id)
    {
        $model = new Pendidikan;
        $query = $model::find();
        $query->andWhere(['pendidikan_m.is_deleted' => false]);
        $query->andWhere(['pendidikan_m.pendidikan_id' => $id]);
        return $query->asArray()->one();
    }

    public function actionUpdatePendidikan($id)
    {
        try {
            $request = Yii::$app->request;
            $model = Pendidikan::findOne($id);
            if($request->post() && !empty($model) ){
                $post = $request->post();
                $model->attributes = $post;
                if($model->validate() && $model->save()){
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                }else{
                    $errors = DocoHelpers::parseError($model->errors,'PendidikanForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }else{
                 throw new \Exception("Data Tidak Di Temukan");
            }          
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
           \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDeletePendidikan($id)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $request = Yii::$app->request;
            $model = Pendidikan::findOne($id);
            $modelPendidikanKualifikasi = new PendidikanKualifikasi;
            $getPendidikanKualifikasi = $modelPendidikanKualifikasi::find()->where(['pendidikan_id'=>$id])->count();
            if($getPendidikanKualifikasi > 0){
                return $response['response'] = [
                            'title' => 'Proses Gagal !',
                            'text' => 'Instalasi ini sedang dipakai',
                            'status' => 422
                       ];
            }else{
                if ($model->delete()) {
                    return $response['response'] = [
                            'title' => 'Proses Berhasil !',
                            'text' => 'Data berhasil dihapus'
                       ];
                } else {
                    return $response['response'] = [
                            'title' => 'Proses Gagal !',
                            'text' => 'Data Gagal di hapus',
                            'status' => 422
                       ];
                }                
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @controller actionExportPdfPendidikan
    * @attribute #datatable# => Untuk menampilkan data table
    */
    public function actionExportPdfPendidikan()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Master Identitas Sosial Pendidikan';
            $get = $request->get();

            $model = new Pendidikan;
            $query = $model::find()->joinWith(['indexing'])->orderby(['pendidikan_urutan'=> SORT_ASC]);
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
          
                if (isset($advancedFilters['pendidikan_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(pendidikan_nama)', strtolower($advancedFilters['pendidikan_nama']) ]);
                }

                if (isset($advancedFilters['pendidikan_namalainnya']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(pendidikan_namalainnya)', strtolower($advancedFilters['pendidikan_namalainnya']) ]);
                }

                if (isset($advancedFilters['indexing_nama']) ) {
                    $query->andWhere(['pendidikan_m.indexing_id' =>  $advancedFilters['indexing_nama'] ]);
                }

                if (isset($advancedFilters['status']) ) {
                    $query->andFilterWhere(['pendidikan_m.is_active' =>  $advancedFilters['status'] ]);
                }
            }
            
            $result = [];
            foreach ($query->asArray()->all() as $key => $value) {
                $value['status'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
                $value['indexing_nama'] = $value['indexing']['indexing_nama'];
                $result[] = $value;
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#datatable#' => $this->renderPartial('_cetak_pdf_pendidikan', [
                    'data' => $result,
                    'title' => $title,
                ]),
            ];
            $print->Output();
                    
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionExportExcelPendidikan()
    {
        try{
            $request = Yii::$app->request;
            $title = 'Master Identitas Sosial Pendidikan';
            $result = [];
            $get = $request->get();
            
            $model = new Pendidikan;
            $query = $model::find()->joinWith(['indexing'])->orderby(['pendidikan_urutan'=> SORT_ASC]);
            $advancedFilters = $request->get('advanced-filter', []);
            $header = $footer = [];
            if(isset($advancedFilters)){
          
                if (isset($advancedFilters['pendidikan_nama']) ) {
                    $header['Nama Pendidikan'] = $advancedFilters['pendidikan_nama'];
                    $query->andFilterWhere(['ILIKE', 'LOWER(pendidikan_nama)', strtolower($advancedFilters['pendidikan_nama']) ]);
                }

                if (isset($advancedFilters['pendidikan_namalainnya']) ) {
                    $header['Nama Pendidikan Lainnya'] = $advancedFilters['pendidikan_namalainnya'];
                    $query->andFilterWhere(['ILIKE', 'LOWER(pendidikan_namalainnya)', strtolower($advancedFilters['pendidikan_namalainnya']) ]);
                }

                if (isset($advancedFilters['indexing_nama']) ) {
                    $header['Indexing'] = '';
                    $query->andWhere(['pendidikan_m.indexing_id' =>  $advancedFilters['indexing_nama'] ]);
                }

                if (isset($advancedFilters['status']) ) {
                    $header['Status'] = ($advancedFilters['status']) ? 'Aktif' : 'Tidak Aktif';
                    $query->andFilterWhere(['pendidikan_m.is_active' =>  $advancedFilters['status'] ]);
                }
            }

            $no = 0;
            foreach ($query->asArray()->all() as $key => $value) {
                $no ++;
                $data['Urutan Pendidikan'] = $value['pendidikan_urutan'];
                $data['Nama Pendidikan'] = $value['pendidikan_nama'];
                $data['Nama Lainnya'] = $value['pendidikan_namalainnya'];
                // if(isset($advancedFilters) && isset($advancedFilters['indexing_nama'])){
                //     $header['Indexing'] = $value['indexing']['indexing_nama'];
                // }
                // $data['Indexing'] = $value['indexing']['indexing_nama'];
                $data['Status'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
                $result[] = $data;
            }

            $filePath = DocoHelpers::exportExcel($title, $result, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
            
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
    /*Identitas Sosial Pendidikan   ------------ End ------------  */

    /**
     * @author: Arief Saputra
     * @Description: Pendidikan Kualifikasi
    **/
    /*Identitas Sosial Pendidikan Kualifikasi  ------------ Start ------------  */

    public function actionGetPendidikanKualifikasi()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'kelompokpegawai_m,pendidikan_m');
        
        $advancedFilters = $request->get('advanced-filter', []);
        $model = new PendidikanKualifikasi;
        $query = $model::find()->joinWith(['kelompokpegawai' => function($query){
                                    $query->from('kelompokpegawai_m');
                                }])
                                ->joinWith(['pendidikan' => function($query){
                                    $query->from('pendidikan_m');
                                }]);
        $query->orderby(['kelompokpegawai_id'=> SORT_ASC, 
                            'pendkualifikasi_kode'=> SORT_ASC
                            ]);
        if(isset($advancedFilters)){
      
            if (isset($advancedFilters['pendidikan_nama']) ) {
                $query->andWhere(['pendidikankualifikasi_m.pendidikan_id' => $advancedFilters['pendidikan_nama'] ]);
            }

            if (isset($advancedFilters['kelompokpegawai_nama']) ) {
                $query->andWhere(['pendidikankualifikasi_m.kelompokpegawai_id' => $advancedFilters['kelompokpegawai_nama'] ]);
            }

            if (isset($advancedFilters['pendkualifikasi_kode']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(pendkualifikasi_kode)', strtolower($advancedFilters['pendkualifikasi_kode']) ]);
            }

            if (isset($advancedFilters['pendkualifikasi_nama']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(pendkualifikasi_nama)', strtolower($advancedFilters['pendkualifikasi_nama']) ]);
            }

            if (isset($advancedFilters['pendkualifikasi_namalainnya']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(pendkualifikasi_namalainnya)', strtolower($advancedFilters['pendkualifikasi_namalainnya']) ]);
            }

            if (isset($advancedFilters['pendkualifikasi_keterangan']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(pendkualifikasi_keterangan)', strtolower($advancedFilters['pendkualifikasi_keterangan']) ]);
            }

            if (isset($advancedFilters['jmlkeblaki']) ) {
                $query->andWhere(['jmlkeblaki' => $advancedFilters['jmlkeblaki'] ]);
            }

            if (isset($advancedFilters['jmlkebperempuan']) ) {
                $query->andWhere(['jmlkebperempuan' => $advancedFilters['jmlkebperempuan'] ]);
            }

            if (isset($advancedFilters['status']) ) {
                $query->andFilterWhere(['pendidikankualifikasi_m.is_active' =>  $advancedFilters['status'] ]);
            }

        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionCreatePendidikanKualifikasi()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $model = new PendidikanKualifikasi;
            $model->attributes = $post;
            if(!$model->validate()){
                $errors = DocoHelpers::parseError($model->errors,'PendidikanKualifikasiForm');
                return [
                        'data' => $errors,
                        'status' => 422
                    ];
            }else{
                if (!$model->save()){
                    $errors = DocoHelpers::parseError($model->errors,'PendidikanKualifikasiForm');
                    return [
                            'data' => $errors,
                            'status' => 422
                        ];
                } 
                else{
                    return ['message' => 'Data Berhasil di simpan'];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionViewPendidikanKualifikasi($id)
    {
        $model = new PendidikanKualifikasi;
        $query = $model::find()->joinWith(['kelompokpegawai' => function($query){
                                    $query->from('kelompokpegawai_m');
                                }])
                                ->joinWith(['pendidikan' => function($query){
                                    $query->from('pendidikan_m');
                                }]);
        $query->andWhere(['pendidikankualifikasi_m.is_deleted' => false]);
        $query->andWhere(['pendidikankualifikasi_m.pendkualifikasi_id' => $id]);

        return $query->asArray()->one();
    }

    public function actionUpdatePendidikanKualifikasi($id)
    {
        try {
            $request = Yii::$app->request;
            $model = PendidikanKualifikasi::findOne($id);
            if($request->post() && !empty($model) ){
                $post = $request->post();
                $model->attributes = $post;
                if(!$model->validate()){
                    $errors = DocoHelpers::parseError($model->errors,'PendidikanKualifikasiForm');
                    return [
                            'data' => $errors,
                            'status' => 422
                        ];
                }else{
                    if ($model->save()) {
                        return [
                            'message' => 'Data Berhasil di simpan',
                        ];
                    } else {
                        $errors = DocoHelpers::parseError($model->errors,'PendidikanKualifikasiForm');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                }
            }else{
                 throw new \Exception("Data Tidak Di Temukan");
            }          
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
           \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDeletePendidikanKualifikasi()
    {
        try {
            $request=Yii::$app->request;
            $get = $request->get();
            $id = $get['id'];
            try {
                $model = PendidikanKualifikasi::findOne($id);
                if ($model->delete($id)) {
                    return [
                        'message' => Yii::t('app', 'Data berhasil dihapus')
                    ];
                }

            } catch (\Exception $e) {
                \Yii::$app->response->statusCode = 500;
                return ['message' => $e->getMessage()];
            }   
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @controller actionExportPdfPendidikanKualifikasi
    * @attribute #datatable# => Untuk menampilkan data table
    */
    public function actionExportPdfPendidikanKualifikasi()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Master Identitas Sosial Pendidikan Kualifikasi';
            $get = $request->get();

            $model = new PendidikanKualifikasi;
            $query = $model::find()->joinWith(['kelompokpegawai' => function($query){
                                    $query->from('kelompokpegawai_m');
                                }])
                                ->joinWith(['pendidikan' => function($query){
                                    $query->from('pendidikan_m');
                                }]);
            $query->orderby(['kelompokpegawai_id'=> SORT_ASC, 
                            'pendkualifikasi_kode'=> SORT_ASC
                            ]);
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
          
                if (isset($advancedFilters['pendidikan_nama']) ) {
                    $query->andWhere(['pendidikankualifikasi_m.pendidikan_id' => $advancedFilters['pendidikan_nama'] ]);
                }

                if (isset($advancedFilters['kelompokpegawai_id']) ) {
                    $query->andWhere(['pendidikankualifikasi_m.kelompokpegawai_id' => $advancedFilters['kelompokpegawai_id'] ]);
                }

                if (isset($advancedFilters['pendkualifikasi_kode']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(pendkualifikasi_kode)', strtolower($advancedFilters['pendkualifikasi_kode']) ]);
                }

                if (isset($advancedFilters['pendkualifikasi_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(pendkualifikasi_nama)', strtolower($advancedFilters['pendkualifikasi_nama']) ]);
                }

                if (isset($advancedFilters['pendkualifikasi_namalainnya']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(pendkualifikasi_namalainnya)', strtolower($advancedFilters['pendkualifikasi_namalainnya']) ]);
                }

                if (isset($advancedFilters['pendkualifikasi_keterangan']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(pendkualifikasi_keterangan)', strtolower($advancedFilters['pendkualifikasi_keterangan']) ]);
                }

                if (isset($advancedFilters['jmlkeblaki']) ) {
                    $query->andWhere(['jmlkeblaki' => $advancedFilters['jmlkeblaki'] ]);
                }

                if (isset($advancedFilters['jmlkebperempuan']) ) {
                    $query->andWhere(['jmlkebperempuan' => $advancedFilters['jmlkebperempuan'] ]);
                }

                if (isset($advancedFilters['status']) ) {
                    $query->andFilterWhere(['pendidikankualifikasi_m.is_active' =>  $advancedFilters['status'] ]);
                }
            }
            
            $result = [];
            foreach ($query->asArray()->all() as $key => $value) {
                $value['pendidikan_nama'] = $value['pendidikan']['pendidikan_nama'] ;
                $value['kelompokpegawai_id'] = $value['kelompokpegawai']['kelompokpegawai_nama'] ;
                $value['status'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
                $result[] = $value;
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#datatable#' => $this->renderPartial('_cetak_pdf_pendidikan_kualifikasi', [
                    'data' => $result,
                    'title' => $title,
                ]),
            ];
            $print->Output();
                    
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionExportExcelPendidikanKualifikasi()
    {
        try{
            $request = Yii::$app->request;
            $title = 'Master Identitas Sosial Pendidikan Kualifikasi';
            $result = [];
            $get = $request->get();
            
            $header = $footer = [];
            $model = new PendidikanKualifikasi;
            $query = $model::find()->joinWith(['kelompokpegawai' => function($query){
                                    $query->from('kelompokpegawai_m');
                                }])
                                ->joinWith(['pendidikan' => function($query){
                                    $query->from('pendidikan_m');
                                }]);
            $query->orderby(['kelompokpegawai_id'=> SORT_ASC, 
                            'pendkualifikasi_kode'=> SORT_ASC
                            ]);
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
          
                if (isset($advancedFilters['pendidikan_nama']) ) {
                    $query->andWhere(['pendidikankualifikasi_m.pendidikan_id' => $advancedFilters['pendidikan_nama'] ]);
                }

                if (isset($advancedFilters['kelompokpegawai_id']) ) {
                    $query->andWhere(['pendidikankualifikasi_m.kelompokpegawai_id' => $advancedFilters['kelompokpegawai_id'] ]);
                }

                if (isset($advancedFilters['pendkualifikasi_kode']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(pendkualifikasi_kode)', strtolower($advancedFilters['pendkualifikasi_kode']) ]);
                }

                if (isset($advancedFilters['pendkualifikasi_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(pendkualifikasi_nama)', strtolower($advancedFilters['pendkualifikasi_nama']) ]);
                }

                if (isset($advancedFilters['pendkualifikasi_namalainnya']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(pendkualifikasi_namalainnya)', strtolower($advancedFilters['pendkualifikasi_namalainnya']) ]);
                }

                if (isset($advancedFilters['pendkualifikasi_keterangan']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(pendkualifikasi_keterangan)', strtolower($advancedFilters['pendkualifikasi_keterangan']) ]);
                }

                if (isset($advancedFilters['jmlkeblaki']) ) {
                    $query->andWhere(['jmlkeblaki' => $advancedFilters['jmlkeblaki'] ]);
                }

                if (isset($advancedFilters['jmlkebperempuan']) ) {
                    $query->andWhere(['jmlkebperempuan' => $advancedFilters['jmlkebperempuan'] ]);
                }

                if (isset($advancedFilters['status']) ) {
                    $query->andFilterWhere(['pendidikankualifikasi_m.is_active' =>  $advancedFilters['status'] ]);
                }
            }

            $no = 0;
            foreach ($query->asArray()->all() as $key => $value) {
                $no ++;
                if(isset($advancedFilters)){
                    if (isset($advancedFilters['pendidikan_nama']) ) {
                        $header['Nama Pendidikan'] = $value['pendidikan']['pendidikan_nama'];
                    }

                    if (isset($advancedFilters['kelompokpegawai_id']) ) {
                        $header['Kelompok Pegawai'] = $value['kelompokpegawai']['kelompokpegawai_nama'];
                    }

                    if (isset($advancedFilters['pendkualifikasi_kode']) ) {
                        $header['Kode Pendidikan Kualifikasi'] = "'".$value['pendkualifikasi_kode']."'";
                    }

                    if (isset($advancedFilters['pendkualifikasi_nama']) ) {
                        $header['Nama Pendidikan Kualifikasi'] = $value['pendkualifikasi_nama'];
                    }

                    if (isset($advancedFilters['pendkualifikasi_namalainnya']) ) {
                        $header['Nama Lain Pendidikan Kualifikasi'] = $value['pendkualifikasi_nama'];
                    }

                    if (isset($advancedFilters['jmlkeblaki']) ) {
                        $header['Jumlah Kebutuhan Laki-laki'] = $advancedFilters['jmlkeblaki'];
                    }

                    if (isset($advancedFilters['jmlkebperempuan']) ) {
                        $header['Jumlah Kebutuhan Perempuan'] = $advancedFilters['jmlkebperempuan'];
                    }

                    if (isset($advancedFilters['status']) ) {
                        $header['Status'] = $advancedFilters['status'] ? 'Aktif' : 'Tidak Aktif' ;
                    }
                }
                $data['Pendidikan'] = $value['pendidikan']['pendidikan_nama'];
                $data['Kelompok Pegawai'] = $value['kelompokpegawai']['kelompokpegawai_nama'];
                $data['Kode Pendidikan Kualifikasi'] = "'".$value['pendkualifikasi_kode']."'";
                $data['Nama Pendidikan Kualifikasi'] = $value['pendkualifikasi_nama'];
                $data['Nama Lainnya'] = $value['pendkualifikasi_namalainnya'] ;
                $data['Jumlah Kebutuhan Laki-laki'] = $value['jmlkeblaki'] ;
                $data['Jumlah Kebutuhan Perempuan'] = $value['jmlkebperempuan'] ;
                $data['Status'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
                $result[] = $data;
            }

            $filePath = DocoHelpers::exportExcel($title, $result, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
     * @author: Arief Saputra
     * @Description: Pekerjaan numpang merge
     * @update : Iqbal@docotel.com
     * @dateUpdate : 08-10-2018
    **/
    /*Identitas Sosial Pekerjaan  ------------ Start ------------  */
    public function actionGetPekerjaan()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'pekerjaan_m');
        
        $advancedFilters = $request->get('advanced-filter', []);
        $model = new Pekerjaan;
        $query = $model::find()->orderby(['pekerjaan_nama'=> SORT_ASC]);
        if(isset($advancedFilters)){
      
            if (isset($advancedFilters['pekerjaan_nama']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(pekerjaan_nama)', strtolower($advancedFilters['pekerjaan_nama']) ]);
            }

            if (isset($advancedFilters['pekerjaan_namalainnya']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(pekerjaan_namalainnya)', strtolower($advancedFilters['pekerjaan_namalainnya']) ]);
            }

            if (isset($advancedFilters['status']) ) {
                $query->andFilterWhere(['pekerjaan_m.is_active' =>  $advancedFilters['status'] ]);
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionCreatePekerjaan()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $model = new Pekerjaan;
            $model->attributes = $post;
            if(!$model->validate()){
                $errors = DocoHelpers::parseError($model->errors,'PekerjaanForm');
                return [
                        'data' => $errors,
                        'status' => 422
                    ];
            }else{
                if (!$model->save()){
                    $errors = DocoHelpers::parseError($model->errors,'PekerjaanForm');
                    return [
                            'data' => $errors,
                            'status' => 422
                        ];
                } 
                else{
                    return ['message' => 'Data Berhasil di simpan'];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionViewPekerjaan($id)
    {
        $model = new Pekerjaan;
        $query = $model::find()
             ->select([
                        'pekerjaan_m.pekerjaan_id',
                        'pekerjaan_m.pekerjaan_nama',
                        'pekerjaan_m.pekerjaan_namalainnya',
                        'pekerjaan_m.is_active',
                  ]);
        $query->andWhere(['pekerjaan_m.is_deleted' => false]);
        $query->andWhere(['pekerjaan_m.pekerjaan_id' => $id]);

        return $query->asArray()->one();
    }

    public function actionUpdatePekerjaan($id)
    {
        try {
            $request = Yii::$app->request;
            $model = Pekerjaan::findOne($id);
            if($request->post() && !empty($model) ){
                $post = $request->post();
                $model->attributes = $post;
                if(!$model->validate()){
                    $errors = DocoHelpers::parseError($model->errors,'PekerjaanForm');
                    return [
                            'data' => $errors,
                            'status' => 422
                        ];
                }else{
                    if ($model->save()) {
                        return [
                            'message' => 'Data Berhasil di simpan',
                        ];
                    } else {
                        $errors = DocoHelpers::parseError($model->errors,'PekerjaanForm');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                }
            }else{
                 throw new \Exception("Data Tidak Di Temukan");
            }          
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
           \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDeletePekerjaan()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $id = $get['id'];
            try {
                $model = Pekerjaan::findOne($id);
                if ($model->delete($id)) {
                    return [
                        'message' => Yii::t('app', 'Data berhasil dihapus')
                    ];
                }

            } catch (\Exception $e) {
                \Yii::$app->response->statusCode = 500;
                return ['message' => $e->getMessage()];
            }   
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @controller actionExportPdfPekerjaan
    * @attribute #datatable# => Untuk menampilkan data table
    */
    public function actionExportPdfPekerjaan()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Master Identitas Sosial Pendidikan';
            $get = $request->get();

            $model = new Pekerjaan;
            $query = $model::find()->orderby(['pekerjaan_nama'=> SORT_ASC]);
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
          
                if (isset($advancedFilters['pekerjaan_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(pekerjaan_nama)', strtolower($advancedFilters['pekerjaan_nama']) ]);
                }

                if (isset($advancedFilters['pekerjaan_namalainnya']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(pekerjaan_namalainnya)', strtolower($advancedFilters['pekerjaan_namalainnya']) ]);
                }

                if (isset($advancedFilters['status']) ) {
                    $query->andFilterWhere(['pekerjaan_m.is_active' =>  $advancedFilters['status'] ]);
                }
            }
            
            $result = [];
            foreach ($query->asArray()->all() as $key => $value) {
                $value['status'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
                $result[] = $value;
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#datatable#' => $this->renderPartial('_cetak_pdf_pekerjaan', [
                    'data' => $result,
                    'title' => $title,
                ]),
            ];
            $print->Output();
                    
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
    
    public function actionExportExcelPekerjaan()
    {
        try{
            $request = Yii::$app->request;
            $title = 'Master Identitas Sosial Pekerjaan';
            $result = [];
            $get = $request->get();
            
            $header = $footer = [];
            $model = new Pekerjaan;
            $query = $model::find()->orderby(['pekerjaan_nama'=> SORT_ASC]);
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
          
                if (isset($advancedFilters['pekerjaan_nama']) ) {
                    $header['Nama Pekerjaan'] = $advancedFilters['pekerjaan_nama'];
                    $query->andFilterWhere(['ILIKE', 'LOWER(pekerjaan_nama)', strtolower($advancedFilters['pekerjaan_nama']) ]);
                }

                if (isset($advancedFilters['pekerjaan_namalainnya']) ) {
                    $header['Nama Lain Pekerjaan'] = $advancedFilters['pekerjaan_namalainnya'];
                    $query->andFilterWhere(['ILIKE', 'LOWER(pekerjaan_namalainnya)', strtolower($advancedFilters['pekerjaan_namalainnya']) ]);
                }

                if (isset($advancedFilters['status']) ) {
                    $header['Status'] = $advancedFilters['status'] ? 'Aktif' : 'Tidak Aktif';
                    $query->andFilterWhere(['pekerjaan_m.is_active' =>  $advancedFilters['status'] ]);
                }
            }

            $no = 0;
            foreach ($query->asArray()->all() as $key => $value) {
                $no ++;
                $data['Nama Pekerjaan'] = $value['pekerjaan_nama'];
                $data['Nama Lainnya'] = $value['pekerjaan_namalainnya'];
                $data['Status'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
                $result[] = $data;
            }

            $filePath = DocoHelpers::exportExcel($title, $result, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
    /*Identitas Sosial Pekerjaan  ------------ End ------------  */

    /**
     * @author: Arief Saputra
     * @Description: Suku
     * @update : Iqbal@docotel.com
     * @dateUpdate : 08-10-2018
    **/
    /*Identitas Sosial Suku  ------------ Start ------------  */

    public function actionGetSuku()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'suku_m');
        
        $advancedFilters = $request->get('advanced-filter', []);
        $model = new Suku;
        $query = $model::find()->orderby(['suku_nama'=> SORT_ASC]);
        if(isset($advancedFilters)){
      
            if (isset($advancedFilters['suku_nama']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(suku_nama)', strtolower($advancedFilters['suku_nama']) ]);
            }

            if (isset($advancedFilters['suku_namalainnya']) ) {
                $query->andFilterWhere(['ILIKE', 'LOWER(suku_namalainnya)', strtolower($advancedFilters['suku_namalainnya']) ]);
            }

            if (isset($advancedFilters['status']) ) {
                $query->andFilterWhere(['suku_m.is_active' =>  $advancedFilters['status'] ]);
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionCreateSuku()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $model = new Suku;
            $model->attributes = $post;
            if(!$model->validate()){
                $errors = DocoHelpers::parseError($model->errors,'SukuForm');
                return [
                        'data' => $errors,
                        'status' => 422
                    ];
            }else{
                if (!$model->save()){
                    $errors = DocoHelpers::parseError($model->errors,'SukuForm');
                    return [
                            'data' => $errors,
                            'status' => 422
                        ];
                } 
                else{
                    return ['message' => 'Data Berhasil di simpan'];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionViewSuku($id)
    {
        $model = new Suku;
        $query = $model::find()
             ->select([
                        'suku_m.suku_id',
                        'suku_m.suku_nama',
                        'suku_m.suku_namalainnya',
                        'suku_m.is_active',
                      ]);
        $query->andWhere(['suku_m.is_deleted' => false]);
        $query->andWhere(['suku_m.suku_id' => $id]);
        return $query->asArray()->one();
    }

    public function actionUpdateSuku($id)
    {
        try {
            $request = Yii::$app->request;
            $model = Suku::findOne($id);
            if($request->post() && !empty($model) ){
                $post = $request->post();
                $model->attributes = $post;
                if(!$model->validate()){
                    $errors = DocoHelpers::parseError($model->errors,'SukuForm');
                    return [
                            'data' => $errors,
                            'status' => 422
                        ];
                }else{
                    if ($model->save()) {
                        return [
                            'message' => 'Data Berhasil di simpan',
                        ];
                    } else {
                        $errors = DocoHelpers::parseError($model->errors,'SukuForm');
                        return [
                            'data' => $errors,
                            'status' => 422
                        ];
                    }
                }
            }else{
                 throw new \Exception("Data Tidak Di Temukan");
            }          
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
           \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDeleteSuku()
    {
        try {
            $request = Yii::$app->request;
            $get = $request->get();
            $id = $get['id'];
            try {
                $model = Suku::findOne($id);
                if ($model->delete($id)) {
                    return [
                        'message' => Yii::t('app', 'Data berhasil dihapus')
                    ];
                }

            } catch (\Exception $e) {
                \Yii::$app->response->statusCode = 500;
                return ['message' => $e->getMessage()];
            }   
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @controller actionExportPdfSuku
    * @attribute #datatable# => Untuk menampilkan data table
    */
    public function actionExportPdfSuku()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Master Identitas Sosial Suku';
            $get = $request->get();

            $model = new Suku;
            $query = $model::find()->orderby(['suku_nama'=> SORT_ASC]);
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
          
                if (isset($advancedFilters['suku_nama']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(suku_nama)', strtolower($advancedFilters['suku_nama']) ]);
                }

                if (isset($advancedFilters['suku_namalainnya']) ) {
                    $query->andFilterWhere(['ILIKE', 'LOWER(suku_namalainnya)', strtolower($advancedFilters['suku_namalainnya']) ]);
                }

                if (isset($advancedFilters['status']) ) {
                    $query->andFilterWhere(['suku_m.is_active' =>  $advancedFilters['status'] ]);
                }
            }
            
            $result = [];
            foreach ($query->asArray()->all() as $key => $value) {
                $value['status'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
                $result[] = $value;
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#datatable#' => $this->renderPartial('_cetak_pdf_suku', [
                    'data' => $result,
                    'title' => $title,
                ]),
            ];
            $print->Output();
                    
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionExportExcelSuku()
    {
        try{
            $request = Yii::$app->request;
            $title = 'Master Identitas Sosial Suku';
            $result = [];
            $get = $request->get();
            
            $model = new Suku;
            $header = $footer = [];
            $query = $model::find()->orderby(['suku_nama'=> SORT_ASC]);
            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
          
                if (isset($advancedFilters['suku_nama']) ) {
                    $header['Nama Suku'] = $advancedFilters['suku_nama'];
                    $query->andFilterWhere(['ILIKE', 'LOWER(suku_nama)', strtolower($advancedFilters['suku_nama']) ]);
                }

                if (isset($advancedFilters['suku_namalainnya']) ) {
                    $header['Nama Lain Suku'] = $advancedFilters['suku_namalainnya'];
                    $query->andFilterWhere(['ILIKE', 'LOWER(suku_namalainnya)', strtolower($advancedFilters['suku_namalainnya']) ]);
                }

                if (isset($advancedFilters['status']) ) {
                    $header['Status'] = $advancedFilters['status'] ? 'Aktif' : 'Tidak Aktif';
                    $query->andFilterWhere(['suku_m.is_active' =>  $advancedFilters['status'] ]);
                }
            }

            $no = 0;
            foreach ($query->asArray()->all() as $key => $value) {
                $no ++;
                $data['Nama Suku'] = $value['suku_nama'];
                $data['Nama Lainnya'] = $value['suku_namalainnya'];
                $data['Status'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
                $result[] = $data;
            }

            $filePath = DocoHelpers::exportExcel($title, $result, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;
            
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
    /*Identitas Sosial Suku  ------------ End ------------  */
}
