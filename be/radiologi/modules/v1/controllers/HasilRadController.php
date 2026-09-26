<?php

/**
 * @author Randy Vianda Putra
 * @todo Input Hasil Radiologi
 * @copyright 26 Juli 2018 aweutist
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\exceptions\ValidationException;
use Doco\components\DocoMessages;
use yii\helpers\ArrayHelper;
// model
use app\modules\v1\models\HasilPemeriksaanRadView;
use app\modules\v1\models\PasienMasukPenunjangT;
use app\modules\v1\models\PemeriksaanPasienRadiologiView;
use app\modules\v1\models\HasilPemeriksaanRad;

class HasilRadController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPasienRadView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["verifikasi"] = ["POST", "PUT"];
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
        $model = PemeriksaanPasienRadiologiView::find();
        if ($id) {
            $model->findByPenunjangId($id);
        }

        return $model->getRowArray();
    }

    private function getHasilRad($id, $pelayananId = null, $tindakanId = null)
    {
        $model = PemeriksaanPasienRadiologiView::find()
                        ->findByPenunjangId($id);
        if ($pelayananId) {
            $model->findByTindakanPelId($pelayananId);
        }

        if ($tindakanId) {
            $model->findByDaftarTindakanId($tindakanId);
        }

        return $model->getRowArray();
    }

    private function getCatatan($id){
        $model = PasienMasukPenunjangT::find()
        ->where(['pasienmasukpenunjang_id' => $id])->asArray()->one();
    
        return $model;
    }

    public function actionGenerateApi($id, $pelayananId = null, $tindakanId = null)
    {
        $data_pasien = $this->getPasienLab($id);
        $count_data_expertise = $this->getCountHasilRad($id);
        $data_hasil_rad = $this->getHasilRad($id, $pelayananId, $tindakanId);
        $data_catatan = $this->getCatatan($id);

        $result = [
            'data-pasien' => $data_pasien,
            'count-data-expertise' => $count_data_expertise,
            'data-hasil-rad' => $data_hasil_rad,
            'data-catatan' => $data_catatan
        ];

        return $result;
    }

    public function actionIndex($id) 
    {
        try {
            $request = Yii::$app->request;
            $model = new HasilPemeriksaanRadView;
            $query = $model::find()
                        ->findByPenunjangId($id);
            if ($pelayananId = $request->get('pelayananId')) {
                $query->findByTindakanPelId($pelayananId);
            }

            if ($tindakanId = $request->get('tindakanId')) {
                $query->findByDaftarTindakanId($tindakanId);
            }

            return $query->getDataArray();
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
        }
    }

    public function actionVerifikasi($id = null, $hasilId = null, $tindakanId = null)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $now = date('Y-m-d H:i:s');
        try {
            if (empty($hasilId) || empty($id)) {
                throw new ValidationException(422, DocoMessages::KEY_ERR_VALIDATION, [
                    'text' => 'Tidak bisa melakukan verifikasi, dikarenakan belum ada Upload Hasil Scan atau Ambil Foto'
                ]);
            }
            $modelhasil = HasilPemeriksaanRad::find()
                            ->findByPenunjangId($id)
                            ->findByHasilId($hasilId)
                            ->asArray()->one();
            
            $daftartindakan_id = ($modelhasil) ? $modelhasil['daftartindakan_id'] : null;
            $modelhasilExpertise = HasilPemeriksaanRad::find()
                            ->findByPenunjangId($id)
                            ->findByHasilId($hasilId);

            if(!empty($daftartindakan_id)) {
                $modelhasilExpertise->findByTindakanId($daftartindakan_id);
            }
            $modelhasilExpertise = $modelhasilExpertise->count();
            if (empty($modelhasilExpertise)) {
                throw new ValidationException(422, DocoMessages::KEY_ERR_CUSTOM, [
                    'text' => 'Data pemeriksaan tidak ditemukan'
                ]);
            }

            $connection->createCommand("
                UPDATE 
                    hasilpemeriksaanrad_t 
                SET 
                    tgl_verifikasi = '$now'
                WHERE
                    hasilpemeriksaanrad_id = {$hasilId}
            ")->execute();

            $countHasilVerif = HasilPemeriksaanRadView::find()
                            ->findByPenunjangId($id)
                            ->findByTglVerifNotNull(null)
                            ->count();

            $countHasil = HasilPemeriksaanRadView::find()
                            ->findByPenunjangId($id)
                            ->count();

            if ($countHasilVerif == $countHasil) {
                $modelPenunjang = PasienMasukPenunjangT::findOne($id);
                $modelPenunjang->status_periksa = DocoConstants::ST_SELESAI;
                $modelPenunjang->save();
            }

            $transaction->commit();

            return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM,[
                'text' => 'Data berhasil di verifikasi'
            ]);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
        } catch (ValidationException $e) {
            $transaction->rollBack();
            return $e->getOptions();
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
        }
    }

    public function actionBatalVerifikasi($id = null, $hasilId = null, $tindakanId = null)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $now = date('Y-m-d H:i:s');
        try {
            $additionalData = [
                'batal_verif' => [
                    'tgl_batal' => $now,
                    'pegawai_id' => Yii::$app->user->id,
                ]
            ];

            $connection->createCommand("
                UPDATE 
                    hasilpemeriksaanrad_t 
                SET 
                    tgl_verifikasi = NULL,
                    additional_data = '". json_encode($additionalData) ."'
                WHERE
                    hasilpemeriksaanrad_id = {$hasilId}
            ")->execute();

            // $countHasilVerif = HasilPemeriksaanRadView::find()
            //                 ->findByPenunjangId($id)
            //                 ->findByTglVerif(null)
            //                 ->count();

            // $countHasil = HasilPemeriksaanRadView::find()
            //                 ->findByPenunjangId($id)
            //                 ->count();

            // if ($countHasilVerif == $countHasil) {
                $modelPenunjang = PasienMasukPenunjangT::findOne($id);
                $modelPenunjang->status_periksa = DocoConstants::ST_PERIKSA;
                $modelPenunjang->save();
            // }

            $transaction->commit();

            return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM,[
                'text' => 'Berhasil membatalkan verifikasi'
            ]);
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
        } catch (ValidationException $e) {
            $transaction->rollBack();
            return $e->getOptions();
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
        }
    }

    private function getCountHasilRad($id = null)
    {
        $model = HasilPemeriksaanRadView::find();
        if ($id) {
            $data = $model->findByPenunjangId($id)
                          ->findByTglHasil(null);
        } 
        return $model->count();
    }

    public function actionUbahDokter()
    {
        $request = Yii::$app->request;
        try {
            if ($post = $request->post()){
                $data_penunjang = PasienMasukPenunjangT::find()
                ->where([
                    'pasienmasukpenunjang_id' => $post["pasienmasukpenunjang_id"],
                    'is_deleted' => false,
                    ])->one();
                if ($data_penunjang){
                    $data_penunjang->pegawai_id = $post["pegawai_id"];
                    $data_penunjang->tglmasukpenunjang = $post["tglmasukpenunjang"];
                    $data_penunjang->status_periksa = DocoConstants::ST_PERIKSA;
                    if($data_penunjang->validate() && $data_penunjang->save()) {
                        return [
                            'message' => 'status_success'
                        ];
                    }
                }else{
                    \Yii::$app->response->statusCode = 203;

                    return [
                        'message' => 'status_notfound'
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
        }
    }

    private function getPasienRad($id = null, $daftartindakan_id = null)
    {
        try{
            $model = PemeriksaanPasienRadiologiView::find()
                        ->select([
                            'no_rekam_medik',
                            'nama_pasien',
                            'tanggal_lahir',
                            'j_kelamin',
                            'ruangan_nama',
                            'nama_pegawai' => 'dokter_perujuk_nama',
                            'no_identitas_pasien'
                        ])
                        ->findByPenunjangId($id)
                        ->findByDaftarTindakanId($daftartindakan_id);
            return $model->getRowArray();
        } catch(\yii\db\Exception $e) {
            $this->logError($e);
        }
    }
    
    private function getHasilPemeriksaan($id, $tindakan_id, $daftartindakan_id)
    {
        $model = HasilPemeriksaanRadView::find()->select([
            'no_hasilrad',
            'tgl_hasilrad',
            'pemeriksaanrad_nama',
            'penanggung_jawab',
            'tgl_ambilfoto',
            'created_date'
        ]);
        if ($id) {
            $model->where([
                'pasienmasukpenunjang_id' => $id,
                'tindakanpelayanan_id' => $tindakan_id,
                'daftartindakan_id' => $daftartindakan_id
            ]);
            $data = $model->asArray()->one();
        }

        $modelGroup = HasilPemeriksaanRadView::find()->select([
            'no_hasilrad',
            'tgl_hasilrad',
            'pemeriksaanrad_nama',
            'penanggung_jawab',
            'tgl_ambilfoto',
            'created_date'
        ]);

        $group_pemeriksaan = [];
        if ($id) {
            $modelGroup->where([
                'pasienmasukpenunjang_id' => $id
            ]);
            $dataGroup = $modelGroup->asArray()->all();

            foreach ($dataGroup as $key => $value) {
                if (!empty($value['pemeriksaanrad_nama'])) {
                    $group_pemeriksaan[] = '- '.$value['pemeriksaanrad_nama'];
                }
            }
        }

        $data['group_pemeriksaan'] = (count($group_pemeriksaan) > 0) ? implode('<br>', $group_pemeriksaan) : '-';

        return $data;
    }
    /**
    * @controller actionCetakLabel
    * @attribute #MedicalRecord# => data MedicalRecord  
    * @attribute #ImageNumber# => data ImageNumber  
    * @attribute #PatientName# => data PatientName  
    * @attribute #Age# => data Age  
    * @attribute #Gender# => data Gender  
    * @attribute #ExaminationDate# => data ExaminationDate  
    * @attribute #Examination# => data Examination  
    * @attribute #ReffPhysician# => data ReffPhysician  
    * @attribute #WardAddress# => data WardAddress  
    * @attribute #Nik# => data no identitas pasien
    * @attribute #date_birth# => data tanggal lahir
    **/
    public function actionCetakLabel($id)
    {
        // HasilRadLabel
        try {
            $request      = Yii::$app->request;
            $get          = $request->get();
            $id           = $get['id'];
            $tindakan_id  = $get['tindakan_id'];
            $penunjang_id = $get['penunjang_id'];
            $data_pasien = $this->getPasienRad($penunjang_id, $id);
            $data_hasil  = $this->getHasilPemeriksaan($penunjang_id, $tindakan_id, $id);
            $noIdentitas = '-';
            if(isset($data_pasien['no_identitas_pasien'])) {
                $noIdentitas = $data_pasien['no_identitas_pasien'];
                $identitas = json_decode($noIdentitas, true);
                if(!empty($identitas)) {
                    if(is_array($identitas)) {
                        foreach ($identitas as $key => $value) {
                            if(isset($value['jenisidentitas'])) {
                                $noIdentitas = ($value['jenisidentitas'] == DocoConstants::IDENTITAS_KTP) ? $value['no_identitas_pasien'] : '-';
                            }
                        }
                    }
                } 
            }


            $pattern = "/[-\s:]/";
            $no_foto = preg_replace($pattern,'', $data_hasil['tgl_ambilfoto']);
            $create_date = preg_replace($pattern,'', $data_hasil['created_date']);

            $hasilKlinis = $this->getCatatanHasil($penunjang_id, $tindakan_id, $id);
            
            $print = new DocoPrint();
            $print->attributes = [
                '#MedicalRecord#'   => $data_pasien['no_rekam_medik'],
                '#ImageNumber#'     => ($data_hasil['no_hasilrad'] == NULL) ? " - " : $data_hasil['no_hasilrad'],
                '#no_foto#'          => !empty($no_foto) ? $no_foto : $create_date,
                '#PatientName#'     => $data_pasien['nama_pasien'],
                '#Nik#'             => $noIdentitas,
                '#date_birth#'      => DocoHelpers::convertDate($data_pasien['tanggal_lahir'], 'd-m-Y'),
                '#Age#'             => DocoHelpers::convertDateToAge($data_pasien['tanggal_lahir']),
                '#Gender#'          => $data_pasien['j_kelamin'],
                //'#ExaminationDate#' => !empty($data_hasil['tgl_hasilrad']) ? DocoHelpers::convertDate($data_hasil['tgl_hasilrad'], 'd-m-Y') : '--',
                //'#ExaminationDate#' => !empty($data_hasil['tgl_ambilfoto']) ? DocoHelpers::convertDate($data_hasil['tgl_ambilfoto'], 'd-m-Y') : '--', 
                '#ExaminationDate#' => !empty($data_hasil['created_date']) ? DocoHelpers::convertDate($data_hasil['created_date'], 'd-m-Y') : '--',
                '#ExaminationDatePhoto#' => !empty($data_hasil['tgl_ambilfoto']) ? DocoHelpers::convertDate($data_hasil['tgl_ambilfoto'], 'd-m-Y') : '--',
                '#NoteKlinis#'       => $hasilKlinis,
                '#Examination#'     => $data_hasil['pemeriksaanrad_nama'],
                '#ExaminationMultiple#' => $data_hasil['group_pemeriksaan'],
                '#ReffPhysician#'   => $data_pasien['nama_pegawai'],
                '#WardAddress#'     => $data_pasien['ruangan_nama'],
            ];
            $print->Output();
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function getCatatanHasil($penunjang_id, $tindakan_id, $daftartindakan_id) {
        $catatan = $this->actionGenerateApi($penunjang_id, $tindakan_id, $daftartindakan_id);

        $dataPasienNote = $catatan['data-pasien'];
        $dataCatatan = $catatan['data-catatan'];
        $catatanDokterPengirim = $dataPasienNote['catatan_dokterpengirim'];
        $catatanPenunjang = $dataCatatan['catatan'];
        $catatanDokter = empty($catatanPenunjang) ? $catatanDokterPengirim : $catatanPenunjang;

        $noteKlinis = !empty($catatanDokter) ? $catatanDokter : '';

        return $noteKlinis;
    }

    public function actionUpdateCatatan($id)
    {
        $request = Yii::$app->request;
        $catatan = $request->post('catatan');
        $model = PasienMasukPenunjangT::find()->andWhere([
            'pasienmasukpenunjang_id' => $id
        ])->one();

        if (empty($model)) return $this->responseJson(422, "Data gagal diupdate", $request->post());

        $model->catatan = $catatan;
        $model->save(false);

        return $this->responseJson(201, "Data berhasil diupdate", $request->post());
    }

    public function actionGetTotalHasilRadiologi()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $pasienadmisi_id = $request->get('pasienadmisi_id', null);
        $is_with_admisi = $request->get('is_with_admisi');
        $is_read = $request->get('is_read', FALSE);

        $query = HasilPemeriksaanRad::find()
			->andWhere(['not', ['tgl_verifikasi' => null]])
			->andWhere(['is_read' => $is_read, 'pendaftaran_id' => $pendaftaran_id]);

        // kondisi pasienadmisi terisi atau kosonng jiga pendaftaran igd ingin mengambil hasil radiologi ranap dengan pendaftaran igd rujuk ranap
		if (!empty($pasienadmisi_id) || (empty($pasienadmisi_id) && $is_with_admisi == FALSE)) {
			$query->andWhere(['pasienadmisi_id' => $pasienadmisi_id]);
		}

		$total_data = $query->count();
		
		return [
            'total_hasil_radiologi' => $total_data,
            'is_read' => $is_read
        ];
    }
}