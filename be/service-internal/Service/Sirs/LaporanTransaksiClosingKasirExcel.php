<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Models\ClosingKasirView;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;

class LaporanTransaksiClosingKasirExcel extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {   
        $data = $this->loadData($this->filter);
        $cacheFiles = Yii::$app->cacheFiles;
        $row = $tmpCache = [];
        $no = 1;
        $prefix = 0;
        foreach ($data as $value) 
        {   
            $tgl_pembayaran = !empty($value['tglbuktibayar']) ? date('d-M-Y H:i:s', strtotime($value['tglbuktibayar'])) : '';
            $jml_pembayaran = isset($value['jmlpembayaran']) ? $value['jmlpembayaran'] : 0;
            $no_pembayaran = isset($value['no_pembayaran']) ? $value['no_pembayaran'] : '';
            $no_pendaftaran = isset($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '';
            $pembayaran_tunai = isset($value['pembayaran_tunai']) ? $value['pembayaran_tunai'] : 0;
            $pembayaran_nontunai = isset($value['pembayaran_nontunai']) ? $value['pembayaran_nontunai'] : 0;
            $pembayaran_penjamin = isset($value['pembayaran_penjamin']) ? $value['pembayaran_penjamin'] : 0;
            $penjamin_nama = isset($value['penjamin_nama']) ? $value['penjamin_nama'] : '';
            $keterangan = isset($value['keterangan']) ? $value['keterangan'] : '-';
            $tmp[1]  = $no;
            $tmp[2]  = $tgl_pembayaran;
            $tmp[3]  = $no_pembayaran;
            $tmp[4]  = $no_pendaftaran;
            $tmp[5]  = $this->getNamaPasien($value);
            $tmp[6]  = $this->getTransaksi($value);
            $tmp[7]  = $keterangan;
            $tmp[8]  = $penjamin_nama;
            $tmp[9]  = $this->getCaraBayarNonTunai($value);
            $tmp[10]  = $jml_pembayaran;
            $tmp[11]  = $pembayaran_tunai;
            $tmp[12]  = $pembayaran_nontunai;
            $tmp[13]  = $pembayaran_penjamin;

            $tmpCache[] = $tmp;
            if (($no%50) == 0) 
            {
                Yii::$app->redis->executeCommand('PUBLISH', [
                   'channel' => 'export-excel:'.$this->unique_str,
                   'message' => json_encode(['unique_process' => $this->unique_str]),
                ]);
                $cacheFiles->set($this->unique_str .'-'. $prefix, $tmpCache);
                $prefix++;
                $tmpCache = [];
            }
            $no++;
        }

        $cacheFiles->set($this->unique_str .'-'. $prefix, $tmpCache);
        
        return json_encode([
            'service' => 'Sirs-LaporanTransaksiClosingKasir',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str,
            'count' => count($data)
        ]);
    }

    private function rupiahDisplay($val) 
    {
        return !empty($val) ? DocoHelpers::formatNumber($val) : DocoHelpers::formatNumber('0');
    }

    private function getNamaPasien($val) 
    {
        $nama     = !empty($val['nama_pasien']) ? $val['nama_pasien'] : '-';
        $noRekamMedik = !empty($val['no_rekam_medik']) ? $val['no_rekam_medik'] : '-';
        
        return $nama.' / '.$noRekamMedik;
    }

    private function getCaraBayar($val) 
    {
        $carabayar_nama = !empty($val['carabayar_nama']) ? $val['carabayar_nama'] : '';
        $penjamin       = !empty($val['penjamin_nama']) ? $val['penjamin_nama'] : '';

        return empty($carabayar_nama) && empty($penjamin) ? '' : $carabayar_nama.' / '.$penjamin;
    }

    private function getTransaksi($val){
        $jenis_transaksi = isset($val['jenis']) ? strtolower($val['jenis']) :'-';
        return ucwords(str_replace('_',  ' ', $jenis_transaksi));
    }

    private function getKeterangan($value){
        $strmetodebayar = "";
        if(!empty($value['additional_nontunai'])){
            $additional_nontunai = json_decode($value['additional_nontunai'], true);
            if(is_array($additional_nontunai)){
                foreach($additional_nontunai as $row){
                    $no_kartu =  !empty($row['no_kartu']) ? ($row['no_kartu']) : "";
                    $strnokartu = !empty($no_kartu) ? "(".$no_kartu.")" : "";
                    $metode_bayar = !empty($row['metode_bayar']) ? $row['metode_bayar'] : "" ;
                    $metodebayarkartu = $metode_bayar . " "  . $strnokartu;
                    $strmetodebayar = (empty($strmetodebayar) ? " /  " . $metodebayarkartu : $strmetodebayar . ', ' . $metodebayarkartu );
                }
            }
        }

        $penjamin_nama = !empty($value['penjamin_nama']) ? $value['penjamin_nama'] : "";
        $strtunai = (!empty($value['pembayaran_tunai']) && $value['pembayaran_tunai'] > 0 ? "Tunai" : "");
        $strnontunai = (!empty($value['pembayaran_nontunai']) && $value['pembayaran_nontunai'] > 0 ? "Non Tunai". $strmetodebayar : "");
        $nilai_tunai = isset($value['pembayaran_tunai']) ? $value['pembayaran_tunai']:0;
        $nilai_nontunai = isset($value['pembayaran_nontunai']) ? $value['pembayaran_nontunai']:0;
        $operatortunainontunai = ($nilai_tunai > 0 && $nilai_nontunai > 0) ? " dan " : "";
        $strtunainontunai = " / " . $strtunai . $operatortunainontunai . $strnontunai;

        $keterangan = $penjamin_nama . $strtunainontunai;

        return $keterangan;
    }

    public function getCaraBayarNonTunai($data){
        // Detail Cara Bayar Non Tunai
        $caraBayarNonTunai = '-';
        if(!empty($data['additional_nontunai'])) {
            $json = json_decode($data['additional_nontunai'], true);
            $arrNonTunai = array();
            foreach ($json as $jsonRow => $valJson) {
                $arrNonTunai[] = $valJson['metode_bayar'].' ('.$valJson['nama_edc'].' || '.$valJson['no_kartu'].')';
            }
            $parsingNonTunai = implode(', ', $arrNonTunai);
            $caraBayarNonTunai = $parsingNonTunai;
        }

        return $caraBayarNonTunai;
    }

    public function loadData($getData)
    {
        $model = new ClosingKasirView;
        $query = $model::find();

        $userId = $getData['pegawai_id'];
        $query->andWhere(['pegawai1_id' => $userId]);
        $query->andWhere(['closingkasir_id' => null]); 

        return $query->asArray()->all();
    }
}