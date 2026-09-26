<?php

/**
 * @Author: Sigit
 * @Date:   2019-02-07 17:29:27
 */

namespace app\modules\ranap\models;

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
            [["pendaftaran_id", "inisiasi_menyusui", "lama_kala", "ulang_oksitosin", "penegangan_tali_pusat", "fundus_uteri", "plasenta_lahir_lengkap", "plasenta_lahir_tidak_lahir", "laserisasi", "laserisasi_perineum", "anoni_uteri"], "required"],
            [["inisiasi_menyusui", "oksitosin_10uim", "ulang_oksitosin", "penegangan_tali_pusat", "fundus_uteri", "plasenta_lahir_lengkap", "plasenta_lahir_tidak_lahir", "laserisasi"], "boolean"],
            [["persalinan_id", "pendaftaran_id", "inisiasi_menyusui", "inisiasi_menyusui_alasan", "lama_kala", "oksitosin_10uim", "oksitosin_10uim_menit", "oksitosin_10uim_alasan", "ulang_oksitosin", "ulang_oksitosin_alasan", "penegangan_tali_pusat", "penegangan_tali_pusat_alasan", "fundus_uteri", "fundus_uteri_alasan", "plasenta_lahir_lengkap", "plasenta_lahir_lengkap_tindakan", "plasenta_lahir_tidak_lahir", "plasenta_lahir_tidak_lahir_tindakan", "laserisasi", "laserisasi_tempat", "laserisasi_perineum", "laserisasi_perineum", "laserisasi_perineum_penjahitan", "laserisasi_perineum_penjahitan_alasan", "anoni_uteri", "anoni_uteri_tindakan", "jumlah_darah_keluar", "masalah_penatalaksanaan", "hasil"], "safe"],
            [["lama_kala", "jumlah_darah_keluar"], "integer"],
            [['persalinan_id', "inisiasi_menyusui_alasan", "oksitosin_10uim_alasan", "ulang_oksitosin_alasan", "penegangan_tali_pusat_alasan", "fundus_uteri_alasan", "laserisasi_perineum_penjahitan_alasan"], 'default', 'value' => null],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            "persalinan_id" => Yii::t("fe", "Persalinan ID"),
            "pendaftaran_id" => Yii::t("fe", "Pendaftaran ID"),
            "inisiasi_menyusui" => Yii::t("fe", "Inisiasi Menyusui Dini"),
            "inisiasi_menyusui_alasan" => Yii::t("fe", "Alasan Inisiasi Menyusui Dini"),
            "lama_kala" => Yii::t("fe", "Lama Kala"),
            "oksitosin_10uim" => Yii::t("fe", "Pemberian Oksitosin 10 U im"),
            "oksitosin_10uim_menit" => Yii::t("fe", "Pemberian Oksitosin 10 U im menit"),
            "oksitosin_10uim_alasan" => Yii::t("fe", "Pemberian Oksitosin 10 U Alasan"),
            "ulang_oksitosin" => Yii::t("fe", "Pemberian Ulang Oksitosin(2x)"),
            "ulang_oksitosin_alasan" => Yii::t("fe", "Pemberian Ulang Oksitosin(2x) Alasan"),
            "penegangan_tali_pusat" => Yii::t("fe", "Penegangan Tali Pusat Terkendali"),
            "penegangan_tali_pusat_alasan" => Yii::t("fe", "Penegangan Tali Pusat Terkendali Alasan"),
            "fundus_uteri" => Yii::t("fe", "Masase Fundus Uteri"),
            "fundus_uteri_alasan" => Yii::t("fe", "Masase Fundus Uteri Alasan"),
            "plasenta_lahir_lengkap" => Yii::t("fe", "Plasenta Lahir Lengkap"),
            "plasenta_lahir_lengkap_tindakan" => Yii::t("fe", "Plasenta Lahir Lengkap Tindakan"),
            "plasenta_lahir_tidak_lahir" => Yii::t("fe", "Plasenta Tidak Lahir"),
            "plasenta_lahir_tidak_lahir_tindakan" => Yii::t("fe", "Plasenta Tidak Lahir Tindakan"),
            "laserisasi" => Yii::t("fe", "Laserisasi"),
            "laserisasi_tempat" => Yii::t("fe", "Tempat Laseri"),
            "laserisasi_perineum" => Yii::t("fe", "Laserisasi Panerium"),
            "laserisasi_perineum_penjahitan" => Yii::t("fe", "Laserisasi Perineum Penjahitan"),
            "laserisasi_perineum_penjahitan_alasan" => Yii::t("fe", "Laserisasi Perineum Penjahitan Alasan"),
            "anoni_uteri" => Yii::t("fe", "Atoni Uteri"),
            "anoni_uteri_tindakan" => Yii::t("fe", "Atoni Uteri Tindakan"),
            "jumlah_darah_keluar" => Yii::t("fe", "Jumlah Darah Keluar"),
            "masalah_penatalaksanaan" => Yii::t("fe", "Masalah dan Penatalaksanaan"),
            "hasil" => Yii::t("fe", "Hasil"),
        ];
    }
}
