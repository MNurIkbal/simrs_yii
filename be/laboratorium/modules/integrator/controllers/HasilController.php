<?php

/**
 * Public REST API - bridging LIS - ORDER
 *
 * @author ardi@docotel.com
 * @return string
 */

namespace app\modules\integrator\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoPublicController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\modules\integrator\components\LisController;
use app\modules\integrator\models\HasilPemeriksaanLabWynacom;
use app\modules\integrator\models\PasienMasukPenunjang;
use Doco\Notifications\LaboratoriumNotification;
use app\modules\integrator\models\BridgingOrderLabView;

class HasilController extends LisController
{
    public $modelClass = '';
    protected $_nonAuthorization = 'non_authorization';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return $behaviors;
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        unset($verbs['index']);
        unset($verbs['update']);
        unset($verbs['view']);
        $verbs["simpan"] = ["POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }
    /**
     * Function Simpan Bridging Wynacom
     * 
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSimpan()
    {
        $request = Yii::$app->request;
        $result = $request->post('result', []);
        // if (empty($result)) {
        //     return $this->responseJson(422, 'Silahkan Cek Inputan', [
        //         'error' => 'Data Hasil Tidak Boleh Kosong!',
        //     ]);
        // }
        $isResult = false;
        $errValidate = $resultItems = $orderNumber = $hisTestId = [];
        foreach ($result as $index => $item) {
            $model = new HasilPemeriksaanLabWynacom;
            $model->attributes = $item;
            if (!in_array($model->his_reg_no, $orderNumber)) {
                $orderNumber[] = $model->his_reg_no;
            }
            $model->scenario = $this->_nonAuthorization;
            if (!$model->validate()) {
                $errValidate['Silahkan cek inputan dengan index-' . $index] = $model->getErrors();
            } else {
                $model->authorization_date = !empty($item['authorization_date']) ? $item['authorization_date'] : NULL;
                $model->result = !empty($item['result']) ? $item['result'] : NULL;
                $resultItems[] = $model->attributes;
                $hisTestId[] = $model->his_test_id;
                $dataResult[] = $model->result;
                if (!empty($model->result)) {
                    $isResult = true;
                }
            }
        }
        if (!empty($errValidate)) {
            return $this->responseJson(422, 'Silahkan Cek Inputan', [
                'error' => 'Data Hasil Tidak Boleh Kosong!',
            ]);
        }
        if (count($orderNumber) > 1) {
            return $this->responseJson(422, 'Silahkan Cek Inputan', [
                'error' => 'Tidak bisa menerima hasil dengan nomor pemesanan yang berbeda!',
            ]);
        }

        $updatePemeriksaanPenunjang = PasienMasukPenunjang::find()->where(['no_masukpenunjang' => $orderNumber])->one();
        if (empty($updatePemeriksaanPenunjang)) {
            return $this->responseJson(404, 'Data Nomor Pemesanan Tidak Ditemukan!');
        }

        if (!$updatePemeriksaanPenunjang->is_hasil) {
            if ($isResult) {
                $updatePemeriksaanPenunjang->is_hasil = true;
                if (!$updatePemeriksaanPenunjang->save()) {
                    return $this->responseJson(500, 'Terjadi Kesalahan di server!');
                }
            }
        }
        // LaboratoriumNotification::updateSampleWynacom([
        //     'orderNumber' => $orderNumber,
        //     'details' => $resultItems
        // ]);
        if(!empty($orderNumber) && !empty($hisTestId)) {
            $xxx = HasilPemeriksaanLabWynacom::updateAll(
                ['is_deleted' => true], 
                ['and', 
                ['IN', 'his_reg_no' , $orderNumber ], 
                ['IN', 'his_test_id', $hisTestId]] 
            );
        }
        
        if(!empty($resultItems)) {
            HasilPemeriksaanLabWynacom::batchInsert($resultItems,false);
        }

        return $this->responseJson(200, 'OK', [
            'transactionMessage' => 'Simpan hasil pemeriksaan berhasil!'
        ]);
    }


    /**
     * Function Simpan Bridging Wynacom
     * 
     * @return JSON
     * @author : iqbal.rukmana@sirs.co.id
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionUpdateFile(){

        $request = Yii::$app->request;
        try {
            $hisRegNo = $request->post('his_reg_no', null);
            $query = PasienMasukPenunjang::find()->where(['no_masukpenunjang' => $hisRegNo]);
            
            if (!$query->exists()) {
                return $this->responseJson(422, 'Silahkan Cek Inputan', [
                    'error' => 'Data tidak ditemukan!',
                ]);
            }

            if (empty($hisRegNo)) {
                return $this->responseJson(422, 'Silahkan Cek Inputan', [
                    'error' => 'Data Hasil Tidak Boleh Kosong!',
                ]);
            }
            $isComplete = $request->post('is_complete', null);
            $updateResult = $request->post('update_result', null);
            $imageLink = $request->post('image_link', null);
            $date = date('Y-m-d H:i:s');

            $getPatient = $query->one();
            if ($updateResult || $imageLink ) {
                $arrResultListResults = [];
                $arrResultImageLinkHistory = [];
                if ($updateResult) {
                    if ($getPatient->list_result) {
                        $arrResultListResults = json_decode($getPatient->list_result,true);
                        $arrListResults[] = ['list'=> $updateResult,'dateTime'=>$date];
                        foreach ($arrListResults as $key => $value) {
                            array_push($arrResultListResults, $value);
                        }                   
                    }else{
                        $arrResultListResults[] = ['list'=> $updateResult,'dateTime'=>$date];
                    }
                }else{
                    $model = HasilPemeriksaanLabWynacom::find()
                    ->select(['hasilpemeriksaanlab_wynacom_t.*','daftartindakan_m.daftartindakan_id','daftartindakan_m.daftartindakan_kode'])
                    ->leftJoin('daftartindakan_m','daftartindakan_m.daftartindakan_kode = hasilpemeriksaanlab_wynacom_t.his_test_id')
                    ->andWhere(['his_reg_no' => $hisRegNo])->asArray()->all();
                    $date = date('Y-m-d H:i:s');
                    $arrResultListResults = []; // Initialize the array to hold the results
                    
                    $listOrder = BridgingOrderLabView::find()
                        ->select(['ordered_items'])
                        ->andWhere(['no_order' => $hisRegNo])
                        ->asArray()
                        ->one();
                    $listTest = isset($listOrder['ordered_items']) ? json_decode($listOrder['ordered_items'], true) : [];
                   
                    if(!empty($listTest)){
                        $testResults = [];
                        foreach ($model as $key => $value) {
                           if($value['daftartindakan_kode']==$value['his_test_id']){

                               $testResult = [
                                   'hasilpemeriksaanlab_wynacom_id' => $value['hasilpemeriksaanlab_wynacom_id'],
                                   'his_reg_no' => $value['his_reg_no'],
                                   'his_test_id' => $value['his_test_id'],
                                   'lis_reg_no' => $value['lis_reg_no'],
                                   'lis_test_id' => $value['lis_test_id'],
                                   'test_name' => $value['test_name'],
                                   'test_method' => $value['test_method'],
                                   'result' => $value['result'],
                                   'daftartindakan_id' => $value['daftartindakan_id'],
                                   'result_comment' => $value['result_comment'],
                                   'reference_value' => $value['reference_value'],
                                   'reference_note' => $value['reference_note'],
                                   'test_flag_sign' => $value['test_flag_sign'],
                                   'test_units_name' => $value['test_units_name'],
                                   'instrument_name' => $value['instrument_name'],
                                   'authorization_date' => $value['authorization_date'],
                                   'authorization_user' => $value['authorization_user'],
                                   'greaterthan_value' => $value['greaterthan_value'],
                                   'lessthan_value' => $value['lessthan_value'],
                                   'age_year' => $value['age_year'],
                                   'age_month' => $value['age_month'],
                                   'age_days' => $value['age_days'],
                                   'sequence' => $value['sequence'],
                                   'transfer_flag' => $value['transfer_flag'],
                                   'test_group' => $value['test_group'],
                                   // 'additional_data ' => $value['additional_data '],
                                   
                               ];
                               $testResults[] = $testResult; 
                           }
                
                        }
                        if(count($testResults)>0){
                            $arrResultListResults[] = ['list' => $testResults, 'dateTime' => $date];
                        }

                    }
                    $getPatient->list_result = json_encode($arrResultListResults);

                  
                }

                if ($imageLink) {
                    if ($getPatient->image_link_history) {
                        $arrResultImageLinkHistory = json_decode($getPatient->image_link_history,true);
                        $arrImageLinkHistory[] = ['list'=> $imageLink,'dateTime'=>$date];
                        foreach ($arrImageLinkHistory as $k => $v) {
                            array_push($arrResultImageLinkHistory, $v);
                        }                   
                    }else{
                        $arrResultImageLinkHistory[] = ['link'=> $imageLink,'dateTime'=>$date];
                    }
                }

                $getPatient->image_link_history = json_encode($arrResultImageLinkHistory);
                $getPatient->list_result = json_encode($arrResultListResults);
            }

            $getPatient->image_link = $imageLink;
            $getPatient->image_link_date = $date;
            $getPatient->is_complete = $isComplete;
            $getPatient->status_periksa = DocoConstants::ST_P_PEN_SELESAI;
            $getPatient->tanggal_verifikasi = date('Y-m-d H:i:s');
            $getPatient->update();

            return $this->responseJson(200, 'OK', [
                'transactionMessage' => 'File hasil pemeriksaan berhasil disimpan!'
            ]);

        } catch (\yii\db\Exception $e) {
            return $e->getMessage();
            return $this->responseJson(500, 'Terjadi Kesalahan di server!');
        } catch (\Exception $e) {
            return $e->getMessage();
            return $this->responseJson(500, 'Terjadi Kesalahan di server!');
        }

    }    
}
