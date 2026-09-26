<?php

/**
 * 
 * @author : Erlangga (librantara.erlangga@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Picqer\Barcode\BarcodeGeneratorPNG;

use Doco\models\pendaftaran\PasienV;
use Doco\models\pendaftaran\PasiencetakanV;
use Doco\models\KonfigSystem;
use yii\helpers\Url;
use Doco\models\Lookup;

class CetakDataPasienProcess extends \Doco\components\DocoBaseProcessExtension 
{

    protected function processFlow()
    {
        # code...
            $jenisIdentitas = '';
            $hubunganKeluarga = '-';
            $request = Yii::$app->request;
            $id = $request->get('id');
            $model = new PasiencetakanV;
            $query = $model::find();
            $query->where(['pasien_id'=>$id]);
            $data = $query->asArray()->one();

            $print = new DocoPrint();

            $skr = time(); 
            $tgl_lahir = strtotime($data['tanggal_lahir']);
            $datediff = $skr - $tgl_lahir;

            $datediff = round($datediff / (60 * 60 * 24));

            $data['umur'] = DocoHelpers::convertToDay($datediff);
            $foto = '';
            if(!empty($data['photopasien'])){
                $foto = Url::base(true) . '/media/img/pasien/' . $data['photopasien'];
            }

            if (!empty($data['additional_pasien'])) {
                $additionalPasien = json_decode($data['additional_pasien']);

                foreach ($additionalPasien as $key => $value) {
                    $jenisIdentitas .= '(';

                    if (isset($value->jenisidentitas) && $value->jenisidentitas != '' ) {
                        $getJenisIdentitas = $this->getJenisIdentitas($value->jenisidentitas);
                        if($getJenisIdentitas == FALSE){
                            $jenisIdentitas.= '';
                        }else{
                            $jenisIdentitas .= $this->getJenisIdentitas($value->jenisidentitas)->lookup_name;
                        }
                    }

                    $jenisIdentitas .= ' - ';

                    if (isset($value->no_identitas_pasien)) {
                        $jenisIdentitas .= $value->no_identitas_pasien;
                    }

                    $jenisIdentitas .= ')';
                }
            } else if (!empty($data['jenisidentitas'])) {
                $jenisIdentitas .= '(' . $data['identitas'] . ' - ' . $data['no_identitas_pasien'] . ')';
            }

            // if ($data['hubungankeluarga'] != '') {
            //     $hubunganKeluarga = Lookup::findOne($data['hubungankeluarga'])->lookup_name;
            // }

            $print->attributes = [
                '#no_rekam_medik#' => $data['no_rekam_medik'],
                '#no_identitas_pasien#' => $jenisIdentitas,
                '#nama_pasien#' => $data['nama_pasien'],
                '#nama_panggilan#' => $data['nama_bin'],
                '#tempat_lahir#' => $data['tempat_lahir'],
                '#tanggal_lahir#' => $data['tanggal_lahir'],
                '#jenis_kelamin#' => $data['jenis_kelamin'],
                '#status_perkawinan#' => $data['status_perkawinan'],
                '#nama_ibu#' => $data['nama_ibu'],
                '#nama_ayah#' => $data['nama_ayah'],
                '#alamat_pasien#' => $data['alamat_pasien'],
                '#rt#' => $data['rt'],
                '#rw#' => $data['rw'],
                '#nomor_pasien#' => isset($data['no_mobile_pasien']) ? $data['no_mobile_pasien'] : $data['no_telepon_pasien'],
                '#propinsi_nama#' => $data['propinsi_nama'],
                '#kabupaten_nama#' => $data['kabupaten_nama'],
                '#kecamatan_nama#' => $data['kecamatan_nama'],
                '#kelurahan_nama#' => $data['kelurahan_nama'],
                '#no_telepon_pasien#' => $data['no_telepon_pasien'],
                '#pekerjaan_nama#' => $data['pekerjaan_nama'],
                '#warganegara#' => $data['warganegara'],
                '#pendidikan#' => $data['pendidikan_nama'],
                '#suku#' => $data['suku_nama'],
                '#anakke#' => $data['anakke'],
                '#no_mobile_pasien#' => $data['no_mobile_pasien'],
                '#saudara#' => $data['jumlah_bersaudara'],
                '#agama_pasien#' => $data['agama_pasien'],
                '#identitas#' => $data['identitas'],
                '#golongan_darah#' => $data['golongan_darah'],
                '#umur#' =>  $data['umur']['tahun'].' Tahun '.$data['umur']['bulan'].' Bulan '.$data['umur']['hari'].' Hari',
                '#foto#' =>  $foto,
                '#alergi#' =>  $data['alergi'],
                '#penanggungjawab_nama#' =>  $data['penanggungjawab_nama'],
                // '#hubungankeluarga#' => $hubunganKeluarga,
                '#hubungankeluarga#' => $data['hubungankeluarga'],
                '#penanggungjawab_alamat#' => $data['penanggungjawab_alamat'],
                '#penanggungjawab_notelp#' => $data['penanggungjawab_notelp'],
                '#carabayar_nama#' => $data['carabayar_nama'],
                '#penjamin_nama#' => $data['penjamin_nama'],
                '#nokartuasuransi#' => $data['nokartuasuransi'],
                '#kode_pos#' => $data['kode_pos']
            ];
            // Print output
            $print->Output();
    }

    protected function getJenisIdentitas($id){
        return Lookup::findOne($id);
    }
}
