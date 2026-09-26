<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-13 15:07:31
 */

namespace app\modules\v1\controllers;

use app\modules\v1\models\PermintaanMakan;
use app\modules\v1\models\InfoPermintaanMakanView;
use app\modules\v1\models\InfoPermintaanMakanDetailView;
use app\modules\v1\models\PegawaiView;

use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\components\DocoRestActiveFilter;
use Doco\rabbitmq\RabbitBgProcess;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\models\LookupTransaksi;


class InfPermintaanMakanController extends DocoActiveController
{
    const KN = 'kn';
    /**
     * @todo Public vars
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public $modelClass = 'app\modules\v1\models\InfoPermintaanMakanView';

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
     * @todo Fungsi untuk mendapatkan list data permintaan makan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataPermintaanMakan()
    {
        $request = Yii::$app->request;

        $model = new InfoPermintaanMakanView;
        $query = $model::find();

        $start = date('Y-m-d', strtotime('NOW'));
        $end = date('Y-m-d', strtotime('NOW'));
        if(isset($_GET['advanced-filter']['tgl_permintaanmakan']) && $_GET['advanced-filter']['tgl_permintaanmakan'] != '') {
            $explode = explode(" - ", $_GET['advanced-filter']['tgl_permintaanmakan']);
            if(count($explode) == 2) {
                $start = date('Y-m-d', strtotime($explode[0]));
                $end = date('Y-m-d', strtotime($explode[1]));

            }
            unset($_GET['advanced-filter']['tgl_permintaanmakan']);
        }
        
        if(isset($_GET['advanced-filter']['is_ditagihkan']) && $_GET['advanced-filter']['is_ditagihkan'] != '') {
            if($_GET['advanced-filter']['is_ditagihkan'] == 'ditagihkan'){
                $query = $query->andWhere(['=', 'is_ditagihkan', TRUE]);
            }else{
                $query = $query->andWhere(['or', ['is_ditagihkan' => FALSE], ['is_ditagihkan' => NULL]]);

            }
            unset($_GET['advanced-filter']['is_ditagihkan']);
        }
        
        $query->andWhere(['between', new \yii\db\Expression('(tgl_permintaanmakan::date)'), $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * @todo Fungsi untuk mendapatkan detail data permintaan makan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetPermintaanMakanDetail($id)
    {
        $model = InfoPermintaanMakanView::find()->where(['permintaaanmakan_id' => $id])->one();
        $model_detail = InfoPermintaanMakanDetailView::find()->where(['permintaaanmakan_id' => $id])->asArray()->all();

        if ($model->tgl_permintaanmakan != '') {
            $model->tgl_permintaanmakan = DocoHelpers::convDateTime($model->tgl_permintaanmakan, false, true);
        }

        if ($model->tgl_pendaftaran != '') {
            $model->tgl_pendaftaran = DocoHelpers::convDateTime($model->tgl_pendaftaran, false, true);
        }

        if ($model->waktu_pembatalan != '') {
            $model->waktu_pembatalan = DocoHelpers::convDateTime($model->waktu_pembatalan, false, true);
        }

        return [
            'permintaan_makan' => $model,
            'permintaan_makan_detail' => $model_detail,
        ];
    }

    /**
     * @todo Fungsi untuk membatalkan permintaan makan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionBatalPermintaanMakan()
    {
        $post = Yii::$app->request->post();
        $id = DocoHelpers::decrypt($post['id']);
        $model = PermintaanMakan::findOne($id);

        if ($model != '') {
            $model->status = 0;
            $model->alasan_pembatalan = $post['alasan_pembatalan'];
            $model->waktu_pembatalan = date('Y-m-d H:i:s', strtotime('NOW'));

            if (!$model->save()) {
                return [
                    'status' => 422,
                    'data' => $model->errors,
                ];
            }

            return [
                'status' => 200,
                'data' => Yii::t('app', 'Data berhasil dibatalkan.'),
                'message' => Yii::t('app', 'Data berhasil dibatalkan.'),
            ];
        }
    }

    /**
    * @controller actionExportPdf
    * @attribute #table# => table
    **/
    public function actionExportPdf()
    {
        try {
            $model = new InfoPermintaanMakanView;
            $query = $model::find()
            ->select([
                '*'
            ]);

            if(isset($_GET['advanced-filter']['tgl_permintaanmakan']) && $_GET['advanced-filter']['tgl_permintaanmakan'] != '') {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_permintaanmakan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d', strtotime($explode[0]));
                    $end = date('Y-m-d', strtotime($explode[1]));

                    // $query->andWhere(['between', 'tgl_permintaanmakan', $start, $end]);
                }
                unset($_GET['advanced-filter']['tgl_permintaanmakan']);
            } else {
                $start = date('Y-m-d', strtotime('NOW'));
                $end = date('Y-m-d', strtotime('NOW'));

                // $query->andWhere(['between', 'tgl_permintaanmakan', $start, $end]);
            }
            $query->andWhere(['between', new \yii\db\Expression('(tgl_permintaanmakan::date)'), $start, $end]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query = $query->all();

            if (!empty($query)) {
                foreach ($query as $key => $value) {
                    $query[$key]['tgl_permintaanmakan'] = DocoHelpers::convDateTime($value['tgl_permintaanmakan'], false, true);
                }
            }

            $print = new DocoPrint();
            $print->attributes = [
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
            $model = new InfoPermintaanMakanView;
            $query = $model::find();

            if(isset($_GET['advanced-filter']['tgl_permintaanmakan']) && $_GET['advanced-filter']['tgl_permintaanmakan'] != '') {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_permintaanmakan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));

                    $query->andWhere(['between', 'tgl_permintaanmakan', $start, $end]);
                }
                unset($_GET['advanced-filter']['tgl_permintaanmakan']);
            } else {
                $start = date('Y-m-d 00:00:00', strtotime('NOW'));
                $end = date('Y-m-d 23:59:59', strtotime('NOW'));

                $query->andWhere(['between', 'tgl_permintaanmakan', $start, $end]);
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query = $query->all();

            $header = [];
            $result = [];
            if (!empty($query)) {
                foreach ($query as $key => $value) {
                    $dataPembayaran = ($value['is_ditagihkan'] != null) && ($value['is_ditagihkan'] != '') ? 'Ditagihkan' : 'Tidak Ditagihkan';

                    $newValue = [];
                    $newValue[\Yii::t('app', 'Tanggal Permintaan')] = date('d-M-Y H:i:s',strtotime($value['tgl_permintaanmakan']));
                    $newValue[\Yii::t('app', 'No. Permintaan')] = $value['no_permintaanmakan'];
                    $newValue[\Yii::t('app', 'Pembayaran')] = $dataPembayaran;
                    $newValue[\Yii::t('app', 'Catatan Diet')] = $value['catatan_diet'];
                    $newValue[\Yii::t('app', 'Ruangan/Kamar')] = $value['ruangan_nama'].'/'.$value['kamarruangan_nokamar'].' - '.$value['no_tempattidur'];
                    $newValue[\Yii::t('app', 'No. Pendaftaran')] = $value['no_pendaftaran'];
                    $newValue[\Yii::t('app', 'No. Rekam Medik')] = $value['no_rekam_medik'];
                    $newValue[\Yii::t('app', 'Nama Pasien')] = $value['nama_pasien'];
                    $newValue[\Yii::t('app', 'Jenis Kelamin')] = $value['jenis_kelamin'];
                    $newValue[\Yii::t('app', 'Tanggal Lahir')] = date('d M Y',strtotime($value['tanggal_lahir']));
                    $newValue[\Yii::t('app', 'Jenis Diet')] = !empty($value['jenisdiet_nama']) ? $value['jenisdiet_nama'] : '-';
                    $newValue[\Yii::t('app', 'Diagnosa')] = !empty($value['diagnosa']) ? $value['diagnosa'] : '-';
                    $newValue[\Yii::t('app', 'Alergi')] = !empty($value['riwayat_alergi']) ? $value['riwayat_alergi'] : '-';
                    $newValue[\Yii::t('app', 'Penjamin')] = $value['penjamin_nama'];
                    $newValue[\Yii::t('app', 'Cara Bayar')] = $value['carabayar_nama'];
                    $newValue[\Yii::t('app', 'Status')] = $value['status_permintaan'];
                    $result[$key] = $newValue;
                }
            }

            $filePath = DocoHelpers::exportExcel(Yii::t('app', 'Informasi Permintaan Makan'), $result, $header, [],[],[],true);

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

    /**
    * @controller actionCetakDetail
    * @attribute #no_rekam_medik# => no_rekam_medik
    * @attribute #tgl_pendaftaran# => tgl_pendaftaran
    * @attribute #no_pendaftaran# => no_pendaftaran
    * @attribute #nama_pasien# => nama_pasien
    * @attribute #jenis_kelamin# => jenis_kelamin
    * @attribute #jeniskasuspenyakit_nama# => jeniskasuspenyakit_nama
    * @attribute #tgl_permintaanmakan# => tgl_permintaanmakan
    * @attribute #tanggal_lahir# => tanggal_lahir
    * @attribute #umur# => umur
    * @attribute #dok_dpjp# => dok_dpjp
    * @attribute #kelaspelayanan_nama# => kelaspelayanan_nama
    * @attribute #kamarruangan_nokamar# => kamarruangan_nokamar
    * @attribute #no_tempattidur# => no_tempattidur
    * @attribute #carabayar_nama# => carabayar_nama
    * @attribute #penjamin_nama# => penjamin_nama
    * @attribute #no_permintaanmakan# => no_permintaanmakan
    * @attribute #alasan_pembatalan# => alasan_pembatalan
    * @attribute #nama_pemesan# => nama_pemesan
    * @attribute #tanggal_dicetak# => tanggal_dicetak
    * @attribute #nama_user# => Nama User
    * @attribute #table# => table
    **/
    public function actionCetakDetail()
    {
        try {
            $jwt = Yii::$app->jwt;
            $params = Yii::$app->request->get();
            $id = $params['id'];
            $data = $this->actionGetPermintaanMakanDetail($id);

            $permintaan_makan = $data['permintaan_makan'];
            $permintaan_makan_detail = $data['permintaan_makan_detail'];

            $print = new DocoPrint();
            $print->attributes = [
                '#no_rekam_medik#' => $permintaan_makan['no_rekam_medik'],
                '#tgl_pendaftaran#' => $permintaan_makan['tgl_pendaftaran'],
                '#no_pendaftaran#' => $permintaan_makan['no_pendaftaran'],
                '#nama_pasien#' => $permintaan_makan['nama_pasien'],
                '#jenis_kelamin#' => $permintaan_makan['jenis_kelamin'],
                '#jeniskasuspenyakit_nama#' => $permintaan_makan['jeniskasuspenyakit_nama'],
                '#tgl_permintaanmakan#' => $permintaan_makan['tgl_permintaanmakan'],
                '#tanggal_lahir#' => DocoHelpers::convDateTime(date('d F Y', strtotime($permintaan_makan['tanggal_lahir'])), false, true),
                '#umur#' => $permintaan_makan['umur'],
                '#dok_dpjp#' => $permintaan_makan['dok_dpjp'],
                '#kelaspelayanan_nama#' => $permintaan_makan['kelaspelayanan_nama'],
                '#kamarruangan_nokamar#' => $permintaan_makan['kamarruangan_nokamar'],
                '#no_tempattidur#' => $permintaan_makan['no_tempattidur'],
                '#carabayar_nama#' => $permintaan_makan['carabayar_nama'],
                '#penjamin_nama#' => $permintaan_makan['penjamin_nama'],
                '#no_permintaanmakan#' => $permintaan_makan['no_permintaanmakan'],
                '#alasan_pembatalan#' => $permintaan_makan['alasan_pembatalan'],
                '#nama_pemesan#' => $permintaan_makan['nama_pemesan'],
                '#pegawai_nama#' => isset($pegawai['pegawai_nama']) ? $pegawai['pegawai_nama'] : '',
                '#tanggal_dicetak#' => DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime('NOW')), false, true),
                '#nama_user#' => $params['pegawai'],
                '#table#' => $this->renderPartial('pdf_detail', [
                    'data' => $permintaan_makan_detail,
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
    * @controller actionBatalCetakDetail
    * @attribute #no_rekam_medik# => no_rekam_medik
    * @attribute #tgl_pendaftaran# => tgl_pendaftaran
    * @attribute #no_pendaftaran# => no_pendaftaran
    * @attribute #nama_pasien# => nama_pasien
    * @attribute #jenis_kelamin# => jenis_kelamin
    * @attribute #jeniskasuspenyakit_nama# => jeniskasuspenyakit_nama
    * @attribute #tgl_permintaanmakan# => tgl_permintaanmakan
    * @attribute #tanggal_lahir# => tanggal_lahir
    * @attribute #umur# => umur
    * @attribute #dok_dpjp# => dok_dpjp
    * @attribute #kelaspelayanan_nama# => kelaspelayanan_nama
    * @attribute #kamarruangan_nokamar# => kamarruangan_nokamar
    * @attribute #no_tempattidur# => no_tempattidur
    * @attribute #carabayar_nama# => carabayar_nama
    * @attribute #penjamin_nama# => penjamin_nama
    * @attribute #no_permintaanmakan# => no_permintaanmakan
    * @attribute #alasan_pembatalan# => alasan_pembatalan
    * @attribute #no_pembatalan# => no_pembatalan
    * @attribute #nama_pemesan# => nama_pemesan
    * @attribute #tanggal_dicetak# => tanggal_dicetak
    * @attribute #nama_user# => Nama User
    * @attribute #table# => table
    **/
    public function actionBatalCetakDetail()
    {
        try {
            $jwt = Yii::$app->jwt;
            $params = Yii::$app->request->get();
            $id = $params['id'];

            $data = $this->actionGetPermintaanMakanDetail($id);

            $permintaan_makan = $data['permintaan_makan'];
            $permintaan_makan_detail = $data['permintaan_makan_detail'];

            $print = new DocoPrint();
            $print->attributes = [
                '#no_rekam_medik#' => $permintaan_makan['no_rekam_medik'],
                '#tgl_pendaftaran#' => $permintaan_makan['tgl_pendaftaran'],
                '#no_pendaftaran#' => $permintaan_makan['no_pendaftaran'],
                '#nama_pasien#' => $permintaan_makan['nama_pasien'],
                '#jenis_kelamin#' => $permintaan_makan['jenis_kelamin'],
                '#jeniskasuspenyakit_nama#' => $permintaan_makan['jeniskasuspenyakit_nama'],
                '#tgl_permintaanmakan#' => $permintaan_makan['tgl_permintaanmakan'],
                '#tanggal_lahir#' => DocoHelpers::convDateTime(date('d F Y', strtotime($permintaan_makan['tanggal_lahir'])), false, true),
                '#umur#' => $permintaan_makan['umur'],
                '#dok_dpjp#' => $permintaan_makan['dok_dpjp'],
                '#kelaspelayanan_nama#' => $permintaan_makan['kelaspelayanan_nama'],
                '#kamarruangan_nokamar#' => $permintaan_makan['kamarruangan_nokamar'],
                '#no_tempattidur#' => $permintaan_makan['no_tempattidur'],
                '#carabayar_nama#' => $permintaan_makan['carabayar_nama'],
                '#penjamin_nama#' => $permintaan_makan['penjamin_nama'],
                '#no_permintaanmakan#' => $permintaan_makan['no_permintaanmakan'],
                '#alasan_pembatalan#' => $permintaan_makan['alasan_pembatalan'],
                '#waktu_pembatalan#' => $permintaan_makan['waktu_pembatalan'],
                '#no_pembatalan#' => $permintaan_makan['no_pembatalan'],
                '#nama_pemesan#' => $permintaan_makan['nama_pemesan'],
                '#tanggal_dicetak#' => DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime('NOW')), false, true),
                '#nama_user#' => $params['pegawai'],
                '#table#' => $this->renderPartial('pdf_detail', [
                    'data' => $permintaan_makan_detail,
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
     * Print Label Makanan
     *
     * @param Array $registration_number
     * @return JSON
     * @author Aris Munandar
     **/

    /**
    * @controller actionPrintLabelMakanan
    * @attribute #dataLabel# => data all Label
    **/
    public function actionPrintLabelMakananTest()
    {
        try {
            $request = Yii::$app->request;
            $id      = $request->get('permintaaanmakan_id');

            $data = $this->actionGetPermintaanMakanDetail($id);
            $permintaan_makan        = $data['permintaan_makan'];
            $permintaan_makan_detail = $data['permintaan_makan_detail'];

            $count = 0;
            foreach ($permintaan_makan_detail as $key => $value)
            {
                $count += $value['jumlah'];
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#dataLabel#' => $this->renderPartial('_print_makanan_label', [
                    'header' => $permintaan_makan,
                    'detail' => $permintaan_makan_detail,
                    'count'  => $count,
                ])
            ];
            $print->Output();
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
    * @controller actionPrintLabelMakanan
    * @attribute #dataLabel# => data all Label
    **/
    public function actionPrintLabelMakanan()
    {
        return Yii::$app->docoPlugin->execute('print_label_makanan');
    }

     /**
    * @controller actionPrintLabelMultiple
    * @attribute #data# => data
    **/

    public function actionSyncExportExcel()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');

        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);

        $total_data = $this->getPermintaanMakanData()->count();
        $randString = isset($getData['randString']) ? $getData['randString'] : null;

        (new RabbitBgProcess())->send([
            'unique_str' => $randString,
            'filter' => $getData,
            'totalPerPage' => $total_data,
            'countData' => $total_data,
            'sendToUrl' => 'inf-permintaan-makan/drop-file',
            'base_uri' => Yii::$app->docoRest->getBaseUri('pendaftaran'),
        ], 'excel_inf_permintaan_makan',  'import_excel_inf_permintaan_makan');

        return [
            'totalPerPage' => $total_data,
            'unique_str' => $randString,
            'countData' => $total_data,
        ];
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;

        $filePath = $request->get('filePath', null);
        if ($request->isPost)
        {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;

            $path = "uploads/".$filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);

            $nameFile = $path .'/'. $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'upload file berhasil!'
                ];
            }
        }
        return [
            'status' => 422,
            'message' => 'upload file gagal!'
        ];
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $fileName = $rootPath.'/' . $no_request . '.xlsx';

        DocoHelpers::downloadFileExcel($fileName);
    }

    private function getPermintaanMakanData()
    {
        $request = Yii::$app->request;
        $model = new InfoPermintaanMakanView;
        $query = $model::find();

        $start = date('Y-m-d', strtotime('NOW'));
        $end = date('Y-m-d', strtotime('NOW'));
        if(isset($_GET['advanced-filter']['tgl_permintaanmakan']) && $_GET['advanced-filter']['tgl_permintaanmakan'] != '') {
            $explode = explode(" - ", $_GET['advanced-filter']['tgl_permintaanmakan']);
            if(count($explode) == 2) {
                $start = date('Y-m-d', strtotime($explode[0]));
                $end = date('Y-m-d', strtotime($explode[1]));

            }
            unset($_GET['advanced-filter']['tgl_permintaanmakan']);
        }

        if(isset($_GET['advanced-filter']['is_ditagihkan']) && $_GET['advanced-filter']['is_ditagihkan'] != '') {
            if($_GET['advanced-filter']['is_ditagihkan'] == 'ditagihkan'){
                $query = $query->andWhere(['=', 'is_ditagihkan', TRUE]);
            }else{
                $query = $query->andWhere(['or', ['is_ditagihkan' => FALSE], ['is_ditagihkan' => NULL]]);

            }
            unset($_GET['advanced-filter']['is_ditagihkan']);
        }

        $query->andWhere(['between', new \yii\db\Expression('(tgl_permintaanmakan::date)'), $start, $end]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return $query;
    }

}
