<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-10 15:56:01
 */

// Namespace
namespace app\modules\ranap\components\traits;

use Yii;
use app\components\DocoController;
use app\components\DocoConstants;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use app\modules\ranap\models\AsesmenMedisForm;
use app\components\Services\AksesFormService;

trait PemeriksaanAsesmenMedisTrait
{
    // Action index
    public function actionAsesmenMedis($id){
        $model = new AsesmenMedisForm;
        $title = Yii::t('fe', 'Asesmen Medis');
        $decId = $id;
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $model->pendaftaran_id = $pendaftaran_id;
        $model->pasienadmisi_id = $this->_data_pasien['pasienadmisi_id'];
        $jeniskelamin = $this->_data_pasien['jenis_kelamin'];
        $getUmur = DocoHelpers::getDiffDateTime($this->_data_pasien['tanggal_lahir'], date('Y-m-d'));
        $getBulan = $getUmur['tahun'] * 12 + $getUmur['bulan'];
        $jeniskelamin_id = $this->_data_pasien['jeniskelamin_id'] == 16 ? 16 : 15;
        $disabled = (!empty($this->_data_pasien['pasienpulang_id']) || $this->_data_pasien['is_stopakomodasi'] == true) ? true : false;
        if(Yii::$app->request->post()){
            $data = Yii::$app->request->post();
            $data['AsesmenMedisForm']['bb_ideal'] = $data['AsesmenMedisForm']['imt'] ? str_replace(",",".",$data['AsesmenMedisForm']['imt']) : '';
            $data['AsesmenMedisForm']['imt'] = $data['AsesmenMedisForm']['bb_ideal'] ? str_replace(",",".",$data['AsesmenMedisForm']['bb_ideal']) : '';
            $data['AsesmenMedisForm']['berat_badan'] = $data['AsesmenMedisForm']['berat_badan'] ? str_replace(",",".",$data['AsesmenMedisForm']['berat_badan']) : '';
            $data['AsesmenMedisForm']['tinggi_badan'] = $data['AsesmenMedisForm']['tinggi_badan'] ? str_replace(",",".",$data['AsesmenMedisForm']['tinggi_badan']) : '';
            $data['AsesmenMedisForm']['berat_badan'] = $data['AsesmenMedisForm']['berat_badan'] ? str_replace(",",".",$data['AsesmenMedisForm']['berat_badan']) : '';
            $data['AsesmenMedisForm']['suhu_tubuh'] = $data['AsesmenMedisForm']['suhu_tubuh'] ? str_replace(",",".",$data['AsesmenMedisForm']['suhu_tubuh']) : '';
            $data['AsesmenMedisForm']['bb_ideal'] = $data['AsesmenMedisForm']['bb_ideal'] ? str_replace(",",".",$data['AsesmenMedisForm']['bb_ideal']) : '';
            $data['AsesmenMedisForm']['imt'] = $data['AsesmenMedisForm']['imt'] ? str_replace(",",".",$data['AsesmenMedisForm']['imt']) : '';
            $data['AsesmenMedisForm']['td_diastolic'] = $data['AsesmenMedisForm']['td_diastolic'] ? str_replace(",",".",$data['AsesmenMedisForm']['td_diastolic']) : '';
            $data['AsesmenMedisForm']['td_systolic'] = $data['AsesmenMedisForm']['td_systolic'] ? str_replace(",",".",$data['AsesmenMedisForm']['td_systolic']) : '';
            $data['AsesmenMedisForm']['r_penyakitdahulu'] = $data['riwayat_penyakit'] ? json_encode($data['riwayat_penyakit']) : '';
            $data['AsesmenMedisForm']['r_alergiobat'] = $data['AsesmenMedisForm']['r_alergiobat'] ? $data['AsesmenMedisForm']['r_alergiobat'] : '';
            $data['AsesmenMedisForm']['r_imunisasi'] = $data['AsesmenMedisForm']['r_imunisasi'] ? json_encode($data['AsesmenMedisForm']['r_imunisasi']) : '';
            $data['AsesmenMedisForm']['r_penyakitkeluarga'] = $data['AsesmenMedisForm']['r_penyakitkeluarga'] ? ($data['AsesmenMedisForm']['r_penyakitkeluarga']) : '';
            $data['AsesmenMedisForm']['pasienadmisi_id'] = $this->_data_pasien['pasienadmisi_id'];
            // Mapping asesmen allo or auto
            if ($data['AsesmenMedisForm']['allo_or_auto'] == '0') {
                $data['AsesmenMedisForm']['sumber_info'] = 1;
                $data['AsesmenMedisForm']['sumber_info_lainnya'] = 0;
            } else if ($data['AsesmenMedisForm']['allo_or_auto'] == '1') {
                $data['AsesmenMedisForm']['sumber_info'] = 0;
                $data['AsesmenMedisForm']['sumber_info_lainnya'] = 1;
            } else {
                $data['AsesmenMedisForm']['sumber_info'] = null;
                $data['AsesmenMedisForm']['sumber_info_lainnya'] = null;
            }
            unset($data['AsesmenMedisForm']['allo_or_auto']);
            unset($data['riwayat_penyakit']);
            $r_tumbuh_kembang = [
            'berat_badan_anak' => $data['AsesmenMedisForm']['berat_badan_anak'] ?  $data['AsesmenMedisForm']['berat_badan_anak'] : '',
            'tinggi_badan_anak' => $data['AsesmenMedisForm']['tinggi_badan_anak'] ?  $data['AsesmenMedisForm']['tinggi_badan_anak'] : '',
            'kelainan_anak' => $data['AsesmenMedisForm']['kelainan_anak'] ?  $data['AsesmenMedisForm']['kelainan_anak'] : '',
            'asi' => $data['AsesmenMedisForm']['asi'] ?  $data['AsesmenMedisForm']['asi'] : '',
            'asi_addon' => $data['AsesmenMedisForm']['asi_addon'] ?  $data['AsesmenMedisForm']['asi_addon'] : '',
            'susu_formula' => $data['AsesmenMedisForm']['susu_formula'] ?  $data['AsesmenMedisForm']['susu_formula'] : '',
            'susu_formula_addon' => $data['AsesmenMedisForm']['susu_formula_addon'] ?  $data['AsesmenMedisForm']['susu_formula_addon'] : '',
            'makanan_padat' => $data['AsesmenMedisForm']['makanan_padat'] ?  $data['AsesmenMedisForm']['makanan_padat'] : '',
            'makanan_padat_addon' => $data['AsesmenMedisForm']['makanan_padat_addon'] ?  $data['AsesmenMedisForm']['makanan_padat_addon'] : '',
            'makanan_tambahan' => $data['AsesmenMedisForm']['makanan_tambahan'] ?  $data['AsesmenMedisForm']['makanan_tambahan'] : '',
            'makanan_tambahan_addon' => $data['AsesmenMedisForm']['makanan_tambahan_addon'] ?  $data['AsesmenMedisForm']['makanan_tambahan_addon'] : '',
            'tengkurap' => $data['AsesmenMedisForm']['tengkurap'] ?  $data['AsesmenMedisForm']['tengkurap'] : '',
            'tengkurap_addon' => $data['AsesmenMedisForm']['tengkurap_addon'] ?  $data['AsesmenMedisForm']['tengkurap_addon'] : '',
            'duduk' => $data['AsesmenMedisForm']['duduk'] ?  $data['AsesmenMedisForm']['duduk'] : '',
            'duduk_addon' => $data['AsesmenMedisForm']['duduk_addon'] ?  $data['AsesmenMedisForm']['duduk_addon'] : '',
            'merangkak' => $data['AsesmenMedisForm']['merangkak'] ?  $data['AsesmenMedisForm']['merangkak'] : '',
            'merangkak_addon' => $data['AsesmenMedisForm']['merangkak_addon'] ?  $data['AsesmenMedisForm']['merangkak_addon'] : '',
            'berdiri' => $data['AsesmenMedisForm']['berdiri'] ?  $data['AsesmenMedisForm']['berdiri'] : '',
            'berdiri_addon' => $data['AsesmenMedisForm']['berdiri_addon'] ?  $data['AsesmenMedisForm']['berdiri_addon'] : '',
            'berjalan' => $data['AsesmenMedisForm']['berjalan'] ?  $data['AsesmenMedisForm']['berjalan'] : '',
            'berjalan_addon' => $data['AsesmenMedisForm']['berjalan_addon'] ?  $data['AsesmenMedisForm']['berjalan_addon'] : '',
            ];
            $data['AsesmenMedisForm']['r_tumbuh_kembang'] = json_encode($r_tumbuh_kembang);
            $model->load($data);

            // untuk kebutuhan change field alergi menjadi free text - issue 1699
            // if ($data['AsesmenMedisForm']['r_alergiobat'] != '') {
            //     $data['AsesmenMedisForm']['r_alergiobat'] = explode(',', $data['AsesmenMedisForm']['r_alergiobat']);
            //     $data['AsesmenMedisForm']['r_alergiobat'] = json_encode($data['AsesmenMedisForm']['r_alergiobat']);
            // }

            if ($model->asesmenmedis_id != '') {
                $data['AsesmenMedisForm']['tgl_asesmenmedis'] = date('Y-m-d H:i:s', strtotime('NOW'));
            } else {
                $data['AsesmenMedisForm']['tgl_asesmenmedis'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $data['AsesmenMedisForm']['tgl_asesmenmedis'])));
            }

            // return DocoHelpers::response($data, 422);

            if ($model->validate()) {
                $post = $this->_restRanap->post('asesmen-medis/save-asesmen', ['form_params'=>$data]);
                $rest = json_decode($post->getBody(), true );
                
                // clear session
                Yii::$app->cache->delete('pasien-pendaftaran-id-' . $decId);
                Yii::$app->cache->delete($this->_pegawai_id.'-latest-data-asesmen-medis-ranap-'.$id);
                Yii::$app->cache->delete($this->_pegawai_id.'-updated-data-asesmen-medis-ranap-'.$id);

                return DocoHelpers::response($rest['response']);
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, substr(strrchr(get_class($model), "\\"), 1));
            }
        }

        $response = $this->_restRanap->post('allow/list-pack-asesmen', ['form_params'=>['pendaftaran_id'=>$pendaftaran_id]]);
        $body = json_decode($response->getBody(), true);
        $body = $body['response'];
        $data_imunisasi = ( isset($body['data_imunisasi']) && !empty($body['data_imunisasi']) ) ? $body['data_imunisasi'] : [];

        // Inisialisasi temp
        $riwayatPenyakitDahulu = [];
        $riwayatPenyakitKeluarga = [];
        $riwayatImunisasi = [];
        $riwayatKelahiran = [];
        $riwayatAlergiObat = [];
        $penyakitDahulu = [];
        $penyakitKeluarga = [];
        $imunisasi = [];
        $alergiObat = '';
        $initPenyakitKeluarga = [];
        $text_diagnosa_id = '';
        $sumber_info = 0;
        $sumber_info_lainnya = 0;

        if (!empty($body['data_riwayat'])) {
            foreach ($body['data_riwayat'] as $key => $value) {
                if (isset($value['r_penyakitkeluarga']) && $value['r_penyakitkeluarga'] != '') {
                    $riwayat = json_decode($value['r_penyakitkeluarga']);

                    if (!empty($riwayat)) {
                        for ($i=0; $i < count($riwayat); $i++) { 
                            // $exploded = explode('_', $riwayat[$i]);

                            // if (!in_array($riwayat[$i], $riwayatPenyakitKeluarga)) {
                            //     if (count($exploded) == 2) {
                            //         $riwayatPenyakitKeluarga[] = $exploded[1];
                            //     } else {
                            //         $riwayatPenyakitKeluarga[] = $exploded[0];
                            //     }
                            // }
                            $riwayatPenyakitKeluarga[] = $riwayat[$i]->text;
                        }
                    }
                }

                if (isset($value['r_imunisasi']) && $value['r_imunisasi'] != '') {
                    $riwayat = json_decode($value['r_imunisasi']);

                    if (!empty($riwayat)) {
                        for ($i=0; $i < count($riwayat); $i++) {
                            if (!in_array($riwayat[$i], $riwayatImunisasi)) {
                                $riwayatImunisasi[] = $riwayat[$i];
                            }
                        }
                    }
                }

                if (isset($value['r_penyakitdahulu']) && $value['r_penyakitdahulu'] != '') {
                    $riwayat = json_decode($value['r_penyakitdahulu'],true);
                    foreach($riwayat as $_riwayat){
                        $riwayatPenyakitDahulu[] = $_riwayat;
                        // Yii::warning($_riwayat,'_riwayat');
                    }
                    
                    /*
                    $riwayat = json_decode($value['r_penyakitdahulu']);

                    if (!empty($riwayat)) {
                        for ($i=0; $i < count($riwayat); $i++) {
                            if (!in_array($riwayat[$i], $riwayatPenyakitDahulu)) {
                                $riwayatPenyakitDahulu[] = $riwayat[$i];
                            }
                        }
                    }
                    */
                }

                if (isset($value['r_kelahiran']) && $value['r_kelahiran'] != '') {
                    $riwayat = $value['r_kelahiran'];

                    $riwayatKelahiran[] = $riwayat;
                }

                // untuk kebutuhan change field alergi menjadi free text - issue 1699
                // if (isset($value['r_alergiobat']) && $value['r_alergiobat'] != '') {
                //     $riwayat = json_decode($value['r_alergiobat']);

                //     if (!empty($riwayat)) {
                //         for ($i=0; $i < count($riwayat); $i++) {
                //             if (!in_array($riwayat[$i], $riwayatAlergiObat) && $riwayat[$i] != '') {
                //                 $riwayatAlergiObat[] = $riwayat[$i];
                //             }
                //         }
                //     }
                // }
            }
        }

        if (!empty($body['data_asesmen']['asesmen'])) {
            $sumber_info = isset($body['data_asesmen']['asesmen']['sumber_info']) ? $body['data_asesmen']['asesmen']['sumber_info'] : '';
            $sumber_info_lainnya = isset($body['data_asesmen']['asesmen']['sumber_info_lainnya']) ? $body['data_asesmen']['asesmen']['sumber_info_lainnya'] : '';
            $temp = json_decode($body['data_asesmen']['asesmen']['r_penyakitkeluarga']);
            $tmpDefaultVal = [];
            if (!empty($temp)) {
                foreach ($temp as $key => $value) {
                    // $exploded = explode('_', $value);
                    // $initPenyakitKeluarga[] = [$value => isset($exploded[1]) ? $exploded[1] : $exploded[0]];
                    // $penyakitKeluarga[] = $value->text;
                    // $initPenyakitKeluarga[] = 
                    $idVarKel = !empty($value->id) ? $value->id .'_'. $value->text : $value->text;
                    $tmpDefaultVal[] = $idVarKel;
                    $penyakitKeluarga[$idVarKel] = $value->text;
                }
                $model->r_penyakitkeluarga = $tmpDefaultVal;
            }

            // $data_imunisasi = ArrayHelper::map($data_imunisasi, 'diagnosa_nama', 'diagnosa_nama');

            $temp = json_decode($body['data_asesmen']['asesmen']['r_imunisasi']);
            if (!empty($temp)) {
                foreach ($temp as $key => $value) {
                    if (!isset($data_imunisasi[$value])) {
                        $data_imunisasi[$value] = $value;
                    }
                    $imunisasi[] = $value;
                }
            }

            $temp = json_decode($body['data_asesmen']['asesmen']['r_penyakitdahulu']);
            if (!empty($temp)) {
                foreach ($temp as $key => $value) {
                    $penyakitDahulu[] = $value;
                }
            }

            // untuk kebutuhan change field alergi menjadi free text - issue 1699
            // $temp = json_decode($body['data_asesmen']['asesmen']['r_alergiobat']);
            // if (!empty($temp)) {
            //     if (isset($temp[0]) && $temp[0] != '') {
            //         $alergiObat = $temp;
            //     }
            // }
        }

        // untuk kebutuhan change field alergi menjadi free text - issue 1699
        // if (is_array($alergiObat)) {
        //     $alergiObat = implode(',', $alergiObat);
        // }

        $model->tinggi_badan = $body['data_askep']['tinggi_badan']; 
        $model->berat_badan = $body['data_askep']['berat_badan'];
        $model->bb_ideal = $body['data_askep']['bb_ideal'];
        $model->imt = $body['data_askep']['imt'];
        $model->ket_imt = $body['data_askep']['ket_imt'];
        $model->skala = $body['data_askep']['skala'];
        $model->kesadaran = 1;
        $model->keadaan_umum = 2;
        $model->td_systolic = isset($body['data_askep']['td_systolic']) ? $body['data_askep']['td_systolic'] : '';
        $model->td_diastolic = isset($body['data_askep']['td_diastolic']) ? $body['data_askep']['td_diastolic'] :'';
        $model->detak_nadi = $body['data_askep']['detak_nadi'];
        $model->suhu_tubuh = $body['data_askep']['suhu_tubuh'];
        // 'asi_addon' => 'Bulan',

        // Cek asesmen
        $val_r_penyakitkeluarga = null;
        if(count($body['data_asesmen']['asesmen']) > 0){
            unset($body['data_asesmen']['asesmen']['additional_data']);
            unset($body['data_asesmen']['asesmen']['created_date']);
            unset($body['data_asesmen']['asesmen']['created_by']);
            unset($body['data_asesmen']['asesmen']['modified_count']);
            unset($body['data_asesmen']['asesmen']['last_modified_date']);
            unset($body['data_asesmen']['asesmen']['last_modified_by']);
            unset($body['data_asesmen']['asesmen']['is_deleted']);
            unset($body['data_asesmen']['asesmen']['is_active']);
            unset($body['data_asesmen']['asesmen']['deleted_by']);
            unset($body['data_asesmen']['asesmen']['deleted_date']);
            $body['data_asesmen']['asesmen']['r_penyakitkeluarga'] = $penyakitKeluarga;
            $body['data_asesmen']['asesmen']['r_imunisasi'] = $imunisasi;
            $body['data_asesmen']['asesmen']['r_penyakitdahulu'] = $penyakitDahulu;
            // untuk kebutuhan change field alergi menjadi free text - issue 1699
            // $body['data_asesmen']['asesmen']['r_alergiobat'] = $alergiObat;
            $body['data_asesmen']['asesmen']['tgl_asesmenmedis'] = date('d/m/Y H:i:s', strtotime(str_replace('/', '-', $body['data_asesmen']['asesmen']['tgl_asesmenmedis'])));
            $body['data_asesmen']['asesmen']['is_terintubasi'] = $body['data_asesmen']['asesmen']['is_terintubasi'] == true ? 1 : 0;
            $body['data_asesmen']['asesmen']['is_merokok'] = $body['data_asesmen']['asesmen']['is_merokok'] == true ? 1 : 0;
            $body['data_asesmen']['asesmen']['discharge_plan'] = $body['data_asesmen']['asesmen']['discharge_plan'] == true ? 1 : 0;
            $text_diagnosa_id = $body['data_asesmen']['asesmen']['text_diagnosa_id'];
            // $dataDiag = json_decode($body['data_asesmen']['asesmen']['text_diagnosa_id'], true);
            // $text_diagnosa_id = $dataDiag['text'];
            // $body[]
            $body['data_asesmen']['asesmen']['tinggi_badan'] = str_replace(".",",",$body['data_asesmen']['asesmen']['tinggi_badan']);
            $body['data_asesmen']['asesmen']['berat_badan']  = str_replace(".",",",$body['data_asesmen']['asesmen']['berat_badan']);
            $body['data_asesmen']['asesmen']['suhu_tubuh']   = str_replace(".",",",$body['data_asesmen']['asesmen']['suhu_tubuh']);
            $body['data_asesmen']['asesmen']['bb_ideal']   = str_replace(".",",",$body['data_asesmen']['asesmen']['bb_ideal']);
            $body['data_asesmen']['asesmen']['imt']   = str_replace(".",",",$body['data_asesmen']['asesmen']['imt']);
            $body['data_asesmen']['asesmen']['td_systolic']   = str_replace(".",",",$body['data_asesmen']['asesmen']['td_systolic']);
            $body['data_asesmen']['asesmen']['td_diastolic']   = str_replace(".",",",$body['data_asesmen']['asesmen']['td_diastolic']);
            if(!empty($body['data_asesmen']['asesmen']['r_tumbuh_kembang'])){
                $body['data_asesmen']['asesmen']['r_tumbuh_kembang'] = json_decode($body['data_asesmen']['asesmen']['r_tumbuh_kembang'], true);
                $model->attributes = $body['data_asesmen']['asesmen']['r_tumbuh_kembang'];
            }   

            $model->attributes = $body['data_asesmen']['asesmen'];
        
            if (!empty($model->diagnosa_id)) {
                $tmpDiag = json_decode($model->diagnosa_id, true);
                $model->diagnosa_id = isset($tmpDiag['id']) ? $tmpDiag['id'] .'_'. $tmpDiag['text'] : $tmpDiag['text'];
            }


        } else {
            $model->tgl_asesmenmedis = date('d/m/Y H:i:s', strtotime('NOW'));
            // $model->r_penyakitkeluarga = $penyakitKeluarga;
            $model->r_imunisasi = $imunisasi;
            $model->r_penyakitdahulu = $penyakitDahulu;
            // untuk kebutuhan change field alergi menjadi free text - issue 1699
            // $model->r_alergiobat = $alergiObat;
        }

        if(!empty($body['data_askep']) && empty($body['data_asesmen']['asesmen'])) {
            $model->r_penyakitsekarang = ! empty($model->r_penyakitsekarang) ? $model->r_penyakitsekarang : ArrayHelper::getValue($body['data_askep'], 'r_penyakitsekarang');
            $model->obat_diberikan = ! empty($model->obat_diberikan) ? $model->obat_diberikan : ArrayHelper::getValue($body['data_askep'], 'obat_diberikan');
            $model->gcs_eye = ! empty($model->gcs_eye) ? $model->gcs_eye :  ArrayHelper::getValue($body['data_askep'], 'gcs_eye');
            $model->gcs_verbal = ! empty($model->gcs_verbal) ? $model->gcs_verbal :  ArrayHelper::getValue($body['data_askep'], 'gcs_verbal');
            $model->gcs_motorik = ! empty($model->gcs_motorik) ? $model->gcs_motorik :  ArrayHelper::getValue($body['data_askep'], 'gcs_motorik');
            $model->jumlah_gcs = ! empty($model->jumlah_gcs) ? $model->jumlah_gcs :  ArrayHelper::getValue($body['data_askep'], 'jumlah_gcs');
            $model->keluhan_utama = ! empty($model->keluhan_utama) ? $model->keluhan_utama : ArrayHelper::getValue($body['data_askep'], 'keluhan_utama');

            if(! empty($body['data_askep']['sumber_hubungan'])) {
                $model->sumber_info_lainnya = ! empty($model->sumber_info_lainnya) ? $model->sumber_info_lainnya : ArrayHelper::getValue($body['data_askep'], 'sumber_info_lainnya');
                $model->sumber_hubungan = ! empty($model->sumber_hubungan) ? $model->sumber_hubungan : ArrayHelper::getValue($body['data_askep'], 'sumber_hubungan');
                $sumber_info_lainnya = $model->sumber_info_lainnya;
                $model->sumber_info = !$sumber_info_lainnya;
                $sumber_info = !$sumber_info_lainnya;
            } else {
                $model->sumber_info = ! empty($model->sumber_info) ? $model->sumber_info : ArrayHelper::getValue($body['data_askep'], 'sumber_info');
                $model->sumber_info = ! empty($model->sumber_info) ? $model->sumber_info : ArrayHelper::getValue($body['data_askep'], 'sumber_info');
                $sumber_info = $model->sumber_info;
                $model->sumber_info_lainnya = !$sumber_info;
                $sumber_info_lainnya = !$sumber_info;
            }
        }

        $tahun = [];
        $start_year = date('Y', null);
        for ( $start_year; $start_year <= date('Y') ; $start_year++) {
            $tahun[] =  ['tahun'=>$start_year];
        }
        $tahun[max(array_keys($tahun)) + 1] = '';
        krsort($tahun);
        
        $waktu_tumbuh_kembang = [
            [
                'waktu' => 'Bulan'
            ],
            [
                'waktu' => 'Tahun'
            ]
        ];

        $data_kunjungan = isset($body['data_kunjungan']) ? $body['data_kunjungan'] : '';
        $data_kontak = [ '1'=>Yii::t('fe','Adekuat'), '0'=>Yii::t('fe','Tidak adekuat')];
        $data_discharge = [ '0'=>Yii::t('fe','Tidak'),  '1'=>Yii::t('fe','Ya').'('.Yii::t('fe','Lanjut ke halaman discharge planning').')'];
        $data_isMerokok = [ '1'=>Yii::t('fe','Ya'), '0'=>Yii::t('fe','Tidak')];
        $data_isTerintubasi = [ '0'=>Yii::t('fe','Terintubasi'), '1'=>Yii::t('fe','Tidak terintubasi')];
        $data_kakududuk = [ '1'=>Yii::t('fe','Ya'), '0'=>Yii::t('fe','Tidak')];
        $data_sumberInfo = ['1'=>'Pasien', '0'=>'Orang lain, Hubungan dengan pasien '];
        $data_kategori_asesmen = ['1'=>Yii::t('fe','Dewasa'), '2'=>Yii::t('fe','Anak')];
        $data_status_meroko = [ '1'=>Yii::t('fe','Pasif'), '0'=>Yii::t('fe','Aktif')];
        $data_kesadaran = ['1'=>Yii::t('fe','Compose Metis'), '2'=>Yii::t('fe','Apatis'), '3'=>Yii::t('fe','Samnolen'), '4'=>Yii::t('fe','Sopor'), '5'=>Yii::t('fe','Koma')];
        $data_kesadaran_umum = ['1'=>Yii::t('fe','Tampak Tidak Sakit'), '2'=>Yii::t('fe','Tampak Sakit Ringan'), '3'=>Yii::t('fe','Tampak Sakit Sedang'), '4'=>Yii::t('fe','Tampak Sakit Berat')];

        // pemeriksaan fisik
        $data_kepala  = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        $data_mata    = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        $data_telinga = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        $data_leher = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        $data_mulut = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        $data_thoraks = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        $data_paru = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        // paru-paru
        $data_pergerakan = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Asimetris')];
        $data_perkusi = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        $data_pernapasan = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        $data_rochi = ['1'=>Yii::t('fe','Ada'), '0'=>Yii::t('fe','Tidak Ada')];
        $data_wheezing = ['1'=>Yii::t('fe','Ada'), '0'=>Yii::t('fe','Tidak Ada')];
        // jantung
        $data_irama = ['1'=>Yii::t('fe','Reguler'), '0'=>Yii::t('fe','Inreguler')];
        $data_bunyi_jantung = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        // abdomen
        $data_kelainan = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        $data_benjolan = ['1'=>Yii::t('fe','Tidak'), '0'=>Yii::t('fe','Ya')];
        $data_nyeri_tekan = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        $data_hernia = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        $data_bising_usus = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        $data_distensi = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        // Tulang Belakang
        $data_tulang_belakang = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        // Sistem Saraf
        $data_sistem_saraf = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        // Genetelia
        $data_genetalia = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        // Ekstremitas
        $data_edema = ['1'=>Yii::t('fe','Tidak'), '0'=>Yii::t('fe','Ya')];
        $data_crt = ['1'=>Yii::t('fe','Normal'), '0'=>Yii::t('fe','Tidak Normal')];
        
        $data_luka_bakar = ['Ringan'=>Yii::t('fe','Ringan'), 'Sedang'=>Yii::t('fe','Sedang'),'Berat'=>Yii::t('fe','Berat')];

        //form asmed anak
        $data_riwayat_kandungan = ['1'=>'Paritas', '2'=>'Abortus', '3'=>'Meninggal'];
        $data_riwayat_partus = ['1'=>'Dokter', '2'=>'Bidan', '3'=>'Lai-lain'];
        $data_komplikasi = [ '1'=>Yii::t('fe','Tidak'),'0'=>Yii::t('fe','Ya') ];
        $data_neotanus = [ '1'=>Yii::t('fe','Tidak'),'0'=>Yii::t('fe','Ya') ];
        $data_maternal = [ '1'=>Yii::t('fe','Tidak'),'0'=>Yii::t('fe','Ya') ];

        // $data_diagnosa = [];
        $data_diagnosa = ( isset($body['data_diagnosa']) && !empty($body['data_diagnosa']) ) ? $body['data_diagnosa'] : [];
        $data_obatalkes = ( isset($body['data_obatalkes']) && !empty($body['data_obatalkes']) ) ? $body['data_obatalkes'] : [];
        $data_gcs = ( isset($body['data-gcs']) && !empty($body['data-gcs']) ) ? $body['data-gcs'] : [];
        $data_metodegcs = ( isset($body['data-metodegcs']) && !empty($body['data-metodegcs']) ) ? $body['data-metodegcs'] : [];
        $data_listgcs = ( isset($body['data-listgcs']) && !empty($body['data-listgcs']) ) ? $body['data-listgcs'] : [];
        $data_bmi = ( isset($body['data-bmi']) && !empty($body['data-bmi']) ) ? $body['data-bmi'] : [];
        $data_bmi_anak = ( isset($body['data-bmi-anak']) && !empty($body['data-bmi-anak']) ) ? $body['data-bmi-anak'] : [];
        $data_tekanandarah = ( isset($body['data-tekanandarah']) && !empty($body['data-tekanandarah']) ) ? $body['data-tekanandarah'] : [];
        $data_bagiantubuh = ( isset($body['data-bagiantubuh']) && !empty($body['data-bagiantubuh']) ) ? $body['data-bagiantubuh'] : [];
        $data_detailbagiantubuh = ( isset($body['data-detailbagiantubuh']) && !empty($body['data-detailbagiantubuh']) ) ? $body['data-detailbagiantubuh'] : [];
        $data_denyutJantung = ( isset($body['data_denyutjantung']) && !empty($body['data_denyutjantung']) ) ? $body['data_denyutjantung'] : [];
        $data_gcsEye = ( isset($data_listgcs['eye']) && !empty($data_listgcs['eye']) ) ? $data_listgcs['eye'] : [];
        $data_gcsVerbal = ( isset($data_listgcs['verbal']) && !empty($data_listgcs['verbal']) ) ? $data_listgcs['verbal'] : [];
        $data_gcsMotorik = ( isset($data_listgcs['motorik']) && !empty($data_listgcs['motorik']) ) ? $data_listgcs['motorik'] : [];
        
        $data_hasil_lab = $body['data_hasilpenunjang'] ? $body['data_hasilpenunjang']['masuklab_id'] : null;
        $data_hasil_rad = $body['data_hasilpenunjang'] ? $body['data_hasilpenunjang']['masukrad_id'] : null;

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
        $data_metodAsesmen = ( isset($body['data_asesmennyeri']) && !empty($body['data_asesmennyeri']) ) ? $body['data_asesmennyeri'] : [];
        $data_anatomiPasien = isset($body['data_asesmen']['anatomi']) ? $body['data_asesmen']['anatomi'] : [];

        if (!empty($data_anatomiPasien)) {
            foreach ($data_anatomiPasien as $key => $value) {
                $data_anatomiPasien[$key]['created_date'] = date('d/m/Y H:i:s', strtotime($data_anatomiPasien[$key]['created_date']));
                $data_anatomiPasien[$key]['counters'] = $key;
            }
        }

        $jsonAnatomi = json_encode($data_anatomiPasien,JSON_FORCE_OBJECT);
        $counter = count($data_anatomiPasien);
        if(! empty($data_anatomiPasien)) {
            foreach ($data_anatomiPasien as $key => $value) {
                if($counter < $value['counters']) {
                    $counter = (int) $value['counters'];
                }
            }
            $counter++;
        } else {
            $counter = count($data_anatomiPasien);
        }

        $model->dokter_id = isset($data_kunjungan['pegawai_id']) ? $data_kunjungan['pegawai_id'] : '';
        $dokterNama = isset($data_kunjungan['nama_pegawai']) ? $data_kunjungan['nama_pegawai'] : '';
        $urlCetak = '';
        $model->kategori_asmed = isset($model->kategori_asmed) ? $model->kategori_asmed : 1;
        $hide = 'show()';
        $status_disabled = $this->getStatusPeriksa($pendaftaran_id);
        if($status_disabled == true){
            $hide = 'hide()';
        }else {
            $status_disabled = 'false';
        }
        if($this->_user_identity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS){
            $model->loged = true;
        }
        // Return
        $isStopAkomodasi = $this->_data_pasien['is_stopakomodasi'] ? 1 : 0;
        $enable_edit = !empty($body['enable_pulang']) ? $body['enable_pulang'] : false;
         
        // set default value
        $model->asi_addon = is_null($model->asi_addon) ? 'Bulan' : $model->asi_addon;
        $model->susu_formula_addon = is_null($model->susu_formula_addon) ? 'Bulan' : $model->susu_formula_addon;
        $model->makanan_padat_addon = is_null($model->makanan_padat_addon) ? 'Bulan' : $model->makanan_padat_addon;
        $model->makanan_tambahan_addon = is_null($model->makanan_tambahan_addon) ? 'Bulan' : $model->makanan_tambahan_addon;
        $model->tengkurap_addon = is_null($model->tengkurap_addon) ? 'Bulan' : $model->tengkurap_addon;
        $model->duduk_addon = is_null($model->duduk_addon) ? 'Bulan' : $model->duduk_addon;
        $model->merangkak_addon = is_null($model->merangkak_addon) ? 'Bulan' : $model->merangkak_addon;
        $model->berdiri_addon = is_null($model->berdiri_addon) ? 'Bulan' : $model->berdiri_addon;
        $model->berjalan_addon = is_null($model->berjalan_addon) ? 'Bulan' : $model->berjalan_addon;
        $model->denyut_jantung = is_null($model->denyut_jantung) ? 'Inreguler' : $model->denyut_jantung;
        $model->anatomi_tubuh = $jsonAnatomi;

        // jika ada perubahan dari form (belum disimpan), gunakan data cache
        $updated_asmed_cache = Yii::$app->cache->get($this->_pegawai_id.'-updated-data-asesmen-medis-ranap-'.Yii::$app->request->get('id'));
        $is_draft = 0;
        if ($updated_asmed_cache) {
            $model->attributes = $updated_asmed_cache;
            $model->r_penyakitkeluarga = $updated_asmed_cache['r_penyakitkeluarga'];
            $model->allo_or_auto = $updated_asmed_cache['allo_or_auto'];
            $model->sumber_hubungan = $updated_asmed_cache['sumber_hubungan'];
            $penyakitDahulu = json_decode($model->r_penyakitdahulu);
            $sumber_info = $updated_asmed_cache['allo_or_auto'] ? $updated_asmed_cache['allo_or_auto'] == false : $model->sumber_info;
            $sumber_info_lainnya = $updated_asmed_cache['allo_or_auto'] ? $updated_asmed_cache['allo_or_auto'] == true : $model->sumber_info_lainnya;
            if (!empty($model->diagnosa_id)) {
                $splitVal = explode('_', $model->diagnosa_id);
                if (count($splitVal) > 1) {
                    $text_diagnosa_id = $splitVal[1];
                } else {
                    $text_diagnosa_id = $model->diagnosa_id;
                }
            }
            $jsonAnatomi = ArrayHelper::getValue($updated_asmed_cache, 'anatomi_tubuh');
            $counterAnatomi = json_decode($jsonAnatomi, true);
            if (is_array($counterAnatomi)) {
                $counter = count($counterAnatomi);
                foreach ($counterAnatomi as $key => $value) {
                    if($counter < $value['counters']) {
                        $counter = (int) $value['counters'];
                    }
                }

                $counter++;
                $model->anatomi_tubuh = $jsonAnatomi;
            }
            $is_draft = 1;
        }
        // set cache data terbaru dari db
        else {
            Yii::$app->cache->set($this->_pegawai_id.'-latest-data-asesmen-medis-ranap-'.Yii::$app->request->get('id'), $model->attributes, DocoConstants::EXPIRED_CACHE);
        }
        $model->is_merokok_pasif = $model->is_merokok_pasif === true ? 1 : ($model->is_merokok_pasif === false ? 0 : null);

        
        $cekAkses = (new AksesFormService)->execute($this->_data_pasien['pasien_id'], DocoConstants::FORM_ASESMEN_MEDIS);
        if ($cekAkses == true || $enable_edit){
            $isStopAkomodasi = 0;
            $status_disabled = 'false';
        }
        
        return $this->renderAjax('asesmen-medis/index', get_defined_vars());
    }

    public function actionGetHasilTd($pendaftaran_id, $nilai = '0/0', $golongan_umur)
    {
        $request = Yii::$app->request;
        $data_td = DocoConstants::TD_HASIL;
        if(count($data_td) < 1) {
            $response = $this->_restRanap->get('allow/get-data-klasifikasi-tekanan-darah');
            $body = json_decode($response->getBody(), true);
            $data_td = $body['response'];
        }
        $nilai = explode('/', $nilai);
        $nilai_systolic = (int)$nilai[0];
        $nilai_diastolic = (int)$nilai[1];
        $hasil = '';

        if (!empty($data_td)) {
            if ($nilai_systolic >= $data_td[4]['sistolik_min'] || $nilai_diastolic >= $data_td[4]['diastolik_min']) {
                $hasil = $data_td[4]['klasifikasitekanadarah'];
            } else {
                if ($nilai_systolic >= $data_td[3]['sistolik_min'] && $nilai_systolic <= $data_td[3]['sistolik_max'] || $nilai_diastolic >= $data_td[3]['diastolik_min'] && $nilai_diastolic <= $data_td[3]['diastolik_max']) {
                    $hasil = $data_td[3]['klasifikasitekanadarah'];
                } else {
                    if ($nilai_systolic >= $data_td[2]['sistolik_min'] && $nilai_systolic <= $data_td[2]['sistolik_max'] || $nilai_diastolic >= $data_td[2]['diastolik_min'] && $nilai_diastolic <= $data_td[2]['diastolik_max']) {
                        $hasil = $data_td[2]['klasifikasitekanadarah'];
                    } else {
                        if ($nilai_systolic >= $data_td[1]['sistolik_min'] && $nilai_diastolic <= $data_td[1]['sistolik_max']) {
                            $hasil = $data_td[1]['klasifikasitekanadarah'];
                        } else {
                            $hasil = $data_td[0]['klasifikasitekanadarah'];
                        }
                    }
                }
            }
        }

        $result = ['hasil' => $hasil];
        return DocoHelpers::response($result);
    }

    public function actionSaveAnatomi()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $data = $request->post('data');
            $pendaftaran_id = $request->post('pendaftaran_id');
            $pasien_id = $request->post('pasien_id');
            $pemeriksaanfisik_id = $request->post('pemeriksaanfisik_id');

            $send_data = [
                'data' => $data,
                'pendaftaran_id' => $pendaftaran_id,
                'pasien_id' => $pasien_id,
                'pemeriksaanfisik_id' => $pemeriksaanfisik_id,
            ];

            $response = $this->_restRanap->post('asesmen-medis/save-periksatubuh', [
                'form_params' => $send_data
            ]);
            $response = json_decode($response->getBody(), true);

            return [
                'status' => 200,
                'message' => $response,
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionGetListDiagnosa()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restRanap->request('POST', 'allow/data-diagnosa',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['diagnosa_id'],'text'=>$value['diagnosa_nama']];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }

    public function actionCetakAsesmen($id)
    {
        $id = DocoHelpers::decrypt($id);
        \app\components\EsignHelpers::previewEsign([
            'type' => 'Asemen Medis Rawat Inap',
            'pendaftaran_id' => $id,
        ]);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $nama_pegawai = $this->_user_identity['nama_pegawai'];
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-asesmen.pdf";
        try {
            $response = $this->_restRanap->get('asesmen-medis/cetak-asesmen',[
                'save_to' => $path,
                'query' => [
                        'id'=>$id,
                        'nama_pegawai' => $nama_pegawai,
                    ],
            ]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    // Hapus asesmen
    public function actionHapusAsesmenMedis($id, $asesmenmedis_id)
    {
        // Try catch
        try {
            // Check post
            if ($asesmenmedis_id != '') {
                // Send to backend
                $request = $this->_restRanap->delete('asesmen-medis/delete?id='.$asesmenmedis_id);
                $response = json_decode($request->getBody(), true);

                // Status code
                Yii::$app->response->statusCode = 200;

                // Response

                // clear session
                Yii::$app->cache->delete('pasien-pendaftaran-id-' . $id);
                Yii::$app->cache->delete($this->_pegawai_id.'-updated-data-asesmen-medis-ranap-'.$id);
                Yii::$app->cache->delete($this->_pegawai_id.'-latest-data-asesmen-medis-ranap-'.$id);

                return DocoHelpers::response([], \Yii::$app->response->statusCode);
            }
        } catch (\Exception $e) {
            // Exception
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (RequestException $e) {
            // Exception
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    // set cache ketika form asmed on change
    public function actionSetCacheAsmed() {
        $is_draft = 0;
        $updated_asmed = Yii::$app->request->get('AsesmenMedisForm');
        $pendaftaran_id = DocoHelpers::encrypt(Yii::$app->request->get('pendaftaran_id'));
        $updated_asmed['r_penyakitdahulu'] = json_encode(Yii::$app->request->get('riwayat_penyakit'));
        $anatomi_json = json_decode($updated_asmed['anatomi_tubuh'], true); 
        $updated_asmed['anatomi_tubuh'] = json_encode($anatomi_json, JSON_FORCE_OBJECT); 
        unset($updated_asmed['jumlah_gcs'], $updated_asmed['berat_luka_bakar']);

        // cek perbedaan data pada form
        $asmed_cache = Yii::$app->cache->get($this->_pegawai_id.'-latest-data-asesmen-medis-ranap-'.$pendaftaran_id);
        $asmed_cache['r_penyakitdahulu'] = empty($asmed_cache['r_penyakitdahulu']) ? '[{"tahun":"","penyakit":"","terapi":""}]' : json_encode($asmed_cache['r_penyakitdahulu']);
        foreach ($asmed_cache as $key => $value) {
            if (isset($updated_asmed[$key])) {
                if (is_null($value) && ($updated_asmed[$key] === '' || ($updated_asmed[$key] === '0'))) continue;
                if ($key == 'jumlah_gcs') continue;
                if ($value != $updated_asmed[$key]) {
                    $asmed_cache[$key] = $updated_asmed[$key];
                    if (!$is_draft) $is_draft = 1;
                }
            }
        }

        // set cache jika ada perubahan, delete cache jika tetap sama
        if ($is_draft) {
            // digunakan di AsesmenMedisProcess actionSetCacheAsmed processFlow()
            Yii::$app->cache->set($this->_pegawai_id.'-updated-data-asesmen-medis-ranap-'.$pendaftaran_id, $asmed_cache, DocoConstants::EXPIRED_CACHE);
        } else {
            Yii::$app->cache->delete($this->_pegawai_id.'-updated-data-asesmen-medis-ranap-'.$pendaftaran_id);
        }

        return DocoHelpers::response(["is_draft" => $is_draft]);
    }
}
?>
