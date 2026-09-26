<?php

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Service\Sirs\Models\PasienV;
use Integrasi\Contracts\DocoImplement;
use Integrasi\Components\DocoConstants;

class ListDataPendaftaranPasienExport extends DocoImplement
{
    public function execute()
    {
        $cacheFiles = Yii::$app->cacheFiles;
        $filter = $this->filter;

        $model = new PasienV;
        $query = $model::find()
            ->select([
                'pasien_id',
                'no_rekam_medik',
                'tgl_rekam_medik',
                'jenis_kelamin',
                "CONCAT(nama_depan,'',nama_pasien) AS nama_pasien",
                'tanggal_lahir',
                'no_identitas_pasien AS nik',
                'alamat_pasien',
                'propinsi_nama',
                'propinsi_id',
                'kabupaten_nama',
                'kabupaten_id',
                'kecamatan_nama',
                'kecamatan_id',
                'additional_pasien',
                'no_mobile_pasien'
            ]);

        $query = $this->populateFilter($query, $filter);

        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
            'message' => json_encode([
                'status' => 'finish',
                'messageProcess' => 'Melakukan proses pencarian data pasien ...',
                'progress' => 10
            ]),
        ]);

        $data = $query->asArray()->all();
        foreach($data as $idx => $value) {
            if (!empty($value['additional_pasien'])) {
                $pasienAdds = json_decode($value['additional_pasien'], true);
                $nikPasien = null;
                if (!empty($pasienAdds) && isset($pasienAdds[0]) && is_array($pasienAdds[0])) {
                    foreach($pasienAdds as $adds) {
                        if ($adds['jenisidentitas'] == DocoConstants::IDENTITAS_KTP) {
                            $nikPasien = $adds['no_identitas_pasien'];
                        }
                    }
                }
                $data[$idx]['nik'] = strval($nikPasien) . ' ';
            } else {
                $data[$idx]['nik'] = null;
            }
            unset($data[$idx]['additional_pasien']);
        }

        $cacheFiles->set($this->unique_str .'-data', $data);
        $cacheFiles->set($this->unique_str .'-filter', $filter);
        return json_encode([
            'service' => 'Sirs-ListDataPendaftaranPasienExport',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $this->unique_str
        ]);
    }

    protected function populateFilter($query, $request)
    {
        $advancedFilters = isset($request['advanced-filter']) ? $request['advanced-filter'] : [];
        if (array_key_exists('no_rekam_medik', $advancedFilters)) {
            $no_rekam_medik = $advancedFilters['no_rekam_medik'];
            $query->andWhere(['no_rekam_medik' => $no_rekam_medik]);
            unset($advancedFilters['no_rekam_medik']);
        }

        if (array_key_exists('nama_pasien', $advancedFilters)) {
            $nama_pasien = $advancedFilters['nama_pasien'];
            $query->andWhere(['like', 'LOWER(nama_pasien)', $nama_pasien])
            ->orWhere(['ilike', 'no_mobile_pasien', $nama_pasien]);
            unset($advancedFilters['nama_pasien']);
        }

        if (array_key_exists('jenis_kelamin', $advancedFilters)) {
            $jenis_kelamin = $advancedFilters['jenis_kelamin'];
            $query->andWhere(['jeniskelamin' => $jenis_kelamin]);
            unset($advancedFilters['jenis_kelamin']);
        }

        if (array_key_exists('alamat_pasien', $advancedFilters)) {
            $alamat_pasien = $advancedFilters['alamat_pasien'];
            $query->andWhere(['ilike', 'alamat_pasien', $alamat_pasien]);
            unset($advancedFilters['alamat_pasien']);
        }

        if (array_key_exists('propinsi_nama', $advancedFilters)) {
            $propinsi_nama = $advancedFilters['propinsi_nama'];
            $query->andWhere(['=', 'propinsi_id', $propinsi_nama]);
            unset($advancedFilters['propinsi_nama']);
        }

        if (array_key_exists('kabupaten_nama', $advancedFilters)) {
            $kabupaten_nama = $advancedFilters['kabupaten_nama'];
            $query->andWhere(['=', 'kabupaten_id', $kabupaten_nama]);
            unset($advancedFilters['kabupaten_nama']);
        }

        if (array_key_exists('kecamatan_nama', $advancedFilters)) {
            $kecamatan_nama = $advancedFilters['kecamatan_nama'];
            $query->andWhere(['=', 'kecamatan_id', $kecamatan_nama]);
            unset($advancedFilters['kecamatan_nama']);
        }

        if (array_key_exists('petugas', $advancedFilters)) {
            $petugas = $advancedFilters['petugas'];
            $query->andWhere(['ilike', 'petugas_nama', $petugas]);
            unset($advancedFilters['petugas']);
        }

        if (array_key_exists('no_identitas_pasien', $advancedFilters)) {
            $petugas = $advancedFilters['no_identitas_pasien'];
            $query->andWhere(['=', 'no_identitas_pasien', $petugas]);
            unset($advancedFilters['no_identitas_pasien']);
        }

        if (array_key_exists('nopeserta_bpjs', $advancedFilters)) {
            $petugas = $advancedFilters['nopeserta_bpjs'];
            $query->andWhere(['=', 'nopeserta_bpjs', $petugas]);
            unset($advancedFilters['nopeserta_bpjs']);
        }

        if (array_key_exists('tanggal_lahir', $advancedFilters)) {
            $tanggal = $advancedFilters['tanggal_lahir'];
            $startDate = date('Y-m-d 23:59:59', strtotime($tanggal));
            $query->andWhere(['=', 'date(tanggal_lahir)', $startDate]);
            unset($advancedFilters['tanggal_lahir']);
        }

        return $query;
    }
}
