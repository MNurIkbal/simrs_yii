<?php

namespace Doco\Traits;

use Yii;
use Doco\models\Pegawai;
use Doco\models\Pendaftaran;
use Doco\models\ResumeMedisRi;
use Doco\models\PasienMasukPenunjang;
use Doco\models\radiologi\HasilPemeriksaanRadView;
use Doco\models\radiologi\PemeriksaanPasienRadiologiView;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoConstansId;
use app\modules\v1\models\AsesmenMedisIGD;
use app\modules\v1\models\HasilPemeriksaanLabRoche; //bedah
use app\modules\v1\models\HasilPemeriksaanLabWynacom;
use yii\db\Expression;

trait ResumeMedisTrait
{
    protected $diagnosaView;
    protected $hasilPemeriksaanLab;

    public function actionGetDataResumeMedis()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        try {
            return ResumeMedisRi::resumeByRegistrationId($pendaftaran_id);
        } catch (Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            return $this->responseJson(500, 'Terjadi Kesalahan pada server');
        }
    }

    /**
     * @todo Method untuk mendapatkan data diagnosa berdasarkan versi tabular list
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetNewDiagnosa()
    {
        try {
            $get = Yii::$app->request->get();
            $q = $get['q'];
            $type = $get['type'];
            $page = Yii::$app->request->get('page',0);
            $limit = Yii::$app->request->get('limit',5);
            $offset = Yii::$app->request->get('offset',0);

            $is_perawat = !empty($get['is_perawat'])?$get['is_perawat']:0;
            $kelompok_diagnosa = DocoConstants::$mapp_kel_diagnosa;

            if ($type == DocoConstants::VAR_KELOMPOK_DIAGNOSA_MASUK) {
                $tabularlist_versi = 'ICD X';
            } else if($type == DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA) {
                $tabularlist_versi = 'ICD X';
            } else if($type == DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA) {
                $tabularlist_versi = 'ICD X';
            } else if($type == DocoConstants::VAR_KELOMPOK_DIAGNOSA_KELUARGA) {
                $tabularlist_versi = 'ICD X';
            } else {
                $tabularlist_versi = 'ICD IX';
            }

            if ($is_perawat) {
                $tabularlist_versi = 'ICD_KEP';
            }

            $model = $this->diagnosaView->find()->andWhere(['is_deleted' => false, 'is_active' => true]);

            if (isset($q) && $q != '') {
                $model->andWhere(['ilike', 'diagnosa_nama', $q]);
                $model->orWhere(['ilike', 'diagnosa_kode', $q]);
            }

            if (isset($tabularlist_versi) && $tabularlist_versi != '') {
                $model->andWhere(['tabularlist_versi' => $tabularlist_versi]);
            }

            return $model->offset($offset)->limit($limit)->all();
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionResumeResult($pendaftaran_id, $type) {
        switch ($type) {
            case 'lab-external':
                return $this->getLabExternalResult($pendaftaran_id);
                break;

            case 'lab':
                return $this->getLabResult($pendaftaran_id);
                break;

            case 'rad':
                return $this->getRadResult($pendaftaran_id);
                break;
        }

        return $this->responseJson(500, 'Terjadi kesalahan pada server');
    }


    /**
     * Return data lab
     *
     * @param String $pendaftaran_id
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    private function getLabResult($pendaftaran_id)
    {
        // $page = Yii::$app->request->get('page');
        $paginationOption = Yii::$app->request->get('paginationOption', []);
        $record = $this->hasilPemeriksaanLab->resultByRegistration($pendaftaran_id, $paginationOption);
        if ($record['status'] == 200) {
            return $record;
        } else {
            return $this->responseJson(isset($record['status']) ? $record['status'] : 500, isset($record['message']) ? $record['message'] : 'Terjadi kesalahan pada server');
        }
    }

    /**
     * return result radiologi which already verified
     *
     * @param String $pendaftaran_id
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    private function getRadResult($pendaftaran_id)
    {
        // first get order by pendaftaran id
        $orders = PemeriksaanPasienRadiologiView::find()
            ->select(['pasienmasukpenunjang_id'])
            ->andWhere(compact('pendaftaran_id'))
            ->asArray()
            ->all();
        $result = [];
        $orderIds = [];
        for ($i=0; $i < count($orders); $i++) {
            if (!in_array($orders[$i]['pasienmasukpenunjang_id'], $orderIds)) {
                $orderIds[] = $orders[$i]['pasienmasukpenunjang_id'];
            }
        }
        $query = HasilPemeriksaanRadView::find()
            ->select(['no_hasilrad', 'tgl_hasilrad', 'daftartindakan_nama', 'kesan as deskripsi', 'kesimpulan as kesan'])
            ->andWhere(['in', 'pasienmasukpenunjang_id', $orderIds])
            ->andWhere(['IS NOT', 'tgl_verifikasi', null]);
        $paginationOption = Yii::$app->request->get('paginationOption', []);
        $additionalResponse = [];
        if (isset($paginationOption['page']) && isset($paginationOption['limit'])) {
            $additionalResponse['total'] = $query->count();
            $query = $query->offset(($paginationOption['page'] - 1) * $paginationOption['limit'])->limit($paginationOption['limit']);
        }
        $result['data'] = $query->asArray()
            ->all();
        return array_merge($result, $additionalResponse);
    }

    private function generateTextNewLine($string, $delimiter = null)
    {
        $newString = '';
        $delimiter = is_null($delimiter) ? "\r" : $delimiter;
        $decodeText = explode($delimiter, $string);
        for ($i=0; $i < count($decodeText); $i++) {
            $string = trim(preg_replace('/\s\s+/', ' ', $decodeText[$i]));
            $tmp[$i] = "<p>". $string ."</p>\n";
        }
        $newString = implode($tmp);
        return $newString;
    }

    /**
     * This function will save data resume medis
     *
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveResumeMedis($pendaftaran_id)
    {
        $registrationRecord = Pendaftaran::find()
            ->select(['pendaftaran_id'])
            ->andWhere(compact('pendaftaran_id'))
            ->asArray()
            ->one();

        if (empty($registrationRecord)) {
            return $this->responseJson(400, 'Pendaftaran tidak ditemukan');
        }
        // $encodedFields = ResumeMedisRIT::$encodedFields;
        $payload = Yii::$app->request->post();
        $model = ResumeMedisRi::find()->select(['resumemedisri_id', 'pendaftaran_id'])->andWhere(['pendaftaran_id' => $pendaftaran_id])->one();
        if (empty($model)) {
            $model = new ResumeMedisRi;
        }
        if(isset($payload['order_laboratorium']) && is_string($payload['order_laboratorium'])) {
            $payload['order_laboratorium'] = [
                'text' => $payload['order_laboratorium']
            ];
        }
        if(isset($payload['order_radiologi']) && is_string($payload['order_radiologi'])) {
            $payload['order_radiologi'] = [
                'text' => $payload['order_radiologi']
            ];
        }

        $payload['tgl_masuk'] = !empty($payload['tgl_masuk']) ? date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $payload['tgl_masuk']))) : null;
        $payload['tgl_keluar'] = !empty($payload['tgl_keluar']) ? date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $payload['tgl_keluar']))) : null;
        $model->attributes = $payload;
        $model->pendaftaran_id = $pendaftaran_id;
        if (!$model->save()) {
            Yii::error([
                "Message" => "Proses simpan Resume medis gagal",
                "Bucket" => $model->errors
            ]);
            return $this->responseJson(400, 'Terjadi kesalahan pada proses penyimpanan data.');
        }
        return $this->responseJson(200, 'Simpan Data Resume Medis Sukses!');
    }

    protected function getLabExternalResult($pendaftaran_id)
    {
        $orders = PasienMasukPenunjang::find()
        ->leftJoin('ruangan_m','ruangan_m.ruangan_id = pasienmasukpenunjang_t.ruangan_id')
        ->leftJoin('pasien_m','pasien_m.pasien_id = pasienmasukpenunjang_t.pasien_id')
        ->leftJoin('pendaftaran_t','pendaftaran_t.pasien_id = pasien_m.pasien_id')
        ->andWhere(['pendaftaran_t.pendaftaran_id'=>$pendaftaran_id])
        ->andWhere(['IS NOT','pasienmasukpenunjang_t.list_result',null])
        ->andWhere(['ruangan_m.instalasi_id'=>DocoConstansId::actionGetId('LAB')])
        ->asArray()
        ->all();

        $result = [];
        $listOrder = [];
        $listResultWynacom = [];
        $listResultRoche = [];
        if ($orders) {
            foreach($orders as $_order){
                $listOrder[] = $_order['no_masukpenunjang'];

            }
            if(count($listOrder)>0 ){
                $listResultWynacom = HasilPemeriksaanLabWynacom::find()
                ->select([
                    'hasilpemeriksaanlab_wynacom_t.*',
                    new Expression('NULL as daftartindakan_id'),
                    new Expression('NULL as daftartindakan_nama')
                ])->andWhere(['his_reg_no' => $listOrder])->asArray()->all();
                $listResultRoche = HasilPemeriksaanLabRoche::find()->andWhere(['order_no' => $listOrder])->asArray()->all();
            }

            //cari hasil lab wynacom berdasarkan no order
            foreach($orders as $order){
                $list_result = json_decode($order['list_result'], true) ?: [];
                if(count($list_result)<1){
                    if(count($listResultRoche)>0){
                            $tmp = [];
                            $tmp['nohasilperiksalab'] = preg_replace('/[^0-9]+/', '', $order['tanggal_verifikasi']);
                            $tmp['tgl_hasilpemeriksaanlab'] = $order['tanggal_verifikasi'];
                            $tmp['results'] = [];
                            
                            foreach ($listResultRoche as $keyListResultRoche => $listRoche) {
                                $tmp['results'][] = [
                                    'daftartindakan_id' => $listRoche['obv_id'],
                                    'daftartindakan_nama' => $listRoche['obv_name'],
                                    'nama_rujukan' => $listRoche['obv_name'],
                                    'hasil' => $listRoche['value']
                                ];
                            }
                            $result[] = $tmp;
                        
                    }else if(count($listResultWynacom)>0){
                        $tmp = [];
                        $tmp['nohasilperiksalab'] = preg_replace('/[^0-9]+/', '', $order['tanggal_verifikasi']);
                        $tmp['tgl_hasilpemeriksaanlab'] = $order['tanggal_verifikasi'];
                        $tmp['results'] = [];
                        
                        foreach ($listResultWynacom as $keyListResultWynacom => $listWynacom) {
                            if ($order['no_masukpenunjang'] == $listWynacom['his_reg_no']) {
                                $tmp['results'][] = [
                                    'daftartindakan_id' => $listWynacom['lis_test_id'],
                                    'daftartindakan_nama' => $listWynacom['test_name'],
                                    'nama_rujukan' => $listWynacom['test_name'],
                                    'hasil' => $listWynacom['result']
                                ];
                            }
                        }
                        $result[] = $tmp;

                    }

                }else{
                    if(count($listResultRoche)>0){
                            
                        foreach ($list_result as $index => $list) {
                            $tmp = [];
                            $tmp['nohasilperiksalab'] = preg_replace('/[^0-9]+/', '', $list['dateTime']);
                            $tmp['tgl_hasilpemeriksaanlab'] = $list['dateTime'];
                            $tmp['results'] = [];
                            
                            foreach ($list['list'] as $item) {
                                $tmp['results'][] = [
                                    'daftartindakan_id' => $item['obv_id'],
                                    'daftartindakan_nama' => $item['obv_name'],
                                    'nama_rujukan' => $item['obv_name'],
                                    'hasil' => $item['value']
                                ];
                            }
                            $result[] = $tmp;
                        }
                    }else if(count($listResultWynacom)>0){
                        foreach ($list_result as $index => $list) {
                            $tmp = [];
                            $tmp['nohasilperiksalab'] = preg_replace('/[^0-9]+/', '', $list['dateTime']);
                            $tmp['tgl_hasilpemeriksaanlab'] = $list['dateTime'];
                            $tmp['results'] = [];
                            
                            foreach ($list['list'] as $item) {
                                $tmp['results'][] = [
                                    'daftartindakan_id' => $item['daftartindakan_id'],
                                    'daftartindakan_nama' => $item['test_name'],
                                    'nama_rujukan' => $item['test_name'],
                                    'hasil' => $item['result']
                                ];
                            }
                            $result[] = $tmp;
                        }

                    }
                }
            }
        }
        return [
            'status' => 200,
            'data' => $result
        ];
    }

}
