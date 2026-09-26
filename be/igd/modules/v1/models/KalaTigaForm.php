<?php


namespace app\modules\v1\models;

use Yii;
use yii\base\Model;

class KalaTigaForm extends Model
{
    /**
     * {@inheritdoc}
     */
    public $persalinan_id;
    public $pendaftaran_id;
    public $inisiasi_menyusui; #Boolean
    public $inisiasi_menyusui_alasan;
    public $lama_kala;
    public $oksitosin_10uim; #Boolean
    public $oksitosin_10uim_menit;
    public $oksitosin_10uim_alasan;
    public $ulang_oksitosin; #Boolean
    public $ulang_oksitosin_alasan;
    public $penegangan_tali_pusat; #Boolean
    public $penegangan_tali_pusat_alasan;
    public $fundus_uteri; #Boolean
    public $fundus_uteri_alasan;
    public $plasenta_lahir_lengkap; #Boolean
    public $plasenta_lahir_lengkap_tindakan;
    public $plasenta_lahir_tidak_lahir; #Boolean
    public $plasenta_lahir_tidak_lahir_tindakan;
    public $laserisasi; #Boolean
    public $laserisasi_tempat;
    public $laserisasi_perineum; #Derajat
    public $laserisasi_perineum_alasan;
    public $laserisasi_perineum_penjahitan;
    public $laserisasi_perineum_penjahitan_alasan;
    public $anoni_uteri;
    public $anoni_uteri_tindakan;
    public $jumlah_darah_keluar;
    public $masalah_penatalaksanaan;
    public $hasil;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [["persalinan_id", "pendaftaran_id", "inisiasi_menyusui", "inisiasi_menyusui_alasan", "lama_kala", "oksitosin_10uim", "oksitosin_10uim_menit", "oksitosin_10uim_alasan", "ulang_oksitosin", "ulang_oksitosin_alasan", "penegangan_tali_pusat", "penegangan_tali_pusat_alasan", "fundus_uteri", "fundus_uteri_alasan", "plasenta_lahir_lengkap", "plasenta_lahir_lengkap_tindakan", "plasenta_lahir_tidak_lahir", "plasenta_lahir_tidak_lahir_tindakan", "laserisasi", "laserisasi_tempat", "laserisasi_perineum", "laserisasi_perineum", "laserisasi_perineum_penjahitan", "laserisasi_perineum_penjahitan_alasan", "anoni_uteri", "anoni_uteri_tindakan", "jumlah_darah_keluar", "masalah_penatalaksanaan", "hasil"], "safe"],
            [["persalinan_id", "pendaftaran_id", "inisiasi_menyusui", "inisiasi_menyusui_alasan", "lama_kala", "oksitosin_10uim", "oksitosin_10uim_menit", "oksitosin_10uim_alasan", "ulang_oksitosin", "ulang_oksitosin_alasan", "penegangan_tali_pusat", "penegangan_tali_pusat_alasan", "fundus_uteri", "fundus_uteri_alasan", "plasenta_lahir_lengkap", "plasenta_lahir_lengkap_tindakan", "plasenta_lahir_tidak_lahir", "plasenta_lahir_tidak_lahir_tindakan", "laserisasi", "laserisasi_tempat", "laserisasi_perineum", "laserisasi_perineum", "laserisasi_perineum_penjahitan", "laserisasi_perineum_penjahitan_alasan", "anoni_uteri", "anoni_uteri_tindakan", "jumlah_darah_keluar", "masalah_penatalaksanaan", "hasil"], 'default', 'value' => '-']
        ];
    }
}
