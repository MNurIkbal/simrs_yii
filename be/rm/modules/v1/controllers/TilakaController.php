<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

use app\models\LoginForm;
use app\models\ContactForm;
use app\modules\v1\models\DokumenSign;

use Doco\components\DocoConstants;
use Doco\components\DocoAccessRule;
use Doco\components\DocoActiveController;
use Doco\components\DocoJwtHttpBearerAuth;
use Doco\Services\Esign\TilakaService;
use Doco\models\Pegawai;

use Doco\rabbitmq\RabbitBgProcess;

class TilakaController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['generate-uuid'] = ["GET"];
        $verbs['upload-file'] = ["POST"];
        $verbs['request-sign'] = ["POST"];
        $verbs['execute-sign'] = ["POST"];
        $verbs['check-sign'] = ["POST", "GET"];
        return $verbs;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => DocoJwtHttpBearerAuth::className(),
            'except' => ['generate-uuid','upload-file','request-sign','check-sign','check-sign-all','check-file-s3','renew-status',]
        ];

        $behaviors['access'] = [
            'class' => DocoAccessRule::className(),
            'except' => ['generate-uuid','upload-file','request-sign','check-sign','check-sign-all','check-file-s3','renew-status',]
        ];

        return $behaviors;
    }

    public function actionGenerateUuid() {
        return [TilakaService::generateUUID()];
    }

    public function actionUploadFile() {
        $file = UploadedFile::getInstanceByName("upfile");
        $filename = $file->getBaseName().".".$file->getExtension();
        $filenameFull = "uploads/".$filename;
        $file->saveAs($filenameFull);
        $contentFile = fopen($filenameFull, 'r' );
        $newFilename = TilakaService::uploadFile($filename, $contentFile);
        unlink($filenameFull);
        return [
            "filename" => $newFilename
        ];
    }

    public function actionRequestSign() {
        $post = Yii::$app->request->post();
        $ttd = json_encode(file_get_contents("http://localhost:8857/uploads/signature/665.png"));
        $options = [
            "signatures" => [
                [
                    "user_identifier" => "audris04",
                    "signature_image" => "data:image/jpeg;base64,".$ttd,
                    "sequence" => 1
                ]
            ],
            "list_pdf" => [
                [
                    "filename" => $post['filename'],
                    "signatures" => [
                        [
                            "user_identifier" => "audris04",
                            "width" => 200,
                            "height" => 200,
                            "coordinate_x" => 0,
                            "coordinate_y" => 300,
                            "page_number" => 1
                        ]
                    ]
                ]
            ],
        ];
        $data = TilakaService::requestSign($options, Yii::$app->urlManagerFrontend->createUrl(''). "rm/esign/callback-tilaka");
        return $data;
    }

    public function actionExecuteSign() {
        $post = Yii::$app->request->post();
        $getDefaultData = DokumenSign::getDefaultData();
        if($post['status'] == 'Sukses') {
            $status = TilakaService::executeSign($post['request_id'], $post['user_identifier']);
            if($status) {
                DokumenSign::updateAll([
                    'doc_status' => DocoConstants::ESIGN_STAT_EXECUTED_TK,
                    'last_modified_date' => $getDefaultData->date,
                    'last_modified_by' => $getDefaultData->by,
                ], [
                    'doc_status' => DocoConstants::ESIGN_STAT_SIGNING_TK,
                    'sign_provider_id' => $post['request_id'],
                ]);
            }
            return $status;
        } else {
            DokumenSign::updateAll([
                'doc_status' => DocoConstants::ESIGN_STAT_GENERATED,
                'sign_provider_id' => null,
                'last_modified_date' => $getDefaultData->date,
                'last_modified_by' => $getDefaultData->by,
            ], [
                'sign_provider_id' => $post['request_id'],
            ]);
            return 'Gagal';
        }
    }

    public function actionCheckSign() {
        $post = Yii::$app->request->post();

        $response = TilakaService::checkSign($post['request_id']);
        return ['data' => $response];
    }

    public function actionCheckSignAll() {
        $getDefaultData = DokumenSign::getDefaultData();
        $all = DokumenSign::find()
            ->select(['dokumensign_t.filename','dokumensign_t.dokumen_sign_id','dokumensign_t.sign_provider_id','dokumensign_t.additional_data','pegawai_m.useresign_id'])
            ->leftJoin('pegawai_m','pegawai_m.pegawai_id = dokumensign_t.pegawai_id')
            ->where([
                'doc_status' => DocoConstants::ESIGN_STAT_EXECUTED_TK,
            ])->asArray()->all();

        $listID = ArrayHelper::index($all, null,'sign_provider_id');
        $listDokFTP = [];

        foreach ($listID as $ID => $listDok) {
            $result = TilakaService::checkSign($ID);
            foreach ($listDok as $dok) {
                $additional_data = json_decode($dok['additional_data'], true) ? : [];
                if(is_null($additional_data) && !empty($dok['additional_data'])) {
                    $additional_data = [
                        'backup' => $dok['additional_data'],
                    ];
                }

                $checkedUser = $checkedPdf = false;
                foreach ($result['status'] as $status) {
                    if(strtolower($status['user_identifier']) == strtolower($dok['useresign_id']) && $status['status'] == 'DONE') {
                        $checkedUser = true;
                        break;
                    }
                }
                foreach ($result['list_pdf'] as $pdf) {
                    if(strtolower($pdf['filename']) == strtolower($dok['filename'])) {
                        $additional_data['presigned_url'] = $pdf['presigned_url'];
                        $checkedPdf = true;
                        break;
                    }
                }

                if($checkedUser && $checkedPdf){
                    $listDokFTP[] = $dok['filename'];
                    DokumenSign::updateAll([
                        'doc_status' => DocoConstants::ESIGN_STAT_SIGNED,
                        'signed_date' => date('Y-m-d H:i:s'),
                        'additional_data' => json_encode($additional_data),
                        'last_modified_date' => $getDefaultData->date,
                        'last_modified_by' => $getDefaultData->by,
                    ], [
                        'doc_status' => DocoConstants::ESIGN_STAT_EXECUTED_TK,
                        'dokumen_sign_id' => $dok['dokumen_sign_id'],
                    ]);
                }
            }
        }
        $this->sendToFTP($listDokFTP);

        return [
            'message' => 'Checked DONE',
        ];
    }

    public function actionCheckFileS3() {
        $params = Yii::$app->params['iniFile'];
        $filesS3 = Yii::$app->minio->getListFiles()->get('Contents');
        $filesS3 = ArrayHelper::getColumn($filesS3, 'Key');

        $filesDb = DokumenSign::find()
            ->select(['dokumensign_t.filename'])
            ->asArray()
            ->column();
        $diffFilesS3 = array_diff($filesS3, $filesDb);
        //remove yang ada di minio tapi tidak ada di database
        foreach ($diffFilesS3 as $filename) {
            Yii::$app->minio->deleteFile($filename);
        }

        $files = DokumenSign::find()
            ->select(['dokumensign_t.filename'])
            ->where([
                'doc_status' => DocoConstants::ESIGN_STAT_SIGNED,
            ])
            ->column();
        $this->sendToFTP($files);
    }

    private function sendToFTP($files) {
        if(empty($files)) {
            Yii::error('List files to sent FTP are empty.');
            return false;
        }

        // kode buat connect ke FTP. ====================================================
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
            $dirExists = ftp_nlist($ftpConn, $remotePath);
            if ($dirExists == false) {
                @ftp_mkdir($ftpConn, $remotePath);
            }
            $filePath = 'uploads/';

            foreach ($files as $filename) {
                try {
                    $fileExists = ftp_nlist($ftpConn, $remotePath . $filename);
                    if($fileExists == false) { // jika file tidak exist
                        Yii::$app->minio->saveFile($filename, $filePath . $filename);
                        $upload =  false;
                        if(file_exists($filePath . $filename)) {
                            $upload = ftp_put($ftpConn, $remotePath . $filename, $filePath . $filename, FTP_BINARY);
                            unlink($filePath . $filename);
                        }
                        if($upload) {
                            Yii::$app->minio->deleteFile($filename);
                        }
                    } else {
                        Yii::$app->minio->deleteFile($filename);
                    }
                    
                } catch (\Aws\S3\Exception\S3Exception $ex) {
                    Yii::error('File tidak ditemukan : ' . $filename );
                }
            }
         }
         ftp_close($ftpConn);
    }

    public function actionRenewStatus() {
        $datas = Pegawai::find()
            ->select(['pegawai_id', 'additional_esign_data'])
            ->where(['not', ['additional_esign_data' => null]])
            ->all();

        foreach ($datas as $data) {
            $additional_esign_data = json_decode($data['additional_esign_data'], true);
            if(isset($additional_esign_data['registration_data'])) {
                $currStatus = TilakaService::getCurrentStatus($additional_esign_data);
                if(in_array($currStatus, [TilakaService::STATUS_VALIDATION, TilakaService::STATUS_REGISTRATION])) {
                    (new RabbitBgProcess())->send([
                        'pegawai_id' => $data['pegawai_id'],
                        'registration_id' => $additional_esign_data['registration_data']['registration_id'],
                    ], 'esign_reg_status', 'tilaka_status');
                }

                if(in_array($currStatus, [TilakaService::STATUS_VALIDATION, TilakaService::STATUS_ACTIVATION, TilakaService::STATUS_REJECTED, TilakaService::STATUS_INACTIVE])) {
                    (new RabbitBgProcess())->send([
                        'pegawai_id' => $data['pegawai_id'],
                        'user_identifier' => $additional_esign_data['registration_result']['tilaka_name'],
                    ], 'esign_cert_status', 'tilaka_status');
                }
            }
        }
    }
}
