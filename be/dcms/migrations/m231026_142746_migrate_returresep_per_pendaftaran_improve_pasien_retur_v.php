<?php

use yii\db\Migration;

/**
 * Class m231026_142746_migrate_returresep_per_pendaftaran_improve_pasien_retur_v
 */
class m231026_142746_migrate_returresep_per_pendaftaran_improve_pasien_retur_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP VIEW IF EXISTS public.pasien_retur_v");

        $this->execute("
        CREATE OR REPLACE VIEW public.pasien_retur_v
        AS  SELECT data.jenis,
    data.tanggal_pendaftaran,
    data.pendaftaran_id,
    data.returresep_id,
    data.nama_pasien,
    data.no_pendaftaran,
    data.instalasi_nama,
    data.carabayar_nama,
    data.penjamin_nama,
    data.no_rekam_medik,
    data.status_periksa,
    data.jumlah_transaksi,
    data.penanda_bayar,
    data.status_retur,
    data.no_returresep,
    data.ruanganakhir
   FROM ( SELECT DISTINCT ON (pendaftaran_t.pendaftaran_id) 'NON-GABUNG-BILING'::text AS jenis,
            pendaftaran_t.created_date AS tanggal_pendaftaran,
            pendaftaran_t.pendaftaran_id,
            returresep_t.returresep_id,
            pasien_m.nama_pasien,
            pendaftaran_t.no_pendaftaran,
            instalasi_m.instalasi_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            lookup_m.lookup_name,
            pasien_m.no_rekam_medik,
            count_pendaftaran.jumlah_transaksi,
            lookup_m.lookup_name AS status_periksa,
            returresep_t.no_returresep,
            returresep_t.status_retur,
                CASE
                    WHEN count_pendaftaran.jumlah_pembayaran = 0 THEN NULL::text
                    WHEN count_pendaftaran.jumlah_pembayaran IS NULL THEN NULL::text
                    ELSE '#ce9bca'::text
                END AS penanda_bayar,
            COALESCE(obat_bmhp.ruangan_nama, obat_resep.ruangan_nama, obat_reseptur.ruangan_nama, ruangan_penunjang.ruangan_nama) AS ruanganakhir
           FROM pendaftaran_t
             LEFT JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.returresep_id,
                    count(a.status_retur) AS status_retur,
                    a.no_returresep,
                    a.pendaftaran_id
                   FROM returresep_t a
                  WHERE a.is_deleted = false AND a.status_retur = 2118
                  GROUP BY a.pendaftaran_id, a.status_retur, a.returresep_id) returresep_t ON pendaftaran_t.pendaftaran_id = returresep_t.pendaftaran_id
             LEFT JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN lookup_m ON pendaftaran_t.status_periksa::integer = lookup_m.lookup_id
             LEFT JOIN ( SELECT c.pendaftaran_id,
                    count(c.pendaftaran_id) AS jumlah_transaksi,
                    count(c.pembayaran_id) AS jumlah_pembayaran
                   FROM obatalkespasien_t c
                  GROUP BY c.pendaftaran_id) count_pendaftaran ON pendaftaran_t.pendaftaran_id = count_pendaftaran.pendaftaran_id
             LEFT JOIN ( SELECT max(a.obatalkespasien_id) AS obatalkespasien_id,
                    a.pendaftaran_id,
                    r.ruangan_nama
                   FROM obatalkespasien_t a
                     JOIN ( SELECT x_1.instruksitindakanbmhp_id,
                            x_1.instruksi_id
                           FROM instruksitindakanbmhp_t x_1) x ON x.instruksitindakanbmhp_id = a.instruksitindakanbmhp_id
                     JOIN ( SELECT z_1.instruksi_id,
                            z_1.cppt_id
                           FROM instruksi_t z_1) z ON z.instruksi_id = x.instruksi_id
                     JOIN ( SELECT c_1.cppt_id,
                            c_1.ruangan_id
                           FROM cppt_t c_1) c ON c.cppt_id = z.cppt_id
                     JOIN ( SELECT r_1.ruangan_id,
                            r_1.ruangan_nama
                           FROM ruangan_m r_1) r ON r.ruangan_id = c.ruangan_id
                  WHERE a.is_deleted = false
                  GROUP BY r.ruangan_nama, a.pendaftaran_id) obat_bmhp ON obat_bmhp.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT max(a.obatalkespasien_id) AS obatalkespasien_id,
                    a.pendaftaran_id,
                    r.ruangan_nama
                   FROM obatalkespasien_t a
                     JOIN ( SELECT x_1.penjualanresep_id,
                            x_1.ruangan_id
                           FROM penjualanresep_t x_1) x ON x.penjualanresep_id = a.penjualanresep_id
                     JOIN ( SELECT r_1.ruangan_id,
                            r_1.ruangan_nama
                           FROM ruangan_m r_1) r ON r.ruangan_id = x.ruangan_id
                  WHERE a.is_deleted = false
                  GROUP BY r.ruangan_nama, a.pendaftaran_id) obat_resep ON obat_resep.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT max(a.obatalkespasien_id) AS obatalkespasien_id,
                    a.pendaftaran_id,
                    r.ruangan_nama
                   FROM obatalkespasien_t a
                     JOIN ( SELECT x_1.penjualanresep_id,
                            x_1.reseptur_id
                           FROM penjualanresep_t x_1) x ON x.penjualanresep_id = a.penjualanresep_id
                     JOIN ( SELECT z_1.reseptur_id,
                            z_1.ruangan_id,
                            z_1.ruanganreseptur_id
                           FROM reseptur_t z_1) z ON z.reseptur_id = x.reseptur_id
                     JOIN ( SELECT r_1.ruangan_id,
                            r_1.ruangan_nama
                           FROM ruangan_m r_1) r ON r.ruangan_id = z.ruanganreseptur_id
                  WHERE a.is_deleted = false
                  GROUP BY r.ruangan_nama, a.pendaftaran_id) obat_reseptur ON obat_reseptur.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT max(a.obatalkespasien_id) AS obatalkespasien_id,
                    a.pendaftaran_id,
                    r.ruangan_nama
                   FROM obatalkespasien_t a
                     JOIN ( SELECT x_1.pasienmasukpenunjang_id,
                            x_1.ruangan_id
                           FROM pasienmasukpenunjang_t x_1) x ON x.pasienmasukpenunjang_id = a.pasienmasukpenunjang_id
                     JOIN ( SELECT r_1.ruangan_id,
                            r_1.ruangan_nama
                           FROM ruangan_m r_1) r ON r.ruangan_id = x.ruangan_id
                  WHERE a.is_deleted = false
                  GROUP BY r.ruangan_nama, a.pendaftaran_id) ruangan_penunjang ON ruangan_penunjang.pendaftaran_id = pendaftaran_t.pendaftaran_id
          WHERE NOT (pendaftaran_t.pendaftaran_id IN ( SELECT gabungpelayanandetail_t.ref_pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted = false AND gabungpelayanandetail_t.is_active = true)) AND NOT (pendaftaran_t.pendaftaran_id IN ( SELECT gabungpelayanandetail_t.pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted = false AND gabungpelayanandetail_t.is_active = true))
        UNION ALL
         SELECT DISTINCT ON (pendaftaran_t.pendaftaran_id) 'GABUNG-BILING'::text AS jenis,
            pendaftaran_t.created_date AS tanggal_pendaftaran,
            pendaftaran_t.pendaftaran_id,
            returresep_t.returresep_id,
            pasien_m.nama_pasien,
            pendaftaran_t.no_pendaftaran,
            instalasi_m.instalasi_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            lookup_m.lookup_name,
            pasien_m.no_rekam_medik,
            count_pendaftaran.jumlah_transaksi,
            lookup_m.lookup_name AS status_periksa,
            returresep_t.no_returresep,
            returresep_t.status_retur,
                CASE
                    WHEN count_pendaftaran.jumlah_pembayaran = 0 THEN NULL::text
                    WHEN count_pendaftaran.jumlah_pembayaran IS NULL THEN NULL::text
                    ELSE '#ce9bca'::text
                END AS penanda_bayar,
            COALESCE(obat_bmhp.ruangan_nama, obat_resep.ruangan_nama, obat_reseptur.ruangan_nama, ruangan_penunjang.ruangan_nama) AS ruanganakhir
           FROM pendaftaran_t
             JOIN gabungpelayanandetail_t ON pendaftaran_t.pendaftaran_id = gabungpelayanandetail_t.ref_pendaftaran_id
             LEFT JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.returresep_id,
                    count(a.status_retur) AS status_retur,
                    a.no_returresep,
                    a.pendaftaran_id
                   FROM returresep_t a
                  WHERE a.is_deleted = false AND a.status_retur = 2118
                  GROUP BY a.pendaftaran_id, a.status_retur, a.returresep_id) returresep_t ON pendaftaran_t.pendaftaran_id = returresep_t.pendaftaran_id
             LEFT JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT c.pendaftaran_id,
                    count(c.pendaftaran_id) AS jumlah_transaksi,
                    count(c.pembayaran_id) AS jumlah_pembayaran
                   FROM obatalkespasien_t c
                  GROUP BY c.pendaftaran_id) count_pendaftaran ON pendaftaran_t.pendaftaran_id = count_pendaftaran.pendaftaran_id
             LEFT JOIN lookup_m ON pendaftaran_t.status_periksa::integer = lookup_m.lookup_id
             LEFT JOIN ( SELECT max(a.obatalkespasien_id) AS obatalkespasien_id,
                    a.pendaftaran_id,
                    r.ruangan_nama
                   FROM obatalkespasien_t a
                     JOIN ( SELECT x_1.instruksitindakanbmhp_id,
                            x_1.instruksi_id
                           FROM instruksitindakanbmhp_t x_1) x ON x.instruksitindakanbmhp_id = a.instruksitindakanbmhp_id
                     JOIN ( SELECT z_1.instruksi_id,
                            z_1.cppt_id
                           FROM instruksi_t z_1) z ON z.instruksi_id = x.instruksi_id
                     JOIN ( SELECT c_1.cppt_id,
                            c_1.ruangan_id
                           FROM cppt_t c_1) c ON c.cppt_id = z.cppt_id
                     JOIN ( SELECT r_1.ruangan_id,
                            r_1.ruangan_nama
                           FROM ruangan_m r_1) r ON r.ruangan_id = c.ruangan_id
                  WHERE a.is_deleted = false
                  GROUP BY r.ruangan_nama, a.pendaftaran_id) obat_bmhp ON obat_bmhp.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT max(a.obatalkespasien_id) AS obatalkespasien_id,
                    a.pendaftaran_id,
                    r.ruangan_nama
                   FROM obatalkespasien_t a
                     JOIN ( SELECT x_1.penjualanresep_id,
                            x_1.ruangan_id
                           FROM penjualanresep_t x_1) x ON x.penjualanresep_id = a.penjualanresep_id
                     JOIN ( SELECT r_1.ruangan_id,
                            r_1.ruangan_nama
                           FROM ruangan_m r_1) r ON r.ruangan_id = x.ruangan_id
                  WHERE a.is_deleted = false
                  GROUP BY r.ruangan_nama, a.pendaftaran_id) obat_resep ON obat_resep.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT max(a.obatalkespasien_id) AS obatalkespasien_id,
                    a.pendaftaran_id,
                    r.ruangan_nama
                   FROM obatalkespasien_t a
                     JOIN ( SELECT x_1.penjualanresep_id,
                            x_1.reseptur_id
                           FROM penjualanresep_t x_1) x ON x.penjualanresep_id = a.penjualanresep_id
                     JOIN ( SELECT z_1.reseptur_id,
                            z_1.ruangan_id,
                            z_1.ruanganreseptur_id
                           FROM reseptur_t z_1) z ON z.reseptur_id = x.reseptur_id
                     JOIN ( SELECT r_1.ruangan_id,
                            r_1.ruangan_nama
                           FROM ruangan_m r_1) r ON r.ruangan_id = z.ruanganreseptur_id
                  WHERE a.is_deleted = false
                  GROUP BY r.ruangan_nama, a.pendaftaran_id) obat_reseptur ON obat_reseptur.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT max(a.obatalkespasien_id) AS obatalkespasien_id,
                    a.pendaftaran_id,
                    r.ruangan_nama
                   FROM obatalkespasien_t a
                     JOIN ( SELECT x_1.pasienmasukpenunjang_id,
                            x_1.ruangan_id
                           FROM pasienmasukpenunjang_t x_1) x ON x.pasienmasukpenunjang_id = a.pasienmasukpenunjang_id
                     JOIN ( SELECT r_1.ruangan_id,
                            r_1.ruangan_nama
                           FROM ruangan_m r_1) r ON r.ruangan_id = x.ruangan_id
                  WHERE a.is_deleted = false
                  GROUP BY r.ruangan_nama, a.pendaftaran_id) ruangan_penunjang ON ruangan_penunjang.pendaftaran_id = pendaftaran_t.pendaftaran_id) data;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231026_142746_migrate_returresep_per_pendaftaran_improve_pasien_retur_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231026_142746_migrate_returresep_per_pendaftaran_improve_pasien_retur_v cannot be reverted.\n";

        return false;
    }
    */
}
