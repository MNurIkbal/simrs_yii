<?php

use yii\db\Migration;

/**
 * Class m220421_103447_hotfix_view_laporanrekapkunjuganrs_v
 */
class m220421_103447_hotfix_view_laporanrekapkunjuganrs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanrekapkunjuganrs_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporanrekapkunjuganrs_v" AS  SELECT x.tahun,
                x.bulan,
                x.unit_pelayanan,
                x.penjamin,
                x.ruangan,
                x.kunjungan,
                x.total_tagihan AS total,
                x.no_pendaftaran
               FROM ( SELECT (to_char(COALESCE(pembayaran.tgl_pembayaran), \'YYYY\'::text))::character varying AS tahun,
                        (to_char(COALESCE(pembayaran.tgl_pembayaran), \'MONTH\'::text))::character varying AS bulan,
                        pendaftaran_t.no_pendaftaran,
                            CASE
                                WHEN ((pendaftaran_t.instalasi_id = ANY (ARRAY[2, 3])) AND (pendaftaran_t.pasienadmisi_id IS NOT NULL)) THEN \'Rawat Inap\'::text
                                WHEN ((pendaftaran_t.instalasi_id = 2) AND (pendaftaran_t.pasienadmisi_id IS NULL)) THEN \'Rawat Jalan\'::text
                                WHEN ((pendaftaran_t.instalasi_id <> ALL (ARRAY[2, 3])) AND (pendaftaran_t.pasienadmisi_id IS NULL)) THEN \'Rawat Jalan\'::text
                                ELSE NULL::text
                            END AS unit_pelayanan,
                        COALESCE(ruangan_ri.ruangan_nama, ruangan_rjrd.ruangan_nama) AS ruangan,
                            CASE
                                WHEN (COALESCE(carabayar_ri.carabayar_id, carabayar_rjrd.carabayar_id) = 5) THEN COALESCE(penjamin_ri.penjamin_nama, penjamin_rjrd.penjamin_nama)
                                ELSE COALESCE(penjamin_ri.penjamin_nama, penjamin_rjrd.penjamin_nama)
                            END AS penjamin,
                        fgetnamalookup((pendaftaran_t.kunjungan)::integer) AS kunjungan,
                        sum(pembayaran.total_tagihan) AS total_tagihan
                       FROM (((((((((pendaftaran_t
                         JOIN ( SELECT a.pasien_id,
                                a.no_rekam_medik,
                                a.nama_pasien 
                               FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                         JOIN ( SELECT a.pendaftaran_id,
                                pembayaran_t.created_date AS tgl_pembayaran,
                                a.no_pembayaran,
                                ((pembayaran_t.total_tagihan + pembayaran_t.total_administrasi) - pembayaran_t.total_discountpembayaran) AS total_tagihan,
                                pembayaran_t.total_dijamin,
                                pembayaran_t.total_dibayar
                               FROM (pembayaranpelayanan_t a
                                 JOIN pembayaran_t ON (((a.pembayaran_id = pembayaran_t.pembayaran_id) AND (pembayaran_t.is_deleted = false))))
                              WHERE (a.is_deleted = false)) pembayaran ON ((pendaftaran_t.pendaftaran_id = pembayaran.pendaftaran_id)))
                         LEFT JOIN ( SELECT a.pasienadmisi_id,
                                a.penjamin_id,
                                a.ruangan_id,
                                a.pasienpulang_id,
                                a.tgl_admisi
                               FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                         LEFT JOIN ruangan_m ruangan_rjrd ON ((pendaftaran_t.ruangan_id = ruangan_rjrd.ruangan_id)))
                         LEFT JOIN ruangan_m ruangan_ri ON ((pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id)))
                         LEFT JOIN penjamin_m penjamin_rjrd ON ((pendaftaran_t.penjamin_id = penjamin_rjrd.penjamin_id)))
                         LEFT JOIN penjamin_m penjamin_ri ON ((pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id)))
                         LEFT JOIN carabayar_m carabayar_rjrd ON ((penjamin_rjrd.carabayar_id = carabayar_rjrd.carabayar_id)))
                         LEFT JOIN carabayar_m carabayar_ri ON ((penjamin_ri.carabayar_id = carabayar_ri.carabayar_id)))
                      GROUP BY pendaftaran_t.instalasi_id, pendaftaran_t.pasienadmisi_id, COALESCE(ruangan_ri.ruangan_nama, ruangan_rjrd.ruangan_nama), COALESCE(penjamin_ri.penjamin_nama, penjamin_rjrd.penjamin_nama), (to_char(COALESCE(pembayaran.tgl_pembayaran), \'YYYY\'::text))::character varying, (to_char(COALESCE(pembayaran.tgl_pembayaran), \'MONTH\'::text)), pendaftaran_t.no_pendaftaran, (fgetnamalookup((pendaftaran_t.kunjungan)::integer)), COALESCE(carabayar_ri.carabayar_id, carabayar_rjrd.carabayar_id)) x;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220421_103447_hotfix_view_laporanrekapkunjuganrs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220421_103447_hotfix_view_laporanrekapkunjuganrs_v cannot be reverted.\n";

        return false;
    }
    */
}
