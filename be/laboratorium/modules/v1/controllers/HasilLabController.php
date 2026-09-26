<?php

/**
 * @author Randy Vianda Putra
 * @todo Input Hasil Laboratorium
 * @copyright 13 Juli 2018 aweutist
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
use app\modules\v1\models\InputHasilLabView;
use app\modules\v1\models\PasienMasukPenunjangT;
use Doco\models\Pendaftaran;
use yii\helpers\ArrayHelper;

class HasilLabController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienLabView';

    public $messageBroker = [
        'verifikasi' => [
            'services' => [
                'Satusehat' => [
                    'DiagnosticReport' => [
                        'query_params' => ['id'],
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ],
            ]
        ]
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

    private function getHasilLab($id = null)
    {
        $model = InputHasilLabView::find();
        if ($id) {
            $model->where(['pasienmasukpenunjang_id' => $id]);
            $data = $model->asArray()->one();
        } else {
            $data = $model->asArray()->all();
        }

        return $data;
    }

    public function actionGenerateApi($id)
    {
        $data_pasien = $this->getPasienLab($id);
        $data_hasil_lab = $this->getHasilLab($id);
        $count_data_expertise = $this->getCountHasilLab($id);
        
        //Blok untuk info pasienrujukan
        $isReferred = ArrayHelper::getValue($data_pasien, 'is_referred', false);
        $pasienkirimkeunitlain_id = ArrayHelper::getValue($data_pasien, 'pasienkirimkeunitlain_id', null);
        if($isReferred && !empty($pasienkirimkeunitlain_id)){
            $data = Yii::$app->db->createCommand(' 
                select p.rujukankeluar_id, 
                        p.alasandirujuk, 
                        p.diagnosa, 
                        p.pegawai_id, 
                        p.tgldirujuk AS tgl_rujukan, 
                        r.rumahsakit_rujukan, 
                        d.daftartindakan_id, 
                        d.daftartindakan_nama, 
                        dokter_perujuk.nama_pegawai AS dokter_perujuk_nama
                from pasiendirujukkeluar_t p
                left JOIN rujukankeluar_m r on r.rujukankeluar_id = p.rujukankeluar_id
                left JOIN permintaankepenunjang_t pk on pk.permintaankepenunjang_id = p.permintaankepenunjang_id
                left JOIN daftartindakan_m d on d.daftartindakan_id = pk.daftartindakan_id
                LEFT JOIN ( SELECT a.pegawai_id,a.nama_pegawai, a.tanda_tangan, a.gelarbelakang, a.gelardepan
                                        FROM pegawai_m a) dokter_perujuk ON p.pegawai_id = dokter_perujuk.pegawai_id
                where pk.is_referred = true 
                AND pk.pasienkirimkeunitlain_id = :pasienkirimkeunitlain_id 
                ORDER BY p.pasiendirujukkeluar_id DESC')
                ->bindValue(':pasienkirimkeunitlain_id', $pasienkirimkeunitlain_id)
                ->queryAll();
            $data_pasien['data_rujukan'] =  $data;
        }
        
        $loginpemakai_id = Yii::$app->request->get('loginpemakai_id', null);
        // $getDataAccess = $this->actionGetUserRoleAccess($loginpemakai_id);
        $getResponseDataAccess = Yii::$app->runAction(
            'v1/inf-pasien-lab-wynacom/get-user-role-access',
            [
                'loginpemakai_id' => $loginpemakai_id,
            ]
        );
        $getDataAccess = $getResponseDataAccess['response'];
        $isException = false;
        foreach ($getDataAccess as $key => $value) {
            if ($value['is_exception'] == true) {
                $isException = true;
            }
        }

        //get data pasienadmisi_id untuk upload dokumen
        $pendaftaran_id = ArrayHelper::getValue($data_pasien, 'pendaftaran_id', null);
        $dataPendaftaran = Pendaftaran::find()->select(['pasienadmisi_id'])->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
        $data_pasien['pasienadmisi_id'] = $dataPendaftaran['pasienadmisi_id'];

        $result = [
            'data-pasien' => $data_pasien,
            'data-hasil-lab' => $data_hasil_lab,
            'count-data-expertise' => $count_data_expertise,
            'user_acces_role' => $getDataAccess,
            'is_exception' => $isException
        ];

        return $result;
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
                    'tindakanpelayanan_id' => !empty($value['tindakanpelayanan_id']) ? $value['tindakanpelayanan_id'] : null,
                    'daftartindakan_nama' => implode('<br> ', $list_pemeriksaan[$value['samplelab_id']]),
                    'is_expertise' => $value['is_expertise'],
                ];
            }
            foreach ($list_data['data'] as $key => $value) {
                $list_sample['data'][] = $value;
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

    public function actionVerifikasi($id)
    {
        try {
            $modelPenunjang = PasienMasukPenunjangT::findOne($id);
            $request = Yii::$app->request;
            $model = new InputHasilLabView;
            $query = $model::find();
            $query->where(['pasienmasukpenunjang_id' => $id]);
            $query->andWhere(['or',
                ['is_expertise' => null],
                ['is_expertise' => 'f']
            ]);
            $data = $query->all();
            $count = count($data);
            if ($count < 1) {
                $modelPenunjang->status_periksa = DocoConstants::ST_SELESAI;
                $modelPenunjang->tanggal_verifikasi = date('Y-m-d H:i:s');
                $modelPenunjang->save();
                $verifikasi = true;
            } else {
                $verifikasi = false;
            }

            return $verifikasi;
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

    public function actionUnverifikasi($id)
    {
        try {
            $modelPenunjang = PasienMasukPenunjangT::findOne($id);
            $request = Yii::$app->request;
            $model = new InputHasilLabView;
            $query = $model::find();
            $query->where(['pasienmasukpenunjang_id' => $id]);
            $data = $query->all();
            $count = count($data);
            if ($count >= 1) {
                $modelPenunjang->status_periksa = DocoConstants::ST_PERIKSA;
                $modelPenunjang->tanggal_verifikasi = null;
                $modelPenunjang->save();
                $unverifikasi = true;
            }
            return $unverifikasi;
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

    private function getCountHasilLab($id = null)
    {
        $model = InputHasilLabView::find();
        if ($id) {
            $model->where(['pasienmasukpenunjang_id' => $id]);
            $model->andWhere(['or',
                ['is_expertise' => null],
                ['is_expertise' => 'f']
            ]);
            $data = $model->asArray()->all();
        } else {
            $data = $model->asArray()->all();
        }

        $count = !empty($data) ? count($data) : 0;

        return $count;
    }
}