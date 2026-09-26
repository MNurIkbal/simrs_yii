<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-13 14:20:14
 */

namespace Doco\gizi\controllers;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use Mpdf\Mpdf;

use GuzzleHttp\Exception\RequestException;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class InfPermintaanMakanController extends DocoController
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
     * @todo Fungsi untuk menampilkan halaman awal informasi permintaan makan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionIndex()
    {
        try {
            // $restGizi = $this->_restGizi->get('allow/get-bundle-data');
            $response = $this->guzzleExec($this->_restGizi, [
                'url' => 'allow/get-bundle-data',
                'payload' => [
                    'query' => [
                        'flag' => 'index-permintaan'
                    ]
                ]
            ]);
            // $responseMaster = json_decode($response->getBody(), true)['response']['master'];
            $responseMaster = ArrayHelper::getValue($response, 'master', []);

            $listRuangan = ArrayHelper::map($responseMaster['ruangan'], 'ruangan_id', 'ruangan_nama');
            $listKamar = ArrayHelper::map($responseMaster['kamar'], 'kamarruangan_id', 'kamarruangan_nokamar');
            $listCaraBayar = ArrayHelper::map($responseMaster['carabayar'], 'carabayar_id', 'carabayar_nama');

            $listStatus = [
                'PROSES' => Yii::t('fe', 'Proses'),
                'BATAL' => Yii::t('fe', 'Batal'),
            ];

            $listPembayaran = [
                'ditagihkan' => Yii::t('fe', 'Ditagihkan'),
                'tidak_ditagihkan' => Yii::t('fe', 'Tidak Ditagihkan'),
            ];

            $konfig_print_gizi = ArrayHelper::getValue($response, 'konfig_print_gizi', false);

            return $this->render('index', get_defined_vars());
        } catch (\Exception $e) {
            Yii::error([
                "Message" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            Yii::error([
                "Message" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    /**
     * @todo Fungsi untuk menampilkan popup detail informasi permintaan makan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionDetail()
    {
        try {
            $id = Yii::$app->request->get('id');
            $decrypted_id = DocoHelpers::decrypt($id);

            $restGizi = $this->_restGizi->get('inf-permintaan-makan/get-permintaan-makan-detail?id='.$decrypted_id);
            $response = json_decode($restGizi->getBody(), true);
            $data = $response['response'];

            return $this->renderAjax('detail', [
                'id' => $id,
                'data' => $data['permintaan_makan'],
                'detail' => $data['permintaan_makan_detail'],
            ]);
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }

    /**
     * @todo Fungsi untuk proses pembatalan permintaan makanan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionBatal()
    {
        try {
            $post = Yii::$app->request->post();

            if (isset($post['alasan_pembatalan']) && $post['alasan_pembatalan'] == '') {
                $return = [];
                $return['metadata']['status'] = 422;
                $return['metadata']['message'] = Yii::t('fe', 'Unprocessable Entity');
                $return['response']['data']['alasan_pembatalan'][] = Yii::t('fe', 'Alasan Pembatalan harus diisi.');

                return DocoHelpers::response($return);
            }

            $restGizi = $this->_restGizi->post('inf-permintaan-makan/batal-permintaan-makan', [
                'form_params' => $post
            ]);
            $response = json_decode($restGizi->getBody(), true);

            if ($response['metadata']['status'] == 200) {
                return DocoHelpers::response($response);
            }else{
                return DocoHelpers::responseTemplate(
                    500,
                    'Error',
                    [],
                    [
                        'title' => Yii::t('fe', 'Peringatan'),
                        'text' => DocoHelpers::response($response['message']),
                        'message' => Yii::t('fe','Terjadi kesalahan').'!',
                    ]
                );
            }
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
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/informasi-permintaan-makan.pdf";

            $restGizi = $this->_restGizi->get('inf-permintaan-makan/export-pdf?'.http_build_query($yiiRestfulParams),[
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
            $url = 'inf-permintaan-makan/export-excel?'.http_build_query($yiiRestfulParams);
            $path = Yii::getAlias("@download") . "/inf-permintaan-makan.xlsx";
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
     * @todo Action untuk melakukan proses cetak detail
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCetakDetail($id, $status)
    {
        try {
            $id = DocoHelpers::decrypt($id);
            $user = Yii::$app->session->get('user_identity')['nama_pegawai'];
            $path = Yii::getAlias("@download") . "/detail-informasi-permintaan-makan.pdf";

            if ($status == 1) {
                $restGizi = $this->_restGizi->get('inf-permintaan-makan/cetak-detail?id='.$id.'&pegawai='.$user, [
                    'save_to' => $path,
                ]);
            } else {
                $restGizi = $this->_restGizi->get('inf-permintaan-makan/batal-cetak-detail?id='.$id.'&pegawai='.$user, [
                    'save_to' => $path,
                ]);
            }

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
     * @todo Fungsi untuk mendapatkan data permintaan makan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataPermintaanMakan()
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

            $restGizi = $this->_restGizi->get('inf-permintaan-makan/get-data-permintaan-makan?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($restGizi->getBody(), true);
            $data = $body['response']['data'];

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    $no++;
                    $value['no'] = $no;
                    $value['primary'] = DocoHelpers::encrypt($value['permintaaanmakan_id']);
                    $value['catatan_diet'] = isset($value['catatan_diet']) ? $value['catatan_diet'] : '-';
                    $value['tgl_permintaanmakan'] = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tgl_permintaanmakan'])), false, true);
                    $value['ruangan_kamar'] = $value['ruangan_nama'].'/'.$value['kamarruangan_nokamar'].' - '.$value['no_tempattidur'];
                    $value['jenisdiet_nama'] = !empty($value['jenisdiet_nama']) ? $value['jenisdiet_nama'] : '-';
                    $value['diagnosa'] = !empty($value['diagnosa']) ? $value['diagnosa'] : '-';
                    $value['riwayat_alergi'] = !empty($value['riwayat_alergi']) ? $value['riwayat_alergi'] : '-';
                    $value['tanggal_lahir'] = date('d M Y',strtotime($value['tanggal_lahir']));
                    unset($value['permintaaanmakan_id']);
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

    /**
     * Print Label Makanan
     *
     * @author Aris Munandar
     **/
    public function actionPrintLabelMakananTes()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request                    = Yii::$app->request;
        $id                         = DocoHelpers::decrypt($request->get('id', null));
        $path                       = Yii::getAlias("@download") . "/print-label-makanan.pdf";
        try {
            $response = $this->_restGizi->post('inf-permintaan-makan/print-label-makanan',
                [
                'query' => [
                    'permintaaanmakan_id' => $id,
                ],
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionPrintLabelMakanan()
    {
        $request = Yii::$app->request;
        $permintaaanmakan_id = $request->get('id', null);
        $decryptPermintaaanmakanId = DocoHelpers::decrypt($permintaaanmakan_id);
        $jumlah = $request->get('jumlah', 1);
        $waktu = $request->get('waktu', null);
        $post = [
            'permintaaanmakan_id' => $decryptPermintaaanmakanId,
            'jumlah' => $jumlah,
            'waktu' => $waktu,
        ];
        $path = Yii::getAlias("@download") . "/print-label-makanan.pdf";

        if(Yii::$app->report->enabled){
            $id = $request->get('id', null);
            $permintaanmakan_ids =[];
            $ex_permintaanmakan_id = explode(',',$id);
            
            foreach($ex_permintaanmakan_id as $_permintaan_id){
                $permintaanmakan_ids[] = DocoHelpers::decrypt($_permintaan_id);
            }
            $decryptPermintaaanmakanIds = implode(',',$permintaanmakan_ids);
            $post = [
                'permintaaanmakan_id' => $decryptPermintaaanmakanIds,
                'jumlah' => $jumlah,
                'waktu' => $waktu,
            ];

            $urlReport = 'label-makan-gizi';
            $path = !empty($optGuzzle['save_to']) ? $optGuzzle['save_to'] : null;

            return Yii::$app->report->exec($urlReport,[
                'queryParameter' => $post,
                'manualRender'=>function() use($post,$path){
                    $response = $this->_restGizi->post('inf-permintaan-makan/print-label-makanan',[
                        'form_params' => $post,
                        'save_to' => $path
                    ]);

                    return DocoHelpers::previewPdf($path);
                }
            ]);
        }

        $rest = $this->_restGizi->post('inf-permintaan-makan/print-label-makanan',
            [
                'form_params' => $post,
                'save_to' => $path
            ]
        );
        $result = json_decode($rest->getBody(), true);
        $body = $result['response'];
        return DocoHelpers::previewPdf($path);
    }
    public function actionPilihJumlahLabelMakanan()
    {
        $title = 'Cetak Label Makanan';
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id');
        $with_jumlah = $request->get('with_jumlah');

        $res_waktu = Yii::$app->docoRest->master->get('lookup/get-lookup-by-type',
                    ['query' => ['type' => 'waktu']
            ]);
        $res_waktu = json_decode($res_waktu->getBody(), True);

        $waktu = ArrayHelper::map(ArrayHelper::getValue($res_waktu, 'response', []), 'lookup_id', 'lookup_value');
        ksort($waktu); // ordering array by asc

        return $this->renderPartial('_modal_jumlah_cetakan', get_defined_vars());
    }


    public function actionGetListDokter()
    {
        $listDokter = $this->helper->guzzleExec($this->_restGizi, [
            'url' => 'allow/get-list-dokter',
            'returnResponse' => true,
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
        ]);

        $payload = Yii::$app->request->get('payload', []);
        if ($payload['page'] == 1 && !empty($listDokter['data'])) {
            $data = $listDokter['data'];
            $allData = [
                'id' => '%',
                'text' => \Yii::t('fe', '-- Pilih --')
            ];
            array_unshift($data, $allData);
            $listDokter['data'] = $data;
        }
        
        return $listDokter;
    }

    public function actionShowPopupExcel()
    {
        $title = Yii::t('fe', 'Unduh Excel Informasi Permintaan Makan Pasien');
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restGizi, [
            'url' => "inf-permintaan-makan/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'Informasi Permintaan Makan Pasien.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->guzzleExec($this->_restGizi,[
            'url' => 'inf-permintaan-makan/download-file',
            'method' => 'GET',
            'payload' => [
                'query' => ['no_request' => $filename],
                'save_to' => $path,
            ],
        ]);

        return DocoHelpers::downloadFile($path,true);
    }


}
