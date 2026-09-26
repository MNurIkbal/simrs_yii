<?php

/**
 * @Author: Sigit
 * @Date:   2019-01-21 10:52:58
 */

namespace Doco\pendaftaran\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\modules\pendaftaran\models\KonfigPendaftaranForm;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class KonfigPendaftaranController extends DocoController
{
    /**
     * @todo Protected vars
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected $_restPendaftaran;
    protected $allowAction = ['*'];

    /**
     * @todo Init function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    /**
     * @todo Behaviors function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['access']);
        unset($behaviors['verbs']);

        return $behaviors;
    }

    /**
     * @todo Fungsi untuk menampilkan form konfig pemilihan dokter
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionIndex()
    {
        try {
            $model = new KonfigPendaftaranForm;
            $path = \Yii::getAlias('@webroot');
            $dir = $path . '/media/img/layar-ketersediaan-kamar';
            $pathLogo = null;

            if (Yii::$app->request->post()) {
                $post = Yii::$app->request->post('KonfigPendaftaranForm');
                $logoFiles = UploadedFile::getInstances($model, "dash_logo");

                if (!empty($logoFiles)) {
                    foreach ($logoFiles as $logo) {
                        $size = $logo->size;
                        $error = $logo->error;

                        if (($size / 1024) > 2000) {
                            return DocoHelpers::responseTemplate(
                                422,
                                'Error',
                                [],
                                [
                                    'title' => Yii::t('fe', 'Peringatan') . '!',
                                    'text' => Yii::t('fe', 'Maksimal ukuran satu logo adalah 2MB.'),
                                    'message' => Yii::t('fe', 'Maksimal ukuran satu logo adalah 2MB.'),
                                ]
                            );
                        }

                        if ($error) {
                            return DocoHelpers::responseTemplate(
                                422,
                                'Error',
                                [],
                                [
                                    'title' => Yii::t('fe', 'Peringatan') . '!',
                                    'text' => Yii::t('fe', 'Terdapat error ketika melakukan upload logo.'),
                                    'message' => Yii::t('fe', 'Terdapat error ketika melakukan upload logo.'),
                                ]
                            );
                        }
                    }

                    foreach ($logoFiles as $logo) {
                        $ext = end(explode('.', $logo->name));

                        if ($ext != 'png') {
                            return DocoHelpers::responseTemplate(
                                422,
                                'Error',
                                [],
                                [
                                    'title' => Yii::t('fe', 'Peringatan') . '!',
                                    'text' => Yii::t('fe', 'Format logo harus berupa .png'),
                                    'message' => Yii::t('fe', 'Format logo harus berupa  .png'),
                                ]
                            );
                        }
                    }

                    foreach ($logoFiles as $logo) {
                        $ext = end(explode('.', $logo->name));
                        $logo->saveAs($dir . '/' . $logo->name);
                        $pathLogo = '/media/img/layar-ketersediaan-kamar/' . $logo->name;
                    }
                }
                if (!$model->load($post, '')) {
                    $formName = substr(strrchr(get_class($model), "\\"), 1);
                    $error = $model->getErrors();
                    return DocoHelpers::response($error, 422, $formName);
                }
                $model->dash_logo = $pathLogo;
                if (!$model->validate()) {
                    $formName = substr(strrchr(get_class($model), "\\"), 1);
                    $error = $model->getErrors();
                    return DocoHelpers::response($error, 422, $formName);
                }

                if ($model->reservasi_awal > $model->reservasi_akhir) {
                    return DocoHelpers::responseTemplate(
                        422,
                        Yii::t('fe', 'Proses Gagal'),
                        [], [
                            'title' => Yii::t('fe', 'Proses Gagal'),
                            'text' => Yii::t('fe', 'Reservasi Awal tidak boleh lebih dari Reservasi Akhir.')
                        ]
                    );
                }

                $restPendaftaran = $this->_restPendaftaran->post('konfig-pendaftaran/ubah-konfig-pendaftaran', [
                    'form_params' => $model->attributes
                ]);
                $response = json_decode($restPendaftaran->getBody(), true);
                return json_encode($response);
                return DocoHelpers::response($response, false);
            } else {
                $restPendaftaran = $this->_restPendaftaran->get('konfig-pendaftaran/get-konfig-system');
                $response = json_decode($restPendaftaran->getBody(), true);
                $data = $response['response'];
                
                if ($data['dash_logo']) {
                    $logoPath[] = $data['dash_logo'];
                    $logoName[0]['caption'] = explode('/', $data['dash_logo'])[4];
                    $logoName[0]['key'] = $data['dash_logo'];
                    $showBrowseLogo = false;
                } else {
                    $logoPath = false;
                    $logoName = false;
                    $showBrowseLogo = true;
                }

                if ($model->load($data, '')) {
                    if ($model->is_pemilihandokter == '') {
                        $model->is_pemilihandokter = 0;
                    }
                    if ($model->is_validasipendaftaranrj == '') {
                        $model->is_validasipendaftaranrj = 0;
                    }
                    if ($model->is_validasipendaftaranri == '') {
                        $model->is_validasipendaftaranri = 0;
                    }
                    if ($model->is_validasipendaftaranrd == '') {
                        $model->is_validasipendaftaranrd = 0;
                    }
                    if ($model->is_limit_tagihan == '') {
                        $model->is_limit_tagihan = 0;
                    }
                    if ($model->support_multipayer == '') {
                        $model->support_multipayer = 0;
                    }
                    if ($model->is_set_igdkeri == '') {
                        $model->is_set_igdkeri = 0;
                    }

                    if ($model->is_reservasi == '') {
                        $model->is_reservasi = 0;
                    }

                    if ($model->is_slot_dokter == '') {
                        $model->is_slot_dokter = 0;
                    }
                    if ($model->is_sep_mandatory_on_edit == '') {
                        $model->is_sep_mandatory_on_edit = 0;
                    }
                    
                    return $this->render('form', [
                        'model' => $model,
                        'logoPath' => $logoPath,
                        'logoName' => $logoName,
                        'showBrowseLogo' => $showBrowseLogo,
                    ]);
                }

                throw new \yii\web\HttpException(422, Yii::t('fe', 'Pemilihan Dokter Tidak Sesuai.'));
            }
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    public function actionHapusLogoHeader()
    {
        try {
            $post = Yii::$app->request->post();
            $file = \Yii::getAlias('@webroot') . $post['key'];

            if (unlink($file)) {
                $res = $this->_restPendaftaran->get('konfig-pendaftaran/hapus-logo-header', [
                    'query' => []
                ]);
                $response = json_decode($res->getBody(), true);

                return true;
            }

            return false;
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), 'KonfigAntrianForm');
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }
}
