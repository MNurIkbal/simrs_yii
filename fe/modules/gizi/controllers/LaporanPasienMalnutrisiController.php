<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-26 13:42:51
 */

namespace Doco\gizi\controllers;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use GuzzleHttp\Exception\RequestException;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class LaporanPasienMalnutrisiController extends DocoController
{
    /**
     * @todo Protected vars
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected $_restGizi;
    protected $allowAction = ['*'];

    /**
     * @todo Init function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function init()
    {
        parent::init();
        $this->_restGizi = Yii::$app->docoRest->gizi;
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
     * @todo Fungsi untuk menampilkan halaman awal laporan pasienn malnutrisi
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionIndex()
    {
        try {
            $restGizi = $this->_restGizi->get('allow/get-bundle-data');
            $responseMaster = json_decode($restGizi->getBody(), true)['response']['master'];

            $listRuangan = ArrayHelper::map($responseMaster['ruangan'], 'ruangan_id', 'ruangan_nama');
            $listKamar = ArrayHelper::map($responseMaster['kamar'], 'kamarruangan_id', 'kamarruangan_nokamar');
            $listDokter = ArrayHelper::map($responseMaster['dokter'], 'pegawai_id', 'nama_pegawai');
            $listJenisKasusPenyakit = ArrayHelper::map($responseMaster['jeniskasuspenyakit'], 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama');

            $listKategori = [
                'Rendah' => Yii::t('fe', 'Rendah'),
                'Sedang' => Yii::t('fe', 'Sedang'),
                'Tinggi' => Yii::t('fe', 'Tinggi'),
            ];

            return $this->render('index', get_defined_vars());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    /**
     * @todo Action untuk melakukan proses export pdf
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportPdf()
    {
        try {
            $docoVars = Yii::$app->docoVars;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/laporan-pasien-malnutrisi.pdf";
            $ruangan_id = $docoVars->workspace('ruangan_id');
            $user_identity = Yii::$app->session->get('user_identity');
            $nama_pegawai = $user_identity['nama_pegawai'];

            $restGizi = $this->_restGizi->get('laporan-pasien-malnutrisi/export-pdf?ruangan_id='.$ruangan_id.'&nama_pegawai='.$nama_pegawai.'&'.http_build_query($yiiRestfulParams), [
                'save_to' => $path,
            ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }

    /**
     * @todo Action untuk melakukan proses export excel
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportExcel()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $url = 'laporan-pasien-malnutrisi/export-excel?'.http_build_query($yiiRestfulParams);
            $path = Yii::getAlias("@download") . "/laporan-pasien-malnutrisi.xlsx";
            $restGizi = $this->_restGizi->get($url,[
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }

    /**
     * @todo Fungsi untuk mendapatkan data pasien malnutrisi
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataPasienMalnutrisi()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $draw = $request->get('draw', 1);
            $no = $request->get('start', 1);
            $data = [];

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;

            $restGizi = $this->_restGizi->get('laporan-pasien-malnutrisi/get-data-pasien-malnutrisi?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($restGizi->getBody(), true);
            $data = $body['response']['data'];

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    if ($value['rendah'] > 3) {
                        $kategori = Yii::t('fe', 'Rendah');
                    } elseif ($value['sedang'] > 3) {
                        $kategori = Yii::t('fe', 'Sedang');
                    } elseif ($value['tinggi'] > 3) {
                        $kategori = Yii::t('fe', 'Tinggi');
                    } elseif ($value['rendah'] == 3) {
                        if ($value['sedang'] == 3) {
                            $kategori = Yii::t('fe', 'Sedang');
                        } else {
                            $kategori = Yii::t('fe', 'Rendah');
                        }
                        if ($value['tinggi'] == 3) {
                            $kategori = Yii::t('fe', 'Tinggi');
                        } else {
                            $kategori = Yii::t('fe', 'Rendah');
                        }
                    } elseif ($value['sedang'] == 3) {
                        if ($value['rendah'] == 3) {
                            $kategori = Yii::t('fe', 'Sedang');
                        } else {
                            $kategori = Yii::t('fe', 'Sedang');
                        }
                        if ($value['tinggi'] == 3) {
                            $kategori = Yii::t('fe', 'Tinggi');
                        } else {
                            $kategori = Yii::t('fe', 'Sedang');
                        }
                    } elseif ($value['tinggi'] == 3) {
                        if ($value['rendah'] == 3) {
                            $kategori = Yii::t('fe', 'Tinggi');
                        } else {
                            $kategori = Yii::t('fe', 'Tinggi');
                        }
                        if ($value['sedang'] == 3) {
                            $kategori = Yii::t('fe', 'Tinggi');
                        } else {
                            $kategori = Yii::t('fe', 'Tinggi');
                        }
                    } else {
                        if ($value['rendah'] == 2 && $value['sedang'] == 2 && $value['tinggi'] == 2) {
                            $kategori = Yii::t('fe', 'Rendah');
                        }
                    }

                    if ($value['lama_rawat'] == '') {
                        $hari_ini = time();
                        $tgl_pendaftaran = strtotime($value['tgl_pendaftaran']);
                        $datediff = $hari_ini - $tgl_pendaftaran;

                        $value['lama_rawat'] = round($datediff / (60 * 60 * 24));
                    }

                    $no++;
                    $value['no'] = $no;
                    $value['tgl_pendaftaran'] = DocoHelpers::convDateTime($value['tgl_pendaftaran'], false, true);
                    $value['no_rm_pendaftaran'] = $value['no_rekam_medik'].'<br>'.$value['no_pendaftaran'].'<br>'.$value['nama_pasien'];
                    $value['ruangan_nama'] = $value['ruangan_nama'].'<br>'.$value['kamarruangan_nokamar'].' - '.$value['no_tempattidur'];
                    $value['hari_rawat'] = $value['lama_rawat'];
                    $value['kategori'] = $kategori;
                    $data[$key] = $value;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

                return $result;
            }
            else {
                $result['data'] = $data;
                $result['recordsTotal'] = 0;
                $result['recordsFiltered'] = 0;

                return $result;
            }
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }
}