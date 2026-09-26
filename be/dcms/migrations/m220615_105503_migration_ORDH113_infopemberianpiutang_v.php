<?php

use yii\db\Migration;

/**
 * Class m220615_105503_migration_ORDH113_infopemberianpiutang_v
 */
class m220615_105503_migration_ORDH113_infopemberianpiutang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopemberianpiutang_v";');
        $this->execute("CREATE VIEW \"public\".\"infopemberianpiutang_v\" AS  SELECT 'tagihan_rs'::text AS jenis,
        pemberianpiutang_t.pemberianpiutang_id,
        pemberianpiutang_t.no_pemberianpiutang,
        pemberianpiutang_t.tgl_pemberianpiutang,
        pendaftaran_t.pendaftaran_id,
        pendaftaran_t.no_pendaftaran,
        pasien_m.pasien_id,
        pasien_m.no_rekam_medik,
        pasien_m.nama_pasien,
        tagihan.total_tagihan AS tagihan,
        pemberianpiutang_t.total_piutang,
        pemberianpiutang_t.total_sisapiutang,
        pemberianpiutang_t.total_bayarpiutang,
            CASE
                WHEN pemberianpiutang_t.is_deleted = true THEN 9999
                ELSE pemberianpiutang_t.status_piutang::integer
            END AS status_piutang,
            CASE
                WHEN pemberianpiutang_t.is_deleted = true THEN 'Batal'::character varying
                ELSE look_statuspiutang.lookup_name
            END AS status_piutang_nama,
        pemberianpiutang_t.pegawai_id,
        pegawai_m.nama_pegawai,
        pemberianpiutang_t.catatan,
        pasien_m.tanggal_lahir,
        pendaftaran_t.tgl_pendaftaran,
        pemberianpiutang_t.pegawaimengetahui_id AS pegawaidibebankan_id,
        peg_mengetahui.nama_pegawai AS pegawaidibebankan_nama,
        peg_mengetahui.nomorindukpegawai AS pegawaidibebankan_nip,
        pemberianpiutang_t.created_date,
        COALESCE(pulang_ri.tglpasienpulang, pulang_rj.tglpasienpulang, pendaftaran_t.tgl_pendaftaran) AS tglpasienpulang,
            CASE
                WHEN pemberianpiutang_t.is_deleted = true THEN true
                ELSE false
            END AS is_batal
       FROM pemberianpiutang_t
         JOIN ( SELECT a.pendaftaran_id,
                a.no_pendaftaran,
                a.tgl_pendaftaran,
                a.pasienadmisi_id,
                a.pasien_id,
                a.pasienpulang_id
               FROM pendaftaran_t a) pendaftaran_t ON pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
         LEFT JOIN ( SELECT a.pasienadmisi_id,
                a.pasienpulang_id
               FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
         JOIN ( SELECT a.pasien_id,
                a.no_rekam_medik,
                a.nama_pasien,
                a.tanggal_lahir
               FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
         JOIN ( SELECT a.nama_pegawai,
                a.pegawai_id
               FROM pegawai_m a) pegawai_m ON pemberianpiutang_t.pegawai_id = pegawai_m.pegawai_id
         LEFT JOIN ( SELECT p.pegawai_id,
                p.nomorindukpegawai,
                p.nama_pegawai
               FROM pegawai_m p
              WHERE p.is_deleted = false AND p.is_active = true) peg_mengetahui ON pemberianpiutang_t.pegawaimengetahui_id = peg_mengetahui.pegawai_id
         LEFT JOIN ( SELECT gabung.pendaftaran_id,
                COALESCE(sum(gabung.total_tindakan), 0::double precision) + COALESCE(sum(gabung.total_obat), 0::double precision) AS total_tagihan
               FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                        sum(tindakanpelayanan_t.tarif_tindakan) AS total_tindakan,
                        NULL::double precision AS total_obat,
                        tindakanpelayanan_t.tindakansudahbayar_id AS sudah_bayar
                       FROM pendaftaran_t pendaftaran_t_1
                         LEFT JOIN ( SELECT a.tarif_tindakan,
                                a.tindakansudahbayar_id,
                                a.pendaftaran_id,
                                a.is_deleted
                               FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                         LEFT JOIN ( SELECT a.pasienadmisi_id,
                                a.pasienpulang_id
                               FROM pasienadmisi_t a) pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
                         LEFT JOIN ( SELECT a.pasienpulang_id,
                                a.carakeluar_id
                               FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t_1.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
                      WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL
                      GROUP BY pendaftaran_t_1.pendaftaran_id, tindakanpelayanan_t.tindakansudahbayar_id
                    UNION ALL
                     SELECT pendaftaran_t_1.pendaftaran_id,
                        NULL::double precision AS total_tindakan,
                        sum(obatalkespasien_t.hargajual_oa) AS total_obat,
                        obatalkespasien_t.obatsudahbayar_id
                       FROM pendaftaran_t pendaftaran_t_1
                         LEFT JOIN ( SELECT a.hargajual_oa,
                                a.obatsudahbayar_id,
                                a.pendaftaran_id,
                                a.is_deleted
                               FROM obatalkespasien_t a) obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
                         LEFT JOIN ( SELECT a.pasienadmisi_id,
                                a.pasienpulang_id
                               FROM pasienpulang_t a) pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
                         LEFT JOIN ( SELECT a.pasienpulang_id,
                                a.carakeluar_id
                               FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t_1.pasienpulang_id = pasienpulang_t.pasienpulang_id AND pasienpulang_t.carakeluar_id <> 5
                      WHERE obatalkespasien_t.obatsudahbayar_id IS NULL
                      GROUP BY pendaftaran_t_1.pendaftaran_id, obatalkespasien_t.obatsudahbayar_id) gabung
                 LEFT JOIN ( SELECT a.pendaftaran_id,
                        a.is_deleted
                       FROM pemberianpiutang_t a) pemberianpiutang_t_1 ON gabung.pendaftaran_id = pemberianpiutang_t_1.pendaftaran_id AND pemberianpiutang_t_1.is_deleted = false
              WHERE gabung.sudah_bayar IS NULL
              GROUP BY gabung.pendaftaran_id) tagihan ON pemberianpiutang_t.pendaftaran_id = tagihan.pendaftaran_id
         LEFT JOIN ( SELECT a.pasienpulang_id,
                a.tglpasienpulang
               FROM pasienpulang_t a) pulang_rj ON pendaftaran_t.pasienpulang_id = pulang_rj.pasienpulang_id
         LEFT JOIN ( SELECT a.pasienpulang_id,
                a.tglpasienpulang
               FROM pasienpulang_t a) pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
         JOIN ( SELECT a.lookup_id,
                a.lookup_name
               FROM lookup_m a) look_statuspiutang ON pemberianpiutang_t.status_piutang::integer = look_statuspiutang.lookup_id
    UNION ALL
     SELECT 'resep_bebas'::text AS jenis,
        pemberianpiutang_t.pemberianpiutang_id,
        pemberianpiutang_t.no_pemberianpiutang,
        pemberianpiutang_t.tgl_pemberianpiutang,
        pemberianpiutang_t.penjualanresep_id AS pendaftaran_id,
        penjualanresep_t.noresep AS no_pendaftaran,
        NULL::integer AS pasien_id,
        NULL::character varying AS no_rekam_medik,
        penjualanresep_t.nama_pembeli AS nama_pasien,
        tagihan_resep.tagihan_obat AS tagihan,
        pemberianpiutang_t.total_piutang,
        pemberianpiutang_t.total_sisapiutang,
        pemberianpiutang_t.total_bayarpiutang,
            CASE
                WHEN pemberianpiutang_t.is_deleted = true THEN 9999
                ELSE pemberianpiutang_t.status_piutang::integer
            END AS status_piutang,
            CASE
                WHEN pemberianpiutang_t.is_deleted = true THEN 'Batal'::character varying
                ELSE look_statuspiutang.lookup_name
            END AS status_piutang_nama,
        pemberianpiutang_t.pegawai_id,
        pegawai_m.nama_pegawai,
        pemberianpiutang_t.catatan,
        NULL::date AS tanggal_lahir,
        penjualanresep_t.tglpenjualan AS tgl_pendaftaran,
        pemberianpiutang_t.pegawaimengetahui_id AS pegawaidibebankan_id,
        peg_mengetahui.nama_pegawai AS pegawaidibebankan_nama,
        peg_mengetahui.nomorindukpegawai AS pegawaidibebankan_nip,
        pemberianpiutang_t.created_date,
        penjualanresep_t.tglpenjualan AS tglpasienpulang,
            CASE
                WHEN pemberianpiutang_t.is_deleted = true THEN true
                ELSE false
            END AS is_batal
       FROM pemberianpiutang_t
         JOIN ( SELECT a.noresep,
                a.nama_pembeli,
                a.tglpenjualan,
                a.penjualanresep_id
               FROM penjualanresep_t a) penjualanresep_t ON pemberianpiutang_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
         JOIN ( SELECT a.nama_pegawai,
                a.pegawai_id
               FROM pegawai_m a) pegawai_m ON pemberianpiutang_t.pegawai_id = pegawai_m.pegawai_id
         LEFT JOIN ( SELECT obatalkespasien_t.penjualanresep_id,
                sum(obatalkespasien_t.hargajual_oa) AS tagihan_obat
               FROM obatalkespasien_t
              WHERE obatalkespasien_t.is_deleted = false
              GROUP BY obatalkespasien_t.penjualanresep_id) tagihan_resep ON penjualanresep_t.penjualanresep_id = tagihan_resep.penjualanresep_id
         LEFT JOIN ( SELECT p.pegawai_id,
                p.nomorindukpegawai,
                p.nama_pegawai
               FROM pegawai_m p
              WHERE p.is_active = true) peg_mengetahui ON pemberianpiutang_t.pegawaimengetahui_id = peg_mengetahui.pegawai_id
         JOIN ( SELECT a.lookup_id,
                a.lookup_name
               FROM lookup_m a) look_statuspiutang ON pemberianpiutang_t.status_piutang = look_statuspiutang.lookup_id;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220615_105503_migration_ORDH113_infopemberianpiutang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220615_105503_migration_ORDH113_infopemberianpiutang_v cannot be reverted.\n";

        return false;
    }
    */
}
