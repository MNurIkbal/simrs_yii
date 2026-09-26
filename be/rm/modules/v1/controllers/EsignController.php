<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use app\modules\v1\models\KonfigDokumenEklaimK;
use app\modules\v1\models\DokumenSign;

use Da\QrCode\QrCode;

use Doco\components\DocoConstansId;
use Doco\components\DocoConstants;
use Doco\components\DocoAccessRule;
use Doco\components\DocoActiveController;
use Doco\components\DocoJwtHttpBearerAuth;

use Doco\models\Pegawai;
use Doco\models\ProfilRsView;

use Doco\rabbitmq\RabbitBgProcess;

use Doco\Services\Esign\TilakaService;

class EsignController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-ungenerated-doc"] = ["GET"];
        $verbs["sign"] = ["POST"];
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['get-ungenerated-doc', 'preview']
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['get-ungenerated-doc', 'preview']
        ];

        return $behaviors;
    }

    public function actionGetUngeneratedDoc() {
        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign',true);

        $listKonfigDoc = KonfigDokumenEklaimK::find(true)->where(['is_esign' => true])->asArray()->all();
        $listKonfigDoc = ArrayHelper::index($listKonfigDoc, function ($element) {
            return trim($element['nama_dokumen']);
        });

        $sql = "SELECT *
            FROM (SELECT pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.instalasi_id,
                    pasienadmisi_t.pasienadmisi_id,
                    pendaftaran_t.pasien_id,
                    pasien_m.no_rekam_medik,
                    dokter_m.nama_pegawai AS nama_dokter,
                    dokteradmisi_m.nama_pegawai AS nama_dokter_admisi,

                    (resumemedis_t.dokumen_sign_id IS NULL AND resumemedis_t.resumemedisri_id IS NOT NULL) as is_resumemedis,
                    resumemedis_t.resumemedisri_id as resumemedis_id,
                    pendaftaran_t.pegawai_id as resumemedis_pegawai_id,
                    
                    (resumemedisri_t.dokumen_sign_id IS NULL AND resumemedisri_t.resumemedisri_id IS NOT NULL) as is_resumemedisri,
                    resumemedisri_t.resumemedisri_id as resumemedisri_id,
                    pasienadmisi_t.pegawai_id as resumemedisri_pegawai_id,

                    (pemeriksaanfisik_t.dokumen_sign_id IS NULL AND pemeriksaanfisik_t.pemeriksaanfisik_id IS NOT NULL) as is_pemeriksaanfisik,
                    pemeriksaanfisik_t.pemeriksaanfisik_id as pemeriksaanfisik_id,
                    pendaftaran_t.pegawai_id as pemeriksaanfisik_pegawai_id,

                    (asesmenmedis_t.dokumen_sign_id IS NULL AND asesmenmedis_t.asesmenmedis_id IS NOT NULL) as is_asesmenmedis,
                    asesmenmedis_t.asesmenmedis_id as asesmenmedis_id,
                    pasienadmisi_t.pegawai_id as asesmenmedis_pegawai_id,

                    (asesmenmedisrd_t.dokumen_sign_id IS NULL AND asesmenmedisrd_t.asesmenmedisrd_id IS NOT NULL) as is_asesmenmedisrd,
                    asesmenmedisrd_t.asesmenmedisrd_id as asesmenmedisrd_id,
                    pendaftaran_t.pegawai_id as asesmenmedisrd_pegawai_id
                FROM (
                    SELECT pendaftaran_id,
                        instalasi_id,
                        pegawai_id,
                        pasien_id,
                        no_pendaftaran
                    FROM pendaftaran_t ) pendaftaran_t
                LEFT JOIN ( 
                    SELECT a.pasienadmisi_id,
                        a.pegawai_id,
                        a.pendaftaran_id
                    FROM pasienadmisi_t a) pasienadmisi_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                LEFT JOIN (
                    SELECT a.pasien_id,
                        a.no_rekam_medik
                    FROM pasien_m a
                    WHERE a.is_deleted = false) pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
                LEFT JOIN (
                    SELECT pegawai_id, nama_pegawai
                    FROM pegawai_m
                    WHERE useresign_id IS NOT NULL
                ) dokter_m ON dokter_m.pegawai_id = pendaftaran_t.pegawai_id
                LEFT JOIN (
                    SELECT pegawai_id, nama_pegawai
                    FROM pegawai_m
                    WHERE useresign_id IS NOT NULL
                ) dokteradmisi_m ON dokteradmisi_m.pegawai_id = pendaftaran_t.pegawai_id

                LEFT JOIN (
                    SELECT a.resumemedisri_id, a.pendaftaran_id, b.dokumen_sign_id
                    FROM (
                        SELECT
                            resumemedisri_id,
                            pendaftaran_id,
                            COALESCE(last_modified_date, created_date) as last_modified_date,
                            is_deleted
                        FROM resumemedisri_t
                        WHERE resumemedisri_t.pasienadmisi_id IS NULL
                    ) a
                    LEFT JOIN (
                        SELECT dokumen_sign_id, pendaftaran_id, transaksi_id, created_date
                        FROM dokumensign_t
                        WHERE TRIM(type) IN ('Resume Medis Rawat Jalan','Resume Rawat Darurat') AND is_deleted = false
                    ) b ON b.transaksi_id = a.resumemedisri_id AND a.pendaftaran_id = b.pendaftaran_id AND (b.created_date >= a.last_modified_date)
                    WHERE a.is_deleted = false AND a.last_modified_date >= :cutoff
                ) resumemedis_t ON resumemedis_t.pendaftaran_id = pendaftaran_t.pendaftaran_id

                LEFT JOIN (
                    SELECT a.resumemedisri_id, a.pendaftaran_id, a.pasienadmisi_id, b.dokumen_sign_id
                    FROM (
                        SELECT
                            resumemedisri_id,
                            pendaftaran_id,
                            pasienadmisi_id,
                            COALESCE(last_modified_date, created_date) as last_modified_date,
                            is_deleted
                        FROM resumemedisri_t
                        WHERE resumemedisri_t.pasienadmisi_id IS NOT NULL
                    ) a
                    LEFT JOIN (
                        SELECT dokumen_sign_id, pendaftaran_id, transaksi_id, created_date
                        FROM dokumensign_t
                        WHERE TRIM(type) IN ('Resume Rawat Inap') AND is_deleted = false
                    ) b ON b.transaksi_id = a.resumemedisri_id AND a.pendaftaran_id = b.pendaftaran_id AND (b.created_date >= a.last_modified_date)
                    WHERE a.is_deleted = false AND a.last_modified_date >= :cutoff
                ) resumemedisri_t ON resumemedisri_t.pendaftaran_id = pendaftaran_t.pendaftaran_id 
                    AND resumemedisri_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id

                LEFT JOIN (
                    SELECT a.pemeriksaanfisik_id, a.pendaftaran_id, b.dokumen_sign_id
                    FROM (
                        SELECT
                            pemeriksaanfisik_id,
                            pendaftaran_id,
                            COALESCE(last_modified_date, created_date) as last_modified_date,
                            is_deleted
                        FROM pemeriksaanfisik_t
                        WHERE pasienadmisi_id is null
                    ) a
                    LEFT JOIN (
                        SELECT dokumen_sign_id, pendaftaran_id, transaksi_id, created_date
                        FROM dokumensign_t
                        WHERE TRIM(type) IN ('Pemeriksaan Fisik') AND is_deleted = false
                    ) b ON b.transaksi_id = a.pemeriksaanfisik_id AND a.pendaftaran_id = b.pendaftaran_id AND (b.created_date >= a.last_modified_date)
                    WHERE a.is_deleted = false AND a.last_modified_date >= :cutoff
                ) pemeriksaanfisik_t ON pemeriksaanfisik_t.pendaftaran_id = pendaftaran_t.pendaftaran_id

                LEFT JOIN(
                    SELECT a.asesmenmedis_id, a.pendaftaran_id, a.pasienadmisi_id, b.dokumen_sign_id
                    FROM (
                        SELECT
                            asesmenmedis_id,
                            pendaftaran_id,
                            pasienadmisi_id,
                            COALESCE(last_modified_date, created_date) as last_modified_date,
                            is_deleted
                        FROM asesmenmedis_t
                        WHERE pasienadmisi_id IS NOT NULL
                    ) a
                    LEFT JOIN (
                        SELECT dokumen_sign_id, pendaftaran_id, transaksi_id, created_date
                        FROM dokumensign_t
                        WHERE TRIM(type) IN ('Asemen Medis Rawat Inap') AND is_deleted = false
                    ) b ON b.transaksi_id = a.asesmenmedis_id AND a.pendaftaran_id = b.pendaftaran_id AND (b.created_date >= a.last_modified_date)
                    WHERE a.is_deleted = false AND a.last_modified_date >= :cutoff
                ) asesmenmedis_t ON asesmenmedis_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                    AND asesmenmedis_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id

                LEFT JOIN (
                    SELECT a.asesmenmedisrd_id, a.pendaftaran_id, b.dokumen_sign_id
                    FROM (
                        SELECT
                            asesmenmedisrd_id,
                            pendaftaran_id,
                            COALESCE(last_modified_date, created_date) as last_modified_date,
                            is_deleted
                        FROM asesmenmedisrd_t
                    ) a
                    LEFT JOIN (
                        SELECT dokumen_sign_id, pendaftaran_id, transaksi_id, created_date
                        FROM dokumensign_t
                        WHERE TRIM(type) IN ('Asemen Medis Rawat Darurat') AND is_deleted = false
                    ) b ON b.transaksi_id = a.asesmenmedisrd_id AND a.pendaftaran_id = b.pendaftaran_id AND (b.created_date >= a.last_modified_date)
                    WHERE a.is_deleted = false AND a.last_modified_date >= :cutoff
                ) asesmenmedisrd_t ON asesmenmedisrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                WHERE pasien_m.no_rekam_medik IS NOT NULL
            ) t
            WHERE (t.is_resumemedis OR t.is_resumemedisri OR t.is_pemeriksaanfisik OR t.is_asesmenmedis OR t.is_asesmenmedisrd)
            AND (t.nama_dokter IS NOT NULL OR t.nama_dokter_admisi IS NOT NULL)
        ";

        $cutoff = $configEsign['cutoff_transaksi'];

        $listData = Yii::$app->db->createCommand($sql)
            ->bindValues([
                ':cutoff' => $cutoff
            ])
            ->queryAll();
        foreach ($listData as $data) {
            foreach ($listKonfigDoc as $konfigDoc) {
                $additional_data = json_decode($konfigDoc['additional_data'], true);
                $konfigDoc['additional_data'] = $additional_data;
                $name = $additional_data['mapping'];
                if($data['is_'.$name]) {
                    (new RabbitBgProcess())->send([
                        'doc_name' => $name,
                        'data' => $data,
                        'doc' => $konfigDoc,
                    ], 'esign_generate', 'esign_dokumen_proses');
                }
            }
        }

        return [
            'message' => 'Generate processed',
        ];
    }

    public function actionSign() {
        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign',true);
        $user = Yii::$app->jwt->user;
        $post = Yii::$app->request->post();

        $sql = "
            SELECT dokumensign_t.*, konfigunduhdokumen_k.additional_data as additional_konfig
            FROM dokumensign_t
            LEFT JOIN konfigunduhdokumen_k ON dokumensign_t.konfig_dokumen_id = konfigunduhdokumen_k.konfig_dokumen_id
            WHERE
            dokumensign_t.doc_status = :doc_status AND
            dokumensign_t.pegawai_id = :pegawai_id
        ";
        $additionalQuery = ($post['doc_id'] == 'all' ? "" : ("AND dokumensign_t.dokumen_sign_id IN (".implode(",", $post['doc_id']).")"));
        $listData = Yii::$app->db->createCommand($sql . $additionalQuery)
            ->bindValues([
                ':doc_status' => DocoConstants::ESIGN_STAT_GENERATED,
                ':pegawai_id' => $user->pegawai_id,
            ])
            ->queryAll();
        if(empty($listData)) {
            return [
                'status' => 500,
                'message' => 'list dokumen tidak ditemukan',
            ];
        }
        $pegawai = Pegawai::find()->select(['nama_pegawai', 'tanda_tangan', 'useresign_id'])
            ->where([
                'pegawai_id' => $user->pegawai_id,
            ])
            ->asArray()->one();
        if(empty($pegawai['useresign_id'])) {
            return [
                'status' => 500,
                'message' => 'Tidak memiliki sertifikat aktif',
            ];
        }
        switch ($configEsign['sign']['image_type']) {
            case 'qrcode':
                $profile = ProfilRsView::find()->asArray()->one();
                $qrCode = (new QrCode(implode(" - ", [
                    "Dokumen Sah dan Ditandatangan",
                    "tanggal " . date("d-m-y"),
                    "oleh ". $pegawai['nama_pegawai'] . " " . $profile['nama_rumahsakit'],
                ])))
                ->setLabel("E-Sign")
                ->setSize(200)
                ->setMargin(0);
                $pegawai['tanda_tangan'] = [
                    'content' => $qrCode->writeString(),
                    'mime_type' => 'image/png',
                ];

                break;
            default:
                $ttd_path = Yii::$app->urlManagerFrontend->createUrl('') . "uploads/signature/" .  $pegawai['tanda_tangan'];
                $ttd_content = file_get_contents($ttd_path);
                file_put_contents('uploads/' . $pegawai['tanda_tangan'], $ttd_content);
                $mime_type = mime_content_type('uploads/' . $pegawai['tanda_tangan']);
                unlink('uploads/' . $pegawai['tanda_tangan']);
                $pegawai['tanda_tangan'] = [
                    'content' => $ttd_content,
                    'mime_type' => $mime_type,
                ];
                break;
        }
        
        $class = $configEsign['provider'];
        $return = $class::signing($listData, $pegawai);
        foreach ($return as $url) {
            if($url['user_identifier'] = $pegawai['useresign_id']) {
                return $url;
            }
        }
        return [
            'status' => 500,
            'message' => 'response tidak ditemukan id yang sama',
        ];
    }

    public function actionGetList() {
        $user = Yii::$app->jwt->user;
        $get = Yii::$app->request->get();
        $query = DokumenSign::find()
            ->select([
              'dokumensign_t.*',
              'pegawai_m.nama_pegawai',
              'pasien_m.no_rekam_medik',
              'pasien_m.nama_pasien',
              'pendaftaran_t.no_pendaftaran',
            ])
            ->leftJoin('pendaftaran_t', 'pendaftaran_t.pendaftaran_id = dokumensign_t.pendaftaran_id')
            ->leftJoin('pasien_m', 'pasien_m.pasien_id = pendaftaran_t.pasien_id')
            ->leftJoin('pegawai_m','pegawai_m.pegawai_id = dokumensign_t.pegawai_id')
            ->where([
                'dokumensign_t.doc_status' => DocoConstants::ESIGN_STAT_GENERATED,
                'dokumensign_t.pegawai_id' => $user->pegawai_id,
            ]);
        $countTotal = $countFiltered = $query->count();
        if(isset($get['start'])) {
            $query = $query->offset($get['start']);
        }
        if(isset($get['length'])) {
            $query = $query->limit($get['length']);
        }
        $data = $query->orderBy(['dokumen_sign_id' => SORT_ASC])->asArray()->all();
        $result['data'] = $data;
        $result['draw'] = isset($get['draw']) ? $get['draw'] : 1;
        $result['recordsFiltered'] = $countFiltered;
        $result['recordsTotal'] = $countTotal;

        return $result;
    }

    public function actionCheckDelayed() {
        $user = Yii::$app->jwt->user;
        $getDefaultData = DokumenSign::getDefaultData();
        $configEsign = (new DocoConstansId)->actionGetAdditional('konfig_esign',true);
        $class = $configEsign['provider'];

        $query = DokumenSign::find()
            ->select([
              'sign_provider_id',
              'pegawai_m.useresign_id',
            ])
            ->leftJoin('pegawai_m','pegawai_m.pegawai_id = dokumensign_t.pegawai_id')
            ->where([
                'dokumensign_t.doc_status' => DocoConstants::ESIGN_STAT_SIGNING_TK,
                'dokumensign_t.pegawai_id' => $user->pegawai_id,
            ]);

        $listChecked = $query->distinct()->asArray()->all();

        foreach ($listChecked as $data) {
            $status = $class::executeSign($data['sign_provider_id'], $data['useresign_id']);
            if($status) {
                DokumenSign::updateAll([
                    'doc_status' => DocoConstants::ESIGN_STAT_EXECUTED_TK,
                    'last_modified_date' => $getDefaultData->date,
                    'last_modified_by' => $getDefaultData->by,
                ], [
                    'doc_status' => DocoConstants::ESIGN_STAT_SIGNING_TK,
                    'sign_provider_id' => $data['sign_provider_id'],
                ]);
            } else {
                $result = TilakaService::checkSign($data['sign_provider_id']);
                $checkedUser = false;
                foreach ($result['status'] as $status) {
                    if($status['user_identifier'] == $data['useresign_id'] && $status['status'] == 'DONE') {
                        $checkedUser = true;
                        break;
                    }
                }
                if($checkedUser) {
                    DokumenSign::updateAll([
                        'doc_status' => DocoConstants::ESIGN_STAT_EXECUTED_TK,
                        'last_modified_date' => $getDefaultData->date,
                        'last_modified_by' => $getDefaultData->by,
                    ], [
                        'doc_status' => DocoConstants::ESIGN_STAT_SIGNING_TK,
                        'sign_provider_id' => $data['sign_provider_id'],
                    ]);
                } else {
                    DokumenSign::updateAll([
                        'sign_provider_id' => null,
                        'doc_status' => DocoConstants::ESIGN_STAT_GENERATED,
                    ],[
                        'sign_provider_id' => $data['sign_provider_id'],
                    ]);
                }
            }
        }
    }

    public function actionPreview() {
        $get = Yii::$app->request->get();

        if(isset($get['name'])) {
            $objectName = $get['name'];
        } else if((isset($get['pendaftaran_id']) || isset($get['transaksi_id'])) && isset($get['type'])) {
            $query = DokumenSign::find()
            ->select(['dokumensign_t.filename', 'konfigunduhdokumen_k.additional_data', 'dokumensign_t.created_date', 'dokumensign_t.transaksi_id'])
            ->leftJoin('konfigunduhdokumen_k', 'konfigunduhdokumen_k.konfig_dokumen_id = dokumensign_t.konfig_dokumen_id')
            ->where([
                'dokumensign_t.type' => $get['type'],
                'dokumensign_t.doc_status' => DocoConstants::ESIGN_STAT_SIGNED,
                'dokumensign_t.is_deleted' => false,
            ]);
            if(isset($get['pendaftaran_id'])) {
                $query = $query->andWhere([
                    'dokumensign_t.pendaftaran_id' => $get['pendaftaran_id']
                ]);
            }
            if(isset($get['transaksi_id'])) {
                $query = $query->andWhere([
                    'dokumensign_t.transaksi_id' => $get['transaksi_id']
                ]);
            }
            $result = $query->asArray()->one();
            if(!empty($result['filename'])) {
                $additional_data = json_decode($result['additional_data'], true);
                if(isset($additional_data['table_mapping'])) {
                    $sql = 'SELECT created_date, last_modified_date FROM ' . $additional_data['table_mapping']['table_name'] .
                        ' WHERE ' . $additional_data['table_mapping']['primary_key'] . ' = :param1';
                    $last_modified = Yii::$app->db->createCommand($sql)
                        ->bindValues([':param1' => $result['transaksi_id']])
                        ->queryOne();
                    if(!empty($last_modified)) {
                        $last_modified_date = empty($last_modified['last_modified_date']) ? $last_modified['created_date'] : $last_modified['last_modified_date'];
                        if($last_modified_date < $result['created_date']) {
                            $objectName = $result['filename'];
                        }
                    }
                }

            }
        } 

        if(empty($objectName)) {
            return [
                'status' => 500,
                'message' => 'object tidak ditemukan',
            ];
        }

        $url = $this->previewFTP($objectName);
        if($url == null) {
            $url = Yii::$app->minio->getPresignedUrl($objectName, '+10 minutes');
            $data = file_get_contents($url);
        } else {
            $data = file_get_contents($url);
            unlink($url);
        }

        header("Content-type: application/pdf");
        header("Content-disposition: attachment;filename=" . $objectName);

        echo $data;
        exit;
    }

    private function previewFTP($objectName) {
        $params = Yii::$app->params['iniFile'];
        $host = isset($params['konfigftp']) ? $params['konfigftp']['host'] : null;
        $user = isset($params['konfigftp']) ? $params['konfigftp']['username'] : null;
        $password = isset($params['konfigftp']) ? $params['konfigftp']['password'] : null;
        $ftpConn = ftp_connect($host);
        $login = ftp_login($ftpConn, $user, $password);
        ftp_pasv($ftpConn, true);

        if ((!$ftpConn) || (!$login)) {
           Yii::error('FTP connection has failed! Attempted to connect to ' . $host . ' for user ' . $user . '.');
        } else {
            $remotePath = isset($params['konfigftp']) ? $params['konfigftp']['path'] . 'esign/' : '/esign/';
            $filePath = 'uploads/';
            $exists = ftp_nlist($ftpConn, $remotePath . $objectName);
            if($exists) {
                ftp_get($ftpConn, $filePath . $objectName, $remotePath . $objectName, FTP_BINARY);
                ftp_close($ftpConn);
                return $filePath . $objectName;
            }
        }

        ftp_close($ftpConn);
        return null;
    }
}