<?php

namespace Doco\Traits;

use Doco\models\Cathlab;
use Doco\models\Pendaftaran;
use Doco\models\InfoPasienRdView;
use Doco\models\InfoPasienRjView;
use Doco\models\kasir\InfoPasienRiView;
use Doco\models\Pegawai;
use Doco\components\DocoPrint;
use Yii;

trait CathlabTrait
{

    /**
     * Return data of index
     * 
     * @param String $pendaftaran_id
     * @param String $pasienadmisi_id
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionIndex()
    {
        return $this->responseJson(200, 'Data berhasil mendapatkan data cathlab', Cathlab::existingRecordOf(Yii::$app->request->get('tipe', null), Yii::$app->request->get()));
    }

    /**
     * This function will create or update cathlab
     * 
     * @return Json
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveCathlab()
    {
        $payload = Yii::$app->request->post();
        $pendaftaran_id = isset($payload['pendaftaran_id']) ? $payload['pendaftaran_id'] : null;
        $pasienadmisi_id = isset($payload['pasienadmisi_id']) ? $payload['pasienadmisi_id'] : null;
        if (empty($pendaftaran_id)) {
            return $this->responseJson(400, 'Pendaftaran ID tidak boleh kosong');
        }
        // first checking the registration
        $isInpatient = false;
        if (!empty($pasienadmisi_id)) {
            $isInpatient = true;
            $clauseRegistration = compact('pasienadmisi_id');
        } else {
            $clauseRegistration = compact('pendaftaran_id');
        }
        $registrationRecord = Pendaftaran::find()->select(['pendaftaran_id', 'pasienadmisi_id'])->andWhere($clauseRegistration)->asArray()->one();
        if (empty($registrationRecord)) {
            $result = $this->responseJson(400, 'Pendaftaran tidak ditemukan');
        } else {
            if (isset($payload['tipe']) && in_array($payload['tipe'], Cathlab::$types)) {
                $type = strtolower($payload['tipe']);
                // Rawat inap
                $additionalClause = [];
                if ($isInpatient && !empty($registrationRecord['pasienadmisi_id'])) {
                    $additionalClause = [
                        'pasienadmisi_id' => $registrationRecord['pasienadmisi_id']
                    ];
                }

                $existingRecord = Cathlab::find()->select(['cathlab_id'])
                    ->andWhere(array_merge(['pendaftaran_id' => $registrationRecord['pendaftaran_id'], 'tipe' => $type], $additionalClause))
                    ->one();
                if (!empty($existingRecord)) {
                    $model = $existingRecord;
                } else {
                    $model = new Cathlab;
                    $model->pendaftaran_id = $registrationRecord['pendaftaran_id'];
                    if ($isInpatient) {
                        $model->pasienadmisi_id = $registrationRecord['pasienadmisi_id'];
                    }
                }

                $model->tipe = $type;
                $model->mapPayload($payload);
                $model->save();
                $result = $this->responseJson(200, 'Cathlab ' . ucwords($model->tipe) . ' berhasil disimpan');
            } else {
                $result = $this->responseJson(400, 'Tipe cathlab tidak ditemukan, silakan cek kembali tipe yang digunakan');
            }
        }
        return $result;
    }

    public function printCathlabPdf($instalasi = null)
    {
        $pendaftaran_id = Yii::$app->request->get('pendaftaran_id', null);
        $tipe = Yii::$app->request->get('tipe');
        $pasienadmisi_id = Yii::$app->request->get('pasienadmisi_id', null);
        $employeeId = Yii::$app->jwt->user->pegawai_id;
        $nama_user = '';
        $employeeRecord = Pegawai::find(true)->select(['nama_pegawai'])->andWhere(['pegawai_id' => $employeeId])->asArray()->one();
        if ($employeeRecord) {
            $nama_user = $employeeRecord['nama_pegawai'];
        }
        $additionalSelect = [];
        if ($instalasi == 'rajal'){
            $resultHeader = InfoPasienRjView::find();
            $additionalSelect = ['nama_pegawai as dokter_jaga'];
        }
        elseif ($pasienadmisi_id) {
            $resultHeader = InfoPasienRiView::find();
            $additionalSelect = ['dokter_admisi as dokter_jaga'];
        } else {
            $resultHeader = InfoPasienRdView::find();
            $additionalSelect = ['dokter_jaga'];
        }
        $resultHeader->select(array_merge([
            'no_rekam_medik',
            'tgl_pendaftaran',
            'no_pendaftaran',
            'nama_pasien',
            'jenis_kelamin',
            'jeniskasuspenyakit_nama',
            'tanggal_lahir',
            'umur',
            'kelaspelayanan_nama',
            'penjamin_nama',
            'carabayar_nama',
            'ruangan_nama',
            'no_pendaftaran',
            'nama_pasien',
        ], $additionalSelect));
        $clauseWhere = compact('pendaftaran_id');
        if ($pasienadmisi_id) {
            $clauseWhere = array_merge($clauseWhere, compact('pasienadmisi_id'));
        }
        $resultHeader = $resultHeader->andWhere($clauseWhere)
            ->asArray()
            ->one();

        $cathlabRecord['data'] = Cathlab::existingRecordOf(Yii::$app->request->get('tipe', null), Yii::$app->request->get());

        $print = new DocoPrint();
        $print->attributes = [
            '#tipe#'               => $tipe ? ucwords(@$tipe) : '',
            '#inf_norekammedik#'   => $resultHeader ? @$resultHeader['no_rekam_medik'] : '',
            '#inf_tglpendaftaran#' => $resultHeader ? ($resultHeader['tgl_pendaftaran'] ? date('d-m-Y', strtotime($resultHeader['tgl_pendaftaran'])) : '') : '',
            '#inf_nopendaftaran#'  => $resultHeader ? @$resultHeader['no_pendaftaran'] : '',
            '#inf_namapasien#'     => $resultHeader ? @$resultHeader['nama_pasien'] : '',
            '#inf_jeniskelamin#'   => $resultHeader ? @$resultHeader['jenis_kelamin'] : '',
            '#inf_kasuspenyakit#'  => $resultHeader ? @$resultHeader['jeniskasuspenyakit_nama'] : '',
            '#inf_tgllahir#'       => $resultHeader ? (isset($resultHeader['tanggal_lahir']) ? date('d-m-Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
            '#inf_umur#'           => $resultHeader ? @$resultHeader['umur'] : '',
            '#inf_dokterjaga#'     => $resultHeader ? @$resultHeader['dokter_jaga'] : '',
            '#inf_kelaspelayanan#' => $resultHeader ? @$resultHeader['kelaspelayanan_nama'] : '',
            '#inf_penjamin#'       => $resultHeader ? $resultHeader['penjamin_nama'] : '',
            '#inf_carabayar#'      => $resultHeader ? $resultHeader['carabayar_nama'] : '',
            '#inf_ruangan#'        => $resultHeader ? $resultHeader['ruangan_nama'] : '',

            '#table_cathlab#' => $this->renderPartial('detail-cathlab', [
                'data' => $cathlabRecord,
                'tipe' => $tipe
            ]),

            '#no_pendaftaran#' => $resultHeader ? $resultHeader['no_pendaftaran'] : '',
            '#nama_pasien#'    => $resultHeader ? $resultHeader['nama_pasien'] : '',
            '#nama_user#'      => $nama_user,
            '#tgl_cetak#'      => date('d-m-Y H:i:s'),
        ];
        $print->Output();
    }
}
