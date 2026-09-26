<?php

namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\UploadedFile;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

use app\modules\pendaftaran\models\BpjsNewForm;
use app\modules\pendaftaran\models\AsuransiForm;
use app\modules\pendaftaran\models\KunjunganForm;
use app\modules\pendaftaran\models\PasienForm;
use app\modules\pendaftaran\models\PendaftaranForm;
use app\modules\pendaftaran\models\RujukanForm;
use app\modules\pendaftaran\models\TipePasienForm;
use app\modules\pendaftaran\models\MultiCarabayarForm;
use app\modules\pendaftaran\models\PjpasienForm;

use GuzzleHttp\Exception\RequestException;

use app\modules\pendaftaran\components\traits\PendaftaranTrait;

class ReservasiMcuController extends DocoController
{
    use PendaftaranTrait;

    protected $_title = 'Pendaftaran Rawat Jalan';
    protected $_module = '/pendaftaran/daftar-rajal';
    protected $_moduleRedirect = '/pendaftaran/daftar-rajal';
    protected $allowAction = ['*'];
    protected $_restPendaftaran;
    protected $_instalasi_id_rj = DocoConstants::INSTALASI_ID_RJ;
    protected $_chace_loket;
    const COMPLETE = 'complete';
    const INCOMPLETE = 'incomplete';
    const DUPLIKASI = 'duplikasi';
    const NORM = 'no_rekam_medik';

    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;
    }

    public function actionIndex($id_booking = null, $pendaftaranol_id = null, $loket_id = null, $params = 'mcu', $janji_id = null)
    {
        if ($janji_id) {
            $janji_id = DocoHelpers::decrypt($janji_id);
        }
        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $loket_nama = '';
        $pasien_id = $carabayar_id = $penjamin_id = $ruangan_id = '';
        $penOl = [];
        /** Menampilkan Form Pendaftaran */
        $instalasi_id = $this->_instalasi_id_rj;
        if ($params != 'rajal') {
            $instalasi_id = DocoConstants::INSTALASI_MCU;
            $params = 'mcu';
            $this->_title = 'Pendaftaran Reservasi MCU';
            $this->_module = '/pendaftaran/reservasi-mcu';
        }
        $module = $this->_module;
        $title = $this->_title;

        $modelPasien = new PasienForm();
        if($params != 'rajal'){
            $modelPasien->scenario = "default";
        } else{
            $modelPasien->scenario = "pendaftaran-rajal";
        }
        $modelKunjungan = new KunjunganForm;
        $tipePasien = new TipePasienForm;
        $multiPayer = new MultiCarabayarForm;
        $modelRujukan = new RujukanForm;
        $modelPj = new PjpasienForm;
        $modelAsuransi = new AsuransiForm;
        $modelBpjs = new BpjsNewForm;
        $modelBpjs->scenario = 'rajalskdp';
        $modelKunjungan->scenario = 'with_mandatory_pjawab_nourut';

        try {
            if (Yii::$app->request->post()) {
                return $this->actionSimpanKunjungan($params);
            }

            $ruanganId = $active_workspace['ruangan_id'];
            $instalasi_workspace = $active_workspace['instalasi_id'];
            $dataForm = $this->getDataApi($instalasi_id, 2, $pendaftaranol_id, null, $janji_id);
            $modelPasien->propinsi_id = isset($dataForm['defaultPropinsi']) ? $dataForm['defaultPropinsi'] : null;
            $modelPasien->kabupaten_id = isset($dataForm['defaultKota']) ? $dataForm['defaultKota'] : null;
            if($pendaftaranol_id) {
                $dataOl = isset($dataForm['pendaftaranol']) ? $dataForm['pendaftaranol'] : [];
                $penOl = [
                    'pendaftaranol_id' => isset($dataOl['pendaftaranol_id']) ? $dataOl['pendaftaranol_id'] : null,
                    'status_pasien' => isset($dataOl['status_pasien']) ? $dataOl['status_pasien'] : null,
                    'pasien_id' => isset($dataOl['pasien_id']) ? $dataOl['pasien_id'] : null,
                    'dokter_id' => isset($dataOl['pegawai_id']) ? $dataOl['pegawai_id'] : null,
                    'carabayar_id' => isset($dataOl['carabayar_id']) ? $dataOl['carabayar_id'] : null,
                    'penjamin_id' => isset($dataOl['penjamin_id']) ? $dataOl['penjamin_id'] : null,
                    'ruangan_id' => isset($dataOl['ruangan_id']) ? $dataOl['ruangan_id'] : null,
                    'no_rekam_medik' => isset($dataOl['no_rekam_medik']) ? $dataOl['no_rekam_medik'] : null,
                    'nama_pasien' => isset($dataOl['nama_pasien']) ? $dataOl['nama_pasien'] : null,
                    'tanggal_lahir' => isset($dataOl['tanggal_lahir']) ? date('d-m-Y',strtotime($dataOl['tanggal_lahir'])) : null,
                    'tgl_pendaftaranol' => isset($dataOl['tgl_pendaftaranol']) ? date('d-m-Y H:i',strtotime($dataOl['tgl_pendaftaranol'])) : null,
                    'all' => $dataOl
                ];
            }
            if($janji_id){
                $dataJanji = isset($dataForm['janjiPoli']) ? $dataForm['janjiPoli'] : [];
                /* permintan konsul override ke var penOl agar form pendaftaran tidak berubah lgi */
                $penOl = [
                    'buatjanjipoli_id' => isset($dataJanji['buatjanjipoli_id']) ? $dataJanji['buatjanjipoli_id'] : null,
                    'pasien_id' => isset($dataJanji['pasien_id']) ? $dataJanji['pasien_id'] : null,
                    'dokter_id' => isset($dataOl['pegawai_id']) ? $dataOl['pegawai_id'] : null,
                    'carabayar_id' => isset($dataJanji['carabayar_id']) ? $dataJanji['carabayar_id'] : null,
                    'penjamin_id' => isset($dataJanji['penjamin_id']) ? $dataJanji['penjamin_id'] : null,
                    'ruangan_id' => isset($dataJanji['ruangan_id']) ? $dataJanji['ruangan_id'] : null,
                    'no_rekam_medik' => isset($dataJanji['no_rekam_medik']) ? $dataJanji['no_rekam_medik'] : null,
                    'nama_pasien' => isset($dataJanji['nama_pasien']) ? $dataJanji['nama_pasien'] : null,
                    'tanggal_lahir' => isset($dataJanji['tanggal_lahir']) ? date('d-m-Y',strtotime($dataJanji['tanggal_lahir'])) : null,
                    'tgl_pendaftaran' => isset($dataJanji['tgl_pendaftaran']) ? date('d-m-Y H:i',strtotime($dataJanji['tgl_pendaftaran'])) : null,
                    'tgl_buatjanji' => isset($dataJanji['tgl_buatjanji']) ? date('d-m-Y H:i',strtotime($dataJanji['tgl_buatjanji'])) : null,
                    'status_janji' =>isset($dataJanji['status_janji']) ? $dataJanji['status_janji'] : null
                ];
            }

            if ($dataForm['konfigSystem']['is_nourut'] && !$pendaftaranol_id && !$janji_id) {
                $modelKunjungan->scenario = 'with_mandatory_pjawab_nourut';
            }

            $rujukan_dari = isset($dataForm['rujukan_dari']) ? $dataForm['rujukan_dari'] : null;
            $render_pasien_data = [
                "tipePasien" => $tipePasien,
                "modelKunjungan" => $modelKunjungan,
                "modelRujukan" => $modelRujukan,
                'modelPasien' => $modelPasien,
                'modelBpjs' => $modelBpjs,
                'data_lookup' => isset($dataForm["lookup"]) ? $dataForm["lookup"] : [],
                'data_master' => $dataForm["master"],
                'optionsProv' => $dataForm['optionsProv'],
                "instalasi_id" => $instalasi_id,
                "instalasi" => $dataForm['instalasi'],
                "ruangan" => $dataForm['ruangan'],
                "asal_rujukan" => $dataForm['asal_rujukan'],
                "carabayar" => $dataForm['cara_bayar'],
                "penjamin_id" => $penjamin_id,
                'carabayarOptions' => $dataForm['carabayarOptions'],
                'penOl' => $penOl,
                'default_asal_rujukan' => $dataForm['default_asal_rujukan'],
                'instalasi_workspace' => $instalasi_workspace,
                'is_hide_alias' => isset($dataForm['konfigSystem']['is_hide_alias']) ? $dataForm['konfigSystem']['is_hide_alias'] : null,
                'support_multipayer' => ArrayHelper::getValue($dataForm,'konfigSystem.support_multipayer',false),
                'multiPayer' => $multiPayer
            ];

            $render_kunjungan_data = [
                "modelKunjungan" => $modelKunjungan,
                "modelPj" => $modelPj,
                'data_lookup' => $dataForm["lookup"],
                'data_master' => $dataForm["master"],
                'kelaspelayanan' => $dataForm["kelas_pelayanan"],
                "instalasi_id" => $instalasi_id,
                "instalasi" => $dataForm['instalasi'],
                "ruangan" => $dataForm['ruangan'],
                "asal_rujukan" => $dataForm['asal_rujukan'],
                "carabayar" => $dataForm['cara_bayar'],
                "penjamin_id" => $penjamin_id,
                'carabayarOptions' => $dataForm['carabayarOptions'],
                'modelAsuransi' => $modelAsuransi,
                'modelBpjs' => $modelBpjs,
                'penOl' => $penOl,
                'ruanganId' => $ruanganId,
                'default_jenis_penyakit' => $dataForm['default_jenis_penyakit'],
                'instalasi_workspace' => $instalasi_workspace,
                'is_nourut' => $dataForm['konfigSystem']['is_nourut'],
                'is_limit_tagihan' => $dataForm['konfigSystem']['is_limit_tagihan'],
                'support_multipayer' => ArrayHelper::getValue($dataForm,'konfigSystem.support_multipayer',false),
                'multiPayer' => $multiPayer
            ];

        } catch (RequestException $e) {
            return DocoHelpers::response([
                'message' => $e->getMessage()
            ],500);
        } catch (Exception $e) {
            $render_pasien_data =
            $render_kunjungan_data = [];
        }
        $jenisantrian_id = DocoConstants::JA_PDN;
        return $this->render('index', get_defined_vars());
    }

    public function actionDownloadTemplate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $penjamin_id = $request->get('penjamin_id', null);
        $query = [
            'penjamin_id' => $penjamin_id
        ];
        try {
            $path = Yii::getAlias("@download") . "/template-mcu.xlsx";
            $response = $this->_restPendaftaran->get('reservasi-mcu/download-excel',[
                'query' => $query,
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];

            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e){
           return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionUploadTemplate()
    {
        $request = Yii::$app->request;
        $model = new TipePasienForm();
        $model->upload_file = UploadedFile::getInstance($model, 'upload_file');
        $result = $info = $tmpRm = $countRm = [];
        $group_carabayar = $request->get('group_carabayar',null);
        $penjamin_id = $request->get('penjamin_id',null);

        if ($model->upload_file == NULL) {

            $response['response']['file'] = '';
            $response['response']['data'] = $result;
            $response['response']['status'] = 422;
            $response['response']['title'] = 'Proses Gagal';
            $response['response']['text'] = 'Format yang di Upload tidak sesuai, harus berupa xls, xlsx';

            return DocoHelpers::response($response, 422);
        }

        $fileName = $model->upload_file->name;
        $file = $model->upload_file->tempName;
        if (isset($model->upload_file)) {
            $getDataImport=  DocoHelpers::getUploadFileExcel($file);
            $nomor = 0;
            $groupOrder = 0;
            $countIncomplete = 0;
            $countComplete = 0;
            $countDouble = 0;
            for($row=4; $row <= $getDataImport['highestRow']; $row++){
                $rowData = $getDataImport['sheet']->rangeToArray('B'.$row.':'.$getDataImport['highestColumn'].$row,Null,true, false);

                foreach ($rowData as $key => $value) {
                    $nomor++;
                    $status = self::COMPLETE;

                    if (!$value[0] && !$value[1] && !$value[2] && !$value[3] && !$value[4] && !$value[5] && !$value[6] && !$value[7] && !$value[8] && !$value[9] && !$value[10] && !$value[11] && !$value[12] && !$value[13] && !$value[15] && !$value[16] && !$value[17] && !$value[18] && !$value[19] && !$value[20]) {
                        continue;
                    } else if (!empty($group_carabayar) && $group_carabayar != 419) {
                        if (!$value[1] || !$value[2] || !$value[4] || !$value[5] || !$value[6] || !$value[7] || !$value[8] || !$value[9] || !$value[10] || !$value[11] || !$value[12] || !$value[13] || !$value[15] || !$value[16] && !$value[17] && !$value[18] && !$value[19] && !$value[20]) {
                            $status = self::INCOMPLETE;
                            $groupOrder = 1;
                            $countIncomplete++;
                        } else {
                            $countComplete++;
                        }
                    } else if (!$value[0] || !$value[1] || !$value[2] || !$value[4] || !$value[5] || !$value[6] || !$value[7] || !$value[8] || !$value[9] || !$value[10] || !$value[11] || !$value[12] || !$value[13] || !$value[15] || !$value[16] && !$value[17] && !$value[18] && !$value[19] && !$value[20]) {
                        $status = self::INCOMPLETE;
                        $groupOrder = 1;
                        $countIncomplete++;
                    } else {
                        $countComplete++;
                    }

                    if($rowData[0][3] != ''){
                        $tmpRm[] = strval($rowData[0][3]);
                    }

                    $tgl = null;
                    if ($rowData[0][6]) {
                        $tgl  = $rowData[0][6];
                        if(!strpos($tgl, '/')) {
                            $tgl = ($tgl - 25569) * 86400;
                            $tgl = gmdate("d-m-Y", $tgl);
                        } else {
                            $tgl = str_replace('/', '-', $rowData[0][6]);
                        }
                    }

                    $jenisidentitas = explode('/', $rowData[0][1]);
                    $no_identitas_pasien = explode('/', $rowData[0][2]);

                    $additional_data = [];
                    for($i = 0; $i < count($jenisidentitas); $i++) {
                        $additional_data[] = [
                            'jenisidentitas' => $jenisidentitas[$i],
                            'no_identitas_pasien' => (isset($no_identitas_pasien[$i])) ? $no_identitas_pasien[$i]:''
                        ];
                    }

                    $tgl_pemeriksaan = null;
                    if ($rowData[0][20]) {
                        $tgl_pemeriksaan  = $rowData[0][20];
                        if(!strpos($tgl_pemeriksaan, '/')) {
                            $tgl_pemeriksaan = ($tgl_pemeriksaan - 25569) * 86400;
                            $tgl_pemeriksaan = gmdate("d-m-Y", $tgl);
                        } else {
                            $tgl_pemeriksaan = str_replace('/', '-', $rowData[0][20]);
                        }
                    }

                    $item = [
                        'no_asuransi'    => !empty($rowData[0][0])? $rowData[0][0]:'',
                        'jenisidentitas'   => !empty($rowData[0][1])? $rowData[0][1]:'',
                        'no_identitas_pasien'  => !empty($rowData[0][2])? $rowData[0][2]:'',
                        self::NORM => $rowData[0][3],
                        'nama_lengkap' => !empty($rowData[0][4])? $rowData[0][4]:'',
                        'nama_pasien' => !empty($rowData[0][4])? $rowData[0][4]:'', // For validation identitas pasien
                        'tempat_lahir' => !empty($rowData[0][5])? $rowData[0][5]:'',
                        'tanggal_lahir' => $tgl,
                        'jenis_kelamin' => !empty($rowData[0][7])? $rowData[0][7]:'',
                        'statusperkawinan' => !empty($rowData[0][8])? $rowData[0][8]:'',
                        'nama_kota' => !empty($rowData[0][9])? $rowData[0][9]:'',
                        'alamat' => !empty($rowData[0][10])? $rowData[0][10]:'',
                        'no_mobile_phone' => !empty($rowData[0][11])? $rowData[0][11]:'',
                        'warga_negara' => !empty($rowData[0][12])? $rowData[0][12]:'',
                        'alamat_domisili' => !empty($rowData[0][13])? $rowData[0][13]:'',
                        'alamatemail' => !empty($rowData[0][14])? $rowData[0][14]:'',
                        'nomorindukpegawai' => !empty($rowData[0][15])? $rowData[0][15]:'',
                        'departemen' => !empty($rowData[0][16])? $rowData[0][16]:'',
                        'posisi_bagian' => !empty($rowData[0][17])? $rowData[0][17]:'',
                        'nama_perusahaan' => !empty($rowData[0][18])? $rowData[0][18]:'',
                        'jenis_mcu' => !empty($rowData[0][19])? $rowData[0][19]:'',
                        'tgl_pemeriksaan' => $tgl_pemeriksaan,
                        'additional_data' => $additional_data,
                        'status'   => $status,
                        'no'   => $nomor,
                        'group' => $groupOrder
                    ];

                    $result[] = $item;

                    $info = [
                        'dataComplete' => $countComplete,
                        'dataIncomplete' => $countIncomplete,
                        'dataDouble' => 0,
                    ];
                }
            }

            $countRm = array_count_values($tmpRm);

            if(!empty($tmpRm)){
                try {
                    $request = $this->_restPendaftaran->post('pendaftaran-mcu/validasi-rekam-medik', [
                        'form_params' => $tmpRm
                    ]);
                    $body = json_decode($request->getBody(),TRUE);
                    $tmpRm = $body['response'];
                } catch (\Exception $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                } catch (RequestException $e){
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                }
            }

            foreach($result as $k => $v) {
                $isValid = true;
                if($v[self::NORM] != ''){
                    if(!empty($tmpRm) && $tmpRm[$v[self::NORM]] != true && $v['status'] == self::COMPLETE){
                        $isValid = false;
                    }
                    if(!empty($countRm) && $countRm[$v[self::NORM]] > 1 && $v['status'] == self::COMPLETE){
                        $isValid = false;
                    }
                    if(!$isValid){
                        $countDouble++;
                        $countComplete--;
                        $result[$k]['status'] = self::DUPLIKASI;
                        $result[$k]['group'] = 1;
                        $info['dataDouble'] = $countDouble;
                        $info['dataComplete'] = $countComplete;
                    }
                }
            }

            if(!empty($result)){
                try {
                    $request = $this->_restPendaftaran->post('reservasi-mcu/validasi-paket', [
                        'form_params' => $result,
                        'penjamin_id' => $penjamin_id
                    ]);
                    $body = json_decode($request->getBody(),TRUE);
                    $result = $body['response']['data'];
                    $info['dataPaketNotfound'] = $body['response']['count'];
                } catch (\Exception $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                } catch (RequestException $e){
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                }
                
                try {
                    $request = $this->_restPendaftaran->post('pendaftaran-mcu/validasi-identitas-pasien', [
                        'form_params' => $result
                    ]);
                    $body = json_decode($request->getBody(),TRUE);
                    $result = $body['response']['data'];
                    $info['dataMultipleRM'] = $body['response']['count'];
                } catch (\Exception $e) {
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                } catch (RequestException $e){
                    return DocoHelpers::response(['message' => $e->getMessage()],500);
                }
            }

            ArrayHelper::multisort($result, ['group', self::NORM], [SORT_DESC, SORT_DESC]);

            $response['response']['file'] = $fileName;
            $response['response']['data'] = $result;
            $response['response']['status'] = 200;
            $response['response']['title'] = 'Proses Berhasil';
            $response['response']['text'] = 'Data Berhasil di upload!';
            $response['response']['info'] = $info;
            $response['response']['tmp'] = $countRm;

            return DocoHelpers::response($response ,200);
        }
        else {
            $response['response']['title'] = 'Proses Gagal!';
            $response['response']['text'] = 'File gagal di upload.';
            return DocoHelpers::response($response, 422);
        }
    }

    public function actionSimpanKunjungan($params)
    {
        $params = 'mcu';
        $request = Yii::$app->request;
        $isMultiPayer = false;
        if($request->post('instalasi_id')) {
            if($request->post('instalasi_id') == DocoConstants::INSTALASI_MCU) {
                $params = DocoConstants::PARAM_DFTR[DocoConstants::WS_MCU];
            }
        }

        $payLoadRequest = [
            'tipe_pasien' => [],
            'kunjungan' => [],
            'pasien' => [],
            'rujukan' => [],
            'penanggung_jawab' => [],
            'pj_pasien' => [],
            'asuransi' => [],
            'bpjs' => [],
            'multi_payer' => [],
        ];

        $instalasi_id = DocoConstants::INSTALASI_MCU;
        $default_scenario = "pendaftaran-mcu";

        $modelKunjungan = new KunjunganForm;
        $modelTipePasien = new TipePasienForm;
        $modelAsuransi = new AsuransiForm;
        $modelKunjungan->scenario = 'form_kunjugan';

        if (!empty($request->post('TipePasienForm'))) {
            $modelTipePasien->attributes = $request->post('TipePasienForm');
            $modelTipePasien->antrian_id = $request->post('antrian_id');
            $modelTipePasien->pendaftaranol_id = $request->post('pendaftaranol_id');
            /** Kondisi ketika Bpjs Error */
            if (!empty($modelPasien->no_rekam_medik)){
                $modelTipePasien->no_rekam_medik = $modelPasien->no_rekam_medik;
                $modelTipePasien->asalrujukan_id = 2; // WIP
            };

            /** Kondisi asal rujukan di MCU */
            if($params == DocoConstants::PARAM_DFTR[DocoConstants::WS_MCU]) {
                $modelTipePasien->asalrujukan_id = $request->post('asalrujukan_id_hidden');
            }
            $payLoadRequest['tipe_pasien'] = $modelTipePasien->attributes;
            if($modelTipePasien->is_multi_payer == true) {
                $isMultiPayer = true;
            }
        }

        // if (!empty($request->post('KunjunganForm'))) {
        //     $kunjungan = $request->post('KunjunganForm');
        //     $dokter = null;

        //     switch ($instalasi_id) {
        //         case DocoConstants::INSTALASI_ID_RJ:
        //             $dokter = $kunjungan['dokter_id'];
        //             break;
        //         case DocoConstants::INSTALASI_ID_RI:
        //             $dokter = null;
        //             break;
        //         case DocoConstants::INSTALASI_ID_RD:
        //             $dokter = $kunjungan['dokter_id'];
        //             break;
        //         case DocoConstants::INSTALASI_MCU:
        //             $dokter = $kunjungan['dokter_id'];
        //             break;
        //         default:
        //             $dokter = (isset($kunjungan['pegawai_id']) && !is_null($kunjungan['pegawai_id'])) ? $kunjungan['pegawai_id'] : null;
        //             break;
        //     }

        //     $modelKunjungan->attributes = $kunjungan;
        //     $modelKunjungan->pegawai_id = $dokter;
        //     $modelKunjungan->tindakan_karcis = $request->post('list_tindakan');
        //     if($params == 'mcu') {
        //         $list_paket = json_decode($request->post('list_penunjang','{}'), true);
        //         if(empty($list_paket)) {
        //             return DocoHelpers::response([
        //                 'response' => [
        //                     'title' => 'Proses Gagal!',
        //                     'text' => 'Paket MCU tidak boleh kosong.'
        //                 ]
        //             ],422);
        //         }
        //         else {
        //             $list_paket = $list_paket['paket'];
        //             $tipepaket_id = $arrPaket = [];
        //             if(!empty($list_paket)) {
        //                 foreach ($list_paket as $key => $value) {
        //                     $tipepaket_id[] = $value['id'];
        //                 }
        //             }

        //             $arrPaket = "[" . implode(",", $tipepaket_id) . "]";
        //             $modelKunjungan->list_paket = $arrPaket;
        //         }

                if($modelTipePasien->is_kolektif == true){
                    $listPasienMcu = $request->post('listPasienMcu');
                    if(empty($listPasienMcu)) {
                        return DocoHelpers::response([
                            'response' => [
                                'title' => 'Proses Gagal!',
                                'text' => 'List Pasien MCU tidak boleh kosong.'
                            ]
                        ],422);
                    }
                    $modelKunjungan->list_pasien_mcu = $listPasienMcu;
                    $no_exportexcel = date('YmdHis');
                    $modelKunjungan->no_exportexcel = $no_exportexcel;
                }
            // }
            // else {
            //     $modelKunjungan->list_penunjang = $request->post('list_penunjang','{}');
            // }

            $payLoadRequest['kunjungan'] = $modelKunjungan->attributes;
            // if ($params == 'penunjang') {
            //     $listPenunjang = json_decode($modelKunjungan->list_penunjang,true);
            //     if (empty($listPenunjang ) && $modelKunjungan->instalasi_id != DocoConstants::INSTALASI_ID_BEDAH) {
            //         return DocoHelpers::response([
            //             'response' => [
            //                 'title' => 'Proses Gagal!',
            //                 'text' => 'Tindakan tidak boleh kosong.'
            //             ]
            //         ], 422);
            //     }
            // }

            // if ($request->post('is_ranap')) {
            //     $ranap = $request->post('PasienAdmisiForm');
            //     $modelKunjungan->tgl_pendaftaran = $ranap['tgl_admisi'];
            //     $modelKunjungan->pegawai_id = isset($ranap['pegawai_id']) && !is_null($ranap['pegawai_id']) ? $ranap['pegawai_id'] : null;
            // }

            // if(!$modelKunjungan->validate()) {
            //     $errors = DocoHelpers::parseError($modelKunjungan->errors, 'KunjunganForm');
            //     return DocoHelpers::responseTemplate(422, 'Error', $errors);
            // }
        // }

        // if (!empty($request->post('AsuransiForm'))) {
        //     $asuransi = $request->post('AsuransiForm');
        //     $modelAsuransi->attributes = $asuransi;
        //     $modelAsuransi->nokartuasuransi = $request->post('no_asuransi');
        //     $payLoadRequest['asuransi'] = $modelAsuransi->attributes;
        // }

        // if (!empty($request->post('no_asuransi'))) {
        //     $asuransi = $request->post('AsuransiForm');
        //     $modelAsuransi->attributes = $asuransi;
        //     $modelAsuransi->nokartuasuransi = $request->post('no_asuransi');
        //     $payLoadRequest['asuransi'] = $modelAsuransi->attributes;
        // }

        // if(!empty($request->post('buatjanjipoli_id'))){
        //     $payLoadRequest['buatjanjipoli_id'] = $request->post('buatjanjipoli_id');
        //     $payLoadRequest['status_janji'] = !empty($request->post('status_janji')) ? $request->post('status_janji') : '';
        // }

        // if ($request->post('is_bbl')) {
        //     $payLoadRequest['is_bbl'] = $request->post('is_bbl');
        //     if (!$request->post('kelahiran_id')) {
        //         return (new DocoHelpers)->macroResponseJson(400, 'Mohon pilih data bayi', []);
        //     }
        //     $payLoadRequest['kelahiran_id'] = $request->post('kelahiran_id');
        //     $payLoadRequest['pendaftaran_ibu_id'] = $request->post('pendaftaran_ibu_id');
        // }
        // if ($request->post('is_ranap')) {
        //     $payLoadRequest['tipe_pasien']['no_rekam_medik'] = $request->post('TipePasienForm')['no_rekam_medik'];
        //     $payLoadRequest['is_ranap'] = $request->post('is_ranap');
        //     $payLoadRequest['kelaspelayanan_selected'] = $request->post('kelaspelayanan_selected');
        //     $payLoadRequest['is_pasientitipan'] = $request->post('pasientitipan', null);
        //     $payLoadRequest['is_aps'] = $request->post('pasienaps');
        //     $payLoadRequest['pasien_admisi'] = $request->post('PasienAdmisiForm');
        //     $payLoadRequest['pendaftaranasal_id'] = $request->post('pendaftaranasal_id');
        //     // $payLoadRequest['pasien_admisi']['is_pasientitipan'] = ((int) $payLoadRequest['is_pasientitipan'] == 1) ? true : false;
        //     $payLoadRequest['pasien_admisi']['is_aps'] = ((int) $payLoadRequest['is_aps'] == 1) ? true : false;
        //     if($payLoadRequest['pasien_admisi']['is_pasientitipan'] == '1' && empty($payLoadRequest['pasien_admisi']['kamar_titipan_id']) && empty($payLoadRequest['pasien_admisi']['tempattidur_titipan_id'])) {
        //         return DocoHelpers::response([
        //             'response' => [
        //                 'title' => 'Proses Gagal!',
        //                 'text' => 'Kamar titipan belum di pilih.'
        //             ]
        //         ], 422);
        //     }
        // }
        $payLoadRequest['allow_bpjs'] = $request->post('allow_bpjs');

        if (isset($request->post('BpjsNewForm')['no_kartu']) && $request->post('BpjsNewForm')['no_kartu'] == '') {
            unset($payLoadRequest['bpjs']);
            $payLoadRequest['allow_bpjs'] = false;
        }

        // if($isMultiPayer && !empty($request->post('MultiCarabayarForm'))) {
        //     $postMultipayer = $request->post('MultiCarabayarForm');
        //     $modelMultiPayer->attributes = $postMultipayer;
        //     $modelMultiPayer->add_no_asuransi_1 = null;
        //     $modelMultiPayer->add_no_asuransi_2 = null;
        //     $modelMultiPayer->add_nokartuasuransi_1 = ($postMultipayer['add_no_asuransi_1']) ? $postMultipayer['add_no_asuransi_1'] : null;
        //     $modelMultiPayer->add_nokartuasuransi_2 = ($postMultipayer['add_no_asuransi_2']) ? $postMultipayer['add_no_asuransi_2'] : null;
        //     $modelMultiPayer->add_namapemilikasuransi_1 = isset($postMultipayer['add_namapemilikasuransi_1']) ? $postMultipayer['add_namapemilikasuransi_1'] : null;
        //     $modelMultiPayer->add_namaperusahaan_1 = isset($postMultipayer['add_namaperusahaan_1']) ? $postMultipayer['add_namaperusahaan_1'] : null;
        //     $modelMultiPayer->add_nomorpokokperusahaan_1 = isset($postMultipayer['add_nomorpokokperusahaan_1']) ? $postMultipayer['add_nomorpokokperusahaan_1'] : null;
        //     $modelMultiPayer->add_asuransipasien_id_1 = ($request->post('multicarabayarform-add_asuransipasien_id_1')) ? $request->post('multicarabayarform-add_asuransipasien_id_1') : null;
        //     if(isset($modelMultiPayer->is_add_payer) && $modelMultiPayer->is_add_payer == true) {
        //         $modelMultiPayer->add_namapemilikasuransi_2 = isset($postMultipayer['add_namapemilikasuransi_2']) ? $postMultipayer['add_namapemilikasuransi_2'] : null;
        //         $modelMultiPayer->add_namaperusahaan_2 = isset($postMultipayer['add_namaperusahaan_2']) ? $postMultipayer['add_namaperusahaan_2'] : null;
        //         $modelMultiPayer->add_nomorpokokperusahaan_2 = isset($postMultipayer['add_nomorpokokperusahaan_2']) ? $postMultipayer['add_nomorpokokperusahaan_2'] : null;
        //         $modelMultiPayer->add_asuransipasien_id_2 = ($request->post('multicarabayarform-add_asuransipasien_id_2')) ? $request->post('multicarabayarform-add_asuransipasien_id_2') : null;
        //     }
        //     $payLoadRequest['multi_payer'] = $modelMultiPayer->attributes;
        // }

        try {
            $sent = $this->_restPendaftaran->post('reservasi-'.$params.'/save-pendaftaran', [
                    'form_params'=> $payLoadRequest
                ]
            );

            $response = json_decode($sent->getBody(), true);

            if (isset($response['metadata']['status'])) {
                $status = $response['metadata']['status'];
                if ($status == 422) {
                    $error = [];
                    if (isset($response['response']['data'])) {
                        $data = $response['response']['data'];
                        if (isset($data['kunjungan'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['kunjungan'],'KunjunganForm'));
                        }

                        if (isset($data['KunjunganForm[data]'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['KunjunganForm[data]'],'KunjunganForm'));
                        }

                        if (isset($data['tipe_pasien'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['tipe_pasien'],'TipePasienForm'));
                        }

                        if (isset($data['pasien_admisi'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['pasien_admisi'],'PasienAdmisiForm'));
                        }

                        if (isset($data['pj_pasien'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['pj_pasien'],'PjpasienForm'));
                        }

                        if (isset($data['asuransi'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['asuransi'],'AsuransiForm'));
                        }

                        if (isset($data['pasien'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['pasien'],'PasienForm'));
                        }

                        if (isset($data['bpjs'])) {
                            $error = array_merge($error, DocoHelpers::parseError($data['bpjs'],'BpjsNewForm'));
                        }

                        $response = [
                            'metadata' => [
                                'status' => 422
                            ],
                            'response' => [
                                'data' => $error
                            ]
                        ];
                    }
                }

                if ($status == 200) {
                    $is_ranap = isset($payLoadRequest['is_ranap']) ? true : false;
                    if($is_ranap) {
                        $data_dashboard['reload'] = 1;
                        $mode = Yii::$app->params->mode;
                        Yii::$app->redis->executeCommand('PUBLISH', [
                            'channel' => 'display-dashboard-kamar-'.$mode,
                            'message' => json_encode(['data' => $data_dashboard])
                        ]);
                    }
                }
            }
            return DocoHelpers::response($response);
        } catch(RequestException $e) {
            $contentGuzzle = json_decode($e->getResponse()->getBody(true));
            if (isset($contentGuzzle->metadata) && $contentGuzzle->metadata->status < 500) {
                return (new DocoHelpers)->macroResponseJson($contentGuzzle->metadata->status, $contentGuzzle->response->message, []);
            } else {
                return (new DocoHelpers)->macroResponseJson(500, 'Terjadi kesalahan pada server', []);
            }
        } catch (\Exception $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody(),null);
        }
    }

    public function actionInformasi()
    {
        $title = 'Informasi Reservasi MCU';
        $model_pasien = new PasienForm;
        $modelKunjungan = new KunjunganForm;
        $status = $this->_status; $options = $this->_options;
        try{
            $session = Yii::$app->session;
            $activeWorkspace = $session->get('active_workspace');
            $response = $this->_restPendaftaran->get('inf-reservasi-mcu/bundle-informasi');
            $body = json_decode($response->getBody(), TRUE);
            $result = $body['response'];
        } catch(\RequestException $e){
            $result = [];
        } catch(\Exception $e){
            $result = [];
        }
        return $this->render('informasi', get_defined_vars());
    }

    public function actionGetDataInformasi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPendaftaran->get('inf-reservasi-mcu/index', [
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['reservasimcu_id']);
                $setNamaPasien = $value['nama_lengkap'];
                $setNoRm = empty($value['no_rekam_medik']) ? "-" : $value['no_rekam_medik'];
                $noPendaftaran = empty($value['no_pendaftaran']) ? "-" : $value['no_pendaftaran'];

                $value['info_pasien'] = $noPendaftaran . ' <br/> ' . $setNoRm . ' <br/> ' . $setNamaPasien;
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['carabayar_penjamin'] = $value['carabayar_nama'].' / '.$value['penjamin_nama'];
                $value['tgl_reservasi'] = DocoHelpers::convDateTime($value['tgl_reservasi']);
                $value['tgl_pemeriksaan'] = DocoHelpers::convDateTime($value['tgl_pemeriksaan'], false, false);
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/informasi-reservasi-mcu.xlsx";
            $response = $this->_restPendaftaran->get('inf-reservasi-mcu/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionSetujui() 
    {
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $active_workspace = $session->get('active_workspace');
        $title     = Yii::t('fe', 'Setujui Reservasi MCU');
        $modelKunjungan = new KunjunganForm;
        $modelKunjungan->scenario = 'with_mandatory_pjawab_nourut';
        $instalasi_id = DocoConstants::INSTALASI_MCU;
        $ruangan = $data_lookup = $payLoadRequest = [];

        if ($request->post()) {
            $kunjungan = $request->post('KunjunganForm');
            $modelKunjungan->attributes = $kunjungan;
            $modelKunjungan->scenario = 'form_kunjungan_reservasi_mcu';

            $listPasienMcu = json_decode($request->post('listPasienMcu', '{}'), true);
            $reservasimcu_id = $arrListPasien = [];
            if(!empty($listPasienMcu)) {
                for($i = 0; $i < count($listPasienMcu); $i++) {
                    if(isset($listPasienMcu[$i]['reservasimcu_id'])) {
                        $reservasimcu_id[] = $listPasienMcu[$i]['reservasimcu_id'];
                    }
                }
            }

            $arrListPasien = "[" . implode(",", $reservasimcu_id) . "]";
            $modelKunjungan->list_pasien_mcu = $arrListPasien;
            $modelKunjungan->tindakan_karcis = $request->post('list_tindakan');

            $payLoadRequest['kunjungan'] = $modelKunjungan->attributes;

            if ($modelKunjungan->validate()) {
                $response = $this->_restPendaftaran->post('inf-reservasi-mcu/approval', [
                    'form_params' => $payLoadRequest
                ]);
                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response);
            }
            return DocoHelpers::response($modelKunjungan->errors,422,'KunjunganForm');
        } else {
            $dataForm = $this->getDataApi($instalasi_id, 2, null, null, null);
            $ruanganId = $active_workspace['ruangan_id'];
            $ruangan = $dataForm['ruangan'];
            $data_lookup = isset($dataForm["lookup"]) ? $dataForm["lookup"] : [];
            $is_nourut = $dataForm['konfigSystem']['is_nourut'];
            $is_limit_tagihan = $dataForm['konfigSystem']['is_limit_tagihan'];

            $response = $this->_restPendaftaran->post('inf-reservasi-mcu/get-default-ruangan', []);
            $response = json_decode($response->getBody(), true);
            $default_ruangan = $response['response']['kode_id'];

            return $this->renderAjax('_modal_approval', get_defined_vars());
        }
    }

    public function actionTolak() 
    {
        $request = Yii::$app->request;
        $payLoadRequest = [];

        $reservasimcu_id = $request->get('reservasimcu_id');
        $listPasienMcu = explode(',', $reservasimcu_id);
        $payLoadRequest['list_pasien_mcu'] = $listPasienMcu;

        $response = $this->_restPendaftaran->post('inf-reservasi-mcu/tolak', [
            'form_params' => $payLoadRequest
        ]);
        $response = json_decode($response->getBody(), true);
        return DocoHelpers::response($response);
    }
}