<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-26 14:45:50
 */

namespace app\modules\v1\controllers;

use app\modules\v1\models\LaporanPasienMalnutrisiView;
use app\modules\v1\models\PegawaiView;

use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class LaporanPasienMalnutrisiController extends DocoActiveController
{
    /**
     * @todo Public vars
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public $modelClass = 'app\modules\v1\models\LaporanPasienMalnutrisiView';

    /**
     * @todo Verbs function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    /**
     * @todo Actions function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    /**
     * @todo Fungsi untuk mendapatkan list data pasien malnutrisi
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataPasienMalnutrisi()
    {
        $request = Yii::$app->request;

        $model = new LaporanPasienMalnutrisiView;
        $query = $model::find();

        if(isset($_GET['advanced-filter']['tgl_pendaftaran']) && $_GET['advanced-filter']['tgl_pendaftaran'] != '') {
            $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:59', strtotime($explode[1]));

                $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
            }
            unset($_GET['advanced-filter']['tgl_pendaftaran']);
        } else {
            $start = date('Y-m-d 00:00:00', strtotime('NOW'));
            $end = date('Y-m-d 23:59:59', strtotime('NOW'));

            $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        }

        if (isset($_GET['advanced-filter']['kategori']) && $_GET['advanced-filter']['kategori'] != '') {
            $kategori = $_GET['advanced-filter']['kategori'];

            if ($kategori == 'Tinggi') {
                $query->andWhere(['>=', 'tinggi', 4]);
                $query->orWhere('rendah = 3 AND tinggi = 3');
                $query->orWhere('sedang = 3 AND tinggi = 3');
                $query->orWhere('rendah != 3 AND sedang != 3 AND tinggi = 3');
            } elseif ($kategori == 'Sedang') {
                $query->andWhere(['>=', 'sedang', 4]);
                $query->orWhere('rendah = 3 AND sedang = 3');
                $query->orWhere('rendah != 3 AND sedang = 3 AND tinggi != 3');
            } else {
                $query->andWhere(['>=', 'rendah', 4]);
                $query->orWhere('rendah = 2 AND sedang = 2 AND tinggi = 2');
                $query->orWhere('rendah = 3 AND sedang != 3 AND tinggi != 3');
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    * @controller actionExportPdf
    * @attribute #periode# => periode
    * @attribute #pj_ruangan# => pj_ruangan
    * @attribute #nama_pegawai# => nama_pegawai
    * @attribute #waktu_dicetak# => waktu_dicetak
    * @attribute #table# => table
    **/
    public function actionExportPdf()
    {
        try {
            $model = new LaporanPasienMalnutrisiView;
            $query = $model::find();

            if (isset($_GET['ruangan_id']) && $_GET['ruangan_id'] != '') {
                $pegawai = PegawaiView::find()->where(['ruangan_id' => $_GET['ruangan_id'], 'jabatan_id' => DocoConstants::VAR_J_K_R])->one();
            } else {
                $pegawai = [];
            }

            if(isset($_GET['advanced-filter']['tgl_pendaftaran']) && $_GET['advanced-filter']['tgl_pendaftaran'] != '') {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));

                    $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
            } else {
                $start = date('Y-m-d 00:00:00', strtotime('NOW'));
                $end = date('Y-m-d 23:59:59', strtotime('NOW'));

                $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
            }

            if (isset($_GET['advanced-filter']['kategori']) && $_GET['advanced-filter']['kategori'] != '') {
                $kategori = $_GET['advanced-filter']['kategori'];

                if ($kategori == 'Tinggi') {
                    $query->andWhere(['>=', 'tinggi', 4]);
                    $query->orWhere('rendah = 3 AND tinggi = 3');
                    $query->orWhere('sedang = 3 AND tinggi = 3');
                    $query->orWhere('rendah != 3 AND sedang != 3 AND tinggi = 3');
                } elseif ($kategori == 'Sedang') {
                    $query->andWhere(['>=', 'sedang', 4]);
                    $query->orWhere('rendah = 3 AND sedang = 3');
                    $query->orWhere('rendah != 3 AND sedang = 3 AND tinggi != 3');
                } else {
                    $query->andWhere(['>=', 'rendah', 4]);
                    $query->orWhere('rendah = 2 AND sedang = 2 AND tinggi = 2');
                    $query->orWhere('rendah = 3 AND sedang != 3 AND tinggi != 3');
                }
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query = $query->asArray()->all();

            if (!empty($query)) {
                foreach ($query as $key => $value) {
                    if ($value['rendah'] > 3) {
                        $kategori = Yii::t('app', 'Rendah');
                    } elseif ($value['sedang'] > 3) {
                        $kategori = Yii::t('app', 'Sedang');
                    } elseif ($value['tinggi'] > 3) {
                        $kategori = Yii::t('app', 'Tinggi');
                    } elseif ($value['rendah'] == 3) {
                        if ($value['sedang'] == 3) {
                            $kategori = Yii::t('app', 'Sedang');
                        } else {
                            $kategori = Yii::t('app', 'Rendah');
                        }
                        if ($value['tinggi'] == 3) {
                            $kategori = Yii::t('app', 'Tinggi');
                        } else {
                            $kategori = Yii::t('app', 'Rendah');
                        }
                    } elseif ($value['sedang'] == 3) {
                        if ($value['rendah'] == 3) {
                            $kategori = Yii::t('app', 'Sedang');
                        } else {
                            $kategori = Yii::t('app', 'Sedang');
                        }
                        if ($value['tinggi'] == 3) {
                            $kategori = Yii::t('app', 'Tinggi');
                        } else {
                            $kategori = Yii::t('app', 'Sedang');
                        }
                    } elseif ($value['tinggi'] == 3) {
                        if ($value['rendah'] == 3) {
                            $kategori = Yii::t('app', 'Tinggi');
                        } else {
                            $kategori = Yii::t('app', 'Tinggi');
                        }
                        if ($value['sedang'] == 3) {
                            $kategori = Yii::t('app', 'Tinggi');
                        } else {
                            $kategori = Yii::t('app', 'Tinggi');
                        }
                    } else {
                        if ($value['rendah'] == 2 && $value['sedang'] == 2 && $value['tinggi'] == 2) {
                            $kategori = Yii::t('app', 'Rendah');
                        }
                    }

                    if ($value['lama_rawat'] == '') {
                        $hari_ini = time();
                        $tgl_pendaftaran = strtotime($value['tgl_pendaftaran']);
                        $datediff = $hari_ini - $tgl_pendaftaran;

                        $value['lama_rawat'] = round($datediff / (60 * 60 * 24));
                    }

                    $value['no_rekam_medik'] = $value['no_rekam_medik'].'/'.$value['no_pendaftaran'];
                    $query[$key]['ruangan_nama'] = $value['ruangan_nama'].'<br>'.$value['kamarruangan_nokamar'].' - '.$value['no_tempattidur'];
                    $query[$key]['hari_rawat'] = $value['lama_rawat'];
                    $query[$key]['kategori'] = $kategori;
                    $query[$key]['tgl_pendaftaran'] = DocoHelpers::convDateTime($value['tgl_pendaftaran'], false, true);
                }
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#periode#' => DocoHelpers::convDateTime($start, false, false).' - '.DocoHelpers::convDateTime($end, false, false),
                '#pj_ruangan#' => isset($pegawai['nama_pegawai']) ? $pegawai['nama_pegawai'] : '',
                '#nama_pegawai#' => isset($_GET['nama_pegawai']) ? $_GET['nama_pegawai'] : '',
                '#waktu_dicetak#' => DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime('NOW')), false, true),
                '#table#' => $this->renderPartial('pdf', [
                    'data' => $query,
                ]),
            ];
            $print->Output();
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
            $request = Yii::$app->request;
            $model = new LaporanPasienMalnutrisiView;
            $query = $model::find();

            if(isset($_GET['advanced-filter']['tgl_pendaftaran']) && $_GET['advanced-filter']['tgl_pendaftaran'] != '') {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));

                    $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']);
            } else {
                $start = date('Y-m-d 00:00:00', strtotime('NOW'));
                $end = date('Y-m-d 23:59:59', strtotime('NOW'));

                $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
            }

            if (isset($_GET['advanced-filter']['kategori']) && $_GET['advanced-filter']['kategori'] != '') {
                $kategori = $_GET['advanced-filter']['kategori'];

                if ($kategori == 'Tinggi') {
                    $query->andWhere(['>=', 'tinggi', 4]);
                    $query->orWhere('rendah = 3 AND tinggi = 3');
                    $query->orWhere('sedang = 3 AND tinggi = 3');
                    $query->orWhere('rendah != 3 AND sedang != 3 AND tinggi = 3');
                } elseif ($kategori == 'Sedang') {
                    $query->andWhere(['>=', 'sedang', 4]);
                    $query->orWhere('rendah = 3 AND sedang = 3');
                    $query->orWhere('rendah != 3 AND sedang = 3 AND tinggi != 3');
                } else {
                    $query->andWhere(['>=', 'rendah', 4]);
                    $query->orWhere('rendah = 2 AND sedang = 2 AND tinggi = 2');
                    $query->orWhere('rendah = 3 AND sedang != 3 AND tinggi != 3');
                }
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query = $query->asArray()->all();

            if (!empty($query)) {
                foreach ($query as $key => $value) {
                    if ($value['rendah'] > 3) {
                        $kategori = Yii::t('app', 'Rendah');
                    } elseif ($value['sedang'] > 3) {
                        $kategori = Yii::t('app', 'Sedang');
                    } elseif ($value['tinggi'] > 3) {
                        $kategori = Yii::t('app', 'Tinggi');
                    } elseif ($value['rendah'] == 3) {
                        if ($value['sedang'] == 3) {
                            $kategori = Yii::t('app', 'Sedang');
                        } else {
                            $kategori = Yii::t('app', 'Rendah');
                        }
                        if ($value['tinggi'] == 3) {
                            $kategori = Yii::t('app', 'Tinggi');
                        } else {
                            $kategori = Yii::t('app', 'Rendah');
                        }
                    } elseif ($value['sedang'] == 3) {
                        if ($value['rendah'] == 3) {
                            $kategori = Yii::t('app', 'Sedang');
                        } else {
                            $kategori = Yii::t('app', 'Sedang');
                        }
                        if ($value['tinggi'] == 3) {
                            $kategori = Yii::t('app', 'Tinggi');
                        } else {
                            $kategori = Yii::t('app', 'Sedang');
                        }
                    } elseif ($value['tinggi'] == 3) {
                        if ($value['rendah'] == 3) {
                            $kategori = Yii::t('app', 'Tinggi');
                        } else {
                            $kategori = Yii::t('app', 'Tinggi');
                        }
                        if ($value['sedang'] == 3) {
                            $kategori = Yii::t('app', 'Tinggi');
                        } else {
                            $kategori = Yii::t('app', 'Tinggi');
                        }
                    } else {
                        if ($value['rendah'] == 2 && $value['sedang'] == 2 && $value['tinggi'] == 2) {
                            $kategori = Yii::t('app', 'Rendah');
                        }
                    }

                    if ($value['lama_rawat'] == '') {
                        $hari_ini = time();
                        $tgl_pendaftaran = strtotime($value['tgl_pendaftaran']);
                        $datediff = $hari_ini - $tgl_pendaftaran;

                        $value['lama_rawat'] = round($datediff / (60 * 60 * 24));
                    }

                    $query[$key]['hari_rawat'] = $value['lama_rawat'];
                    $query[$key]['kategori'] = $kategori;
                    $query[$key]['tgl_pendaftaran'] = DocoHelpers::convDateTime($value['tgl_pendaftaran'], false, true);
                }
            }

            $header = [];
            $result = [];
            if (!empty($query)) {
                foreach ($query as $key => $value) {
                    $newValue = [];
                    $newValue[\Yii::t('app', 'Tgl. Masuk')] = $value['tgl_pendaftaran'];
                    $newValue[\Yii::t('app', 'No. RM')] = $value['no_rekam_medik'];
                    $newValue[\Yii::t('app', 'No. Pendaftaran')] = $value['no_pendaftaran'];
                    $newValue[\Yii::t('app', 'Nama Pasien')] = $value['nama_pasien'];
                    $newValue[\Yii::t('app', 'Jenis Kelamin')] = $value['jenis_kelamin'];
                    $newValue[\Yii::t('app', 'Dokter DPJP')] = $value['nama_pegawai'];
                    $newValue[\Yii::t('app', 'Kasus Penyakit')] = $value['jeniskasuspenyakit_nama'];
                    $newValue[\Yii::t('app', 'Nama Ruangan')] = $value['ruangan_nama'];
                    $newValue[\Yii::t('app', 'No. Kamar')] = $value['kamarruangan_nokamar'];
                    $newValue[\Yii::t('app', 'No. Bed')] = $value['no_tempattidur'];
                    $newValue[\Yii::t('app', 'Kategori')] = $value['kategori'];
                    $result[$key] = $newValue;
                }
            }

            $filePath = DocoHelpers::exportExcel(Yii::t('app', 'Laporan Pasien Malnutrisi'), $result, $header, array(
                "uploadPath" => "./uploads",
                "subTitle" => "Periode ".DocoHelpers::convDateTime($start, false, false).' - '.DocoHelpers::convDateTime($end, false, false),
            ),[],[],true);
            
            $filePath->save('php://output');
            die;
        } catch (RequestException $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \yii\web\HttpException(500, $message);
        }
    }
}