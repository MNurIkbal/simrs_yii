<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "sensuspasienranap_r".
 *
 * @property int $id
 * @property int $ruangan_id
 * @property int $kelaspelayanan_id
 * @property string $tgl_sensus
 * @property int $pasien_awal
 * @property int $pasien_masuk
 * @property int $pasien_pindahan
 * @property int $pasien_keluarhidup
 * @property int $pasien_keluardipindahkan
 * @property int $pasien_keluarmeninggalkur48
 * @property int $pasien_keluarmeninggalleb48
 * @property int $pasien_akhir
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class SensuspasienranapR extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sensuspasienranap_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'kelaspelayanan_id', 'pasien_awal', 'pasien_masuk', 'pasien_pindahan', 'pasien_keluarhidup', 'pasien_keluardipindahkan', 'pasien_keluarmeninggalkur48', 'pasien_keluarmeninggalleb48', 'pasien_akhir', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'kelaspelayanan_id', 'pasien_awal', 'pasien_masuk', 'pasien_pindahan', 'pasien_keluarhidup', 'pasien_keluardipindahkan', 'pasien_keluarmeninggalkur48', 'pasien_keluarmeninggalleb48', 'pasien_akhir', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by','id'], 'integer'],
            [['tgl_sensus', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ruangan_id' => 'Ruangan ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'tgl_sensus' => 'Tgl Sensus',
            'pasien_awal' => 'Pasien Awal',
            'pasien_masuk' => 'Pasien Masuk',
            'pasien_pindahan' => 'Pasien Pindahan',
            'pasien_keluarhidup' => 'Pasien Keluarhidup',
            'pasien_keluardipindahkan' => 'Pasien Keluardipindahkan',
            'pasien_keluarmeninggalkur48' => 'Pasien Keluarmeninggalkur48',
            'pasien_keluarmeninggalleb48' => 'Pasien Keluarmeninggalleb48',
            'pasien_akhir' => 'Pasien Akhir',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }

    public function getDataPasienSedangRawat($date)
    {
        $query = Yii::$app->db->createCommand("
            SELECT 
                count(pasien_id) as pasien_awal , tgl_admisi::DATE , ruangan_id, kelaspelayanan_id , 0 as pasien_masuk, 0 as pasien_pindahan, 0 as pasien_keluarhidup, 0 as pasien_keluardipindahkan, 0 as pasien_keluarmeninggalkur48, 0 as pasien_keluarmeninggalleb48,  count(pasien_id) as pasien_akhir , '{$date}' as tgl_sensus
            FROM laporansensusharianri_sedangrawatinap_v WHERE tgl_admisi::DATE < '{$date}' AND tgl_pasienplg::DATE > '{$date}'
            GROUP BY tgl_admisi::DATE , ruangan_id, kelaspelayanan_id
        ")->queryAll();

        $result = ArrayHelper::index($query, 'tgl_sensus', [function ($element) use ($date) {
            return $element['ruangan_id'];
        }, 'kelaspelayanan_id']);

        return $result;
    }

    public function getDataPasienMasuk($date, $isAwal = false, $isDaily = false)
    {
        if($isAwal == true || $isDaily == true) {
            $condition = (new Expression(" = '{$date}'"));
        } else {
            $condition = (new Expression(" > '{$date}'"));
        }
        $query = Yii::$app->db->createCommand("
            SELECT 
                COUNT(pasienadmisi_t.pendaftaran_id) as pasien_masuk, pasienadmisi_t.tgl_admisi::date,masukkamar_t.ruangan_id, masukkamar_t.kelaspelayanan_id
            FROM pasienadmisi_t
            JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
            JOIN masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
            WHERE pasienadmisi_t.tgl_admisi::DATE {$condition}
            AND pasienadmisi_t.status_ranap != 453
            AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
            AND ((masukkamar_t.tgl_masukkamar)::time without time zone = '00:00:00'::time without time zone)
            GROUP BY pasienadmisi_t.tgl_admisi::DATE,  masukkamar_t.ruangan_id, masukkamar_t.kelaspelayanan_id
        ")->queryAll();

        $result = ArrayHelper::index($query, 'tgl_admisi', [function ($element) {
            return $element['ruangan_id'];
        }, 'kelaspelayanan_id']);

        return $result;
    }

    public function getDataPasienPindahan($date, $isKonfig = false, $isAwal = false)
    {
        $condition = '';
        $conditionAwal = (new Expression(" > '{$date}'"));
        if($isKonfig == true) {
            $condition = (new Expression(" AND pasienadmisi_t.tgl_admisi >= '{$date}'"));
        }
        if($isAwal == true) {
            $conditionAwal = (new Expression(" = '{$date}'"));
        }
        $query = Yii::$app->db->createCommand("
            SELECT 
                COUNT(pasienadmisi_t.pendaftaran_id) as pasien_pindahan,pindahkamar_t.ruangan_id,pindahkamar_t.kelaspelayanan_id, pindahkamar_t.tgl_pindahkamar::date
            FROM pasienadmisi_t
            LEFT JOIN pindahkamar_t ON pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            WHERE pindahkamar_t.tgl_pindahkamar::DATE {$conditionAwal}
            {$condition}
            GROUP BY pindahkamar_t.ruangan_id,pindahkamar_t.kelaspelayanan_id, pindahkamar_t.tgl_pindahkamar::date
        ")->queryAll();

        $result = ArrayHelper::index($query, 'tgl_pindahkamar', [function ($element) {
            return $element['ruangan_id'];
        }, 'kelaspelayanan_id']);

        return $result;
    }

    public function getDataPasienKeluarHidup($date, $isKonfig = false, $isAwal = false)
    {
        $condition = '';
        $conditionAwal = (new Expression(" > '{$date}'"));
        if($isKonfig == true) {
            $condition = (new Expression(" AND  pasienadmisi_t.tgl_admisi >= '{$date}'"));
        } 
        if($isAwal == true) {
            $conditionAwal = (new Expression(" = '{$date}'"));
        }
        // $query = Yii::$app->db->createCommand("
        //     SELECT 
        //         COUNT(pasienadmisi_t.pendaftaran_id) as pasien_keluarhidup, pasienpulang_t.ruanganakhir_id, pasienadmisi_t.kelaspelayanan_id, pasienpulang_t.tglpasienpulang::DATE
        //     FROM pasienadmisi_t
        //     LEFT JOIN pasienpulang_t ON pasienpulang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        //     WHERE pasienpulang_t.tglpasienpulang::DATE {$conditionAwal}
        //     AND pasienpulang_t.pasienadmisi_id IS NOT NULL
        //     AND pasienpulang_t.carakeluar_id IN (1,3,5,6,7)
        //     {$condition}
        //     GROUP BY pasienpulang_t.ruanganakhir_id, pasienadmisi_t.kelaspelayanan_id, pasienpulang_t.tglpasienpulang::DATE
        // ")->queryAll();

        $query = Yii::$app->db->createCommand("
            SELECT 
                COUNT(pasienadmisi_t.pendaftaran_id) as pasien_keluarhidup, pasienpulang_t.ruanganakhir_id as ruangan_id, pasienadmisi_t.kelaspelayanan_id, pasienpulang_t.tglpasienpulang::DATE
            FROM pendaftaran_t 
            JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
            WHERE pasienpulang_t.tglpasienpulang::date {$conditionAwal}
            AND pasienpulang_t.carakeluar_id IN (1,3,5,6,7)
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            {$condition}
            GROUP BY pasienpulang_t.ruanganakhir_id, pasienadmisi_t.kelaspelayanan_id, pasienpulang_t.tglpasienpulang::DATE
        ")->queryAll();

        $result = ArrayHelper::index($query, 'tglpasienpulang', [function ($element) {
            return $element['ruangan_id'];
        }, 'kelaspelayanan_id']);

        return $result;
    }

    public function getDataPasienKeluarPindahan($date, $isKonfig = false, $isAwal = false)
    {
        $condition = '';
        $conditionAwal = (new Expression(" > '{$date}'"));
        if($isKonfig == true) {
            $condition = (new Expression(" AND  pasienadmisi_t.tgl_admisi >= '{$date}'"));
        } 
        if($isAwal == true) {
            $conditionAwal = (new Expression(" = '{$date}'"));
        }
        $query = Yii::$app->db->createCommand("
            SELECT 
                COUNT(pasienadmisi_t.pendaftaran_id) as pasien_keluardipindahkan ,masukkamar_t.ruangan_id,masukkamar_t.kelaspelayanan_id, pindahkamar_t.tgl_pindahkamar::DATE
            FROM pasienadmisi_t
            LEFT JOIN pindahkamar_t ON pindahkamar_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
            LEFT JOIN masukkamar_t ON masukkamar_t.pindahkamar_id = pindahkamar_t.pindahkamar_id
            WHERE pindahkamar_t.tgl_pindahkamar::DATE {$conditionAwal}
            AND masukkamar_t.pindahkamar_id IS NOT NULL
            {$condition}
            GROUP BY masukkamar_t.ruangan_id,masukkamar_t.kelaspelayanan_id, pindahkamar_t.tgl_pindahkamar::DATE
        ")->queryAll();

        $result = ArrayHelper::index($query, 'tgl_pindahkamar', [function ($element) {
            return $element['ruangan_id'];
        }, 'kelaspelayanan_id']);

        return $result;
    }

    public function getDataPasienMeninggalKur48($date, $isKonfig = false, $isAwal = false)
    {
        $condition = '';
        $conditionAwal = (new Expression(" > '{$date}'"));
        if($isKonfig == true) {
            $condition = (new Expression(" AND  pasienadmisi_t.tgl_admisi >= '{$date}'"));
        } 
        if($isAwal == true) {
            $conditionAwal = (new Expression(" = '{$date}'"));
        }
        $query = Yii::$app->db->createCommand("
            SELECT 
                COUNT(pasienadmisi_t.pendaftaran_id) as pasien_keluarmeninggalkur48, pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id, pasienpulang_t.tglpasienpulang::DATE
            FROM pasienadmisi_t
            JOIN pasienpulang_t ON pasienpulang_t.pasienpulang_id = pasienadmisi_t.pasienpulang_id
            WHERE pasienpulang_t.tglpasienpulang::DATE {$conditionAwal}
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id = 4
            AND (pasienpulang_t.tglpasienpulang::date - pasienadmisi_t.tgl_admisi::date) <= 2
            {$condition}
            GROUP BY pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id, pasienpulang_t.tglpasienpulang::DATE
        ")->queryAll();

        $result = ArrayHelper::index($query, 'tglpasienpulang', [function ($element) {
            return $element['ruangan_id'];
        }, 'kelaspelayanan_id']);

        return $result;
    }

    public function getDataPasienMeninggalLeb48($date, $isKonfig = false, $isAwal = false)
    {
        $condition = '';
        $conditionAwal = (new Expression(" > '{$date}'"));
        if($isKonfig == true) {
            $condition = (new Expression(" AND  pasienadmisi_t.tgl_admisi >= '{$date}'"));
        } 
        if($isAwal == true) {
            $conditionAwal = (new Expression(" = '{$date}'"));
        }
        $query = Yii::$app->db->createCommand("
            SELECT 
                COUNT(pasienadmisi_t.pendaftaran_id) as pasien_keluarmeninggalleb48, pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id, pasienpulang_t.tglpasienpulang::DATE
            FROM pasienadmisi_t
            JOIN pasienpulang_t ON pasienpulang_t.pasienpulang_id = pasienadmisi_t.pasienpulang_id
            WHERE pasienpulang_t.tglpasienpulang::DATE {$conditionAwal}
            AND pasienpulang_t.pasienadmisi_id IS NOT NULL
            AND pasienpulang_t.carakeluar_id = 4
            AND (pasienpulang_t.tglpasienpulang::date - pasienadmisi_t.tgl_admisi::date) > 2
            {$condition}
            GROUP BY pasienadmisi_t.ruangan_id, pasienadmisi_t.kelaspelayanan_id, pasienpulang_t.tglpasienpulang::DATE
        ")->queryAll();

        $result = ArrayHelper::index($query, 'tglpasienpulang', [function ($element) {
            return $element['ruangan_id'];
        }, 'kelaspelayanan_id']);

        return $result;
    }
}