<?php

namespace Integrasi\Service\Ris;

use Yii;
use Integrasi\Components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use Integrasi\Service\Ris\Models\OrderRadiologiView;
use yii\db\Query;

class GenerateOrder extends \Integrasi\Contracts\DocoImplement
{
    public function execute()
    {
        $data_bridging = $this->data_bridging;
        try {
            $data_order = (new \yii\db\Query())
                ->select([
                    'permintaankepenunjang_t.daftartindakan_id',
                    'daftartindakan_m.daftartindakan_nama',
                    'pemeriksaanrad_m.pemeriksaanrad_kode',
                    'pemeriksaanrad_m.pemeriksaanrad_nama',
                    'pasienkirimkeunitlain_t.pegawai_id',
                    'pegawai_m.nama_pegawai',
                    'jenispemeriksaanrad_m.jenispemeriksaanrad_kode',
                    'permintaankepenunjang_t.tarif_pelayanan'
                ])
                ->from('permintaankepenunjang_t')
                ->leftJoin('pasienkirimkeunitlain_t','pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id')
                ->leftJoin('pendaftaran_t','pendaftaran_t.pendaftaran_id = pasienkirimkeunitlain_t.pendaftaran_id')
                ->leftJoin('daftartindakan_m','daftartindakan_m.daftartindakan_id = permintaankepenunjang_t.daftartindakan_id')
                ->leftJoin('pemeriksaanrad_m','pemeriksaanrad_m.daftartindakan_id = permintaankepenunjang_t.daftartindakan_id')
                ->leftJoin('pegawai_m','pegawai_m.pegawai_id = pasienkirimkeunitlain_t.pegawai_id')
                ->leftJoin('jenispemeriksaanrad_m','jenispemeriksaanrad_m.jenispemeriksaanrad_id = pemeriksaanrad_m.jenispemeriksaanrad_id')
                ->where([
                    'pasienkirimkeunitlain_t.pasienkirimkeunitlain_id'=>$data_bridging['pasienkirimkeunitlain_id'],
                    'pendaftaran_t.no_pendaftaran' => $data_bridging['no_daftar']
                ])
                ->all();
            $mapping_order = [];
            foreach ($data_order as $kd => $data_order_item) {
                $mapping_order[] = [
                    'procedure' => [
                        'procedureCode' => $data_order_item['pemeriksaanrad_kode'],
                        'procedureName' => $data_order_item['pemeriksaanrad_nama'],
                        'modalityCode' => $data_order_item['jenispemeriksaanrad_kode'],
                        'procedureFee' => $data_order_item['tarif_pelayanan'],
                    ],
                    'readingPhysician' => [
                        'radStaffCode' => $data_order_item['pegawai_id'],
                        'radStaffName' => $data_order_item['nama_pegawai']
                    ]
                ];
            }
            $data_visite = (new \yii\db\Query())
                ->select([
                    'pendaftaran_t.no_pendaftaran',
                    'pendaftaran_t.pendaftaran_id',
                    'pasien_m.pasien_id',
                    'pasien_m.nama_pasien',
                    'pasien_m.no_rekam_medik',
                    'pasien_m.tanggal_lahir',
                    'pasien_m.alamat_pasien',
                    'pendaftaran_t.pegawai_id',
                    'pegawai_m.nama_pegawai'
                ])
                ->from('pendaftaran_t')
                ->leftJoin('pasien_m','pendaftaran_t.pasien_id = pasien_m.pasien_id')
                ->leftJoin('pegawai_m','pegawai_m.pegawai_id = pendaftaran_t.pegawai_id')
                ->where(['pendaftaran_t.no_pendaftaran'=>$data_bridging['no_daftar']])
                ->one();
            $map_order = [
                'uniqueOrder' => $data_bridging['pasienkirimkeunitlain_id'],
                'uniqueVisit' => $data_visite['pendaftaran_id'],
                'orderDetail' => [$mapping_order],
                'referringPhysician' => [
                        [
                            'refPhyCode' => $data_visite['pegawai_id'],
                            'refPhyName' => $data_visite['nama_pegawai'],
                        ]
                ],
                'patient' => [
                    'patientID' => $data_visite['pasien_id'],
                    'mrn' => $data_visite['no_rekam_medik'],
                    'patientName' => $data_visite['nama_pasien'],
                    'dateOfBirth' => $data_visite['tanggal_lahir'],
                    'address' => $data_visite['alamat_pasien'],
                    'sex' => 'M',
                    'size' => '0',
                    'weight' => '0',
                    'maritalStatus' => ''
                ]
            ];
            $encode = json_encode($map_order);
            $dataEncrypt = DocoHelpers::encryptInacbg($encode, $this->signature);
            $response = $this->docoRest->post('order',[
                        'form_params' => [
                            'data' => $dataEncrypt
                        ]
                    ]);
            $response = json_decode($response->getBody(),true);
            $response = json_encode($response);
        } catch (Throwable $e) {
            return 'salah';
            $response = $e->getMessage();
        }

        return $response;
    }
}