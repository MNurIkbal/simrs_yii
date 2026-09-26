<?php

use yii\db\Migration;

/**
 * Class m220705_043352_migrate_MHG1045_view_fpemberianpiutang_v
 */
class m220705_043352_migrate_MHG1045_view_fpemberianpiutang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."fpemberianpiutang_v";
        ');

        $this->execute('
            CREATE VIEW "public"."fpemberianpiutang_v" AS  SELECT \'tagihan_rs\'::text AS jenis,
                pendaftaran_t.no_pendaftaran,
                NULL::character varying AS no_resep, 
                pasien_m.no_rekam_medik,
                pendaftaran_t.pendaftaran_id,
                NULL::integer AS penjualanresep_id,
                pasien_m.nama_pasien,
                COALESCE(total_tagihan.total_tagihan, 0::double precision) + COALESCE(reseptur.total_adm, 0::double precision) AS total_tagihan,
                COALESCE(pemberianpiutang_t.total_bayarpiutang, 0::double precision) AS piutang_sudahbayar,
                pasien_m.tanggal_lahir,
                pendaftaran_t.umur,
                pendaftaran_t.tgl_pendaftaran,
                COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_piutang,
                konfigsystem_k.adm_persen,
                adm.tarif_max,
                COALESCE(tagihan_ranap.total_tagihan, 0::double precision) AS tagihan_ranap,
                bayaruangmuka_t.jumlah_uangmuka AS uang_muka,
                konfigsystem_k.is_pembulatankeatas,
                konfigsystem_k.satuanpembulatan,
                    CASE
                        WHEN pemberianpiutang_t.is_deleted = true THEN true
                        ELSE false
                    END AS is_batal,
                pemberianpiutang_t.created_date
               FROM pendaftaran_t
                 LEFT JOIN ( SELECT a.pasienadmisi_id,
                        a.kelaspelayanan_id,
                        a.penjamin_id
                       FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                 JOIN ( SELECT a.pasien_id,
                        a.no_rekam_medik,
                        a.nama_pasien,
                        a.tanggal_lahir
                       FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                        sum(tagihan.tagihan) AS total_tagihan
                       FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                                sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                               FROM tindakanpelayanan_t
                              WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL
                              GROUP BY tindakanpelayanan_t.pendaftaran_id
                            UNION ALL
                             SELECT obatalkespasien_t.pendaftaran_id,
                                sum(obatalkespasien_t.hargajual_oa) AS tagihan
                               FROM obatalkespasien_t
                              WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.obatsudahbayar_id IS NULL
                              GROUP BY obatalkespasien_t.pendaftaran_id) tagihan
                      GROUP BY tagihan.pendaftaran_id) total_tagihan ON pendaftaran_t.pendaftaran_id = total_tagihan.pendaftaran_id
                 LEFT JOIN ( SELECT a.pendaftaran_id,
                        a.total_piutang,
                        a.total_bayarpiutang,
                        a.is_deleted,
                        a.created_date
                       FROM pemberianpiutang_t a
                      WHERE a.is_deleted IS FALSE) pemberianpiutang_t ON pendaftaran_t.pendaftaran_id = pemberianpiutang_t.pendaftaran_id
                 LEFT JOIN ( SELECT a.pendaftaran_id,
                        sum(a.jumlah_uangmuka) AS jumlah_uangmuka
                       FROM bayaruangmuka_t a
                      WHERE a.is_deleted IS FALSE
                      GROUP BY a.pendaftaran_id) bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
                 LEFT JOIN ( SELECT a.is_pembulatankeatas,
                        a.satuanpembulatan,
                        a.is_deleted,
                        a.adm_persen
                       FROM konfigsystem_k a) konfigsystem_k ON konfigsystem_k.is_deleted = false
                 LEFT JOIN ( SELECT tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.daftartindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        tariftindakan_m.penjamin_id,
                        tariftindakan_m.harga_tariftindakan AS tarif_max
                       FROM tariftindakan_m
                         JOIN ( SELECT a.adm_tindakan_id
                               FROM konfigsystem_k a) konfig_tarif ON tariftindakan_m.daftartindakan_id = konfig_tarif.adm_tindakan_id
                      WHERE tariftindakan_m.is_deleted = false AND tariftindakan_m.komponentarif_id = 6) adm ON adm.kelaspelayanan_id = pasienadmisi_t.kelaspelayanan_id AND adm.penjamin_id = pasienadmisi_t.penjamin_id
                 LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                        sum(tagihan.tagihan) AS total_tagihan
                       FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                                sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                               FROM tindakanpelayanan_t
                                 JOIN ( SELECT a.ruangan_id,
                                        a.ruangan_nama,
                                        a.instalasi_id
                                       FROM ruangan_m a) ruangan_m ON ruangan_m.ruangan_id = tindakanpelayanan_t.ruangan_id
                              WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.pasienadmisi_id IS NOT NULL AND ruangan_m.instalasi_id = 3
                              GROUP BY tindakanpelayanan_t.pendaftaran_id
                            UNION ALL
                             SELECT obatalkespasien_t.pendaftaran_id,
                                sum(obatalkespasien_t.hargajual_oa) AS tagihan
                               FROM obatalkespasien_t
                                 JOIN ( SELECT a.ruangan_id,
                                        a.ruangan_nama,
                                        a.instalasi_id
                                       FROM ruangan_m a) ruangan_m ON ruangan_m.ruangan_id = obatalkespasien_t.ruangan_id
                              WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.obatsudahbayar_id IS NULL AND obatalkespasien_t.pasienadmisi_id IS NOT NULL AND ruangan_m.instalasi_id = 3
                              GROUP BY obatalkespasien_t.pendaftaran_id) tagihan
                      GROUP BY tagihan.pendaftaran_id) tagihan_ranap ON pendaftaran_t.pendaftaran_id = tagihan_ranap.pendaftaran_id
                 LEFT JOIN ( SELECT sum(penjualanresep_t.biayaadministrasi) AS total_adm,
                        penjualanresep_t.pendaftaran_id
                       FROM penjualanresep_t
                      WHERE penjualanresep_t.status_bayar = 349
                      GROUP BY penjualanresep_t.pendaftaran_id) reseptur ON reseptur.pendaftaran_id = pendaftaran_t.pendaftaran_id
              WHERE pendaftaran_t.is_deleted = false
            UNION ALL
             SELECT \'resep_bebas\'::text AS jenis,
                penjualanresep_t.noresep AS no_pendaftaran,
                penjualanresep_t.noresep AS no_resep,
                NULL::character varying AS no_rekam_medik,
                penjualanresep_t.penjualanresep_id AS pendaftaran_id,
                penjualanresep_t.penjualanresep_id,
                penjualanresep_t.nama_pembeli AS nama_pasien,
                tagihan_resep.tagihan_obat + COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS total_tagihan,
                COALESCE(pemberianpiutang_t.total_bayarpiutang, 0::double precision) AS piutang_sudahbayar,
                NULL::date AS tanggal_lahir,
                NULL::character varying AS umur,
                penjualanresep_t.tglresep AS tgl_pendaftaran,
                COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_piutang,
                0 AS adm_persen,
                0 AS tarif_max,
                0 AS tagihan_ranap,
                0 AS uang_muka,
                konfigsystem_k.is_pembulatankeatas,
                konfigsystem_k.satuanpembulatan,
                    CASE
                        WHEN pemberianpiutang_t.is_deleted = true THEN true
                        ELSE false
                    END AS is_batal,
                pemberianpiutang_t.created_date
               FROM penjualanresep_t
                 LEFT JOIN ( SELECT obatalkespasien_t.penjualanresep_id,
                        sum(obatalkespasien_t.hargajual_oa) AS tagihan_obat
                       FROM obatalkespasien_t
                      WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.obatsudahbayar_id IS NULL
                      GROUP BY obatalkespasien_t.penjualanresep_id) tagihan_resep ON penjualanresep_t.penjualanresep_id = tagihan_resep.penjualanresep_id
                 LEFT JOIN ( SELECT a.penjualanresep_id,
                        a.total_piutang,
                        a.total_bayarpiutang,
                        a.is_deleted,
                        a.created_date
                       FROM pemberianpiutang_t a
                      WHERE a.is_deleted IS FALSE) pemberianpiutang_t ON penjualanresep_t.penjualanresep_id = pemberianpiutang_t.penjualanresep_id
                 LEFT JOIN ( SELECT a.is_pembulatankeatas,
                        a.satuanpembulatan,
                        a.is_deleted
                       FROM konfigsystem_k a) konfigsystem_k ON konfigsystem_k.is_deleted = false
              WHERE penjualanresep_t.status_bayar = 349 AND penjualanresep_t.status_reseptur <> 432 AND penjualanresep_t.is_deleted = false AND penjualanresep_t.pendaftaran_id IS NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220705_043352_migrate_MHG1045_view_fpemberianpiutang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220705_043352_migrate_MHG1045_view_fpemberianpiutang_v cannot be reverted.\n";

        return false;
    }
    */
}
