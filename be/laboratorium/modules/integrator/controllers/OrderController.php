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
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\integrator\models\BridgingOrderLabView;
use app\modules\integrator\components\LisController;
use app\modules\integrator\models\PasienKirimKeUnitLain;
use Doco\components\DocoConstansId;

class OrderController extends LisController
{

    public $modelClass = '';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return $behaviors;
    }

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["list"] = ["POST"];
        $verbs["detail"] = ["POST"];
        $verbs["flagging"] = ["POST"];
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
     * Function List Lab Data for wynacom bridging
     * 
     * @param String orderDateTimeStart (timestamp)
     * @param String orderDateTimeEnd (timestamp)
     * @param Boolean receiveFlag
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionList()
    {
        $request = Yii::$app->request;
        $start_date = $request->post('orderDateTimeStart',date('Y-m-d 00:00:00'));
        $end_date = $request->post('orderDateTimeEnd',date('Y-m-d H:i:s'));
        $flag = $request->post('receivedFlag',false);
        try {
            if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/",$start_date) == FALSE || preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/",$end_date) == FALSE) {
                return $this->responseJson(400, 'Format Tanggal Tidak Sesuai!');
            }

            $model = new BridgingOrderLabView;
            $query = $model::find()->andWhere(['between','created_date',$start_date,$end_date]);
            if(!$flag){
                $query->andWhere(['or',
                    ['received_flag' => $flag],
                    ['received_flag' => NULL]
                ]);
            }else{
                $query->andWhere(['=','received_flag', $flag]);
            }
            $query->andWhere(['is_bayar' => true]);
            $query->andWhere(['IS NOT', 'ordered_items', null]);
            $query->orderBy([ 'created_date' => SORT_DESC ]);
            $list_data_order_lab = $query->all();
            $message = empty($list_data_order_lab) ? 'Data tidak ditemukan' : 'Data Ditemukan';
            $res_data = [];
            foreach ($list_data_order_lab as $k_orderlab => $v_orderlab) {
                $diagnosaExplode = !empty( $v_orderlab->diagnosa_name) ?  explode(" - ", $v_orderlab->diagnosa_name['text']) : null;
                $additionalPasien = json_decode($v_orderlab->additional_pasien, true);
                $passport_number = null;
                $identity_number = null;
                if(!empty($additionalPasien)) {
                    foreach( $additionalPasien as $key => $item) {
                        if( isset($item['jenisidentitas']) && $item['jenisidentitas'] == '94' ) {
                            $identity_number = $item['no_identitas_pasien'];
                        }

                        if( isset($item['jenisidentitas']) && $item['jenisidentitas'] == '99' ) {
                            $passport_number = $item['no_identitas_pasien'];
                        }
                    }
                }
                $diagnosaKode = $diagnosaName = null;
                if(!is_null($diagnosaExplode)){
                    $diagnosaKode = current($diagnosaExplode);
                    $diagnosaName = end($diagnosaExplode);
                }
                $res_data[] = [
                    'mr_no' => @$v_orderlab->no_rekam_medik,
                    'gender_id' => @$v_orderlab->gender_id,
                    'gender_name' => @$v_orderlab->gender_name,
                    'birth_date' => @$v_orderlab->dateofbirth,
                    'patient_name' => @$v_orderlab->patient_name,
                    'identity_number' => $identity_number,
                    'passport_number' => $passport_number,
                    'patient_address' => @$v_orderlab->patient_address,
                    'city_id' => @$v_orderlab->city_id,
                    'city_name' => @$v_orderlab->city_name,
                    'phone_number'=> @$v_orderlab->phone_number,
                    'fax_number'=>@$v_orderlab->fax_number,
                    'mobile_number'=>@$v_orderlab->mobile_number,
                    'email' => @$v_orderlab->email,
                    'visit_number'=>@$v_orderlab->visit_number,
                    'order_number'=>@$v_orderlab->no_order,
                    'order_datetime'=>@$v_orderlab->order_datetime,
                    'service_unit_id'=>@$v_orderlab->service_unit_id,
                    'service_unit_name'=>@$v_orderlab->service_unit_name,
                    'guarantor_id'=>@$v_orderlab->guarantor_id,
                    'guarantor_name'=>@$v_orderlab->guarantor_name,
                    'agreement_id'=>@$v_orderlab->aggreement_id,
                    'agreement_name'=>@$v_orderlab->aggreement_name,
                    'doctor_id'=>@$v_orderlab->doctor_id,
                    'doctor_name'=>@$v_orderlab->doctor_name,
                    'class_id'=>@$v_orderlab->class_id,
                    'class_name'=>@$v_orderlab->class_name,
                    'ward_id'=>@$v_orderlab->ward_id,
                    'ward_name'=>@$v_orderlab->ward_name,
                    'room_id'=>@$v_orderlab->room_id,
                    'room_name'=>@$v_orderlab->room_name,
                    'bed_id'=>@$v_orderlab->bed_id,
                    'bed_name'=>@$v_orderlab->bed_name,
                    'diagnosa_id'=> $diagnosaKode,
                    'diagnosa_name'=> $diagnosaName,
                    'is_cyto'=>@$v_orderlab->is_cyto,
                    'reg_user_id'=>@$v_orderlab->reg_user_id,
                    'reg_user_name'=>@$v_orderlab->reg_user_name,
                    'LIS_REG_NO'=>@$v_orderlab->lis_reg_no,
                    'ReceivedFlag'=>@$v_orderlab->received_flag,
                    'ReceivedDatetime'=>@$v_orderlab->retrieved_dt,
                    'kode_negara' => @$v_orderlab->kode_negara,
                    'negara' => @$v_orderlab->negara,
                    'kode_kemendag_propinsi' => @$v_orderlab->kode_kemendag_propinsi,
                    'kode_kemendag_kabupaten' => @$v_orderlab->kode_kemendag_kabupaten,
                    'kode_kemendag_kecamatan' => @$v_orderlab->kode_kemendag_kecamatan,
                    'kode_kemendag_kelurahan' => @$v_orderlab->kode_kemendag_kelurahan,
                    'propinsi' => @$v_orderlab->propinsi,
                    'kabupaten' => @$v_orderlab->kabupaten,
                    'kecamatan' => @$v_orderlab->kecamatan,
                    'kelurahan' => @$v_orderlab->kelurahan,
                    'rt' => @$v_orderlab->rt,
                    'rw' => @$v_orderlab->rw,
                ];
            }
            return $this->responseJson(200, $message, $res_data);
        } catch (\yii\db\Exception $e) {
            return $e->getMessage();
            return $this->responseJson(500, 'Terjadi Kesalahan Pada Server');
        } catch (\Exception $e) {
            return $e->getMessage();
            return $this->responseJson(500, 'Terjadi Kesalahan Pada Server');
        }
    }
    /**
     * Function Detail Order for Wynacom Bridging 
     * 
     * @param String orderNumber
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionDetail()
    {
        $request = Yii::$app->request;
        $order_number = $request->post('orderNumber',null);
        try{
            $model = new BridgingOrderLabView;
            $data_order = $model::find()
                            ->where(['no_order'=>$order_number])
                            ->andWhere(['is_bayar' => true])
                            ->andWhere(['IS NOT', 'ordered_items', null])
                            ->one();
            $message = empty($data_order) ? 'Data tidak ditemukan' : 'Data Ditemukan';
            if(is_null($data_order)){
                return $this->responseJson(404, 'Data dengan nomor order '. $order_number. ' Tidak Ditemukan');
            }
            $diagnosaExplode = !empty( $data_order->diagnosa_name) ?  explode(" - ", $data_order->diagnosa_name['text']) : null;
            $additionalPasien = json_decode($data_order->additional_pasien, true);
            $passport_number = null;
            $identity_number = null;
            if(!empty($additionalPasien)) {
                foreach( $additionalPasien as $key => $item) {
                    if( isset($item['jenisidentitas']) && $item['jenisidentitas'] == '94' ) {
                        $identity_number = $item['no_identitas_pasien'];
                    }

                    if( isset($item['jenisidentitas']) && $item['jenisidentitas'] == '99' ) {
                        $passport_number = $item['no_identitas_pasien'];
                    }
                }
            }
            $diagnosaKode = $diagnosaName = null;
            if(!is_null($diagnosaExplode)){
                $diagnosaKode = current($diagnosaExplode);
                $diagnosaName = end($diagnosaExplode);
            }

            if(empty($data_order->ordered_items)){
                $no_order = $data_order->no_order;
                $sql_header = "
                    SELECT 
                        tindakanpelayanan_t.tipepaket_id, 
                        pasienmasukpenunjang_t.pasienmasukpenunjang_id, 
                        pasienmasukpenunjang_t.no_masukpenunjang
                    FROM pasienmasukpenunjang_t 
                    join tindakanpelayanan_t on pasienmasukpenunjang_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                    WHERE pasienmasukpenunjang_t.no_masukpenunjang = '{$no_order}'
                    AND tindakanpelayanan_t.tipepaket_id IS NOT NULL";

                $data_header = Yii::$app->db->createCommand($sql_header)->queryOne();
                $tipepaket_id = !empty($data_header['tipepaket_id']) ? $data_header['tipepaket_id'] : null;
                $pasienmasukpenunjang_id = !empty($data_header['pasienmasukpenunjang_id']) ? $data_header['pasienmasukpenunjang_id'] : null;
                $instalasi_id = (new DocoConstansId)->actionGetId('LAB');

                if (!empty($tipepaket_id)) {
                    $sql = "
                        SELECT 
                            paketpelayanan_mp.daftartindakan_id,
                            daftartindakan_m.daftartindakan_nama,
                            daftartindakan_m.daftartindakan_kode 
                        FROM paketpelayanan_mp 
                        join ruangan_m on ruangan_m.ruangan_id = paketpelayanan_mp.ruangan_id
                        join daftartindakan_m on paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id 
                        where tipepaket_id = {$tipepaket_id} AND ruangan_m.instalasi_id = {$instalasi_id}
                        ";
                    $data_order_item = Yii::$app->db->createCommand($sql)->queryAll();
                    $data = [];
                    foreach($data_order_item as $val){
                        $daftartindakan_id = !empty($val['daftartindakan_id']) ?  $val['daftartindakan_id'] : null;
                        $daftartindakan_nama = !empty($val['daftartindakan_nama']) ?  $val['daftartindakan_nama'] : null;
                        $daftartindakan_kode = !empty($val['daftartindakan_kode']) ?  $val['daftartindakan_kode'] : null;
                        $data[] = [
                            "no_order" => $no_order,
                            "no_pemeriksaan" => $daftartindakan_id,
                            "order_item_id" => $daftartindakan_kode,
                            "order_item_name" => $daftartindakan_nama,
                            "pasienmasukpenunjang_id" => $pasienmasukpenunjang_id
                        ];
                    }
                    $data_order->ordered_items = $data;
                }
            }

            return $this->responseJson(200, $message, [
                    'mr_no' => @$data_order->no_rekam_medik,
                    'gender_id' => @$data_order->gender_id,
                    'gender_name' => @$data_order->gender_name,
                    'birth_date' => @$data_order->dateofbirth,
                    'patient_name' => @$data_order->patient_name,
                    'identity_number' => $identity_number,
                    'passport_number' => $passport_number,
                    'patient_address' => @$data_order->patient_address,
                    'city_id' => @$data_order->city_id,
                    'city_name' => @$data_order->city_name,
                    'phone_number'=> @$data_order->phone_number,
                    'fax_number'=>@$data_order->fax_number,
                    'mobile_number'=>@$data_order->mobile_number,
                    'email' => @$data_order->email,
                    'visit_number'=>@$data_order->visit_number,
                    'order_number'=>@$data_order->no_order,
                    'order_datetime'=>@$data_order->order_datetime,
                    'service_unit_id'=>@$data_order->service_unit_id,
                    'service_unit_name'=>@$data_order->service_unit_name,
                    'guarantor_id'=>@$data_order->guarantor_id,
                    'guarantor_name'=>@$data_order->guarantor_name,
                    'agreement_id'=>@$data_order->aggreement_id,
                    'agreement_name'=>@$data_order->aggreement_name,
                    'doctor_id'=>@$data_order->doctor_id,
                    'doctor_name'=>@$data_order->doctor_name,
                    'class_id'=>@$data_order->class_id,
                    'class_name'=>@$data_order->class_name,
                    'ward_id'=>@$data_order->ward_id,
                    'ward_name'=>@$data_order->ward_name,
                    'room_id'=>@$data_order->room_id,
                    'room_name'=>@$data_order->room_name,
                    'bed_id'=>@$data_order->bed_id,
                    'bed_name'=>@$data_order->bed_name,
                    'diagnosa_id'=> $diagnosaKode,
                    'diagnosa_name'=> $diagnosaName,
                    'is_cyto'=>@$data_order->is_cyto,
                    'reg_user_id'=>@$data_order->reg_user_id,
                    'reg_user_name'=>@$data_order->reg_user_name,
                    'LIS_REG_NO'=>@$data_order->lis_reg_no,
                    'ReceivedFlag'=>@$data_order->received_flag,
                    'ReceivedDatetime'=>@$data_order->retrieved_dt,
                    'kode_negara' => @$data_order->kode_negara,
                    'negara' => @$data_order->negara,
                    'kode_kemendag_propinsi' => @$data_order->kode_kemendag_propinsi,
                    'kode_kemendag_kabupaten' => @$data_order->kode_kemendag_kabupaten,
                    'kode_kemendag_kecamatan' => @$data_order->kode_kemendag_kecamatan,
                    'kode_kemendag_kelurahan' => @$data_order->kode_kemendag_kelurahan,
                    'propinsi' => @$data_order->propinsi,
                    'kabupaten' => @$data_order->kabupaten,
                    'kecamatan' => @$data_order->kecamatan,
                    'kelurahan' => @$data_order->kelurahan,
                    'rt' => @$data_order->rt,
                    'rw' => @$data_order->rw,
                    'OrderItems' => @$data_order->ordered_items,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            \Yii::error([
                "mm" => $e->getMessage()
            ]);
            return [
                'status' => 500,
                'message'=>'Gagal'
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            \Yii::error([
                "handap" => $e->getMessage()
            ]);
            return [
                'status' => 500,
                'message'=>'Gagal'
            ];
        }
    }
    /**
     * Function Update Order Flag for Wynacom Bridging
     * 
     * @param String orderNumber
     * @param String LIS_REG_NO
     * @param Boolean receivedFlag
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionFlagging()
    {
        $request = Yii::$app->request;
        $order_number = $request->post('orderNumber',null);
        $lis_reg_no = $request->post('LIS_REG_NO',null);
        $flag = $request->post('receivedFlag',false);
        if(is_null($order_number) || is_null($lis_reg_no)){
            return $this->responseJson(422, 'Parameter orderNumber dan LIS_REG_NO tidak boleh kosong!');
        }
        try{
            $model = PasienKirimKeUnitLain::find()
                     ->where(['pasienmasukpenunjang_t.no_masukpenunjang' => $order_number])
                     ->rightJoin('pasienmasukpenunjang_t', 'pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id')
                     ->one();
            if( empty($model) ){
                $model = PasienMasukPenunjangT::find()->where(['no_masukpenunjang' => $order_number])->one();
                if( empty($model) ){
                    return $this->responseJson(404, 'Data Tidak Ditemukan!');
                }
            }
            $additionalData = empty($model->additional_data) ? [] : json_decode($model->additional_data, true) ;
            $model->additional_data = json_encode(array_merge([
                'lisattr' => [
                    'lis_reg_no' => $lis_reg_no,
                    'retrieved_dt' => date('Y-m-d H:i:s'),
                    'status' => 0,
                    'retrieved_flag' => $flag ? 1 : 0,
                    'received_flag' => $flag ? 1 : 0
                ]
            ], $additionalData));
            if( !$model->save() ){
                Yii::error([
                    'logdata' => $model->getErrors()
                ]);
                return $this->responseJson(422, 'Terjadi Kesalahan!');
            }
            return $this->responseJson(200, 'berhasil update status penerimaan data pemesanan dengan nomor '.$order_number, [
                'orderNumber' => $order_number,
                "LIS_REG_NO" => $lis_reg_no,
                'ReceivedFlag' => $flag ? true : false,
                'ReceivedDatetime' => date('Y-m-d H:i:s')
            ]);
        }catch(\Exception $e){
            Yii::error([
                'logdata' => $e->getMessage()
            ]);
            return $this->responseJson(500, 'Terjadi Kesalahan di server!');
        }
    }
}