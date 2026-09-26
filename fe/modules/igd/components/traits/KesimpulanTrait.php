<?php
//Author: Rizal Faidin

namespace app\modules\igd\components\traits;

// Using Yii
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\data\ArrayDataProvider;

// Using Guzzles
use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

// Using components
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

// Using model
use app\modules\igd\models\PasienPulangForm;
use app\modules\igd\models\KesimpulanKeluarForm;
use app\modules\igd\models\KesimpulanPulangForm;
use app\modules\igd\models\ResepturForm;
use app\modules\igd\models\ResepturDetailForm;
use app\modules\igd\models\ResepturNrDetailForm;
use app\modules\igd\models\JenazahForm;

// Trait
trait KesimpulanTrait
{
    public function actionKesimpulan()
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($request->get('id'));
        $data_pasien = $this->_data_pasien;
        // echo "<pre>";var_dump($id);die();
        // Melapas Validasi instruksi dan cppt jangan di hapus komen ini !!!!
        // $req_diag = $this->_restIgd->get('kesimpulan/check-diagnosa?id='.$id);
        // $res_diag = json_decode($req_diag->getBody(),TRUE);
        // if($res_diag['response']['is_diagnosa_terisi'] === false){
        //     return $this->renderPartial('kesimpulan/diagnosa_kosong');
        // }
        $req = $this->_restIgd->get('kesimpulan/bundle-data-kesimpulan?id='.$id);
        $res = json_decode($req->getBody(), true);

        $resMaster = $res['response']['bundle'];
        $resListGcs = $res['response']['data_listgcs'];
        $pasienPulang = $res['response']['pasienPulang'];
        $kesimpulan = $res['response']['kesimpulan'];
        $getSuggestGcs = $res['response']['getSuggestGcs'];
        $getSuggestTtv = $res['response']['getSuggestTtv'];
        $data_obat = $res['response']['data_obat'];

        $konfig_keramat_spri =  $res['response']['konfig_keramat_spri'];

        $listJk = ArrayHelper::map($res['response']['listjk'], 'lookup_id', 'lookup_value');
        $listHubungan = ArrayHelper::map($res['response']['listhubungan'], 'lookup_id', 'lookup_value');
        $datajenazah = (isset($res['datajenazah']) && count($res['datajenazah']) > 0) ? $res['datajenazah'] : [];
        $userIdentity = Yii::$app->session->get('user_identity');
        if(isset($userIdentity['kelompokpegawai_id'])) {
            if($userIdentity['kelompokpegawai_id'] != DocoConstants::KELOMPOK_MEDIS || $kesimpulan !== null || $pasienPulang !== null){
                return $this->viewKesimpulan($id);
            }
        }

        if ($pasienPulang['carakeluar_id'] == 4){
            $is_hidden_cetak_surat_kematian = false;
        } else {
            $is_hidden_cetak = false;
        }
        if($pasienPulang === null){
            $is_hidden_cetak = true;
            $is_hidden_cetak_surat_kematian = true;
        }

        $data_carakeluar = isset($resMaster['cara_keluar']) && is_array($resMaster['cara_keluar']) ? $resMaster['cara_keluar'] : [];
        $listDataApotek = ArrayHelper::map($res['response']['listDataApotek'], 'ruangan_id', 'ruangan_nama');
        $listDataSigna = ArrayHelper::map($res['response']['listDataSigna'], 'signa_id', 'signa_nama');

        $data_kondisi_keluar = isset($resMaster['kondisi_keluar']) && is_array($resMaster['kondisi_keluar']) ? $resMaster['kondisi_keluar'] : [];
        $kondisikeluar_options = [];
        foreach ($data_kondisi_keluar as $index => $kondisikeluar) {
            $key_index = (int) $index + 1;
            $kondisikeluar_options[$key_index] = ['data-carakeluar_id'=>$kondisikeluar['carakeluar_id']];
        }
        $kondisi_keluar = ArrayHelper::map($data_kondisi_keluar, 'kondisikeluar_id', 'kondisikeluar_nama');

        // set model
        $modelPasienPulang = new PasienPulangForm;
        $modelKesimpulanKeluar = new KesimpulanKeluarForm;
        $modelKesimpulanPulang = new KesimpulanPulangForm;
        $modelReseptur = new ResepturForm;
        $modelResepturDetailRacikan = new ResepturDetailForm;
        $modelResepturDetailNonRacikan = new ResepturNrDetailForm;
        $modelJenazah = new JenazahForm;
        $modelPasienPulang->catatan_tindakan = $res['response']['catatan_tindakan'];

        $modelReseptur->pegawai_id = @$data_pasien['dokter_jaga_id'];
        $modelReseptur->tglreseptur = date('Y-m-d H:i:s');
        $val_dokter_reseptur = @$data_pasien['dokter_jaga'];
        if ($pasienPulang) {
            $modelPasienPulang->attributes = $pasienPulang;
            $modelKesimpulanPulang->pasienpulang_id = $modelPasienPulang->pasienpulang_id;
            $modelKesimpulanKeluar->pasienpulang_id = $modelPasienPulang->pasienpulang_id;
        }else{
            $modelPasienPulang->pendaftaran_id = $data_pasien['pendaftaran_id'];
            $modelPasienPulang->pasien_id = $data_pasien['pasien_id'];
            $modelPasienPulang->ruanganakhir_id = $data_pasien['ruangan_id'];
        }

        if($kesimpulan){
            $modelKesimpulanKeluar->attributes = $kesimpulan;
            $modelKesimpulanPulang->attributes = $kesimpulan;
        }
        $inisialKeluar =0;
        if(isset($modelKesimpulanKeluar->gcs_eye_id)){
            $inisialKeluar = 1;
        }
        $inisialPulang = 0;
        if(isset($modelKesimpulanPulang->instruksi_lanjutan)){
            $inisialPulang = 1;
        }

        if(!isset($modelKesimpulanPulang->pendaftaran_id)){
            $modelKesimpulanPulang->pendaftaran_id = $data_pasien['pendaftaran_id'];
        }

        if(!isset($modelKesimpulanKeluar->pendaftaran_id)){
            $modelKesimpulanKeluar->pendaftaran_id = $data_pasien['pendaftaran_id'];
        }

        // per gcs an
        $data_gcsEye = ( isset($resListGcs['eye']) && !empty($resListGcs['eye']) ) ? $resListGcs['eye'] : [];
        $data_gcsVerbal = ( isset($resListGcs['verbal']) && !empty($resListGcs['verbal']) ) ? $resListGcs['verbal'] : [];
        $data_gcsMotorik = ( isset($resListGcs['motorik']) && !empty($resListGcs['motorik']) ) ? $resListGcs['motorik'] : [];

        $gcsEyeOptions = [];
        $gcsVerbalOptions = [];
        $gcsMotorikOptions = [];
        if(count($data_gcsEye) > 0){
            foreach ($data_gcsEye as $keyEye => $valueEye) {
                $gcsEyeOptions[$valueEye['metodegcs_id']]['data-nilai'] = $valueEye['metodegcs_nilai'];
            }
        }
        if(count($data_gcsVerbal) > 0){
            foreach ($data_gcsVerbal as $keyVerbal => $valueVerbal) {
                $gcsVerbalOptions[$valueVerbal['metodegcs_id']]['data-nilai'] = $valueVerbal['metodegcs_nilai'];
            }
        }
        if(count($data_gcsMotorik) > 0){
            foreach ($data_gcsMotorik as $keyMotorik => $valueMotorik) {
                $gcsMotorikOptions[$valueMotorik['metodegcs_id']]['data-nilai'] = $valueMotorik['metodegcs_nilai'];
            }
        }

        if(!isset($modelPasienPulang->tglpasienpulang)){
            $modelPasienPulang->tglpasienpulang = date('d-m-Y H:i:s');
        }else{
            $modelPasienPulang->tglpasienpulang = date('d-m-Y H:i:s',strtotime($modelPasienPulang->tglpasienpulang));
        }

        if(isset($modelPasienPulang->tgl_meninggal)){
            $modelPasienPulang->tgl_meninggal = date('d-m-Y H:i:s',strtotime($modelPasienPulang->tgl_meninggal));
        }

        if (!empty($getSuggestGcs)) {
            $modelKesimpulanKeluar->gcs_eye_id     = $getSuggestGcs['gcseye_id'];
            $modelKesimpulanKeluar->gcs_verbal_id  = $getSuggestGcs['gcsverbal_id'];
            $modelKesimpulanKeluar->gcs_motorik_id = $getSuggestGcs['gcsmotorik_id'];
            $modelKesimpulanKeluar->hasil_gcs      = $getSuggestGcs['hasil_gcs'];
            $modelKesimpulanKeluar->is_kapitis     = $getSuggestGcs['is_kapitis'];
        }

        if (!empty($getSuggestTtv)) {
            $modelKesimpulanKeluar->hr   = $getSuggestTtv['hr'];
            $modelKesimpulanKeluar->rr   = $getSuggestTtv['rr'];
            $modelKesimpulanKeluar->spo2 = $getSuggestTtv['spo2'];
            $modelKesimpulanKeluar->t    = $getSuggestTtv['t'];
        }

        $dataInstruksiTindakan = $this->dataInstruksiTindakan($data_pasien['pendaftaran_id']);
        $getdataInstruksiTindakan = $dataInstruksiTindakan['response'];
        if( !empty($getdataInstruksiTindakan) ){
            foreach ($getdataInstruksiTindakan as $key => $value) {
                if ($value['tindakan_deleted'] == true) {
                    unset($getdataInstruksiTindakan[$key]);
                }

                /*if ($value['tindakan_deleted'] == true) {
                    $statusInstruksiTindakan = false;
                }else{
                    $statusInstruksiTindakan = true;
                }*/
            }
        }

        $statusInstruksiTindakan = !empty($getdataInstruksiTindakan) ? 1 : 0;
        // $messageInstruksiTindakan = ' - '.Yii::t('fe', 'Masih ada instruksi yang belum diimplementasikan');
        $messageInstruksiTindakan = Yii::t('fe', 'Masih ada instruksi yang belum diimplementasikan, Apakah anda yakin ?');

        return $this->renderAjax('kesimpulan/index', get_defined_vars());
    }

    public function actionSaveKesimpulan($id)
    {
        $request = Yii::$app->request;
        try{

            $post = $request->post();
            $post['PasienPulangForm']['user'] = Yii::$app->docoVars->user("nama");
            $mPasienPulang = new PasienPulangForm;
            $mPasienPulang->attributes = $post['PasienPulangForm'];
            $mPasienPulang->tgl_pendaftaran = !empty($this->_data_pasien['tgl_pendaftaran'])
                ? $this->_data_pasien['tgl_pendaftaran'] : date('Y-m-d H:i:s');

            $mPasienPulang->tglpasienpulang = ($mPasienPulang->tglpasienpulang) ? date('Y-m-d H:i:s',strtotime($mPasienPulang->tglpasienpulang)) : date('Y-m-d H:i:s') ;
            $mPasienPulang->tgl_meninggal = ($mPasienPulang->tgl_meninggal) ? date('Y-m-d H:i:s',strtotime($mPasienPulang->tgl_meninggal)) : '' ;

            //Perubahan format tanggal tgl_kremasi dan waktu_pemeriksaan_jenazah
            $mPasienPulang->tgl_kremasi = ($mPasienPulang->tgl_kremasi) ? date('Y-m-d H:i:s',strtotime($mPasienPulang->tgl_kremasi)) : '' ;
            $mPasienPulang->waktu_pemeriksaan_jenazah = ($mPasienPulang->waktu_pemeriksaan_jenazah) ? date('Y-m-d H:i:s',strtotime($mPasienPulang->waktu_pemeriksaan_jenazah)) : '' ;

            if(!$mPasienPulang->validate()){
                $formName = substr(strrchr(get_class($mPasienPulang), "\\"), 1);
                $response = $mPasienPulang->errors;
                return DocoHelpers::response($response, 422, $formName);
            }
            $mKesimpulanKeluar = new KesimpulanKeluarForm;
            if(isset($post['KesimpulanKeluarForm'])){
                $mKesimpulanKeluar->attributes = $post['KesimpulanKeluarForm'];
                if(!$mKesimpulanKeluar->validate()){
                    $formName = substr(strrchr(get_class($mKesimpulanKeluar), "\\"), 1);
                    $response = $mKesimpulanKeluar->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
            }

            $mKesimpulanPulang = new KesimpulanPulangForm;
            if(isset($post['KesimpulanPulangForm'])){
                $mKesimpulanPulang->attributes = $post['KesimpulanPulangForm'];
                if(!$mKesimpulanPulang->validate()){
                    $formName = substr(strrchr(get_class($mKesimpulanPulang), "\\"), 1);
                    $response = $mKesimpulanPulang->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
            }

            $mReseptur = new ResepturForm;
            if(isset($post['ResepturForm'])){
                $mReseptur->scenario = ResepturForm::SCENARIO_KESIMPULAN;
                $mReseptur->attributes = $post['ResepturForm'];
                if(!$mReseptur->validate()){
                    $formName = substr(strrchr(get_class($mReseptur), "\\"), 1);
                    $response = $mReseptur->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
            }

            $jenazahForm = new JenazahForm;
            if ($mPasienPulang['carakeluar_id'] == 4) {
                $cache = Yii::$app->cache;
                $jenazahForm->scenario = JenazahForm::SCENARIO_REQ;
                if(isset($post['JenazahForm'])){
                    $jenazahForm->attributes = $post['JenazahForm'];
                    if(!$jenazahForm->validate()){
                        $formName = substr(strrchr(get_class($jenazahForm), "\\"), 1);
                        $response = $jenazahForm->errors;
                        return DocoHelpers::response($response, 422, $formName);
                    }
                    $cacheObat = $cache->get($id.'-'.'obat');
                    $cacheTindakan = $cache->get($id.'-'.'tindakan');
                    if(count($cacheTindakan) > 0){
                        foreach ($cacheTindakan as $key => $value) {
                            $cacheTindakan[$key]['additional_data'] = json_decode($cacheTindakan[$key]['additional_data'], true);
                        }
                    }
                    $cacheLinen = $cache->get($id.'-'.'linen');
                    $cacheAlat = $cache->get($id.'-'.'alat');
                    $post['JenazahForm']['list_order'] = json_encode([
                        'tindakan' => $cacheTindakan,
                        'obat' => $cacheObat
                    ]);
                    $post['JenazahForm']['list_linen'] = json_encode([
                        'alat' => $cacheAlat,
                        'linen' => $cacheLinen,
                    ]);
                    $post['JenazahForm']['pegawai'] = $this->_pegawai_id;
                }
            }
            $request = $this->_restIgd->post('kesimpulan/save-kesimpulan', [
                'form_params' => $post
            ]);
            $response = json_decode($request->getBody(), true);
            Yii::$app->cache->delete('data-pasien-igd-' . $this->_pendaftaran_id);
            return DocoHelpers::response($response['response']);

        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    public function actionCetakPdfKesimpulan($id)
    {
        $path = Yii::getAlias("@download") . "/kesimpulan.pdf";
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $nama_usercetak = Yii::$app->session->get('user_identity')['nama'];
        $id_usercetak = Yii::$app->session->get('user_identity')['id_pegawai'];
        try {
            $request = $this->_restIgd->get('kesimpulan/cetak-pdf-kesimpulan', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'ruangan_id' => $this->_ruangan_id,
                    'pegawai_id' => $this->_pegawai_id,
                    'kelompokpegawai_id' => $this->_user_identity['kelompokpegawai_id'],
                    'nama_usercetak' => $nama_usercetak,
                    'id_usercetak' => $id_usercetak
                ],
                'save_to' => $path,
            ]);
            $body = json_decode($request->getBody(), true);
            // Download pdf
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakPdfSuratKematian($id)
    {
        $path = Yii::getAlias("@download") . "/kesimpulan.pdf";
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $nama_usercetak = Yii::$app->session->get('user_identity')['nama'];
        $id_usercetak = Yii::$app->session->get('user_identity')['id_pegawai'];
        try {
            $request = $this->_restIgd->get('kesimpulan/cetak-pdf-surat-kematian', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'ruangan_id' => $this->_ruangan_id,
                ],
                'save_to' => $path,
            ]);
            $body = json_decode($request->getBody(), true);
            // Download pdf
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }


    public function actionCetakSpri($id,$type)
    {
        $path = Yii::getAlias("@download") . "/SPRI.pdf";
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $nama_usercetak = Yii::$app->session->get('user_identity')['nama'];
        $id_usercetak = Yii::$app->session->get('user_identity')['id_pegawai'];
        try {
            // Report Designer
            if(Yii::$app->report->enabled){
                return Yii::$app->report->exec('cetak-spri?pendaftaran_id='.$pendaftaran_id.'&type='.$type);
            }

            $request = $this->_restIgd->get('kesimpulan/cetak-spri', [
                'query' => [
                    'type' => $type,
                    'pendaftaran_id' => $pendaftaran_id,
                    'ruangan_id' => $this->_ruangan_id,
                    'pegawai_id' => $this->_pegawai_id,
                    'kelompokpegawai_id' => $this->_user_identity['kelompokpegawai_id'],
                    'nama_usercetak' => $nama_usercetak,
                    'id_usercetak' => $id_usercetak
                ],
                'save_to' => $path,
            ]);
            $body = json_decode($request->getBody(), true);
            // Download pdf
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function viewKesimpulan($id)
    {
        $infoKesimpulan = $infoJenazah = null;
        try{
            $req = $this->_restIgd->get('kesimpulan/view-kesimpulan?id='.$id);
            $res = json_decode($req->getBody(), true);
            $infoKesimpulan = $res['response']['kesimpulan'];
            $infoResep = isset($infoKesimpulan['info_resep'])?$infoKesimpulan['info_resep']:null;
            $infoDetailResep = is_array($infoKesimpulan['detail_resep'])?$infoKesimpulan['detail_resep']:[];
            $infoJenazah = (count($res['response']['datajenazah']) > 0) ? $res['response']['datajenazah'] : null;
            $obat_pulang = isset($res['response']['obat_pulang'])?$res['response']['obat_pulang']:null;
        } catch (RequestException $e) {
            $infoKesimpulan = $infoJenazah = null;
        } catch (\Exception $e) {
            $infoKesimpulan = $infoJenazah = null;
        }
        $iter = '';
        foreach ($infoDetailResep as $key => $value) {
            $iter = $value['iter'];
        }
        $iter = ($iter != '') ? $iter : '-';
        $encryptedId = DocoHelpers::encrypt($id);
        return $this->renderAjax('kesimpulan/view_only',[
                                'infoKesimpulan'=>$infoKesimpulan,
                                'encryptedId'=>$encryptedId,
                                'infoResep'=>$infoResep,
                                'infoDetailResep'=>$infoDetailResep,
                                'iter'=>$iter,
                                'datajenazah' => $infoJenazah,
                                'obat_pulang' => $obat_pulang
                            ]);
    }

    public function actionKesimpulanValidasiRacikan()
    {
        $post = Yii::$app->request->post();
        $data = [];
        if(isset($post['ResepturDetailForm']) && is_array($post['ResepturDetailForm'])){
            foreach ($post['ResepturDetailForm'] as $key_racikan => $reseptur_racikan) {
                $modelResepturDetailRacikan = new ResepturDetailForm;
                $modelResepturDetailRacikan->scenario = ResepturDetailForm::SCENARIO_ADD_RACIKAN;
                $modelResepturDetailRacikan->attributes = $reseptur_racikan;
                if(!$modelResepturDetailRacikan->validate()){
                    $formName = substr(strrchr(get_class($modelResepturDetailRacikan), "\\"), 1);
                    $response = $modelResepturDetailRacikan->errors;
                    return DocoHelpers::response($response, 422, $formName.'['.$key_racikan.']');
                }
                $reseptur_racikan['rke'] = $post['rke'];
                $data[$key_racikan] = $reseptur_racikan;
            }
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['response'=>['data'=>$data]];
    }
    public function actionGetCache($cachetype, $id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        try {
            $listdata = [];
            $cacheName = $id.'-'.$cachetype;
            $getCache = $cache->get($cacheName);
            if($getCache){
                $listdata = $getCache;
            }
            if(!$listdata){
                $return = [
                    'data' => [],
                    'draw' => $request->post('draw'),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0
                ];
                return DocoHelpers::response($return);
            }
            $data_tables = [];
            $no = 1;
            foreach ($listdata as $key => $value) {
                if(!$value['is_deleted']){
                    $value['rownum'] = $no;
                    $value['aksi'] = Html::button(
                        '<i class="fa fa-times"></i>',
                        [
                            'class' => 'btn btn-danger btn-xs delete-data',
                            'action' => '/igd/pemeriksaan-igd/delete-data?id='.$id.'&key='. $key.'&type='.$cachetype,
                            'data-confirm-message' => Yii::t('fe', 'confirm_batal'),
                            'data-type' => $cachetype
                        ]
                    );
                    if(isset($value['harga'])){
                        $value['harga'] = DocoHelpers::formatNumber($value['harga']);
                    }

                    array_push($data_tables, $value);
                    $no++;
                }
            }

            $return = [
                'data' => $data_tables,
                'draw' => $request->post('draw'),
                'recordsTotal' => count($data_tables),
                'recordsFiltered' => count($data_tables)
            ];
            return DocoHelpers::response($return);
        } catch (Exception $e) {
            $return = [
                    'data' => [],
                    'draw' => $request->post('draw'),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0
                ];
            return DocoHelpers::response($return);
        } catch(\RequestException $e){
            var_dump($e->getMessage()); die();
        }
    }
    public function actionGetLinen()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            if(isset($get['q']['term']) && !empty($get['q']['term'])){
                $response = $this->_restIgd->request('POST', 'kesimpulan/get-linen',[
                                'form_params'=>['term'=>$get['q']['term']],
                            ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id'=>$value['barang_id'],'text'=>$value['barang_nama']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        } catch (\Exception $e) {
            return [];
        } catch (\RequestException $e){
            return [];
        }
    }
    public function actionSaveCache($id, $type)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $cache = Yii::$app->cache;
        try {
            $listdata = [];
            $cacheName = $id.'-'.$type;
            $getCache = $cache->get($cacheName);
            if($getCache){
                $listdata = $getCache;
            }
            $post['is_deleted'] = false;
            $keychange = null;
            foreach ($listdata as $key => $value) {
                if($type == 'tindakan'){
                    if($value['daftartindakan_id'] == $post['daftartindakan_id']){
                        $keychange = $key;
                    }
                }
                if($type == 'obat'){
                    if($value['obatalkes_id'] == $post['obatalkes_id']){
                        $keychange = $key;
                    }
                }
                if($type == 'linen' && ($value['barang_id'] == $post['barang_id'])){
                    $keychange = $key;
                }
                if($type == 'alat' && ($value['obatalkes_id'] == $post['obatalkes_id'])){
                    $keychange = $key;
                }
            }
            if($keychange === null){
                $listdata[] = $post;
            }else{
                if($type == 'tindakan'){
                    $listdata[$key]['qty'] += $post['qty'];
                    $listdata[$key]['harga'] = $listdata[$key]['qty'] * $post['harga_tariftindakan'];
                    $listdata[$key]['qty_tindakan'] = $listdata[$key]['qty'];
                }
                if($type == 'obat'){
                    $listdata[$key]['qty'] += $post['qty'];
                    if($listdata[$key]['qty'] > $listdata[$key]['qty_tersedia']){
                        return DocoHelpers::responseTemplate(
                            422,
                            'Error',
                            [],
                            [
                                'title' => Yii::t('fe', 'Terjadi Kesalahan'),
                                'text' => 'Qty yang dipesan tidak boleh melebihi stok tersedia',
                                'message' => 'Qty yang dipesan tidak boleh melebihi stok tersedia',
                            ]
                        );
                    }
                    $listdata[$key]['harga'] = $listdata[$key]['qty'] * $post['hargajual'];
                }
                if($type == 'linen' || $type == 'alat'){
                    $listdata[$key]['qty'] += $post['qty'];
                }
            }
            $cache->set($cacheName, $listdata);
            return DocoHelpers::response(['title' => 'Berhasil', 'message' => 'Data Berhasil Disimpan']);
        } catch (Exception $e) {
            return DocoHelpers::response($e->getMessage(), 500);
        }
    }
    public function actionResetCache($id, $type){
        $cache = Yii::$app->cache;
        $cacheName = $id.'-'.$type;
        try {
            if($type == 'all'){
                $cache->set($id.'-linen', []);
                $cache->set($id.'-obat', []);
                $cache->set($id.'-tindakan', []);
                $cache->set($id.'-alat', []);
            }else{
                $cache->set($cacheName, []);
            }
            return true;
        } catch (Exception $e) {
            return DocoHelpers::response($e->getMessage(), 500);
        }
    }
    public function actionGetAlat()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            if(isset($get['q']['term']) && !empty($get['q']['term'])){
                $response = $this->_restIgd->request('POST', 'kesimpulan/get-alat',[
                                'form_params'=>['term'=>$get['q']['term']],
                            ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id'=>$value['obatalkes_id'],'text'=>$value['obatalkes_nama']];
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
                return DocoHelpers::response($return);
            }
        } catch (\Exception $e) {
            return [];
        } catch (\RequestException $e){
            return [];
        }
    }
    public function actionGetObatAlkes()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            if(isset($get['q']['term']) && !empty($get['q']['term'])){
                $response = $this->_restIgd->request('POST', 'kesimpulan/get-obat-alkes',[
                                'form_params'=>[
                                    'term'=>$get['q']['term'],
                                    'ruangan_id' => 38
                                ],
                            ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                $dataobat = [];
                foreach ($body['response'] as $key => $value) {
                    $data[] = ['id'=>$value['obatalkes_id'],'text'=>$value['obatalkes_nama'] .' - '.$value['qty_tersedia']];
                    $dataobat[$value['obatalkes_id']] = $value;
                }
                $total = count($body['response']);
                $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false, 'obatalkes_data' => $dataobat];
                return DocoHelpers::response($return);
            }
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        } catch (\RequestException $e){
            return json_encode(['message' => $e->getMessage()]);
        }
    }
    public function actionGetTarifRs()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            if(isset($get['q']['term']) && !empty($get['q']['term'])){
                $response = $this->_restIgd->request('POST', 'kesimpulan/get-tarif-rs',[
                                'form_params'=>[
                                    'term'=>$get['q']['term'],
                                ],
                                'query' => [
                                    'ruangan_id' => 38,
                                    'kelaspelayanan_id' => $this->_data_pasien['kelaspelayanan_id'],
                                    'penjamin_id' =>  $this->_data_pasien['penjamin_id'],
                                ]
                            ]);
                $body = json_decode($response->getBody(), true);
                $data = [];
                $datatindakan = $datakomponen = [];
                foreach ($body['response'] as $key => $value) {
                    $value['dokterpenanggungjawab_id'] = $this->_data_pasien['dokter_id'];
                    if($value['komponentarif_id'] != 6){
                        $value['tarif_kompsatuan'] = $value['tarif_satuan'];
                        $value['tarif_tindakankomp'] = $value['tarif_tindakan'];
                        $value['tarifcyto_tindakankomp'] = $value['tarifcyto_tindakan'];
                        $value['subsidiasuransikomp'] = 0;
                        $value['subsidipemerintahkomp'] = 0;
                        $value['subsidirumahsakitkomp'] = 0;
                        $value['iurbiayakomp'] = 0;
                        $datakomponen[$value['daftartindakan_id']][] = $value;
                    }else{
                        $datatindakan[$value['daftartindakan_id']] = $value;
                    }
                }
                foreach ($datatindakan as $key => $value) {
                    $data[] = ['id' => $value['daftartindakan_id'], 'text' => $value['daftartindakan_nama']];
                    $value['additional_data']['list_komponen'] = $datakomponen[$value['daftartindakan_id']];
                    $datatindakan[$key] = $value;
                }
                $total = count($body['response']);
                $return = [
                    'result'=>$data,
                    'total_count'=>$total,
                    'incomplete_results'=>false,
                    'tindakandata' => $datatindakan
                ];
                return DocoHelpers::response($return);
            }
        } catch (\Exception $e) {
            return json_encode(['message' => $e->getMessage()]);
        } catch (\RequestException $e){
            return json_encode(['message' => $e->getMessage()]);
        }
    }
    public function actionDeleteData(){
        $request = Yii::$app->request;
        $get = $request->get();
        $id = $get['id'];
        $type = $get['type'];
        $key = $get['key'];
        $cache = Yii::$app->cache;
        $cacheName = $id.'-'.$type;
        try {
            $getCache = $cache->get($cacheName);
            if(isset($getCache[$key])){
                $listdata = $getCache;
                unset($listdata[$key]);
                $cache->set($cacheName, $listdata);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
