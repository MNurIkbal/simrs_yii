<?php

namespace Doco\Traits;

use Doco\models\KegiatanKeperawatan;
use Doco\models\NursingNote;
use Doco\models\JenisKasusPenyakit;
use Doco\models\KelasPelayanan;
use Doco\models\Penjamin;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use Doco\models\InfoDokterView;
use Doco\models\Pegawai;
use Doco\models\Ruangan;
use Doco\models\WorklistPasien;
use Doco\models\Lookup;
use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\models\Pendaftaran;

/**
 * Trait of Nursing Note
 */
trait NursingNoteTrait
{
    /**
     * This function will return API datatable
     * 
     * @return Json
     * @author : Ilhamsyah 
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */

    public function actionGetKegiatanKeperawatan(){
        try {
            $request = Yii::$app->request;
            $id = $request->get('id'. null);
            $jenis_kegiatan = $request->get('jenis_kegiatan'. null);
            $cari = $request->get('cari'. null);
    
            $data = KegiatanKeperawatan::find()->where(['is_deleted' => false]);
            if(!is_null($jenis_kegiatan)){
                $data->andWhere(['jenis_kegiatan' => $jenis_kegiatan ]);
            }
            if(!is_null($cari)){
                $data->andWhere(['LIKE', 'LOWER(nama_kegiatan)', strtolower($cari)]);
            }

            $items = ArrayHelper::map($data->all(), 'kegiatankeperawatan_id', 'nama_kegiatan');
            
            $result = [
                'data' => $items,
                'totalResult' => count($items),
                'query' => $data->asArray()->all()
            ];
            return $result;
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

    public function actionSaveNursingNote($id,$pasienadmisi_id = null,$catatankeperawatan_id=null)
    {
        try {
            $transaction = Yii::$app->db->beginTransaction();
            $request = Yii::$app->request;
            $payload = Yii::$app->request->post('NursingNoteForm');
            $pendaftaran_id  = DocoHelpers::decrypt($id);
            $count =count($payload['kegiatan_perawat']);

            if($count >= 1){
                $listNewId = [];
                foreach ($payload['kegiatan_perawat'] as $key => $value) {
                    $kegiatanExplode = explode(',',$value);
                    $model = new NursingNote;
                    $model->tgl_catatan = date_format(date_create_from_format('d/m/Y', $payload['tanggal']), 'Y-m-d').date(' H:i:00', strtotime($payload['jam']));
                    $model->pendaftaran_id = $pendaftaran_id;
                    $model->waktu_catatan = $payload['jam'];
                    $model->pasienadmisi_id = $pasienadmisi_id;
                    $model->kegiatankeperawatan_id = $kegiatanExplode[0];
                    $model->kegiatan_perawat = $kegiatanExplode[1];
                    $model->catatan = $payload['catatan'];
                    $model->pegawai_id = $payload['nama_pegawai'];
        
                    if ($request->post()) {
                        $model->attributes = $request->post();
                        if ($model->save(false)) {
                            $listNewId[] = $model->catatankeperawatan_id;
                            Yii::error($model->attributes);                      
                        }
                    }
                }

                if(!empty($catatankeperawatan_id)) {
                    $user = Yii::$app->jwt->user;
                    $data = NursingNote::find(true)->where([
                        'catatankeperawatan_id' => $catatankeperawatan_id
                    ])->one();
                    $additional = json_decode($data->additional_data);
                    $additional['user_updated'] = !empty($user->loginpemakai_id) ? $user->loginpemakai_id : '1';
                    $additional['accessor_catatankeperawatan_id'] = $listNewId;

                    $data->additional_data = json_encode($additional);
                    $data->is_deleted = true;
                    $data->is_active = false;
                    $data->deleted_date = date('Y-m-d H:i:s');
                    $data->deleted_by = !empty($user->loginpemakai_id) ? $user->loginpemakai_id : '1';

                    if(!$data->save(false)) {
                        throw new \Exception("Gagal mengupdate data yang lama", 1);                        
                    }
                }
                
                $transaction->commit();
                return ['message' => 'Data Berhasil di simpan'];
            }
              else {
                    $transaction->rollback(); // sebenernya ga perlu tapi daripada gantung transactionnya
                    $errors = DocoHelpers::parseError($model->errors,'NursingNoteForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
    
        } catch (\yii\db\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            throw $e;
            
            // return [
            //     'message' => $e->getMessage()
            // ];
        } catch (\Exception $e) {
            $transaction->rollback();
            \Yii::$app->response->statusCode = 500;
            throw $e;
            
            // return [
            //     'message' => $e->getMessage()
            // ];
        }
    }
    
    public function actionGetNursingNote()
    {
        $limit = Yii::$app->request->get('per-page', 10);
        $page = Yii::$app->request->get('page', 2);
        $id = Yii::$app->request->get('id');
        $order = Yii::$app->request->get('order');
        $model = new NursingNote;
        if (!empty($order)) {
            $explodeOrder = explode(" ", $order);
            $orderKey = $explodeOrder[0];
            $orderType = strtolower($explodeOrder[1]);
            $orderType = $orderType == 'asc' ? SORT_ASC : SORT_DESC;
        }
        $pendaftaran_id  = DocoHelpers::decrypt($id);
        $data = NursingNote::find(true)->select([
            'catatankeperawatan_t.catatankeperawatan_id',
            'catatankeperawatan_t.waktu_catatan',
            'catatankeperawatan_t.tgl_catatan',
            'catatankeperawatan_t.kegiatan_perawat',
            'catatankeperawatan_t.catatan',
            'catatankeperawatan_t.is_deleted',
            'catatankeperawatan_t.deleted_by',
            'login1.loginpemakai_id',
            'login1.pegawai_id',
            'pegawai_m1.pegawai_id',
            'pegawai_m1.nama_pegawai',
            'pegawai_m2.pegawai_id as peg_delete_id',
            'pegawai_m2.nama_pegawai as peg_delete_nama',
            'pegawai_m3.pegawai_id as peg_update_id',
            'pegawai_m3.nama_pegawai as peg_update_nama',
            ])
            ->leftJoin('pegawai_m pegawai_m1', 'pegawai_m1.pegawai_id = catatankeperawatan_t.pegawai_id')
            ->leftJoin('loginpemakai_k login1', 'catatankeperawatan_t.deleted_by = login1.loginpemakai_id')
            ->leftJoin('pegawai_m pegawai_m2', 'login1.pegawai_id = pegawai_m2.pegawai_id')
            ->leftJoin('loginpemakai_k login2', "(catatankeperawatan_t.additional_data::json ->> 'user_updated')::integer = login2.loginpemakai_id")
            ->leftJoin('pegawai_m pegawai_m3', 'login2.pegawai_id = pegawai_m3.pegawai_id');
        if(!is_null($id)){
            $data->andWhere(['catatankeperawatan_t.pendaftaran_id' => $id ]);
        }
        
        $data = DocoRestActiveFilter::advancedFilter($model, $data);

        if (isset($orderKey) && isset($orderType)) {
            if($orderKey == 'tanggal'){
                $data = $data->orderBy([
                    'tgl_catatan' => $orderType,
                    'catatankeperawatan_id' => $orderType,
                ]);
            }else if($orderKey == 'jam'){
                $data = $data->orderBy([
                    'waktu_catatan' => $orderType,
                    'catatankeperawatan_id' => $orderType,
                ]);
            }
        }
        $totalRecord = $data->count();

        $data = $data->offset(($page - 1) * $limit)->limit($limit)->asArray()->all();
        return [
            'data' => $data,
            'totalRecord' => $totalRecord,
            'recordsFiltered' => $totalRecord,
        ];
    }

    public function actionHapusNursingNote($catatankeperawatan_id)
    {
        try {
            $request = Yii::$app->request;
            $pegawai_id = $request->get('pegawai_id', null);
            $deleteNursingNote = NursingNote::updateAll([
                'is_deleted' => true,
                'is_active' => false,
                'deleted_date' => date('Y-m-d H:i:s'),
                'deleted_by' => $pegawai_id,
            ], 'catatankeperawatan_id = :catatankeperawatan_id', [
                ':catatankeperawatan_id' => $catatankeperawatan_id
            ]);

            return ['message' => 'Data Berhasil di hapus'];
    
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
  
    public function actionGetDataPasien()
    {
        $id = Yii::$app->request->get('id');
        $pendaftaran_id  = DocoHelpers::decrypt($id);
        $result = Pendaftaran::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        return $result;
    }

}
