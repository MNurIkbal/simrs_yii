<?php

/**
 * @author : ilham pramono (ilham.pramono@sirs.co.id)
 * Powered by Sirs
 */

namespace Extensions\igd;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanPermintaanMakanView;
use app\modules\v1\models\PasienPulang;
use app\modules\v1\models\InfoPasienPulangRjRd;
use app\modules\v1\models\CpptRjV;
use Doco\models\SoapRsView;
use app\modules\v1\models\AsesmenMedisRD;
use app\modules\v1\models\InfoPasienRdV;
use app\modules\v1\models\InfoPasienRi;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\KelasPelayanan;
use Doco\components\DocoPrint;
use GuzzleHttp\Client;

class CetakSpriKramat extends \Doco\components\DocoBaseProcessExtension
{
    protected function processFlow()
    {
        $request = Yii::$app->request;
        $type = $request->get('type','');
        $pendaftaran_id = $request->get('pendaftaran_id',0);
        $prev_pendaftaran_id = $request->get('prev_no_pendaftaran',0);
        $ruangan_id = $request->get('ruangan_id',0);
        $pegawai_id = $request->get('pegawai_id',0);
        $kelompokpegawai_id = $request->get('kelompokpegawai_id',0);
        $nama_usercetak = $request->get('nama_usercetak','');
        $id_usercetak = $request->get('id_usercetak',0);

        $pegawailogin_id   = Yii::$app->jwt->user->pegawai_id;
        $pegawailogin      = Pegawai::findOne($pegawailogin_id);
        $pegawailogin_nama = $pegawailogin->nama_pegawai;
        $waktu_cetak       = date('d F Y');
        $qry = new PasienPulang;
        if($type == 'rj'){
            $pulang = $qry::find()->Select([
                'ruanganakhir_id',
                'ruangan_m.instalasi_id',
                'ruangan_m.ruangan_nama',
                'tempattidurtujuan_id',
                "CONCAT(kamarruangan_v.ruangan_nama, ' / ', kamarruangan_v.kamarruangan_nokamar, ' / ', kamarruangan_v.no_tempattidur) as ruangan_tujuan",
                'dokterspesialis_id',
                'pegawai_m.nama_pegawai as nama_dokter_tujuan',
                'catatan_tindakan'
            ])
            ->join('LEFT JOIN', 'ruangan_m', 'ruangan_m.ruangan_id=pasienpulang_t.ruanganakhir_id')
            ->join('LEFT JOIN', 'kamarruangan_v', 'kamarruangan_v.kamartempattidur_id=pasienpulang_t.tempattidurtujuan_id')
            ->join('LEFT JOIN', 'pegawai_m', 'pegawai_m.pegawai_id=pasienpulang_t.dokterspesialis_id')
            ->andWhere([
                'pendaftaran_id'=>$pendaftaran_id
            ])->asArray()->one();


            $modelHeader = new InfoPasienPulangRjRd;
            $queryHeader = $modelHeader::find()
                ->andWhere([
                    'pendaftaran_id'=>$request->get('pendaftaran_id')
                ]);
            $resultHeader = $queryHeader->asArray()->one();
            $dokter_merawat = $resultHeader['dokter'];
            $diagnosa_asesmen = CpptRjV::find()->select([
                'a_diag_utama',
                'tgl_soaprj',
            ])
            ->Where([
                'pegawai_id'=>$resultHeader['pegawai_id'],
                'pendaftaran_id'=>$pendaftaran_id,
            ])->asArray()->all();
                $arr= [];
                $diagnosis_masuk = ' - ';
                if(!empty($diagnosa_asesmen)){
                    foreach ($diagnosa_asesmen as $key => $value) {
                        if(empty($arr)){
                            $arr = $value['a_diag_utama'];
                        }
                    }
                    $diagnosa = json_decode($arr);
                    $diagnosis_masuk = $diagnosa->text == '' ? ' - ' : $diagnosa->text ;
                }

        }else if($type == 'rd'){
            $pulang = $qry::find()->Select([
                'ruanganakhir_id',
                'ruangan_m.instalasi_id',
                'ruangan_m.ruangan_nama',
                'tempattidurtujuan_id',
                "CONCAT(kamarruangan_v.ruangan_nama, ' / ', kamarruangan_v.kamarruangan_nokamar, ' / ', kamarruangan_v.no_tempattidur) as ruangan_tujuan",
                'dokterspesialis_id',
                'pegawai_m.nama_pegawai as nama_dokter_tujuan',
                'catatan_tindakan'
            ])
            ->join('LEFT JOIN', 'ruangan_m', 'ruangan_m.ruangan_id=pasienpulang_t.ruanganakhir_id')
            ->join('LEFT JOIN', 'kamarruangan_v', 'kamarruangan_v.kamartempattidur_id=pasienpulang_t.tempattidurtujuan_id')
            ->join('LEFT JOIN', 'pegawai_m', 'pegawai_m.pegawai_id=pasienpulang_t.dokterspesialis_id')
            ->andWhere([
                'pendaftaran_id'=>$pendaftaran_id
            ])->asArray()->one();

            $diagnosa_asesmen = AsesmenMedisRD::find()->select([
                'asesmenmedisrd_t.diagnosa_id',
                'diagnosa_m.diagnosa_nama',
            ])
            ->join('JOIN', 'diagnosa_m', 'diagnosa_m.diagnosa_id=asesmenmedisrd_t.diagnosa_id')
            ->andWhere([
                'pendaftaran_id'=>$pendaftaran_id
            ])->asArray()->one();

            $diagnosis_masuk = ' - ';

            $modelHeader = new InfoPasienRdV;
            $queryHeader = $modelHeader::find()
                ->andWhere([
                    'pendaftaran_id'=>$request->get('pendaftaran_id')
                ]);
            $resultHeader = $queryHeader->asArray()->one();
            $diagnosa_utama = SoapRsView::find()->select([
                'a_diag_utama',
                'tgl_soaprj',
            ])
            ->Where([
                'pendaftaran_id'=>$pendaftaran_id,
            ])->asArray()->all();

            if(!empty($diagnosa_asesmen)){
                $diagnosis_masuk = $diagnosa_asesmen['diagnosa_nama'];
            }else {
                if(!empty($diagnosa_utama)){
                    foreach ($diagnosa_utama as $key => $value) {
                        if(empty($arr)){
                            $arr = $value['a_diag_utama'];
                        }
                    }
                    $diagnosa = json_decode($arr);
                    $diagnosis_masuk = $diagnosa->text == '' ? ' - ' : $diagnosa->text ;
                }
            }
        }

        $print = new DocoPrint('igd-spri-kramat');
        $print->attributes = [
            '#nama#' => $resultHeader ? @$resultHeader['nama_pasien'] : '',
            '#umur#' => $resultHeader ? @$resultHeader['umur'] : '',
            '#jk#' => $resultHeader ? @$resultHeader['jenis_kelamin'] : '',
            '#no_rm#' => $resultHeader ? @$resultHeader['no_rekam_medik'] : '',
            '#no_pendaftaran#' => $resultHeader ? @$resultHeader['no_pendaftaran'] : '',
            '#diagnosa#' => isset($diagnosis_masuk) ? $diagnosis_masuk : ' - ',
            '#asal#' => isset($pulang['ruangan_nama']) ? $pulang['ruangan_nama'] : ' - ',
            '#ruangan#' => isset($pulang['ruangan_tujuan']) ? strlen($pulang['ruangan_tujuan']) < 7 ? ' - ' : $pulang['ruangan_tujuan'] : ' - ',
            '#dokter_tujuan#' => isset($pulang['nama_dokter_tujuan']) ? $pulang['nama_dokter_tujuan'] : ' - ',
            '#catatan_tindakan#' => isset($pulang['catatan_tindakan']) ? nl2br(htmlspecialchars($pulang['catatan_tindakan'])): '',
            '#tanggal_skr#' => date('d F Y'),
            '#inf_dokterdpjp#' => $resultHeader ? @$resultHeader['dokter'] : '',
            '#nama_pegawai#' => $pegawailogin_nama,
            '#timestamps#' => $waktu_cetak,
        ];
        $print->Output();
    }
}