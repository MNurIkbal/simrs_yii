<?php

namespace app\modules\v1\services;

use app\modules\v1\services\Contracts\BpjsInterface;

use yii\helpers\ArrayHelper;
use yii\base\DynamicModel;
use yii\db\Expression;

use Doco\components\DocoHelpers;
use Doco\components\DocoMessages;
use Doco\components\DocoConstants;
use Doco\components\constans\BpjsConstans;

use app\modules\v1\models\Bpjs;
use app\modules\v1\models\Pasien;

class BpjsService implements BpjsInterface
{
    public $model;
    
    public function __construct()
    {
        $this->model = new Bpjs;
    }

    /**
     * @method get history pelayanan peserta bpjs
     * @param string $noKartu
     * @return array
     * @todo add unit testing
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function historyPelayanan($noKartu) 
    {
        $tglmulai = date('Y-m-d', strtotime('-89 days'));
        $tglselesai = date('Y-m-d');

        $params = [
            'noKartu'=>$noKartu,
            'tglMulai'=>$tglmulai,
            'tglAkhir'=>$tglselesai,
        ];
        $result = $this->model->detailHistoryBpjs($params);
        return $result;
    }

    /**
     * @method pencarian pasien dari data bpjs by param nama,tgllahir dan jenis kelamin
     * @param array $peserta
     * @return array
     * @todo add unit testing
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function cariPasienBpjsByNama($peserta)
    {
        $qPasien = [];
        $nama = ArrayHelper::getValue($peserta, 'nama');
        $tglLahir = ArrayHelper::getValue($peserta, 'tglLahir');
        $jk = ArrayHelper::getValue($peserta, 'sex');

        $validator = DynamicModel::validateData([
            'nama' => $nama,
            'tanggal_lahir' => $tglLahir,
            'jenis_kelamin' => $jk
        ], [
            [['nama', 'tanggal_lahir', 'jenis_kelamin'], 'required']
        ]);

        if($validator->hasErrors()) return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, ['data' => $validator->errors]);

        $jk = (strtoupper($jk) == BpjsConstans::JENIS_KELAMIN_PESERTA_LAKI) ? DocoConstants::VAR_LK : DocoConstants::VAR_PR;

        $qPasien = Pasien::find()->select([
            'nama_pasien',
            'no_rekam_medik'
        ])->where([
            'tanggal_lahir' => $tglLahir,
            'jeniskelamin' => $jk
        ])->andWhere([
            'like', 'LOWER(nama_pasien)', strtolower($nama)
        ])->asArray()->one();

        return $qPasien;
    }

    /**
     * @method pencarian pasien dari data bpjs by mr
     * @param array $peserta
     * @return array
     * @todo add unit testing
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function cariPasienBpjsByRm($peserta)
    {
        $qPasien = [];
        $mr = ArrayHelper::getValue($peserta, 'mr');

        $validator = DynamicModel::validateData([
            'no_rekam_medik' => $mr,
        ], [
            [['no_rekam_medik'], 'required']
        ]);

        if($validator->hasErrors()) return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, ['data' => $validator->errors]);

        $qPasien = Pasien::find()->select([
            'nama_pasien',
            'no_rekam_medik'
        ])->where([
            'no_rekam_medik' => $mr
        ])->asArray()->one();

        return $qPasien;
    }

    /**
     * @method pencarian pasien dari data bpjs by nokartu
     * @param array $peserta
     * @return array
     * @todo add unit testing
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function cariPasienBpjsByNokartu($peserta)
    {
        $qPasien = [];
        $noka = ArrayHelper::getValue($peserta, 'noKartu');

        $validator = DynamicModel::validateData([
            'nokartuasuransi' => $noka,
        ], [
            [['nokartuasuransi'], 'required']
        ]);

        if($validator->hasErrors()) return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, ['data' => $validator->errors]);

        $qPasien = (new Pasien)->getPasienBpjs($noka);
        return $qPasien;
    }

    /**
     * @method pencarian pasien dari data bpjs by NIK
     * @param array $peserta
     * @return array
     * @todo add unit testing
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function cariPasienBpjsByNik($peserta)
    {
        $qPasien = [];
        $noktp = ArrayHelper::getValue($peserta, 'nik');

        $validator = DynamicModel::validateData([
            'no_identitas_pasien' => $noktp,
        ], [
            [['no_identitas_pasien'], 'required']
        ]);

        if($validator->hasErrors()) return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, ['data' => $validator->errors]);

        $qPasien = Pasien::find()->select([
            'nama_pasien',
            'no_rekam_medik'
        ])
        ->where([
            'no_identitas_pasien' => $noktp
        ])
        ->orWhere(new Expression('additional_pasien::text ILIKE '."'%$noktp%'".''))
        ->asArray()->one();

        return $qPasien;
    }

    public function cariRujukanByNoKartu($noka, $type)
    {
        return $this->model->cariRujukanPeserta($noka, false, $type);
    }

    public function cariListRujukanByNoKartu($noka, $type)
    {
        return $this->model->cariRujukanPeserta($noka, true, $type);
    }

    public function cariDataRencanaKontrol($bulan, $tahun, $nokartu, $filter)
    {
        return $this->model->cariDataRencanaKontrol($bulan, $tahun, $nokartu, $filter);
    }
}