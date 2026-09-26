<?php
namespace app\modules\bedah\components\traits;


use Yii;
use yii\web\Response;
use app\components\DocoConstants;
use app\components\DocoHelpers;
use Doco\bedah\models\LaporanEndoskopiForm;
use yii\web\UploadedFile;

trait LaporanEndoskopiTrait
{
    public function actionLaporanEndoskopi($id){
      $request = Yii::$app->request;
      $id = $request->get('id', null);
      $pasienmasukpenunjang_id = $id;
      $title = 'Laporan Endoskopi';
      $model = new LaporanEndoskopiForm;
      $data = $this->getDataEndoskopi($id);
      $attributes = $this->setAttributesEndoskopi($data, $model);
      $model->attributes = $attributes['model'];
      $options = $attributes['options'];
      
      return $this->renderAjax('detail-partial/_laporan_endoskopi', [
         'id' => $id,
         'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id,
         'title' => $title,
         'model' => $model,
         'options' => $options,
         'max_upload' => DocoConstants::MAX_UPLOAD_ENDOSKOPI,
      ]);
    }

    private function getDataEndoskopi($id){
        $data = $this->guzzleExec($this->_restBedah, [
            'url' => 'laporan-endoskopi/get-data-endoskopi',
            'method' => 'POST',
            'payload' => [
               'form_params' => [
                'id' => $id
               ]
            ],
         ]);
         $data['procedure_performed'] = json_decode($data['procedure_performed'], true);
        //  $data['pre_diagnosis'] = json_decode($data['pre_diagnosis'], true);
         $data['pre_diagnosis_sekunder'] = !empty($data['pre_diagnosis_sekunder']) ? json_decode($data['pre_diagnosis_sekunder'], true) : [];
        //  $data['endoscopic_diagnosis'] = json_decode($data['endoscopic_diagnosis'], true);
         $data['endoscopic_diagnosis_sekunder'] = !empty($data['endoscopic_diagnosis_sekunder'])  ? json_decode($data['endoscopic_diagnosis_sekunder'], true) : [];
         $data['additional_photo'] = !empty($data['additional_photo']) ? json_decode($data['additional_photo'], true) : [];

        return $data;

    }
    
   private function setAttributesEndoskopi($data, $model)
   {
      $model->simptoms = !empty($data['simptoms']) ? $data['simptoms'] : null;
      $model->pre_diagnosis = !empty($data['pre_diagnosis']) ? $data['pre_diagnosis'] : null;
      $model->pre_diagnosis_sekunder = !empty($data['pre_diagnosis_sekunder']) ? $data['pre_diagnosis_sekunder'] : null;
      $model->indications_examinations = !empty($data['indications_examinations']) ? $data['indications_examinations'] : null;
      $model->instrument = !empty($data['instrument']) ? $data['instrument'] : null;
      $model->pre_medications = !empty($data['pre_medications']) ? $data['pre_medications'] : null;
      $model->procedure_performed = !empty($data['procedure_performed']) ? $data['procedure_performed'] : null;
      $model->findings = !empty($data['findings']) ? $data['findings'] : null;
      $model->sampling = !empty($data['sampling']) ? $data['sampling'] : null;
      $model->recommendations = !empty($data['recommendations']) ? $data['recommendations'] : null;
      $model->additional_data = !empty($data['additional_data']) ? $data['additional_data'] : null;
      $model->additional_photo = !empty($data['additional_photo']) ? $data['additional_photo'] : null;
      $model->endoscopic_diagnosis = !empty($data['endoscopic_diagnosis']) ? $data['endoscopic_diagnosis'] : null;
      $model->endoscopic_diagnosis_sekunder = !empty($data['endoscopic_diagnosis_sekunder']) ? $data['endoscopic_diagnosis_sekunder'] : null;
      $options['procedure_performed'] = $this->explodeData($model->procedure_performed);
      $options['pre_diagnosis'] = $this->explodeData($model->pre_diagnosis);
      $options['pre_diagnosis_sekunder'] = $this->explodeData($model->pre_diagnosis_sekunder);
      $options['endoscopic_diagnosis'] = $this->explodeData($model->endoscopic_diagnosis);
      $options['endoscopic_diagnosis_sekunder'] = $this->explodeData($model->endoscopic_diagnosis_sekunder);

      return [
         'model' => $model->attributes,
         'options' => $options
      ];
   }

   public function actionSimpanLaporanEndoskopi()
   {
        $path = \Yii::getAlias('@webroot');
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $model = new LaporanEndoskopiForm;
        $model->load($request->post('LaporanEndoskopiForm'));
        $postData = $request->post('LaporanEndoskopiForm'); 
        $existingPhoto = !empty($postData['additional_photo_arr'] ) ? $postData['additional_photo_arr'] : [];
        $deletedPhoto = !empty($postData['delete_photo_arr']) ? $postData['delete_photo_arr'] : [];
        $fileExtension = ['jpeg', 'jpg', 'png'];
        try {
            $files = UploadedFile::getInstances($model, "additional_photo");
            if($model->validate()){
                $image= [];
                if (!empty($files)) {
                    $folderName = '/uploads/laporan-endoskopi/';
                    if (!file_exists($path.$folderName)) {
                        mkdir($path.$folderName, 0777, true);
                    }
                    foreach ($files as $file) {
                        if($file->extension)
                        $filename = $folderName .$id.'-'.$file->baseName . '.' . $file->extension; 
                        if(in_array(strtolower($file->extension), $fileExtension)){
                            $save = $file->saveAs($path.$filename);
                            Yii::error(["hmmm"=>$save]);
                            if($save==true ){
                                $image[] = $filename;
                            }
                        }else{
                            throw new \Exception("Tipe File tidak diizinkan");
                        }
                    }
                }
                $postData['additional_photo'] =  !empty(array_merge($image, $existingPhoto)) ? array_merge($image, $existingPhoto) : '';
                $postData['procedure_performed'] = $postData['procedure_performed'];
                $result = $this->guzzleExec($this->_restBedah, [
                    'url' => 'laporan-endoskopi/simpan-laporan',
                    'method' => 'POST',
                    'payload' => [
                    'form_params' => $postData,
                    'query' => [
                        'id' => $id
                    ]
                    ],
                    'returnResponse' => true,
                ]);
                if($result){
                    foreach($deletedPhoto as $del){
                        if (file_exists($path.$del)) {
                            unlink($path.$del);
                        }
                    }
                }
                return $result;
            }else{
                $errors = DocoHelpers::parseError($model->errors, 'LaporanEndoskopiForm');
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal',
                    'text' => 'Gagal mengupload gambar, extensi tidak diizinkan'
                ];
            }
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
   }

    public function actionGetNewDiagnosa($q = '',$page = null,$type = 'diagnosa_masuk', $all_text = 0, $id_with_text = 0) {
        try {
            $limit = 10;
            $offset = ($page-1)*10;
            Yii::$app->response->format = Response::FORMAT_JSON;
            $result = [];
            $result['results'] = [];

            if ($type == 'diagnosa_masuk') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_MASUK;
            } else if ($type == 'diagnosa_utama') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA;
            } else if ($type == 'diagnosa_penyerta') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA;
            } else if ($type == 'diagnosa_operasi') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_OPERASI;
            } else if ($type == 'diagnosa_keluarga') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_KELUARGA;
            } else if ($type == 'diagnosa_terapi') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_TERAPI;
            } else {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_AWALAN;
            }

            $request = Yii::$app->docoRest->rajal->get('allow/get-new-diagnosa',['query'=>['q'=>$q,'type'=>$type,'page'=>$page,'offset'=>$offset,'limit'=>$limit]]);
            $response = json_decode($request->getBody(), true);

            $list = $response['response'];
            if ($all_text == 1) {
                foreach ($response['response'] as $value) {
                    $result['results'] = [
                        'id' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama'],
                        'text' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama']
                    ];
                }
            } else {
                if ($id_with_text == 1) {
                    foreach ($response['response'] as $value) {
                        $result['results'][] = [
                            'id' => $value['diagnosa_id'].'||'.$value['diagnosa_kode'].'||'.$value['diagnosa_nama'],
                            'text' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama']
                        ];
                    }
                } else {
                    foreach ($response['response'] as $value) {
                        $result['results'][] = [
                            'id' => $value['diagnosa_id'],
                            'text' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama']
                        ];
                    }
                }
            }

            $result['pagination'] = [ 'more' => !empty($list)?true:false ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetTindakanEndoskopi($q = '',$all_text = 0, $id_with_text = 0, $page = null) {
        try {
            $limit = 10;
            $offset = ($page-1)*10;
            Yii::$app->response->format = Response::FORMAT_JSON;
            $result = [];
            $result['results'] = [];

            $request = $this->_restBedah->get('laporan-endoskopi/get-tindakan-endoskopi',['query'=>['q'=>$q,'page'=>$page]]);
            $response = json_decode($request->getBody(), true);

            $list = $response['response'];
            if ($all_text == 1) {
                foreach ($response['response'] as $value) {
                    $result['results'][] = [
                        'id' => $value['id'].'||'.$value['text'],
                        'text' => $value['text']
                    ];
                }
            } else {
                if ($id_with_text == 1) {
                    foreach ($response['response'] as $value) {
                        $result['results'][] = [
                            'id' => $value['id'].'||'.$value['text'],
                            'text' => $value['id'].' - '.$value['text']
                        ];
                    }
                } else {
                    foreach ($response['response'] as $value) {
                        $result['results'][] = [
                            'id' => $value['id'],
                            'text' => $value['text']
                        ];
                    }
                }
            }

            $result['pagination'] = [ 'more' => !empty($list)?true:false ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    private function explodeData($data){
        if(!is_array($data)){
            $dataExploded = explode("||",$data);
            $strtext1 = !empty($dataExploded[1]) ? $dataExploded[1] :"";
            $strtext2 = !empty($dataExploded[2]) ? '-'.$dataExploded[2] :"";
            $text = $strtext1.$strtext2;
            $id = $data;
            return [
                $id => $text
            ];
        }else if(is_array($data)){
            $result = [];
            foreach($data as $value){
                $dataExploded = explode("||",$value);
                if(count($dataExploded) > 1){
                    $strtext1 = !empty($dataExploded[1]) ? $dataExploded[1] :"";
                    $strtext2 = !empty($dataExploded[2]) ? '-'.$dataExploded[2] :"";
                    $text = $strtext1.$strtext2;
                    $id = $value;
                    $result[]= [$id=> $text];
                }else{
                    $text = $dataExploded[0];
                    $id = $value;
                    $result[]= [$id => $text];
                }
            }
            return $result;
        }else{
            return [];
        }
    }
}