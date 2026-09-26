<?php

namespace app\modules\v1\Traits;

use app\modules\v1\models\RujukBalik;
use app\modules\v1\models\SignaObat;
use Doco\models\bpjs\Bpjs;
use Yii;
use yii\helpers\ArrayHelper;

trait RujukBalikTrait
{

    /**
     * This function will return data form asesmen medis
     * 
     * @param String $pendaftaran_id
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetRujukBalik($pendaftaran_id)
    {
        // firstly check data periksa fisik
        $limit = Yii::$app->request->get('per-page', 10);
        $page = Yii::$app->request->get('page', 1);

        $query = RujukBalik::find(true)
            ->select([
                't.rujukbalik_id', 't.no_srb', 't.is_deleted', 't.parent_id', 'pegawai_m.nama_pegawai as peg_deleted_nama',
                't.created_date', 't.tgl_rujukbalik', 'pasien_m.no_rekam_medik', 'bpjs_t.nosep', 'count(child.rujukbalik_id) as has_child'])
            ->alias('t')
            ->innerJoin('pendaftaran_t', 'pendaftaran_t.pendaftaran_id = t.pendaftaran_id')
            ->innerJoin('pasien_m', 'pasien_m.pasien_id = pendaftaran_t.pasien_id')
            ->leftJoin('rujukbalik_t as child', 't.rujukbalik_id = child.parent_id')
            ->leftJoin('bpjs_t', 'bpjs_t.bpjs_id = pendaftaran_t.bpjs_id')
            ->leftJoin('loginpemakai_k', 'loginpemakai_k.loginpemakai_id = t.deleted_by')
            ->leftJoin('pegawai_m', 'pegawai_m.pegawai_id = loginpemakai_k.pegawai_id')
            ->andWhere(['t.pendaftaran_id' => $pendaftaran_id]);
        $totalRecord = $query->count();
        $records = $query
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->orderBy(['t.created_date' => SORT_DESC])
            ->asArray()
            ->groupBy(['t.rujukbalik_id', 't.no_srb', 't.is_deleted', 't.parent_id', 'pegawai_m.nama_pegawai',
                't.created_date', 't.tgl_rujukbalik', 'pasien_m.no_rekam_medik', 'bpjs_t.nosep'])
            ->all();
        return [
            'recordsFiltered' => $totalRecord,
            'recordsTotal' => $totalRecord,
            'data' => $records,
        ];
    }

    public function actionGetDataRujukBalik($pendaftaran_id, $rujukbalik_id)
    {
        try {
            $model = RujukBalik::find()
                ->select([
                    'rujukbalik_t.*', 'pasien_m.no_rekam_medik', 'bpjs_t.nosep', 'pasien_m.nama_pasien'
                ])
                ->innerJoin('pendaftaran_t', 'pendaftaran_t.pendaftaran_id = rujukbalik_t.pendaftaran_id')
                ->innerJoin('pasien_m', 'pasien_m.pasien_id = pendaftaran_t.pasien_id')
                ->leftJoin('bpjs_t', 'bpjs_t.bpjs_id = pendaftaran_t.bpjs_id')
                ->andWhere(['rujukbalik_t.pendaftaran_id' => $pendaftaran_id, 'rujukbalik_t.rujukbalik_id' => $rujukbalik_id])
                ->asArray()
                ->one();
            if(empty($model)) {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => 'Data tidak ditemukan'
                ];
            }
            $dataBpjs = Bpjs::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
            $listDpjp = [];
            $listDiagnosa = [];
            if(!empty($dataBpjs)) {
                $tglsep = strtotime($dataBpjs->tglsep);
                $result = $dataBpjs->referensiDokter($dataBpjs->jnspelayanan, date('Y-m-d', $tglsep), $dataBpjs->politujuan);
                $listDpjp = isset($result['response']['list']) && !empty($result['response']['list']) ? $result['response']['list'] : [];

                $result = $dataBpjs->diagnosaprb();
                $listDiagnosa = isset($result['response']['list']) && !empty($result['response']['list']) ? $result['response']['list'] : [];
            }

            // rekondisi data signa yang tidak komplit
            $data_reseptur = json_decode($model['data_reseptur'], true);
            if(!empty($data_reseptur)) {
                $list_signaid = ArrayHelper::getColumn($data_reseptur, 'signa_id');
                $list_signa = SignaObat::find()->andWhere(['signa_id' => $list_signaid])->asArray()->all();
                $list_signa = ArrayHelper::index($list_signa, 'signa_id');
                foreach ($data_reseptur as $key => $reseptur) {
                    $signa_id = $reseptur['signa_id'];
                    if(isset($list_signa[$signa_id])) {
                        $reseptur['signa_nama'] = @$list_signa[$signa_id]['signa_nama'];
                        $reseptur['signa'] = json_encode([
                            'id' => $signa_id,
                            'text' => @$list_signa[$signa_id]['signa_nama'],
                            'kode' => @$list_signa[$signa_id]['signa_kode'],
                        ]);
                        $reseptur['qty_signa'] = @$list_signa[$signa_id]['qty_obat'];
                        $reseptur['iterasi_signa'] = @$list_signa[$signa_id]['iterasi'];
                    }
                    $data_reseptur[$key] = $reseptur;
                }
                $data_reseptur = array_values($data_reseptur);
                $model['data_reseptur'] = json_encode($data_reseptur);
            }

            return [ 
                'data' => $model,
                'list-dpjp-bpjs' => $listDpjp,
                'list-diagnosa' => $listDiagnosa,
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

    public function actionUpdateRujukBalik($rujukbalik_id)
    {
        try {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            $request = Yii::$app->request;

            $oldModel = RujukBalik::find()
                ->andWhere(['rujukbalik_id' => $rujukbalik_id])
                ->one();
            if($oldModel == null) {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => 'Data tidak ditemukan'
                ];
            }
            $model = new RujukBalik;
            $attributes = $oldModel->attributes;
            unset($attributes['rujukbalik_id']);
            $model->attributes = $attributes;
            $model->parent_id = $oldModel->rujukbalik_id;

            $postData = $request->post('rujukBalik', []);

            // processing data reseptur
            $postDataReseptur =  isset($postData['data_reseptur']) ? $postData['data_reseptur'] : [];
            $data_reseptur =  json_decode($model->data_reseptur, true);
            $obat = [];
            foreach ($data_reseptur as $key => $resep) {
                if(isset($postDataReseptur[$resep['resepturdetail_id']])) {
                    $reseptur =  $postDataReseptur[$resep['resepturdetail_id']];

                    if(isset($reseptur['kode_bpjs']) && !empty($reseptur['kode_bpjs'])) {
                        $obat[] = [
                            "kdObat" => $reseptur["kode_bpjs"],
                            "signa1" => $reseptur["qty_signa"] ? : "1",
                            "signa2" => $reseptur["iterasi_signa"] ? : "1",
                            "jmlObat" => $reseptur["qty_reseptur"],
                        ];
                    }
                    $resep['kode_bpjs'] = $reseptur['kode_bpjs'];
                    $resep['obatalkes_nama'] = $reseptur['obatalkes_nama'];
                    $resep['signa'] = json_encode($reseptur['signa']);
                    $resep['signa_id'] = isset($reseptur['signa']['id']) ? 
                        $reseptur['signa']['id'] :  $resep['signa_id'];
                    $resep['signa_nama'] = isset($reseptur['signa']['text']) ?
                        $reseptur['signa']['text'] : $resep['signa_nama'];
                    $resep['qty_reseptur'] = $reseptur['qty_reseptur'];

                }
                $data_reseptur[$key] = $resep;
            }
            $postData['data_reseptur'] = json_encode(array_values($data_reseptur));
            
            $model->attributes = $postData;

            // processing data bpjs
            $additional_data = json_decode($model->additional_data, true);

            $t_prb = $additional_data['t_prb'];
            $t_prb['noSrb'] = $model->no_srb;
            $t_prb["alamat"] = $model->alamat;
            $t_prb["email"] = $model->email;
            // $t_prb["programPRB"] = $model->diagnosa;
            $t_prb["kodeDPJP"] = $model->kode_dpjp;
            $t_prb["saran"] = $model->saran;
            $t_prb["obat"] = $obat;
            unset($t_prb['noKartu']);
            unset($t_prb['programPRB']);

            $modelBpjs = new Bpjs;
            $respPRB = $modelBpjs->updatePRB($t_prb);
           
            if(!isset($respPRB['response']) || $respPRB['response'] != $model->no_srb) {
                $transaction->rollBack();
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => 'Gagal Update PRB'
                ];
            }
            $model->additional_data = json_encode(['t_prb' => $t_prb, 'res_prb' => $respPRB]);

            if($model->save() && $oldModel->delete()) {
                $transaction->commit();
                return ['message' => 'Data Berhasil di update'];
            } else {
                $transaction->rollBack();
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => 'Data gagal di update'
                ];
            }

        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            throw $e;

            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;

            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }

    }

    public function actionDeleteRujukBalik($rujukbalik_id)
    {
        try {
            $model = RujukBalik::find()
                ->andWhere(['rujukbalik_id' => $rujukbalik_id])
                ->one();
            if($model == null) {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => 'Data tidak ditemukan'
                ];
            }
            $additional_data = json_decode($model->additional_data, true);
            $t_prb = [
                "noSrb" => $model->no_srb,
                "noSep" => $additional_data['t_prb']['noSep'],
                "user"  => $additional_data['t_prb']['user'],
            ];

            $modelBpjs = new Bpjs;
            $respPRB = $modelBpjs->deletePRB($t_prb);

            $additional_data['res_prb_deleted'] = $respPRB;
            $model->additional_data = json_encode($additional_data);

            if($model->delete()) {
                return ['message' => 'Data Berhasil di hapus'];
            } else {
                \Yii::$app->response->statusCode = 500;
                return [
                    'message' => 'Data gagal di delete'
                ];
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

}
