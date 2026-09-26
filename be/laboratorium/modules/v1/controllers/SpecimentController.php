<?php

/**
 * @author Randy Vianda Putra
 * @todo Input Speciment Laboratorium
 * @copyright 10 Juli 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
// model
use app\modules\v1\models\InfoPasienLabView;
use app\modules\v1\models\InfoPasienLabDetailView;
use app\modules\v1\models\SampleLab;
use app\modules\v1\models\SatuanLab;
use app\modules\v1\models\AmbilSample;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\InputHasilLabView;
use app\modules\v1\models\TindakanPelayananT;

class SpecimentController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienLabView';

    public $messageBroker = [
        'save' => [
            'services' => [
                'Satusehat' => [
                    'Specimen' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ],
            ]
        ],
    ];

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

    private function getPasienLab($id = null)
    {
        $model = InfoPasienLabView::find();
        if ($id) {
            $model->where(['pasienmasukpenunjang_id' => $id]);
            $data = $model->asArray()->one();
        } else {
            $data = $model->asArray()->all();
        }

        return $data;
    }

    private function getPasienLabDetail($id = null)
    {
        $result = [];
        if ($id) {
            $header = $this->getPasienLab($id);
            $model = InfoPasienLabDetailView::find();
            $model->where(['ambilsample_id' => null, 'pasienmasukpenunjang_id' => $id]);
            $result = $model->asArray()->all();
        }

        return $result;
    }

    public function actionGenerateApi($id)
    {
        $data_pasien = $this->getPasienLab($id);
        $data_pasien_detail = $this->getPasienLabDetail($id);
        $data_sample = SampleLab::find()->all();
        $data_satuan = SatuanLab::find()->all();

        $result = [
            'data-pasien' => $data_pasien,
            'data-pasien-detail' => $data_pasien_detail,
            'data-sample' => $data_sample,
            'data-satuan' => $data_satuan
        ];

        return $result;
    }

    public function actionSave()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $request = Yii::$app->request;
        $post = $request->post();
        $dataInsert = [];
        try {
            $pasienmasukpenunjang_id = '';
            foreach ($post as $key => $value) {
                $pasienmasukpenunjang_id = isset($value['pasienmasukpenunjang_id']) ? $value['pasienmasukpenunjang_id'] : null;
                $daftartindakan_id = isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
                $tindakanpelayanan_id = isset($value['tindakanpelayanan_id']) ? $value['tindakanpelayanan_id'] : null;
                $samplelab_id = isset($value['samplelab_id']) ? $value['samplelab_id'] : null;
                $satuan_jumlah = isset($value['satuanlab_id']) ? $value['satuanlab_id'] : null;
                $keterangan = isset($value['keterangan']) ? $value['keterangan'] : null;
                $jumlah = isset($value['jumlah']) ? $value['jumlah'] : 0;
                $tgl_ambilsample = isset($value['tanggal']) ? date('Y-m-d', strtotime($value['tanggal'])) : '';
                $jam_ambilsample = isset($value['tanggal']) ? date('H:i:s', strtotime($value['tanggal'])) : '';


                if (is_array($daftartindakan_id) || is_array($tindakanpelayanan_id)) {
                    for ($i=0; $i < count($daftartindakan_id); $i++) { 
                        $dataInsert[] = [
                            'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id,
                            'tindakanpelayanan_id' => $tindakanpelayanan_id[$i],
                            'tindakanpaket_id' => $daftartindakan_id[$i],
                            'samplelab_id' => $samplelab_id,
                            'tgl_ambilsample' => $tgl_ambilsample,
                            'jam_ambilsample' => $jam_ambilsample,
                            'jumlah' => $jumlah,
                            'satuan_jumlah' => $satuan_jumlah,
                            'keterangan' => $keterangan
                        ];
                    }
                } else {
                    $daftartindakan_id = $this->setValid($daftartindakan_id);
                    $tindakanpelayanan_id = $this->setValid($tindakanpelayanan_id);
                    $dataInsert[] = [
                        'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id,
                        'tindakanpelayanan_id' => $tindakanpelayanan_id,
                        'tindakanpaket_id' => $daftartindakan_id,
                        'samplelab_id' => $samplelab_id,
                        'tgl_ambilsample' =>$tgl_ambilsample,
                        'jam_ambilsample' => $jam_ambilsample,
                        'jumlah' => $jumlah,
                        'satuan_jumlah' => $satuan_jumlah,
                        'keterangan' => $keterangan
                    ];
                }
            }
            $model = PasienMasukPenunjangT::findOne($pasienmasukpenunjang_id);
            $model->status_periksa = DocoConstants::ST_SMPL;
            $model->save();

            AmbilSample::batchInsert($dataInsert);
            $transaction->commit();

            return [
                'pasienkirimkeunitlain_id' => $model->pasienkirimkeunitlain_id,
                'pendaftaran_id' => $model->pendaftaran_id,
                'tgl_ambilsample' => $tgl_ambilsample,
                'jam_ambilsample' => $jam_ambilsample,
                'message' => 'Data Berhasil di simpan'
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionIndex() 
    {
        try {
            $request = Yii::$app->request;
            $id = $request->get('id');
            $model = new InputHasilLabView;
            $query = $model::find();
            $query->where(['pasienmasukpenunjang_id' => $id]);
            $data = $query->all();
            $list_data = $list_pemeriksaan = $list_sample = [];
            foreach ($data as $key => $value) {
                $list_pemeriksaan[$value['samplelab_id']][] = $value['daftartindakan_nama']; 
                $list_data['data'][$value['samplelab_id']] = [
                    'pasienmasukpenunjang_id' => $value['pasienmasukpenunjang_id'],
                    'samplelab_id' => $value['samplelab_id'],
                    'nama_sample' => $value['nama_sample'],
                    'daftartindakan_nama' => implode('<br> ', $list_pemeriksaan[$value['samplelab_id']]),
                    'is_expertise' => $value['is_expertise'],
                    'tgl_ambilsample' => $value['tgl_ambilsample'],
                    'jam_ambilsample' => $value['jam_ambilsample'],
                    'keterangan' => $value['keterangan'],
                    'jumlah' => $value['jumlah'],
                    'satuan_jumlah' => $value['satuan_jumlah'],
                    'satuanlab_nama' => $value['satuanlab_nama'],
                ];
            }
            if(!empty($list_data['data'])) {
                foreach ($list_data['data'] as $key => $value) {
                    $list_sample['data'][] = $value;
                }
            }
            
            return $list_sample;
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

    private function setValid($itemId)
    {
        $exp = explode(',', $itemId);
        $cnt = count($exp);
        if($cnt == 2) {
            if(isset($exp[0]) && $exp[0] == '') {
                $itemId = isset($exp[1]) ? $exp[1] : null;
            }
        }
        return $itemId;
    }

    public function actionBatal()
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $pasienmasukpenunjang_id = $request->post('pasienmasukpenunjang_id', null);
            $pegawai_id = $request->post('pegawai_id', null);
            $date = date('Y-m-d H:i:s', time());
            if (!empty($pasienmasukpenunjang_id)) {
                Yii::$app->db->createCommand("
                        UPDATE ambilsample_t SET is_deleted = true, deleted_by = {$pegawai_id}, deleted_date = '{$date}'
                        WHERE pasienmasukpenunjang_id = {$pasienmasukpenunjang_id} 
                    ")->queryAll();
    
                $model = PasienMasukPenunjangT::findOne($pasienmasukpenunjang_id);
                $model->status_periksa = DocoConstants::BLM_PERIKSA;
                $model->save();
                $transaction->commit();
            }

            return ['message' => 'Data Berhasil di simpan'];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            
            Yii::error([
                "messagedbException" => $e->getMessage()
            ]);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}