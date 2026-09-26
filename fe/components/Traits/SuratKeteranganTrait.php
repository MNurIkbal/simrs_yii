<?php

namespace app\components\Traits;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use Doco\components\DocoMessages;
use app\components\Traits\Form\SuratKeteranganPasienForm;
use app\components\Pelayanan\PelayananHelpers;

trait SuratKeteranganTrait
{
    /**
     * @var String $type
     * @author ilham.pramono@sirs.co.id
     */
    public $type;
    public $urlBackendMaster;
    public $urlFrontend;
    public $urlBackend;
    public $modul;
    public $serviceRest;
    public $serviceRestMaster;

    private function setServicePath()
    {
        $urlFrontend = null;
        $urlBackend = null;
        $urlBackendMaster = 'allow';
        $serviceRestMaster = Yii::$app->docoRest->master;
        $modul = null;
        $serviceRest = null;
        switch ($this->type) {
            case 'RJ':
                $urlFrontend = 'pemeriksaan';
                $urlBackend = 'tra-pemeriksaan';
                $modul = 'rajal';
                $serviceRest = Yii::$app->docoRest->rajal;
                break;
            case 'RI':
                $urlFrontend = 'pemeriksaan-rawat-inap';
                $urlBackend = 'pemeriksaan-rawat-inap';
                $modul = 'ranap';
                $serviceRest = Yii::$app->docoRest->ranap;
                break;
            case 'RD':
                $urlFrontend = 'pemeriksaan-igd';
                $urlBackend = 'pemeriksaan-igd';
                $modul = 'igd';
                $serviceRest = Yii::$app->docoRest->igd;
                break;
            default:
                break;
        }
        $this->urlBackendMaster = $urlBackendMaster;
        $this->serviceRestMaster = $serviceRestMaster;
        $this->serviceRest = $serviceRest;
        $this->urlFrontend = $urlFrontend;
        $this->urlBackend = $urlBackend;
        $this->modul = $modul;
    }

    public function actionSuratKeterangan() {
        $this->setServicePath();
        $url = $this->urlFrontend;
        $modul = $this->modul;
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id');
        return $this->renderAjax('//surat-keterangan/list_surat_keterangan', compact('pendaftaran_id', 'url', 'modul'));
    }

    public function actionGetMasterSurat() {
        $this->setServicePath();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $pendaftaran_id = DocoHelpers::decrypt($request->get('pendaftaran_id'));
        $payload['pendaftaran_id'] = $pendaftaran_id;
        $masterSurat = $this->guzzleExec($this->serviceRest, [
            'url' => $this->urlBackend . '/get-master-surat',
            'payload' => [
                'query' => $payload,
            ]
        ]);

        foreach (ArrayHelper::getValue($masterSurat, 'data') as $key => $value) {
            $disabled = '';
            if(empty($value['pendaftaran_id'])) {
                $disabled = 'disabled';
            }

            $checked = '';
            $isEklaim = is_null($value['is_eklaim_skpt']) ? false : $value['is_eklaim_skpt'];
            if ($isEklaim) {
                $checked = 'checked';
            }
            
            $masterSurat['data'][$key]['aksi'] = "
                <button 
                    type='button' 
                    style='margin-right: 5px' 
                    id='surat-keterangan-". $value['surat_keterangan_id'] ."'
                    data-href='/".$this->modul."/".$this->urlFrontend."/edit-surat-keterangan?pendaftaran_id=#pendaftaran_id#&konsulpoli_id=#konsulpoli_id#&surat_keterangan_id=".$value['surat_keterangan_id']."' 
                    data-is-eklaim='".($isEklaim?"1":"0")."' 
                    class='btn btn-labeled btn-info btn-xs btn-edit-surat' 
                    data-width='90%' 
                    data-wrapper='#modal-surat-keterangan .modal-content'>
                    <b><i class='fa fa-pencil'></i></b> Edit
                </button>" . "
                <button 
                    type='button' 
                    style='margin-right: 5px' 
                    data-target='/reports/viewer/".$value['code_report']."?pendaftaran_id=#pendaftaran_id#&surat_keterangan_id=".$value['surat_keterangan_id']."'
                    class='btn btn-labeled btn-info btn-xs btn-print-surat' ".$disabled.">
                    <b><i class='fa fa-print'></i></b> Cetak
                </button>
                <input 
                    type='checkbox' 
                    class='btn-checklist-dokumen'
                    data-surat-keterangan-id='".$value['surat_keterangan_id']."'
                    " . $checked . "
                    " . $disabled ."
                    > Dokumen Eklaim
                </input>
                ";
        }

        $masterSurat['recordsTotal'] = $masterSurat['_meta']['totalCount'];
        $masterSurat['recordsFiltered'] = $masterSurat['_meta']['totalCount'];
        
        return $masterSurat;
    }

    public function actionEditSuratKeterangan() {
        try {
            $this->setServicePath();
            $url = $this->urlFrontend;
            $modul = $this->modul;

            $suratCustom = [14, 19];
            $request = Yii::$app->request;
            $pendaftaran_id = DocoHelpers::decrypt($request->get('pendaftaran_id'));
            $konsulpoli_id = PelayananHelpers::coalesce([$request->get('konsulpoliId'), $request->get('konsulpoli_id')], '');
            $surat_keterangan_id = $request->get('surat_keterangan_id');
            $is_eklaim = $request->get('is_eklaim');
            $model = new SuratKeteranganPasienForm;
            $templateData = $this->guzzleExec($this->serviceRest, [
                'url' => $this->urlBackend . '/get-template-surat',
                'payload' => [
                    'query' => [
                        'pendaftaran_id' => $pendaftaran_id,
                        'surat_keterangan_id' => $surat_keterangan_id
                    ]
                ]
            ]);

            $data = ArrayHelper::getValue($templateData, 'data');
            $content = ArrayHelper::getValue($templateData, 'content');
            $model->attributes = isset($data) ? $data : null;
            $model->is_eklaim = $is_eklaim;
            if(empty($data)) {
                $model->pendaftaran_id = $pendaftaran_id;
                $model->surat_keterangan_id = $surat_keterangan_id;
                $model->attributes = $this->guzzleExec($this->serviceRest, [
                    'url' => $this->urlBackend . '/get-data-pasien',
                    'payload' => [
                        'query' => [
                            'pendaftaran_id' => $pendaftaran_id
                        ]
                    ]
                ]);
            }

            if(in_array($surat_keterangan_id, $suratCustom)) {

                $lastInputData = json_decode($model->additional_data, true);
                $lastAnamnesaCreateDate = ArrayHelper::getValue($lastInputData, 'anamnesa_created_date');
                $lastDiagnosaCreate = ArrayHelper::getValue($lastInputData, 'diagnosa_created_date');
                $lastJumlahTerapi = ArrayHelper::getValue($lastInputData, 'jumlah_terapi');
                $lastJumlahTindakan = ArrayHelper::getValue($lastInputData, 'jumlah_tindakan');

                $newCustomData = $this->guzzleExec($this->serviceRest, [
                'url' => $this->urlBackend . '/get-custom-data-surat-keterangan',
                    'payload' => [
                        'query' => [
                            'pendaftaran_id' => $pendaftaran_id,
                            'surat_keterangan_id' => $surat_keterangan_id,
                            'modul' => $modul
                        ]
                    ]
                ]);
                
                $asesmen_medis = ArrayHelper::getValue($newCustomData, 'data.asesmen_medis');
                $terapi = ArrayHelper::getValue($newCustomData, 'data.terapi.text');
                $jumlahTerapi = ArrayHelper::getValue($newCustomData, 'data.terapi.jumlah_tindakan');
                $listingTerapi = ArrayHelper::getValue($newCustomData, 'data.terapi.listing');
                $jumlahTindakan = ArrayHelper::getValue($newCustomData, 'data.tindakan.jumlah_tindakan');
                $listingTindakan = ArrayHelper::getValue($newCustomData, 'data.tindakan.listing');
                $tindakan = ArrayHelper::getValue($newCustomData, 'data.tindakan.text');
                $diagnosa = ArrayHelper::getValue($newCustomData, 'data.diagnosa.diagnosa_utama');
                $diagnosaCreatedDate = ArrayHelper::getValue($newCustomData, 'data.diagnosa.diagnosa_created_date');
                $anamnesa = ArrayHelper::getValue($asesmen_medis, 'keadaan_umum');
                $anamnesaCreateDate = ArrayHelper::getValue($asesmen_medis, 'created_date');
                $anamnesaLastUpdate = ArrayHelper::getValue($asesmen_medis, 'last_modified_date');
                $pemeriksaanAll = '';
                // Check data apabila sudah di edit 
                if(! empty($anamnesaLastUpdate)) {
                    $compareDateAnamnesa = $anamnesaLastUpdate;
                } else {
                    $compareDateAnamnesa = $anamnesaCreateDate;
                }
                
                // Compare data 
                if($compareDateAnamnesa > $lastAnamnesaCreateDate) {
                    $newAnamnesaCreateDate = $compareDateAnamnesa;
                    $pemeriksaanAll .= 'Suhu tubuh: '.  ArrayHelper::getValue($asesmen_medis, 'suhu_tubuh'). "\n";
                    $pemeriksaanAll .= 'Tekanan Darah: '.  ArrayHelper::getValue($asesmen_medis, 'tekanan_darah'). "\n";
                    $pemeriksaanAll .= 'Denyut nadi: '.  ArrayHelper::getValue($asesmen_medis, 'nadi'). '/Menit'. "\n";
                    $pemeriksaanAll .= 'Pernapasan: '.  ArrayHelper::getValue($asesmen_medis, 'pernapasan'). '/Menit'. "\n";
                } else {
                    $anamnesa = ArrayHelper::getValue($lastInputData, 'anamnesa');
                    $newAnamnesaCreateDate = $lastAnamnesaCreateDate;
                    $pemeriksaanAll .= 'Suhu tubuh: '.  ArrayHelper::getValue($lastInputData, 'suhu'). "\n";
                    $pemeriksaanAll .= 'Tekanan Darah: '.  ArrayHelper::getValue($lastInputData, 'tekanan_darah'). "\n";
                    $pemeriksaanAll .= 'Denyut nadi: '.  ArrayHelper::getValue($lastInputData, 'nadi'). '/Menit'. "\n";
                    $pemeriksaanAll .= 'Pernapasan: '.  ArrayHelper::getValue($lastInputData, 'pernapasan'). '/Menit'. "\n";
                }
                
                // Check new data tindakan
                if($lastJumlahTindakan != $jumlahTindakan) {
                    $newTindakan = $tindakan;
                    $newJumlahTindakan = $jumlahTindakan;
                    $newListingTindakan = $listingTindakan;
                    $pemeriksaanAll .= $tindakan;
                } else {
                    $newTindakan = ArrayHelper::getValue($lastInputData, 'tindakan');
                    $newJumlahTindakan = ArrayHelper::getValue($lastInputData, 'jumlah_tindakan');
                    $newListingTindakan = ArrayHelper::getValue($lastInputData, 'listing_tindakan');;
                    $pemeriksaanAll .= $newTindakan;
                }

                // Check new data terapitext
                if($lastJumlahTerapi != $jumlahTerapi) {
                    $newTerapi = $terapi;
                    $newJumlahTerapi = $jumlahTerapi;
                    $newListingTerapi = $listingTerapi;
                    $pemeriksaanAll .= $terapi;
                } else {
                    $newTerapi = ArrayHelper::getValue($lastInputData, 'terapi');
                    $newJumlahTerapi = ArrayHelper::getValue($lastInputData, 'jumlah_terapi');
                    $newListingTerapi = ArrayHelper::getValue($lastInputData, 'listing_terapi');
                    $pemeriksaanAll .= $newTerapi;
                }

                // Check new data diagnosa
                if($diagnosaCreatedDate > $lastDiagnosaCreate) {
                    $newDiagnosa = ArrayHelper::getValue($diagnosa, 'text');
                    $newDiagnosaCreate = $diagnosaCreatedDate;
                } else {
                    $newDiagnosa = ArrayHelper::getValue($lastInputData, 'diagnosa_sementara');
                    $newDiagnosaCreate = $lastDiagnosaCreate;
                }  

                $model->additional_data = json_encode([
                    "ts" => ArrayHelper::getValue($lastInputData, 'ts'),
                    "rs" => ArrayHelper::getValue($lastInputData, 'rs'),
                    "faskes" => ArrayHelper::getValue($lastInputData, 'faskes'),
                    "pemeriksaan_all" => $pemeriksaanAll,
                    "pemeriksaan_all_listing" => str_replace("\n", ", ", $pemeriksaanAll),
                    "anjuran" => ArrayHelper::getValue($lastInputData, 'anjuran'),
                    "lokasi" => ArrayHelper::getValue($lastInputData, 'lokasi'),
                    "anamnesa" => $anamnesa,    
                    "anamnesa_created_date" => $newAnamnesaCreateDate,
                    "suhu" => $compareDateAnamnesa > $lastAnamnesaCreateDate ? ArrayHelper::getValue($asesmen_medis, 'suhu_tubuh') : ArrayHelper::getValue($lastInputData, 'suhu'),   
                    "tekanan_darah" => $compareDateAnamnesa > $lastAnamnesaCreateDate ? ArrayHelper::getValue($asesmen_medis, 'tekanan_darah') : ArrayHelper::getValue($lastInputData, 'tekanan_darah'),
                    "nadi" => $compareDateAnamnesa > $lastAnamnesaCreateDate ? ArrayHelper::getValue($asesmen_medis, 'nadi') : ArrayHelper::getValue($lastInputData, 'nadi'),
                    "pernapasan" => $compareDateAnamnesa > $lastAnamnesaCreateDate ? ArrayHelper::getValue($asesmen_medis, 'pernapasan') : ArrayHelper::getValue($lastInputData, 'pernapasan'),
                    "terapi" => $newTerapi,
                    "jumlah_terapi" => $newJumlahTerapi,
                    "listing_terapi" => $newListingTerapi,
                    "tindakan" => $newTindakan,
                    "jumlah_tindakan" => $newJumlahTindakan,
                    "listing_tindakan" => $newListingTindakan,
                    "diagnosa_sementara" => $newDiagnosa,
                    "diagnosa_created_date" => $newDiagnosaCreate
                ]);   
            }

            $model->additional_data = is_null($model->additional_data) ? "{}" : $model->additional_data;
            $model->no_surat = ArrayHelper::getValue($templateData, 'no_surat');

            $dataPasien = [];
            $dokterDpjpId = null;
            $namaDokterDpjp = "";
            if($this->type == 'RJ') {
                $cache_pendaftaran_key = implode('-', array_filter(['pasien-pendaftaran-id', DocoHelpers::encrypt($pendaftaran_id), $konsulpoli_id])); // concat string to pasien-pendaftaran-id-$pendaId-$konsulpoli_id
                $dataPasien = Yii::$app->cache->get($cache_pendaftaran_key);
                $dokterDpjpId = $dataPasien['pegawai_id'];
                $namaDokterDpjp = $dataPasien['nama_pegawai'];
            } else if($this->type == 'RI') {
                $dataPasien = Yii::$app->cache->get('pasien-pendaftaran-id-' . DocoHelpers::encrypt($pendaftaran_id));
                $dokterDpjpId = isset($dataPasien['admisi_dokter_id']) ? $dataPasien['admisi_dokter_id'] : $dataPasien['dokter_admisi_id'];
                $namaDokterDpjp = isset($dataPasien['admisi_dokter']) ? $dataPasien['admisi_dokter'] : $dataPasien['dokter_admisi'];
            } else if($this->type == 'RD') {
                $dataPasien = Yii::$app->cache->get('data-pasien-igd-' . $pendaftaran_id);
                $dokterDpjpId = $dataPasien['dokter_jaga_id'];
                $namaDokterDpjp = $dataPasien['dokter_jaga'];
            }

            $user = Yii::$app->session->get('user_identity');
            $isDokter = $user['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS;
            $model->nama_pegawai = "";
            $model->pegawai_id = Yii::$app->docoVars->user('id_pegawai');
            $dokter = [];
            $dokter = $this->guzzleExec($this->serviceRest, [
                'url' => $this->urlBackend . '/get-dokter-by-id',
                'payload' => [
                    'query' => [
                        'pegawai_id' => $isDokter ? Yii::$app->docoVars->user('id_pegawai') : $dokterDpjpId
                    ]
                ]
            ]);

            $model->no_rekam_medik = isset($dataPasien['no_rekam_medik']) ? $dataPasien['no_rekam_medik'] : '-';
            $model->nama_pegawai = isset($data['nama_pegawai']) ? $data['nama_pegawai'] : (isset($dokter['nama_pegawai']) ? $dokter['nama_pegawai'] : '-');
            $model->nip_pegawai = isset($data['nip_pegawai']) ? $data['nip_pegawai'] : (isset($dokter['nip_pegawai']) ? $dokter['nip_pegawai'] : '-');
            $model->jabatan_pegawai = isset($data['jabatan_pegawai']) ? $data['jabatan_pegawai'] : (isset($dokter['jabatan_pegawai']) ? $dokter['jabatan_pegawai'] : '-');

            if(!$isDokter) { // jika pegawai login bukan dokter, maka set dokter DPJP
                $model->nama_pegawai = $namaDokterDpjp;
                $model->pegawai_id = $dokterDpjpId;
            }

            if(!empty($data['nama_pegawai'])) {
                $model->nama_pegawai = $data['nama_pegawai'];
            }

            if($content['enable_form_pegawai']) {
                $model->pegawai_id = null;
            }
            $model->nama_pegawai = !empty($data['nama_pegawai']) ? $data['nama_pegawai'] : $model->nama_pegawai;
            return $this->renderAjax('//surat-keterangan/_edit', compact(
                'data', 
                'content', 
                'pendaftaran_id', 
                'model',
                'modul',
                'url',
                'dataPasien',
                'pegawaiLogin',
                'isDokter'
            ));
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionPrintSuratKeterangan() {
        $request = Yii::$app->request;

        $path = Yii::getAlias("@download") . "/surat-keterangan-pemeriksaan.pdf";
        $urlReport = 'surat-keterangan-pemeriksaan';
        $pendaftaran_id = DocoHelpers::decrypt($request->get('pendaftaran_id'));
        $post = [
            'pendaftaran_id' => $pendaftaran_id,
            'surat_keterangan_id' => $request->get('surat_keterangan_id')
        ];
        
        if(Yii::$app->report->isAvailable($urlReport)){
            return Yii::$app->report->exec($urlReport,[
                'queryParameter' => $post,
                'manualRender' => function() use($post,$path){
                    $this->controller->guzzleExec($this->_restRajal, [
                        'url' => 'rajal/print-surat-keterangan',
                        'method' => 'get',
                        'payload' => [
                            'save_to' => $path,
                            'query' => $post
                        ]
                    ]);

                    return DocoHelpers::previewPdf($path);
                }
            ]);
        }
    }

    public function actionSaveEditSuratKeterangan() {
        $this->setServicePath();
        try {
            $model = new SuratKeteranganPasienForm;
            $request = Yii::$app->request;
            $suratKeteranganForm = $request->post('SuratKeteranganPasienForm');
            $model->attributes = $suratKeteranganForm;
            $model->tgl_lahir = isset($suratKeteranganForm['tgl_lahir']) ? date('Y-m-d', strtotime($suratKeteranganForm['tgl_lahir'])) : null;
            if ($model->validate()) {
                return $this->guzzleExec($this->serviceRest, [
                    'url' => $this->urlBackend . '/save-surat-keterangan',
                    'method' => 'POST',
                    'payload' => [
                        'form_params' => $model->attributes,
                    ],
                    'returnResponse' => true
                ]);
            } else {
                $errors = DocoHelpers::parseError($model->errors, 'SuratKeteranganPasienForm');
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionAllPegawaiList($kelompok)
    {
        $this->setServicePath();
        $pegawaiList = $this->guzzleExec($this->serviceRestMaster, [
            'url' => $this->urlBackendMaster . '/all-pegawai-list',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'payload' => Yii::$app->request->get('payload', []),
                    'kelompok' => $kelompok
                ]
            ],
            'returnResponse' => true
        ]);

        $payload = Yii::$app->request->get('payload', []);
        if ($payload['page'] == 1 && !empty($pegawaiList['data'])) {
            $data = $pegawaiList['data'];
            $allData = [
                'id' => '%',
                'text' => \Yii::t('fe', 'Lihat Semua')
            ];
            array_unshift($data, $allData);
            $pegawaiList['data'] = $data;
        }

        return $pegawaiList;
    }

    public function actionUpdateStatusEklaim()
    {
        try {
            $this->setServicePath();
            $request = Yii::$app->request;
            
            return $this->guzzleExec($this->serviceRest, [
                'url' => $this->urlBackend . '/update-status-eklaim',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'pendaftaran_id' => DocoHelpers::decrypt($request->get("pendaftaran_id")),
                        'surat_keterangan_id' => $request->get("surat_keterangan_id"),
                        'status_eklaim' => filter_var($request->get("status_eklaim"), FILTER_VALIDATE_BOOLEAN),
                    ]
                ],
                'returnResponse' => true
            ]);
        } catch (RequestException $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            $this->logError($e);
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }
}