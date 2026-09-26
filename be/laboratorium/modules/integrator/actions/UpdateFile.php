<?php

namespace app\modules\integrator\actions;

use Yii;
use yii\base\Action;
use app\modules\integrator\models\PasienMasukPenunjang;
use Doco\Traits\ControllerHelperTrait;

class UpdateFile extends Action
{

    use ControllerHelperTrait;

    public function run()
    {  
        $request = Yii::$app->request;
        Yii::error(
            'Message : Payload Accepted --||--Line : 55 --||--File : UpdateFile.php --||--API URL : ' . Yii::$app->request->getPathInfo() . '--||--Method : POST --||--Payload : ' . json_encode($request->post()),
            'server-error'
        );
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
