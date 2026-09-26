<?php

/**
 * @Author: Sigit
 * @Date:   2019-01-14 17:48:57
 */

namespace Doco\antrian\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\modules\antrian\models\KonfigAntrianForm;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class KonfigAntrianController extends DocoController
{
    /**
     * @todo Protected vars
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected $_restAntrian;
    protected $allowAction = ['*'];

    /**
     * @todo Init function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function init()
    {
        parent::init();
        $this->_restAntrian = Yii::$app->docoRest->antrian;
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
     * @todo Fungsi untuk menampilkan form konfig kuota antrian
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionIndex()
    {
        try {
            $model = new KonfigAntrianForm;
            $path = \Yii::getAlias('@webroot');
            $dir = $path . '/media/img/layar-antrian';
            $pathLogo = null;
            $pathSlides = array();

            if (Yii::$app->request->post()) {
                $post = Yii::$app->request->post('KonfigAntrianForm');
                $logoFiles = UploadedFile::getInstances($model, "logo");
                $slideFiles = UploadedFile::getInstances($model, "slides");

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
                        $pathLogo = '/media/img/layar-antrian/' . $logo->name;
                    }
                }

                if (!empty($slideFiles)) {
                    foreach ($slideFiles as $slide) {
                        $size = $slide->size;
                        $error = $slide->error;
                        $ext = end(explode('.', $slide->name));

                        if ($post['is_slider'] == 0) {
                            if (($size / 1024) > 2000) {
                                return DocoHelpers::responseTemplate(
                                    422,
                                    'Error',
                                    [],
                                    [
                                        'title' => Yii::t('fe', 'Peringatan') . '!',
                                        'text' => Yii::t('fe', 'Maksimal ukuran satu gambar adalah 2MB.'),
                                        'message' => Yii::t('fe', 'Maksimal ukuran satu gambar adalah 2MB.'),
                                    ]
                                );
                            }

                            if ($ext != 'jpg' && $ext != 'png' && $ext != 'PNG' && $ext != 'jpeg') {
                                return DocoHelpers::responseTemplate(
                                    422,
                                    'Error',
                                    [],
                                    [
                                        'title' => Yii::t('fe', 'Peringatan') . '!',
                                        'text' => Yii::t('fe', 'Format gambar harus berupa .jpg, .png atau .jpeg'),
                                        'message' => Yii::t('fe', 'Format gambar harus berupa .jpg, .png atau .jpeg'),
                                    ]
                                );
                            }
                        } elseif ($post['is_slider'] == 1) {
                            if (($size / 1024) > 50000) {
                                return DocoHelpers::responseTemplate(
                                    422,
                                    'Error',
                                    [],
                                    [
                                        'title' => Yii::t('fe', 'Peringatan') . '!',
                                        'text' => Yii::t('fe', 'Maksimal ukuran satu video adalah 50MB.'),
                                        'message' => Yii::t('fe', 'Maksimal ukuran satu video adalah 50MB.'),
                                    ]
                                );
                            }

                            if ($ext != 'mp4' && $ext != 'mkv' && $ext != 'avi') {
                                return DocoHelpers::responseTemplate(
                                    422,
                                    'Error',
                                    [],
                                    [
                                        'title' => Yii::t('fe', 'Peringatan') . '!',
                                        'text' => Yii::t('fe', 'Format video harus berupa .mp4, .mkv atau .avi'),
                                        'message' => Yii::t('fe', 'Format video harus berupa .mp4, .mkv atau .avi'),
                                    ]
                                );
                            }
                        }

                        if ($error) {
                            return DocoHelpers::responseTemplate(
                                422,
                                'Error',
                                [],
                                [
                                    'title' => Yii::t('fe', 'Peringatan') . '!',
                                    'text' => Yii::t('fe', 'Terdapat error ketika melakukan upload slide.'),
                                    'message' => Yii::t('fe', 'Terdapat error ketika melakukan upload slide.'),
                                ]
                            );
                        }
                    }

                    foreach ($slideFiles as $slide) {
                        $ext = end(explode('.', $slide->name));
                        $slide->saveAs($dir . '/img-slider/' . $slide->name);
                        $pathSlides[] = '/media/img/layar-antrian/img-slider/' . $slide->name;
                    }
                }

                if (!empty($post)) {
                    $post['path_logoheader'] = $pathLogo;
                    $post['slides'] = $pathSlides;
                    $restAntrian = $this->_restAntrian->post('konfig-antrian/ubah-konfig-antrian', [
                        'form_params' => [
                            'post' => $post
                        ]
                    ]);
                    $response = json_decode($restAntrian->getBody(), true);
                    // Delete cache
                    $cache = Yii::$app->cache;
                    $cache->delete('app');

                    if (isset($response['metadata']['status']) && $response['metadata']['status'] == 422) {
                        return DocoHelpers::response($response, $response['metadata']['status'], 'KonfigAntrianForm');
                    } else {
                        return DocoHelpers::response($response, true);
                    }
                } else {
                    $message = Yii::t('fe', 'Tidak ada data yang disimpan.');
                    return DocoHelpers::responseTemplate(
                        500,
                        'Error',
                        [],
                        [
                            'title' => Yii::t('fe', 'Proses Gagal.'),
                            'text' => $message,
                            'message' => $message,
                        ]
                    );
                }
            } else {
                $restAntrian = $this->_restAntrian->get('konfig-antrian/get-bundle-data');
                $response = json_decode($restAntrian->getBody(), true);
                $data = $response['response'];
                $konfig = $data['konfig'];
                $slides = $data['slides'];
                $listKuotaAntrian = ArrayHelper::map($data['listKuotaAntrian'], 'lookup_id', 'lookup_name');

                if ($konfig['path_logoheader'] != '') {
                    $logoPath[] = $konfig['path_logoheader'];
                    $logoName[0]['caption'] = explode('/', $konfig['path_logoheader'])[4];
                    $logoName[0]['key'] = $konfig['path_logoheader'];
                    $showBrowseLogo = false;
                } else {
                    $logoPath = false;
                    $logoName = false;
                    $showBrowseLogo = true;
                }

                if (!empty($slides)) {
                    foreach ($slides as $key => $slide) {
                        $slidePaths[] = $slide['file'];
                        $slideNames[$key]['caption'] = explode('/', $slide['file'])[5];
                        $slideNames[$key]['key'] = DocoHelpers::encrypt($slide['konfigsystemdetail_id']) . '---' . $slide['file'];

                        if ($konfig['is_slider'] == 1) {
                            $slideNames[$key]['type'] = 'video';
                            $slideNames[$key]['filetype'] = 'video/mp4';
                            $slideNames[$key]['downloadUrl'] = \Yii::getAlias('@webroot') . $slide['file'];
                        }
                    }
                } else {
                    $slidePaths = array();
                    $slideNames = array();
                }

                if ($konfig['is_slider'] == '1') {
                    $model->is_slider = '1';
                } else if ($konfig['is_slider'] == '2') {
                    $model->is_slider = '2';
                } else {
                    $model->is_slider = '0';
                }

                if ($konfig['is_keteranganpasien'] == false) {
                    $model->is_pilihketeranganpasien = 0;
                } else {
                    $model->is_pilihketeranganpasien = 1;
                }

                if ($model->load($konfig, '')) {
                    if ($model->is_banyakloket == '') {
                        $model->is_banyakloket = '0';
                    } else {
                        $model->is_banyakloket = '1';
                    }

                    if ($model->is_nourut == '') {
                        $model->is_nourut = '0';
                    } else {
                        $model->is_nourut = '1';
                    }

                    if ($model->is_pisah_cabar == '') {
                        $model->is_pisah_cabar = '0';
                    } else {
                        $model->is_pisah_cabar = '1';
                    }

                    if($model->kuota_antrian == 598) {
                        $attr = '';
                    } else {
                        $attr = 'hidden';
                    }

                    return $this->render('form', [
                        'model' => $model,
                        'logoPath' => $logoPath,
                        'logoName' => $logoName,
                        'slidePaths' => $slidePaths,
                        'slideNames' => $slideNames,
                        'showBrowseLogo' => $showBrowseLogo,
                        'listKuotaAntrian' => $listKuotaAntrian,
                        'attr' => $attr,
                    ]);
                }

                throw new \yii\web\HttpException(422, Yii::t('fe', 'Konfig Antrian Tidak Sesuai.'));
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), 'KonfigAntrianForm');
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Fungsi untuk hapus logo header
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionHapusLogoHeader()
    {
        try {
            $post = Yii::$app->request->post();
            $file = \Yii::getAlias('@webroot') . $post['key'];

            if (file_exists($file)) {
                unlink($file);
            }

            $restAntrian = $this->_restAntrian->get('konfig-antrian/hapus-logo-header', [
                'query' => []
            ]);
            $response = json_decode($restAntrian->getBody(), true);
            return true;
            
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), 'KonfigAntrianForm');
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    /**
     * @todo Fungsi untuk hapus slideshow
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionHapusSlideshow()
    {
        try {
            $post = Yii::$app->request->post();
            $exploded = explode('---', $post['key']);
            $id = DocoHelpers::decrypt($exploded[0]);
            $file = \Yii::getAlias('@webroot') . $exploded[1];

            if (unlink($file)) {
                $restAntrian = $this->_restAntrian->get('konfig-antrian/hapus-slideshow', [
                    'query' => ['id' => $id]
                ]);
                $response = json_decode($restAntrian->getBody(), true);

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
