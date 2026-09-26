<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-13 16:54:34
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-02-19 14:36:34
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

use app\modules\v1\models\InfoTerimabayarKlaim;
use app\modules\v1\models\InfoTerimabayarKlaimDetail;
use app\modules\v1\models\InfoPenerimaanPembayaranView;
use app\modules\v1\models\InfoPengajuanKlaimView;
use app\modules\v1\models\TerimaBayarKlaim;
use app\modules\v1\models\TerimaBayarKlaimDetail;
use app\modules\v1\models\PengajuanKlaim;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use yii\web\UploadedFile;
use app\modules\v1\models\UploadPayload;
use app\modules\v1\cache\Cache;
use yii\helpers\ArrayHelper;
use Doco\Services\InternalService;

class InfPenerimaanPembayaranController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoPenerimaanPembayaranView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['save']);
        unset($actions['update']);
        return $actions;
    }

    public function actionIndex()
    {
        $data = $this->getData();
        return new ActiveDataProvider([
            'query' => $data,
        ]);
    }

    public function getData()
    {
        $model = new InfoPenerimaanPembayaranView;
        $query = $model::find(true);

        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_terimabayarklaim'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_terimabayarklaim']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_terimabayarklaim']); // Unset Advanced Filter  date range
            }
        }
        $query->andWhere(['between', 'tgl_terimabayarklaim', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    public function actionBatalPenerimaan($id)
    {
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $header = (new TerimaBayarKlaim)->delete([
                'terimabayarklaim_id' => $id
            ]);
            $statusProses = DocoConstants::BELUM_PENGAJUAN;
            Yii::$app->db->createCommand("
                UPDATE pengajuanklaim_t SET status_pengajuanklaim = {$statusProses} 
                FROM terimabayarklaimdetail_t
                WHERE pengajuanklaim_t.pengajuanklaim_id = terimabayarklaimdetail_t.pengajuanklaim_id
                AND terimabayarklaimdetail_t.terimabayarklaim_id = {$id}
                AND terimabayarklaimdetail_t.is_deleted = false
            ")->execute();
            $detail = (new TerimaBayarKlaimDetail)->delete([
                'terimabayarklaim_id' => $id
            ]);
            $transaction->commit();
            return [
                'title' => 'Proses Berhasil!',
                'text' => 'Proses batal penerimaan pembayaran berhasil.'
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return [
                'messages' => $e->getMessage(),
                'status' => 500
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'messages' => $e->getMessage(),
                'status' => 500
            ];
        }
    }

    public function actionInitIndex()
    {
        $result['carabayar'] = [];
        $result['penjamin'] = [];
        $result['pengajuan_klaim'] = [];
        try {
            $result['carabayar'] = Yii::$app->runAction('v1/allow/get-cara-bayar');
            $result['carabayar'] = isset($result['carabayar']['response']) ? $result['carabayar']['response'] : [];
            $result['penjamin'] = Yii::$app->runAction('v1/allow/get-penjamin');
            $result['penjamin'] = isset($result['penjamin']['response']) ? $result['penjamin']['response'] : [];
            return $result;
        } catch (\Exception $e) {
            return $result;
        }
    }

    public function actionExportExcel()
    {
        $model = new InfoPengajuanKlaimView;
        $query = $model::find();
        
        $title = 'Informasi Penerimaan Pembayaran Klaim';
        
        $tgl_awal  = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');
        
        $header = [];
        $header[Yii::t('app', "Tanggal Penerimaan")] = null;

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_terimabayarklaim'])) {
                $helper = new DocoHelpers;
                $tglTerimaBayarKlaimRange = $helper->parsingRangeDate($_GET['advanced-filter']['tgl_terimabayarklaim']);

                $tgl_awal = $tglTerimaBayarKlaimRange['startDate'];
                $tgl_akhir = $tglTerimaBayarKlaimRange['endDate'];
            }

            if (isset($_GET['advanced-filter']['no_terimabayarklaim'])) {
                $header['No Pembayaran'] = $_GET['advanced-filter']['no_terimabayarklaim'];
            } else {
                $header['No Pembayaran'] = '-';
            }
            
            if (isset($_GET['advanced-filter']['carabayar_id'])) {
                $caraBayarId = $_GET['advanced-filter']['carabayar_id'];
                $caraBayar = CaraBayar::find()->where(['carabayar_id' => $caraBayarId])->one();
                $header['Cara Bayar'] = $caraBayar->carabayar_nama;
            } else {
                $header['Cara Bayar'] = '-';
            }
            
            if (isset($_GET['advanced-filter']['penjamin_id'])) {
                $penjaminId = $_GET['advanced-filter']['penjamin_id'];
                $penjamin = Penjamin::find()->where(['penjamin_id' => $penjaminId])->one();
                $header['Penjamin'] = $penjamin->penjamin_nama;
            } else {
                $header['Penjamin'] = '-';
            }

            if (isset($_GET['advanced-filter']['final_alokasi'])) {
                if ($_GET['advanced-filter']['final_alokasi'] == 1) {
                    $statusAlokasi = 'Sudah Alokasi';
                } else {
                    $statusAlokasi = 'Belum Alokasi';
                }

                $header['Status Alokasi'] = $statusAlokasi;
            } else {
                $header['Status Alokasi'] = '-';
            }
        } else {
            $header['Tanggal Penerimaan'] = '-';
            $header['No Pembayaran'] = '-';
            $header['Cara Bayar'] = '-';
            $header['Penjamin'] = '-';
            $header['Status Alokasi'] = '-';
        }

        $query = $this->getData();

        $header[Yii::t('app', "Tanggal Penerimaan")] = ((date('d M Y', strtotime($tgl_awal))." - ".date('d M Y', strtotime($tgl_akhir))));

        $options = [
            array("uploadPath" => "./uploads"),
            "titleStyle" => [
                "fontSize"   => 15,
                "alignment"  => "center",
                "fontWeight" => 600,
            ],
            "customFormatCode" => [
                [
                    'selectColumn' => 'A',
                    'formatCode'   => 'general'
                ],
                [
                    'selectColumn' => 'B',
                    'formatCode'   => 'date'
                ],
                [
                    'selectColumn' => 'C',
                    'formatCode'   => 'general',
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
                    ]
                ],
                [
                    'selectColumn' => 'D',
                    'formatCode'   => 'general',
                    'alignment'    => [
                        'wrapText' => true
                    ]
                ],
            ]
        ];

        $result = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Tanggal penerimaan')] = date('d M Y', strtotime($value['tgl_terimabayarklaim']));
            $newValue[\Yii::t('app', 'No Pembayaran')] = $value['no_terimabayarklaim'];
            $newValue[\Yii::t('app', 'Cara Bayar / Penjamin')] = $value['carabayar_nama'] . "\n" . $value['penjamin_nama'];
            $newValue[\Yii::t('app', 'Total Pembayaran')] = $value['total_terimabayar'];
            $newValue[\Yii::t('app', 'Status Alokasi')] = DocoHelpers::coalesce(DocoConstants::$statusAlokasi[$value['final_alokasi']], null);
            $result[$key] = $newValue;
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, $options, [], [], true);
        $filePath->save('php://output');
        die;
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #periode#   => periode tanggal
    * @attribute #tanggal#   => tanggal sekarang
    * @attribute #jabatan#   => jabatan
    * @attribute #nip#       => nip
    * @attribute #pegawai#   => pegawai mengetahui
    */
    public function actionExportPdf()
    {
        $query = $this->getData();
        $print = new DocoPrint();
        
        $tgl_awal  = date('Y-m-d 00:00:00');
        $tgl_akhir = date('Y-m-d 23:59:59');

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tgl_terimabayarklaim'])) {
                $helper = new DocoHelpers;
                $tglTerimaBayarKlaimRange = $helper->parsingRangeDate($_GET['advanced-filter']['tgl_terimabayarklaim']);

                $tgl_awal = $tglTerimaBayarKlaimRange['startDate'];
                $tgl_akhir = $tglTerimaBayarKlaimRange['endDate'];
            }
        }

        $lookupTransaksi = LookupTransaksi::find()->where(['kode_transaksi' => DocoConstants::KABAG_KEUANGAN])->one();
        $pegawai         = PegawaiView::find()->where(['jabatan_id' => $lookupTransaksi->kode_id])->andWhere(['not', ['pegawai_last_modified_date' => null]])->orderBy(['pegawai_last_modified_date' => SORT_DESC])->one();

        $jabatan = ($pegawai) ? $pegawai->jabatan_nama : '';
        $nip = ($pegawai) ? $pegawai->nomorindukpegawai : '';
        $mengetahui = ($pegawai) ? $pegawai->nama_pegawai : '';

        $print->attributes = [
            '#periode#' => date('d M Y', strtotime($tgl_awal)).' - '.date('d M Y', strtotime($tgl_akhir)),
            '#tanggal#' => date('d M Y'),
            '#pegawai#' => $mengetahui,
            '#jabatan#' => $jabatan,
            '#nip#' => $nip,
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $query->asArray()->all(),
            ]),
        ];

        $print->Output();
    }

    /**
    * @controller actionCetakPenerimaan
    * @attribute #no_pembayaran# => Untuk menampilkan no pembayaran
    * @attribute #jumlah_pembayaran# => untuk menampilkan jumlah pembayaran
    * @attribute #cara_bayar# => untuk menampilkan cara bayar
    * @attribute #penjamin# => untuk menampilkan penjamin
    * @attribute #petugas_penerima# => untuk menmpilkan nama petugas penerima
    * @attribute #catatan# => untuk menampilkan catatan
    * @attribute #tanggal_penerimaan# => untuk menampilkan tanggal penerimaan
    * @attribute #pemilik_rekening# => untuk menampilkan pemilik rekening
    * @attribute #nama_bank# => untuk menampilkan nama bank
    * @attribute #no_rekening# => untuk menampilkan no rekening
    * @attribute #list_no_ajuan# => untuk menampilkan data pengajuan
    * @attribute #tanggal# => untuk menampilkan tanggal 
    * @attribute #nip# =>  untuk menapilkan nip pegawai
    * @attribute #nama# => untuk menmpilkan nama pegawai
    */
    public function actionCetakPenerimaan()
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $id = isset($getData['id']) ? $getData['id'] : null;
        if(!empty($id)) {
            $id = DocoHelpers::decrypt($id);
        }
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $fetchLimit = 30;
        $countData = $this->getDataDetail($id);
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $totalPerPage = ceil($countData/$fetchLimit);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'PenjaminAsuransi\PenerimaanPembayaran\DataDetail' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'id' => $id,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'PenjaminAsuransi\PenerimaanPembayaran\CetakDetail' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'PenjaminAsuransi\PenerimaanPembayaran\UploadDetail' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'unique_str' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionGetAttributes($id)
    {
        $header = InfoTerimabayarKlaim::find()->where([
            'terimabayarklaim_id' => $id
        ])->one();
        
        $detail = InfoTerimabayarKlaimDetail::find()->where([
            'terimabayarklaim_id' => $id
        ])->all();

        $carabayar = Cache::getCaraBayar();

        return [
            'header' => $header,
            'detail' => $detail,
            'cara_bayar' => $carabayar
        ];
    }

    public function actionSave($id)
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            if ($post = $request->post()) {
                $model = TerimaBayarKlaim::find()->where([
                    'terimabayarklaim_id' => $id
                ])->one();
                $model->attributes = $post;
                $statusProses = DocoConstants::BELUM_PENGAJUAN;
                $caraBayarId = ArrayHelper::getValue($post, 'carabayar_id');
                if ($model->validate() && $model->save()) {
                    $idParent = $model->terimabayarklaim_id;
                    $dataPengajuan = ArrayHelper::getValue($post, 'data_pengajuan', []);
                    $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
                    (new InternalService)->sendTo([
                        'Sirs' => [
                            'PenjaminAsuransi\InsertDetailKlaim' => [
                                'data' => $dataPengajuan,
                                'terimabayarklaim_id' => $idParent,
                                'status_proses' => $statusProses,
                                'carabayar_id' => $caraBayarId,
                                'transaction' => $transaction,
                                'is_edit' => true,
                                'user_id' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : null,
                            ]
                        ]
                    ]);
                    
                    $transaction->commit();
                    return [
                        'title' => 'Data Berhasil !',
                        'text' => 'Penerimaan pembayaran berhasil disimpan.',
                        'id' => DocoHelpers::encrypt($idParent),
                        'no_pembayaran' => ArrayHelper::getValue($post, 'no_terimabayarklaim')
                    ];
                } else {
                    return [
                        'status' => 422,
                        'data' => $model->errors
                    ];
                }
            } 
            return [
                'status' => 422,
                'messages' => 'Tidak ada data yang dikirimkan'
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return [
                'messages' => $e->getMessage(),
                'status' => 500
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'messages' => $e->getMessage(),
                'status' => 500
            ];
        }
    }

    public function actionSendFile()
    {
        $request = Yii::$app->request;
        $filePath = $request->get('filePath', null);
        $model = new UploadPayload;
        if($request->isPost) {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;
            $path = 'uploads/'. $filePath;
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            $nameFile = $path.'/'.$model->file;
            if($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'Upload File Berhasil'
                ];
            }
        }
    }

    public function actionDownloadFileDetail()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $rootPath = './uploads';
        $dir = $rootPath . '/' . $filename;
        $fileName = $dir . '/'.$filename.'.xlsx';
        DocoHelpers::downloadFileExcel($fileName);
    }

    private function getDataDetail($id)
    {
        return Yii::$app->db->createCommand("
            SELECT COUNT(*) AS count FROM infoterimabayarklaimdetail_v WHERE terimabayarklaim_id = {$id}
        ")->queryScalar();
    }

    public function actionExportFile()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        $randString = isset($get['randString']) ? $get['randString'] : null;
        $advancedFilter = isset($get['advanced-filter']) ? $get['advanced-filter'] : [];
        $advancedFilterExcel = isset($get['advancedFilter']) ? $get['advancedFilter'] : [];
        $filter = !empty($advancedFilter) ? $advancedFilter : $advancedFilterExcel;
        $order = ArrayHelper::getValue($get, 'order');
        $type = ArrayHelper::getValue($get, 'type');
        $data = $this->getDataPenerimaan(true);
        $totalData = count($data);
        $totalPerPage = $countData = $totalData;
      
        (new InternalService)->sendTo([
            'Sirs' => [
            'PenjaminAsuransi\PenerimaanPembayaran\DataExportFile' => [
                'token' => $auth,
                'xOwner' => $xOwner,
                'unique_str' => $randString,
                'filter' => $filter,
                'order' => $order,
                'type' => $type
            ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'PenjaminAsuransi\PenerimaanPembayaran\ExportFile' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $filter,
                    'type' => $type
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [
                'PenjaminAsuransi\PenerimaanPembayaran\UploadFile' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'type' => $type
                ]
            ]
        ], true);

        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData,
        ];
    }

    private function getDataPenerimaan($isExcel = false)
    {
        $model = new InfoPenerimaanPembayaranView;
        $query = $model::find();
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');
        $request = Yii::$app->request;
        $get = $request->get();
        $advancedFilter = ArrayHelper::getValue($get, 'advanced-filter');
        if(empty($advancedFilter)) {
            $advancedFilter = ArrayHelper::getValue($get, 'advancedFilter');
        }

        if(isset($advancedFilter)) {
            if(isset($advancedFilter['tgl_terimabayarklaim'])) {
                $explode = explode(" - ", $advancedFilter['tgl_terimabayarklaim']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($advancedFilter['tgl_terimabayarklaim']);
            }
        }

        $query->andWhere(['between', 'tgl_terimabayarklaim', $start, $end]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        if($isExcel) {
            $query = $query->asArray()->all();
        }
         
        return $query;
    }

    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $type = $request->get('type', null);
        $filename = $request->get('filename', null);
        $rootPath = './uploads';
        $dir = $rootPath;
        $ext = ($type == 1) ? '.pdf' : '.xlsx';
        $fileName = $dir . '/'.$filename.$ext;
        if($type == 1) {
            if(file_exists($fileName)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/pdf');
                header("Content-Disposition: inline; filename=$fileName");
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                ob_clean();
                flush();
                readfile($fileName);
                unlink($fileName);
                die();
            }
        }
        else {
            DocoHelpers::downloadFileExcel($fileName);
        }
    }
}