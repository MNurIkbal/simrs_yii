<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-26 16:10:15
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-26 16:53:14
 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstansId;
use app\modules\v1\models\PegawaiView;
use Doco\components\DocoActiveController;
use app\modules\v1\models\PengajuanKlaim;
use app\modules\v1\models\LookupTransaksi;
use app\modules\v1\models\TerimaBayarKlaim;
use app\modules\v1\models\InfoTerimabayarKlaim;
use app\modules\v1\models\InfoPengajuanKlaimView;
use app\modules\v1\models\TerimaBayarKlaimDetail;
use app\modules\v1\models\InfoTerimabayarKlaimDetail;
use Doco\Services\InternalService;
use yii\web\UploadedFile;
use app\modules\v1\models\UploadForm;

class TransaksiPenerimaanPembayaranController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\TerimaBayarKlaim';
    public $messageBroker = [
        'save' => [
            'services' => [
                'Sirs' => [
                    'InsertDetailKlaim' => [
                        'result' => true,
                        'successProcess' => false,
                    ]
                ],
            ]
        ],
    ];

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["get-pengajuan"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['save']);
        return $actions;
    }

    public function actionGetPengajuan()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $page = $request->get('page', 1);

        // Get Data Ajuan
        $modal = new InfoPengajuanKlaimView;
        $query = $modal::find()->where(['is_active' => true]);

        if (isset($get['term'])) {
            $query->andWhere(['ILIKE', 'no_pengajuanklaim', $get['term']]);
        }

        if (isset($get['penjamin_id'])) {
            $query->andWhere([
                'penjamin_id' => $get['penjamin_id']
            ]);
        }

        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;

        return $query->limit($limit)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        try {
            if ($post = $request->post()) {
                $caraBayarId = ArrayHelper::getValue($post, 'carabayar_id');
                $randString = $request->get('randString', null);
                $connection = Yii::$app->db;
                $transaction = $connection->beginTransaction();
                $model = new TerimaBayarKlaim;
                $model->attributes = $post;
                if($model->validate() && $model->save()) {
                    $idParent = $model->terimabayarklaim_id;
                    $transaction->commit();
                    return [
                        'title' => 'Data Berhasil !',
                        'text' => 'Penerimaan pembayaran berhasil disimpan.',
                        'terimabayarklaim_id' => $idParent,
                        'randString' => $randString,
                        'carabayar_id' => $caraBayarId,
                        'data_pengajuan' => $request->post('data_pengajuan', []),
                        'no_pembayaran' => DocoHelpers::encrypt(ArrayHelper::getValue($post, 'no_terimabayarklaim')) 
                    ];
                }
                else {
                    $transaction->rollBack();
                    return [
                        'status' => 422,
                        'data' => $model->errors,
                    ];
                }
            }
            return [
                'status' => 422,
                'messages' => 'Tidak ada data yang dikirimkan'
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'messages' => $e->getMessage(),
                'status' => 500
            ];
        } catch (\Exception $e) {
            return [
                'messages' => $e->getMessage(),
                'status' => 500
            ];
        }
    }

    /**
     * @controller actionCetakPenerimaan
     * @attribute #no_ajuan# => Untuk menampilkan no ajuan
     * @attribute #cara_bayar# => untuk menampilkan carabyar
     * @attribute #penjamin# => untuk menampilkan penjamin
     * @attribute #nominal# => untuk menampilkan nominal
     * @attribute #list_no_ajuan# => untuk menampilkan list no ajuan
     * @attribute #nominal_terbilang# => untuk menmpilkan nominal terbilang
     * @attribute #tanggal_penerimaan# => untuk menampilkan tanggal penerimaan
     * @attribute #nip# =>  untuk menapilkan nip pegawai
     * @attribute #nama# => untuk menmpilkan nama pegawai
     */
    public function actionCetakPenerimaan()
    {
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $noPembayaran = $request->get('no_pembayaran', null);
        $condition = !empty($id) ? ['terimabayarklaim_id' => $id] : ['no_terimabayarklaim' => $noPembayaran];
        $print = new DocoPrint('bukti-penerimaan-pembayaran-klaim');
        $header = InfoTerimabayarKlaim::find()->where($condition)->one();
        $caraBayarId = ArrayHelper::getValue($header, 'carabayar_id');
        if($caraBayarId == DocoConstants::CARA_BAYAR_BPJS) {
            $detail = [];
        }
        else {
            $detail = InfoTerimabayarKlaimDetail::find()->select(['no_pengajuanklaim'])->where($condition)->all();
        }
        
        $_html = "<ul>";
        foreach ($detail as $value) {
            $noPengajuanKlaim = ArrayHelper::getValue($value, 'no_pengajuanklaim');
            if(!empty($noPengajuanKlaim)) {
                $_html .= "<li>" . $noPengajuanKlaim . "</li>";
            }
        }
        $_html .= "</ul>";
        $totalTerimaBayar = ArrayHelper::getValue($header, 'total_terimabayar', 0);
        $tglTerimaBayarKlaim = ArrayHelper::getValue($header, 'tgl_terimabayarklaim');
        if(!empty($tglTerimaBayarKlaim)) {
            $tglTerimaBayarKlaim = date('d-M-Y H:i:s', strtotime($tglTerimaBayarKlaim));
        }
        $print->attributes = [
            '#no_ajuan#' => ArrayHelper::getValue($header, 'no_terimabayarklaim'),
            '#cara_bayar#' => ArrayHelper::getValue($header, 'carabayar_nama'),
            '#penjamin#' => ArrayHelper::getValue($header, 'penjamin_nama'),
            '#nominal#' => DocoHelpers::formatNumber($totalTerimaBayar),
            '#list_no_ajuan#' => ($caraBayarId == DocoConstants::CARA_BAYAR_BPJS) ? 'Terlampir' : $_html,
            '#nominal_terbilang#' => $this->terbilang($totalTerimaBayar),
            '#tanggal#' => $tglTerimaBayarKlaim,
            '#nip#' => ArrayHelper::getValue($header, 'nomorindukpegawai'),
            '#nama#' => ArrayHelper::getValue($header, 'pegawai_penerima'),
        ];

        $print->Output();
    }

    private function penyebut($nilai) {
		$nilai = abs($nilai);
		$huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$temp = $this->penyebut($nilai - 10). " Belas";
		} else if ($nilai < 100) {
			$temp = $this->penyebut($nilai/10)." Puluh". $this->penyebut($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " Seratus" . $this->penyebut($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = $this->penyebut($nilai/100) . " Ratus" . $this->penyebut($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " Seribu" . $this->penyebut($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = $this->penyebut($nilai/1000) . " Ribu" . $this->penyebut($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = $this->penyebut($nilai/1000000) . " Juta" . $this->penyebut($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = $this->penyebut($nilai/1000000000) . " Milyar" . $this->penyebut(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = $this->penyebut($nilai/1000000000000) . " Trilyun" . $this->penyebut(fmod($nilai,1000000000000));
		}     
		return $temp;
	}

    private function terbilang($nilai) {
		if($nilai<0) {
			$hasil = "minus ". trim($this->penyebut($nilai));
		} else {
			$hasil = trim($this->penyebut($nilai));
		}     		
		return $hasil;
	}

    public function actionConvertExcel()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;
        $files = UploadedFile::getInstancesByName("upload_file");
        $files = isset($files[0]) ? $files[0] : $files;
        $path = "/tmp/";
        $randString = $request->get('randString');
        if (!file_exists($path)) mkdir($path, 0755, true);
        $nameFile = $path . '/' . $randString;
        if ($files->saveAs($nameFile, false)) {
            $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;

            (new InternalService)->sendTo([
                'Sirs' => [
                    'PenjaminAsuransi\PenerimaanPembayaran\ImportKlaim' => [
                        'nameFile' => $nameFile,
                        'randString' => $randString,
                    ]
                ]
            ], true);

            (new InternalService)->sendTo([
                'Sirs' => [
                    'PenjaminAsuransi\PenerimaanPembayaran\InsertImportKlaim' => [
                        'randString' => $randString,
                        'user_id' => !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : null,
                    ]
                ]
            ], true);

            return [
                'randString' => $randString,
            ];
        }
    }
}
