<?php
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "fpemberianpiutang_v".
 *
 * @property string jenis
 * @property string no_pendaftaran
 * @property string no_rekam_medik
 * @property int pendaftaran_id
 * @property string nama_pasien
 * @property int total_tagihan
 * @property int piutang_sudahbayar
 * @property string umur
 * @property string tgl_pendaftaran
 * @property int total_piutang
 * @property string adm_persen
 * @property string tarif_max
 * @property string tagihan_ranap
 * @property string uang_muka
 * @property string is_pembulatankeatas
 * @property string satuanpembulatan

 */
class PemberianPiutangView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fpemberianpiutang_v';
    }

    public function getPiutangByPendaftaranId($pendaftaran_id)
    {
        $query = 'SELECT
                pemberianpiutang.pendaftaran_id,
                pemberianpiutang.no_pendaftaran,
                pemberianpiutang.kelaspelayanan_id,
                pemberianpiutang.penjamin_id,
                pemberianpiutang.pasienadmisi_id,
                COALESCE(pemberianpiutang.total_tagihan_tanpa_biayaadm_1, 0::DOUBLE PRECISION) + COALESCE(pemberianpiutang.total_tagihan_tanpa_biayaadm_2, 0::DOUBLE PRECISION) AS total_tagihan,
                COALESCE(pemberianpiutang.total_tagihan_jpk_1, 0::DOUBLE PRECISION) + COALESCE(pemberianpiutang.total_tagihan_jpk_2, 0::DOUBLE PRECISION) AS total_jpk,
                COALESCE(pemberianpiutang.piutangutama, 0::DOUBLE PRECISION) + COALESCE(pemberianpiutang.piutangref, 0::DOUBLE PRECISION) AS total_piutang,
                pemberianpiutang.piutangutama,
                pemberianpiutang.piutangref
            FROM (
                SELECT
                    pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.no_pendaftaran,
                    utangutama.total_piutang as piutangutama,
	                utangref.total_piutang as piutangref,
                    -- Ambil kelas pelayanan dari pasienadmisi jika ada, jika tidak ambil dari pendaftaran
                    CASE
                        WHEN pt.pasienadmisi_id IS NULL THEN pendaftaran_t.kelaspelayanan_id
                        ELSE pt.kelaspelayanan_id
                    END AS kelaspelayanan_id,
                    -- Ambil penjamin dari pasienadmisi jika ada, jika tidak ambil dari pendaftaran
                    CASE
                        WHEN pt.pasienadmisi_id IS NULL THEN pendaftaran_t.penjamin_id
                        ELSE pt.penjamin_id
                    END AS penjamin_id,
                    pt.pasienadmisi_id,
                    (
                        SELECT SUM(tagihan.tagihan) AS total_tagihan
                            FROM (
                                SELECT tindakanpelayanan_t.pendaftaran_id, SUM(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                                FROM tindakanpelayanan_t
                                WHERE tindakanpelayanan_t.is_deleted = FALSE
                                AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL
                                GROUP BY tindakanpelayanan_t.pendaftaran_id
                                UNION ALL

                                SELECT obatalkespasien_t.pendaftaran_id, SUM(obatalkespasien_t.hargajual_oa) AS tagihan
                                FROM obatalkespasien_t
                                WHERE obatalkespasien_t.is_deleted = FALSE
                                AND obatalkespasien_t.obatsudahbayar_id IS NULL
                                GROUP BY obatalkespasien_t.pendaftaran_id
                            ) tagihan where tagihan.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        GROUP BY tagihan.pendaftaran_id
                    ) as total_tagihan_tanpa_biayaadm_1,
                    (
                        SELECT SUM(tagihan.tagihan) AS total_tagihan
                        FROM (
                            SELECT tindakanpelayanan_t.pendaftaran_id, SUM(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                            FROM tindakanpelayanan_t
                            WHERE tindakanpelayanan_t.is_deleted = FALSE
                            AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL
                            GROUP BY tindakanpelayanan_t.pendaftaran_id

                            UNION ALL

                            SELECT obatalkespasien_t.pendaftaran_id, SUM(obatalkespasien_t.hargajual_oa) AS tagihan
                            FROM obatalkespasien_t
                            WHERE obatalkespasien_t.is_deleted = FALSE
                            AND obatalkespasien_t.obatsudahbayar_id IS NULL
                            GROUP BY obatalkespasien_t.pendaftaran_id
                        ) tagihan where tagihan.pendaftaran_id = gt.pendaftaran_id
                        GROUP BY tagihan.pendaftaran_id
                    ) as total_tagihan_tanpa_biayaadm_2,
                    (
                        SELECT  SUM(tagihan.tagihan) AS total_tagihan
                        FROM (
                            SELECT tindakanpelayanan_t.pendaftaran_id, SUM(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                            FROM tindakanpelayanan_t
                            WHERE tindakanpelayanan_t.is_deleted = FALSE
                            AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL
                            AND tindakanpelayanan_t.daftartindakan_id IN (
                                SELECT unnest(string_to_array(REGEXP_REPLACE(additional_value, \'[\[\]]\', \'\', \'g\'), \',\'))::int
                                FROM lookuptransaksi_m
                                WHERE kode_transaksi = \'JPK\'
                                LIMIT 1
                            )
                            GROUP BY tindakanpelayanan_t.pendaftaran_id
                        ) tagihan where tagihan.pendaftaran_id = pendaftaran_t.pendaftaran_id
                        GROUP BY tagihan.pendaftaran_id
                    ) as total_tagihan_jpk_1,
                    (
                        SELECT SUM(tagihan.tagihan) AS total_tagihan
                        FROM (
                            SELECT tindakanpelayanan_t.pendaftaran_id, SUM(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                            FROM tindakanpelayanan_t
                            WHERE tindakanpelayanan_t.is_deleted = FALSE
                            AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL
                            AND tindakanpelayanan_t.daftartindakan_id IN (
                                SELECT unnest(string_to_array(REGEXP_REPLACE(additional_value, \'[\[\]]\', \'\', \'g\'), \',\'))::int
                                FROM lookuptransaksi_m
                                WHERE kode_transaksi = \'JPK\'
                            )
                            GROUP BY tindakanpelayanan_t.pendaftaran_id
                        ) tagihan where tagihan.pendaftaran_id = gt.pendaftaran_id
                        GROUP BY tagihan.pendaftaran_id
                    ) as total_tagihan_jpk_2
                FROM pendaftaran_t
                LEFT JOIN pasienadmisi_t pt ON pt.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pt.is_deleted IS FALSE
                LEFT JOIN gabungpelayanandetail_t gt ON gt.ref_pendaftaran_id = pendaftaran_t.pendaftaran_id AND gt.is_deleted IS FALSE
                LEFT JOIN pemberianpiutang_t utangutama ON utangutama.pendaftaran_id = pendaftaran_t.pendaftaran_id AND utangutama.is_deleted is false
                LEFT JOIN pemberianpiutang_t utangref ON utangref.pendaftaran_id = gt.pendaftaran_id AND utangref.is_deleted is false
                -- Filter berdasarkan nomor pendaftaran
                WHERE pendaftaran_t.pendaftaran_id = :pendaftaran_id AND pendaftaran_t.is_active IS TRUE AND pendaftaran_t.is_deleted IS FALSE
            ) pemberianpiutang';

        return Yii::$app->db->createCommand($query)
                    ->bindValue(':pendaftaran_id', $pendaftaran_id)
                    ->queryOne();
    }
}
