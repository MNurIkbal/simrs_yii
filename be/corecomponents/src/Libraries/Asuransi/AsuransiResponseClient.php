<?php

namespace Doco\Libraries\Asuransi;

class AsuransiResponseClient
{
    public function responseClient(array $data)
    {
        return [
            "data" => $data,
            "status" => [
                "code" => 200,
                "message" => "Success"
            ]
        ];
    }

    /**
     * Wraps the given data into a standardized response format.
     *
     * @param array $data The data to be wrapped.
     * @return array The wrapped data in a standardized response format.
     */
    public static function wrappingResponse($data = [])
    {
        return [
            'status' => isset($data['status']) ? $data['status'] : null,
            "data" => isset($data['data']) ? $data['data'] : [],
            "message" => isset($data['message']) ? $data['message'] : null,
            "biaya" => isset($data['biaya']) ? $data['biaya'] : [],
            "benefit" => isset($data['benefit']) ? $data['benefit'] : [],
            "kode_item" => isset($data['kode_item']) ? $data['kode_item'] : null
        ];
    }

    /**
     * Must have transform $data
     * @param array $data
     */
    public static function responsePendaftaran($data = [], $limitBenefit = [])
    {
        $dataPeserta = [
            "noklaim" => isset($data['noklaim']) ? $data['noklaim'] : null,
            "namapeserta" => isset($data['namapeserta']) ? $data['namapeserta'] : null,
            "tanggallahir" => isset($data['tanggallahir']) ? $data['tanggallahir'] : null,
            "nokartu" => isset($data['nokartu']) ? $data['nokartu'] : null,
            "nopolis" => isset($data['nopolis']) ? $data['nopolis'] : null,
            "nobpjs" => isset($data['nobpjs']) ? $data['nobpjs'] : null,
            "nosep" => isset($data['nosep']) ? $data['nosep'] : null,
            "nomorrujukan" => isset($data['nomorrujukan']) ? $data['nomorrujukan'] : null,
            "planid" => isset($data['planid']) ? $data['planid'] : null,
            "masapolis" => isset($data['masapolis']) ? $data['masapolis'] : null,
            "namaperusahaan" => isset($data['namaperusahaan']) ? $data['namaperusahaan'] : null,
            "namapenjamin" => isset($data['namapenjamin']) ? $data['namapenjamin'] : null,
            "tanggalmasuk" => isset($data['tanggalmasuk']) ? $data['tanggalmasuk'] : null,
            "tanggalkeluar" => isset($data['tanggalkeluar']) ? $data['tanggalkeluar'] : null,
            "hakkamar" => isset($data['hakkamar']) ? $data['hakkamar'] : null,
            "hakicu" => isset($data['hakicu']) ? $data['hakicu'] : null,
            "nosuratjaminan" => isset($data['nosuratjaminan']) ? $data['nosuratjaminan'] : null,
            "namapegawai" => isset($data['namapegawai']) ? $data['namapegawai'] : null,
            "namabenefit" => isset($data['namabenefit']) ? $data['namabenefit'] : null,
            "kodediagnosa" => isset($data['kodediagnosa']) ? $data['kodediagnosa'] : null,
            "keterangan" => isset($data['keterangan']) ? $data['keterangan'] : null,
            "catatanTC1" => isset($data['catatanTC1']) ? $data['catatanTC1'] : null,
            "catatanTC2" => isset($data['catatanTC2']) ? $data['catatanTC2'] : null,
            "catatanTC3" => isset($data['catatanTC3']) ? $data['catatanTC3'] : null,
            "catatanTC4" => isset($data['catatanTC4']) ? $data['catatanTC4'] : null,
            "catatanTC5" => isset($data['catatanTC5']) ? $data['catatanTC5'] : null,
            "catatanTC6" => isset($data['catatanTC6']) ? $data['catatanTC6'] : null,
            "catatanTC7" => isset($data['catatanTC7']) ? $data['catatanTC7'] : null,
            "catatanTC8" => isset($data['catatanTC8']) ? $data['catatanTC8'] : null,
            "catatanTC9" => isset($data['catatanTC9']) ? $data['catatanTC9'] : null,
            "catatanTC10" => isset($data['catatanTC10']) ? $data['catatanTC10'] : null,
            "statusrujukan" => isset($data['statusrujukan']) ? $data['statusrujukan'] : "N",
            "statusklaim" => isset($data['statusklaim']) ? $data['statusklaim'] : null,
            "notransaksiprovider" => isset($data['notransaksiprovider']) ? $data['notransaksiprovider'] : null,
            "inacbgscode" => isset($data['inacbgscode']) ? $data['inacbgscode'] : null,
            "inacbgsamount" => isset($data['inacbgsamount']) ? $data['inacbgsamount'] : null
        ];

        $limitSubBenefit = self::responseSubBenefit($limitBenefit);

        return [
            'dataPeserta' => !empty($data) ? $dataPeserta : [],
            'limitSubBenefit' => empty($limitBenefit) ? $limitBenefit : $limitSubBenefit
        ];
    }

    public static function responseBenefit($data = [])
    {
        return [
            'kodebenefit' => isset($data['kodebenefit']) ? $data['kodebenefit'] : null,
            'namabenefit' => isset($data['namabenefit']) ? $data['namabenefit'] : null,
            'planid' => isset($data['planid']) ? $data['planid'] : null
        ];
    }

    public static function responseEligible($data = [])
    {

        $dataPeserta = [
            "nokartu" => isset($data['Data']['nokartu']) ? $data['Data']['nokartu'] : null,
            "memberid" => isset($data['Data']['memberid']) ? $data['Data']['memberid'] : null,
            "namapeserta" => isset($data['Data']['namapeserta']) ? $data['Data']['namapeserta'] : null,
            "nomorbpjs" => isset($data['Data']['nomorbpjs']) ? $data['Data']['nomorbpjs'] : null,
            "jeniskelamin" => isset($data['Data']['jeniskelamin']) ? $data['Data']['jeniskelamin'] : null,
            "tanggallahir" => isset($data['Data']['tanggallahir']) ? $data['Data']['tanggallahir'] : null,
            "hubungankeluarga" => isset($data['Data']['hubungankeluarga']) ? $data['Data']['hubungankeluarga'] : null,
            "namaperusahaan" => isset($data['Data']['namaperusahaan']) ? $data['Data']['namaperusahaan'] : null,
            "pesertavip" => isset($data['Data']['pesertavip']) ? $data['Data']['pesertavip'] : null,
            "namapenjamin" => isset($data['Data']['namapenjamin']) ? $data['Data']['namapenjamin'] : null,
            "nomorpolis" => isset($data['Data']['nomorpolis']) ? $data['Data']['nomorpolis'] : null,
            "tglmulaipolis" => isset($data['Data']['tglmulaipolis']) ? $data['Data']['tglmulaipolis'] : null,
            "tglberakhirpolis" => isset($data['tglberakhirpolis']) ? $data['tglberakhirpolis'] : null,
            "phone" => isset($data['Data']['phone']) ? $data['Data']['phone'] : null,
            "email" => isset($data['Data']['email']) ? $data['Data']['email'] : null
        ];

        $dataBenefit = [];
        if (isset($data['Benefit'])) {
            foreach ($data['Benefit'] as $key => $value) {
                $dataBenefit[] = self::responseBenefit($value);
            }
        }

        return [
            'dataPeserta' => $dataPeserta,
            'dataBenefit' => $dataBenefit
        ];
    }

    public static function responsePembatalan($data = [])
    {
        return false;
    }

    public static function responseDischarging($data = [])
    {
        return false;
    }

    public static function responseSubBenefit($data = [])
    {
        $subBenefit = [];

        if (! empty($data)) {
            foreach ($data as $key => $value) {
                $subBenefit[] = [
                    "kodesubbenefit" => $value['kodesubbenefit'],
                    "namasubbenefit" => $value['namasubbenefit'],
                    "batasan" => $value['batasan'],
                ];
            }
        }

        return $subBenefit;
    }
}
