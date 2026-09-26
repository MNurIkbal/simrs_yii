<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\models\Loginpemakai;
use Doco\models\Pegawai;
use Doco\Services\Esign\TilakaService;

class ProfileController extends DocoActiveController
{
    public $modelClass = Loginpemakai::class;

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-data"] = ["GET"];
        $verbs["update"] = ["POST","PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionChangePassword($id)
    {
        $request = Yii::$app->request;
        $model = Loginpemakai::find()->where(['loginpemakai_id' => $id])->one();
        if ($request->post() && !empty($model)) {
            $postForm = $request->post();
            $post = $postForm['ProfileForm'];
            $password_old = $post['password_old'];
            $password_new = $post['password_new'];
            $password_confirm = $post['password_confirm'];
            // $password_hashold = Yii::$app->security->generatePasswordHash($password_old);
            $password_hashnew = Yii::$app->security->generatePasswordHash($password_new);

            // if ($model->katakunci_pemakai != $password_hashold) {
            if (!Yii::$app->security->validatePassword($password_old, $model->katakunci_pemakai)) {
                Yii::$app->response->statusCode = 422;
                return [['field'=>'password_old', 'message'=>'Kata kunci lama tidak sesuai']];
            }
            if ($password_new != $password_confirm) {
                Yii::$app->response->statusCode = 422;
                return [['field'=>'password_confirm', 'message'=>'Konfirmasi kata sandi tidak sesuai']];
            }

            $model->katakunci_pemakai = $password_hashnew;

            if ($model->save()) {
                return ['message' => 'Data Berhasil di simpan'];
            } else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        }
    }

    public function actionGetEsignStatus($id) {
        $return = [
            'status' => 200,
            'message' => 'Sukses',
            'data' => [],
        ];
        $data = Loginpemakai::find()
            ->select(['pegawai_m.pegawai_id', 'pegawai_m.useresign_id', 'pegawai_m.additional_esign_data'])
            ->leftJoin('pegawai_m', 'loginpemakai_k.pegawai_id = pegawai_m.pegawai_id')
            ->where(['loginpemakai_k.loginpemakai_id' => $id])->asArray()->one();
        if(!empty($data)) {
            $additional_esign_data = json_decode($data['additional_esign_data'], true);

            $status = TilakaService::getCurrentStatus($additional_esign_data);
            $url = [];
            switch ($status) {
                case TilakaService::STATUS_ACTIVATION :
                    if(isset($additional_esign_data['registration_data']['done_reenroll']) && $additional_esign_data['registration_data']['done_reenroll']){
                        $url[] = TilakaService::generateActivationReenrollWebview($additional_esign_data['registration_data']['registration_id']);
                    } else {
                        $url[] = TilakaService::generateActivationWebview($additional_esign_data['registration_data']['registration_id']);
                    }
                    break;
                case TilakaService::STATUS_REGISTRATION :
                    $url[] = TilakaService::generateRegistrationWebview($additional_esign_data['registration_data']['registration_id']);
                    break;
                case TilakaService::STATUS_ACTIVE :
                    $url[] = TilakaService::generateChangeMFAWebview($data['useresign_id']);
                    if(isset($additional_esign_data['revoke_data']['url'])) {
                        if(isset($additional_esign_data['revoke_data']['last_revoke']) && $additional_esign_data['revoke_data']['last_revoke'] == date('Y-m-d')) {
                            $url[] = $additional_esign_data['revoke_data']['url'];
                        } else {
                            try {
                                $revoke = TilakaService::revoke([
                                    'user_identifier' => $data['useresign_id'],
                                    'reason' => $additional_esign_data['revoke_data']['reason'],
                                ]);
                                $additional_esign_data['revoke_data']['revoke_id'] = $revoke[0];
                                $additional_esign_data['revoke_data']['url'] = $revoke[1];
                                $url[] = $revoke[1];
                            } catch (\Exception $e) {
                                unset($additional_esign_data['revoke_data']['revoke_id']);
                                unset($additional_esign_data['revoke_data']['url']);
                            } finally {
                                $additional_esign_data['revoke_data']['last_revoke'] = date('Y-m-d');
                            }

                            Pegawai::updateAll([
                                'additional_esign_data' => json_encode($additional_esign_data),
                            ], [
                                'pegawai_id' => $data['pegawai_id'],
                            ]);
                        }
                    } else {
                        if(isset($additional_esign_data['revoke_data']['last_revoke']) && $additional_esign_data['revoke_data']['last_revoke'] < date('Y-m-d')) {
                            try {
                                $revoke = TilakaService::revoke([
                                    'user_identifier' => $data['useresign_id'],
                                    'reason' => $additional_esign_data['revoke_data']['reason'],
                                ]);
                                $additional_esign_data['revoke_data']['revoke_id'] = $revoke[0];
                                $additional_esign_data['revoke_data']['url'] = $revoke[1];
                                $url[] = $revoke[1];
                            } catch (\Exception $e) {
                                unset($additional_esign_data['revoke_data']['revoke_id']);
                                unset($additional_esign_data['revoke_data']['url']);
                            } finally {
                                $additional_esign_data['revoke_data']['last_revoke'] = date('Y-m-d');
                            }

                            Pegawai::updateAll([
                                'additional_esign_data' => json_encode($additional_esign_data),
                            ], [
                                'pegawai_id' => $data['pegawai_id'],
                            ]);
                        }
                    }
                    break;
                case TilakaService::STATUS_REENROLL :
                    $url[] = TilakaService::generateReEnrollWebview($additional_esign_data['registration_data']['registration_id']);
                default:
                    break;
            }

            $return['data'] = [
                'status' => $status,
                'url' => $url,
            ];
        } else {
            throw new \Exception("Data Tidak ditemukan");
        }
        return $return;
    }

    public function actionUpdateCallback($id) {
        $request = Yii::$app->request;
        $get = $request->get();
        $return = [
            'status' => 200,
            'message' => 'Sukses',
            'data' => [],
        ];

        $data = Loginpemakai::find()
            ->select(['pegawai_m.pegawai_id', 'pegawai_m.useresign_id', 'pegawai_m.additional_esign_data'])
            ->leftJoin('pegawai_m', 'loginpemakai_k.pegawai_id = pegawai_m.pegawai_id')
            ->where(['loginpemakai_k.loginpemakai_id' => $id])->asArray()->one();
        if(!empty($data)) {
            $additional_esign_data = json_decode($data['additional_esign_data'], true);
            switch ($get['type']) {
                case 'revoke':
                    if(isset($additional_esign_data['revoke_data']['revoke_id']) && 
                        $additional_esign_data['revoke_data']['revoke_id'] == $get['revoke_id']) {
                        switch ($get['status']) { 
                            case 'Berhasil' :
                                $additional_esign_data['cert_status'] = TilakaService::certificateStatus($data['useresign_id']);
                                $data['useresign_id'] = null;
                                unset($additional_esign_data['revoke_data']);
                                break;
                            case 'Gagal' :
                                try {
                                    $revoke = TilakaService::revoke([
                                        'user_identifier' => $data['useresign_id'],
                                        'reason' => $additional_esign_data['revoke_data']['reason'],
                                    ]);
                                    $additional_esign_data['revoke_data']['revoke_id'] = $revoke[0];
                                    $additional_esign_data['revoke_data']['url'] = $revoke[1];
                                } catch (\Exception $e) {
                                    unset($additional_esign_data['revoke_data']['revoke_id']);
                                    unset($additional_esign_data['revoke_data']['url']);
                                } finally {
                                    $additional_esign_data['revoke_data']['last_revoke'] = date('Y-m-d');
                                }
                                break;
                        }
                    }
                    break;
                case 'register':
                    if(isset($additional_esign_data['registration_data']['registration_id']) && 
                        $additional_esign_data['registration_data']['registration_id'] == $get['registration_id']) {
                        $additional_esign_data['registration_result'] = TilakaService::registrationResult($get['registration_id']);
                        if($get['status'] == 'S' && isset($additional_esign_data['registration_result']['tilaka_name'])) {
                            $additional_esign_data['cert_status'] =
                                TilakaService::certificateStatus($additional_esign_data['registration_result']['tilaka_name']);

                        }
                    }
                    break;
                case 'activation':
                    if(isset($additional_esign_data['registration_data']['registration_id']) && 
                        ($additional_esign_data['registration_data']['registration_id'] == $get['registration_id'] ||
                            $get['registration_id'] == 'undefined') &&
                        isset($additional_esign_data['registration_result']['tilaka_name']) && 
                        $additional_esign_data['registration_result']['tilaka_name'] == $get['tilaka_name']) {
                            $additional_esign_data['cert_status'] =
                                TilakaService::certificateStatus($additional_esign_data['registration_result']['tilaka_name']);
                            $data['useresign_id'] = $get['tilaka_name'];
                    }
                    break;
                case 'reenroll':
                    if(isset($additional_esign_data['registration_data']['registration_id']) && 
                        $additional_esign_data['registration_data']['registration_id'] == $get['issue_id']) {
                            $additional_esign_data['registration_data']['done_reenroll'] = true;
                    }
                    break;
                default:
                    break;
            }
            $updatedData = $data;
            unset($updatedData['pegawai_id']);
            $updatedData['additional_esign_data'] = json_encode($additional_esign_data);
            Pegawai::updateAll($updatedData, [
                'pegawai_id' => $data['pegawai_id'],
            ]);
        } else {
            throw new \Exception("Data Tidak ditemukan");
        }
        return $return;
    }
}