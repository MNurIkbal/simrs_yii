<?php 

namespace Integrasi\Service\Sirs\SinkronDataBpjs;

use Yii;
use yii\helpers\ArrayHelper;
use Integrasi\Components\DocoConstants;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\Services\AsuransiPenjaminService;
use Integrasi\Service\Sirs\Models\SyKunjunganPasien;
use Integrasi\Service\Sirs\Models\Cron;
use Integrasi\Service\Sirs\Models\PenjaminAsuransi\SyInfoKlaimInacbg;
use Integrasi\Service\Sirs\Models\PenjaminAsuransi\SyKlaimInacbg;
use Integrasi\Service\Sirs\Models\SyKunjunganTagihan;

class KirimKlaimOnline extends \Integrasi\Contracts\DocoImplement
{

    public function execute()
    {   
        $tgl_awal      = $this->tgl_awal;
        $tgl_akhir     = $this->tgl_akhir;
        $jenis_rawat   = $this->jenis_rawat;
        $tipe_tanggal  = $this->tipe_tanggal;
        $ini = $this->ini;
        $reponseUpdate = [];
        $reponseRaw = [];

        $model = new SyInfoKlaimInacbg;
        $query = $model::find();  
        
        if ($tipe_tanggal && $tgl_awal) {
            $start = date('Y-m-d 00:00:00', strtotime($tgl_awal));
            $end = date('Y-m-d 23:59:59', strtotime($tgl_akhir));
            $query->andWhere(['between', 'tgl_keluar', $start, $end]);   
            $query->andWhere(['is_terkirim' => false]);
        }
        $query = $query->asArray()->all();
        $tmpResponse = [];

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
               'status' => 'finish', 
               'messageProcess' => 'Sedang mempersiapkan pengiriman data.',
               'progress' => 30,
               'process' => 'berhasil'
            ]),
        ]);
   

        foreach ($query as $key => $value) {
            if(isset($value['tgl_keluar'])) {
                $data = [
                    'metadata'=>[
                        'method'=>'send_claim',
                    ],
                    'data'=>[
                        'start_dt' => isset($value['tgl_keluar']) ? $value['tgl_keluar'] : $tgl_awal,
                        'stop_dt'  => isset($value['tgl_keluar']) ? $value['tgl_keluar'] : $tgl_awal,
                        'jenis_rawat' => $jenis_rawat,
                        'date_type'   => $tipe_tanggal,
                    ],
                ];
                
                $response = json_decode(self::restInacbgs($data, $ini), true);
                $tmpResponse[] = isset($response['metadata']['code']) ? $response['metadata']['code'] : null;
                $code = isset($response['metadata']['code']) ? $response['metadata']['code'] : null;
                $reponseRaw[] = $response;

                if($code == 200) {
                    Yii::$app->redis->executeCommand('PUBLISH', [
                        'channel' => 'export-excel:'.$this->unique_str,
                        'message' => json_encode([
                        'status' => 'finish', 
                        'messageProcess' => 'Data klaim tanggal <b>'. $value['tgl_keluar'] .' </b> Berhasil dikirim.',
                        'progress' => 40,
                        'process' => 'berhasil'
                        ]),
                    ]);
                }else{
                    Yii::$app->redis->executeCommand('PUBLISH', [
                        'channel' => 'export-excel:'.$this->unique_str,
                        'message' => json_encode([
                        'status' => 'finish', 
                        'messageProcess' => 'Data klaim tanggal <b>'. $value['tgl_keluar'] .' </b> Gagal dikirim.',
                        'progress' => 40,
                        'process' => 'gagal'
                        ]),
                    ]);
                }
            }
        }

        $valueResponse = $tmpResponse;
        if (isset($valueResponse[0])) {
            if($valueResponse[0] === 200) {
                $reponseUpdate[] = self::updatedDataKlaim($tgl_awal, $tgl_akhir);
            }
        }

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
            'status' => 'finish', 
            'messageProcess' => 'Data berhasil dikirim.',
            'progress' => 100,
            'process' => 'berhasil'
            ]),
        ]);

        return json_encode([
            'service' => 'Sirs-KirimKlaimOnline',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $reponseUpdate,
            'raw' => $reponseRaw
        ]);
    }

    private function updatedDataKlaim($tgl_awal, $tgl_akhir)
    {
        $tanggal = $tgl_awal;
        $model = new SyInfoKlaimInacbg;
        $query = $model::find();  
        $arr_nosep = [];
        $arr_kunjungan = [];

        if ($tgl_awal == $tgl_akhir) {
            $start = date('Y-m-d 00:00:00', strtotime($tanggal));
            $end = date('Y-m-d 23:59:59', strtotime($tanggal));
            $query->andWhere(['between', 'tgl_keluar', $start, $end]);   
            $query->andWhere(['is_terkirim' => false]);
        }else{
            $start = date('Y-m-d 00:00:00', strtotime($tgl_awal));
            $end = date('Y-m-d 23:59:59', strtotime($tgl_akhir));
            $query->andWhere(['between', 'tgl_keluar', $start, $end]); 
            $query->andWhere(['is_terkirim' => false]);
        }

        $query = $query->asArray()->all();

        foreach ($query as $key => $value) {
          if(isset($value['no_sep'])) {
            $nosep = $value['no_sep'];
            if (!in_array($nosep, $arr_nosep)) {
                $arr_nosep[] = $nosep;
            }
          }
        }

        $dataKunjungan = self::getKunjunganId($arr_nosep);
        foreach ($dataKunjungan as $key => $kunjungan) {
            if(isset($kunjungan['kunjungan_id'])) {
                $kunjunganid = $kunjungan['kunjungan_id'];
                if (!in_array($kunjunganid, $arr_kunjungan)) {
                    $arr_kunjungan[] = $kunjunganid;
                }
            }
        }

        if ($arr_nosep) {
            $update = SyKlaimInacbg::updateAll(['is_terkirim' => true], ['in', 'kunjungan_id', $arr_kunjungan]);
            if($update){
                return [
                    'status' => 200,
                    'title' => 'Proses Berhasil !',
                    'text' => 'Kirim Klaim (Online) berhasil! <br> Klaim yang terkirim sejumlah: '.$update.'<br> Status pengiriman kemenkes : Received'
                ];
            } else {
                return [
                    'status' => 422,
                    'title' => 'Proses Gagal !',
                    'text' => 'Kirim Klaim (Online) Gagal! <br> Terjadi kesalahan <br> Jumlah Pengiriman Klaim yang gagal : ' .( count($arr_nosep))
                ];
            }
        } else {
            return [
                'status' => 422,
                'title' => 'Proses Gagal !',
                'text' => 'Kirim Klaim (Online) Gagal! <br> Terjadi kesalahan <br> Jumlah Pengiriman Klaim yang gagal : ' .( count($arr_nosep))
            ];
        }
    }

    private static function getKunjunganId($arr_nosep)
    {
        $connection = Yii::$app->db;
        $tmpNosep = implode("','", $arr_nosep);
        $result = $connection->createCommand("SELECT kunjungan_id FROM sy_kunjungan WHERE nosep IN ('$tmpNosep')")->queryAll();
        return $result;
    }

    public static function restInacbgs($postdata, $ini)
    {
        $key = isset($ini['inacbg']['bpjs_key']) ? $ini['inacbg']['bpjs_key'] : DocoConstants::BPJS_KEY;
        $json_request = json_encode($postdata);
        $inacbgsent = DocoHelpers::encryptInacbg($json_request, $key);

        $header = isset($ini['inacbg']['header']) ? [$ini['inacbg']['header']] : ["Content-Type:application/x-www-form-urlencoded"];
        $url = isset($ini['inacbg']['url']) ? $ini['inacbg']['url'] : "http://192.168.200.250/e-klaim/ws.php";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $inacbgsent);

        $response = curl_exec($ch);
        return DocoHelpers::decryptInacbg($response, $key, true);
    }

}