<?php

use yii\db\Migration;

/**
 * Class m241002_040450_migrate_RPP1423_view_infokunjungantagihan_v
 */
class m241002_040450_migrate_RPP1423_view_infokunjungantagihan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS sy_kunjungantagihan_v;');

        $this->execute('
            CREATE VIEW "public"."sy_kunjungantagihan_v" AS  SELECT \'Tindakan RJ/RD\'::text AS jenis,
    \'RJ/RD\'::text AS tipe,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id, 
    pasien_m.no_rekam_medik,
    pendaftaran_t.pendaftaran_id,
    tagihan.tindakan_obat_kode,
    tagihan.tindakan_obat_nama,
    ruangan_m.ruangan_singkatan,
    ruangan_m.ruangan_nama,
    tagihan.no_tindakan_obat,
    tagihan.qty,
    tagihan.dijamin_payer AS layanan_tarif,
    tagihan.dijamin_payer AS jasa_rs,
    0 AS jasa_dokter,
    pegawai_m.dokter_id AS dokter_kode,
    pegawai_m.nama_pegawai,
    pasienpulang_t.tglpasienpulang,
    kelaspelayanan_m.kelaspelayanan_kode,
    pendaftaran_t.tgl_pendaftaran,
    pembayaran_t.no_pembayaran,
    NULL::text AS kode_nota,
    NULL::text AS kel_report,
    0 AS tarifrs_akt,
    NULL::text AS total_adjust,
    NULL::text AS kode_adjust,
    tagihan.groupinacbg_id,
    tagihan.groupinacbg_nama,
    tagihan.groupinacbg_kode
   FROM pendaftaran_t
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.pembayaran_id,
            a.pendaftaran_id,
            a.no_pembayaran
           FROM pembayaran_t a
          WHERE a.is_deleted = false) pembayaran_t ON pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id
     JOIN ( SELECT a.pembayaran_id,
            a.no_tindakanpelayanan AS no_tindakan_obat,
            a.qty_tindakan AS qty,
            a.dijamin_payer,
            a.tarif_tindakan,
            daftartindakan_m.daftartindakan_kode AS tindakan_obat_kode,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            a.dokterpenanggungjawab_id AS pegawai_id,
            a.kelaspelayanan_id,
            daftartindakan_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_kode,
            groupinacbg_m.groupinacbg_nama,
            a.pendaftaran_id
           FROM tindakanpelayanan_t a
             JOIN ( SELECT a_1.daftartindakan_id,
                    a_1.daftartindakan_kode,
                    a_1.daftartindakan_nama,
                    a_1.groupinacbg_id
                   FROM daftartindakan_m a_1) daftartindakan_m ON a.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN ( SELECT a_1.groupinacbg_id,
                    a_1.groupinacbg_kode,
                    a_1.groupinacbg_nama
                   FROM groupinacbg_m a_1) groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE a.is_deleted = false) tagihan ON pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id AND pembayaran_t.pembayaran_id = tagihan.pembayaran_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.ruangan_singkatan
           FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.dokter_id
           FROM pegawai_m a) pegawai_m ON tagihan.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_kode
           FROM kelaspelayanan_m a) kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT a.carabayar_id
           FROM carabayar_m a
          WHERE a.groupcarabayar_id = 418) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.pasienpulang_id,
            a.tglpasienpulang
           FROM pasienpulang_t a
          WHERE a.is_deleted = false AND a.pasienbatalpulang_id IS NULL) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
  WHERE pendaftaran_t.pasienadmisi_id IS NULL AND (pendaftaran_t.status_periksa::integer <> ALL (ARRAY[402, 628])) AND pendaftaran_t.pasienbatalperiksa_id IS NULL
UNION ALL
 SELECT \'Obat RJ/RD\'::text AS jenis,
    \'RJ/RD\'::text AS tipe,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pendaftaran_t.pendaftaran_id,
    tagihan.tindakan_obat_kode,
    tagihan.tindakan_obat_nama,
    ruangan_m.ruangan_singkatan,
    ruangan_m.ruangan_nama,
    tagihan.no_tindakan_obat,
    tagihan.qty,
    tagihan.dijamin_payer AS layanan_tarif,
    tagihan.dijamin_payer AS jasa_rs,
    0 AS jasa_dokter,
    pegawai_m.dokter_id AS dokter_kode,
    pegawai_m.nama_pegawai,
    pasienpulang_t.tglpasienpulang,
    kelaspelayanan_m.kelaspelayanan_kode,
    pendaftaran_t.tgl_pendaftaran,
    pembayaran_t.no_pembayaran,
    NULL::text AS kode_nota,
    NULL::text AS kel_report,
    0 AS tarifrs_akt,
    NULL::text AS total_adjust,
    NULL::text AS kode_adjust,
    tagihan.groupinacbg_id,
    tagihan.groupinacbg_nama,
    tagihan.groupinacbg_kode
   FROM pendaftaran_t
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.pembayaran_id,
            a.pendaftaran_id,
            a.no_pembayaran
           FROM pembayaran_t a
          WHERE a.is_deleted = false) pembayaran_t ON pendaftaran_t.pendaftaran_id = pembayaran_t.pendaftaran_id
     JOIN ( SELECT a.pembayaran_id,
            a.no_obatalkespasien AS no_tindakan_obat,
            a.qty_oa AS qty,
            a.dijamin_payer,
            a.hargajual_oa AS tarif_tindakan,
            obatalkes_m.obatalkes_kode AS tindakan_obat_kode,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            a.pegawai_id,
            a.kelaspelayanan_id,
            obatalkes_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_kode,
            groupinacbg_m.groupinacbg_nama,
            a.pendaftaran_id
           FROM obatalkespasien_t a
             JOIN ( SELECT a_1.pembayaran_id,
                    a_1.obatalkespasien_id
                   FROM obatsudahbayar_t a_1) obatsudahbayar_t ON a.obatalkespasien_id = obatsudahbayar_t.obatalkespasien_id
             JOIN ( SELECT a_1.obatalkes_id,
                    a_1.obatalkes_kode,
                    a_1.obatalkes_nama,
                    a_1.groupinacbg_id
                   FROM obatalkes_m a_1) obatalkes_m ON a.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN ( SELECT a_1.groupinacbg_id,
                    a_1.groupinacbg_kode,
                    a_1.groupinacbg_nama
                   FROM groupinacbg_m a_1) groupinacbg_m ON
                CASE
                    WHEN a.is_kronis THEN 17
                    ELSE obatalkes_m.groupinacbg_id
                END = groupinacbg_m.groupinacbg_id
          WHERE a.is_deleted = false) tagihan ON pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id AND pembayaran_t.pembayaran_id = tagihan.pembayaran_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.ruangan_singkatan
           FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.dokter_id
           FROM pegawai_m a) pegawai_m ON tagihan.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_kode
           FROM kelaspelayanan_m a) kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT a.carabayar_id
           FROM carabayar_m a
          WHERE a.groupcarabayar_id = 418) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.pasienpulang_id,
            a.tglpasienpulang
           FROM pasienpulang_t a
          WHERE a.is_deleted = false AND a.pasienbatalpulang_id IS NULL) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
  WHERE pendaftaran_t.pasienadmisi_id IS NULL AND (pendaftaran_t.status_periksa::integer <> ALL (ARRAY[402, 628])) AND pendaftaran_t.pasienbatalperiksa_id IS NULL
UNION ALL
 SELECT \'Tindakan RI\'::text AS jenis,
    \'RI\'::text AS tipe,
    pendaftaran_t.no_pendaftaran,
    pasienadmisi_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasienadmisi_t.pendaftaran_id,
    tagihan.tindakan_obat_kode,
    tagihan.tindakan_obat_nama,
    ruangan_m.ruangan_singkatan,
    ruangan_m.ruangan_nama,
    tagihan.no_tindakan_obat,
    tagihan.qty,
    tagihan.dijamin_payer AS layanan_tarif,
    tagihan.dijamin_payer AS jasa_rs,
    0 AS jasa_dokter,
    pegawai_m.dokter_id AS dokter_kode,
    pegawai_m.nama_pegawai,
    pasienpulang_t.tglpasienpulang,
    kelaspelayanan_m.kelaspelayanan_kode,
    pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
    pembayaran_t.no_pembayaran,
    NULL::text AS kode_nota,
    NULL::text AS kel_report,
    0 AS tarifrs_akt,
    NULL::text AS total_adjust,
    NULL::text AS kode_adjust,
    tagihan.groupinacbg_id,
    tagihan.groupinacbg_nama,
    tagihan.groupinacbg_kode
   FROM pasienadmisi_t
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran
           FROM pendaftaran_t a) pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik
           FROM pasien_m a) pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.pembayaran_id,
            a.pendaftaran_id,
            a.no_pembayaran
           FROM pembayaran_t a
          WHERE a.is_deleted = false) pembayaran_t ON pasienadmisi_t.pendaftaran_id = pembayaran_t.pendaftaran_id
     JOIN ( SELECT a.pembayaran_id,
            a.no_tindakanpelayanan AS no_tindakan_obat,
            a.qty_tindakan AS qty,
            a.dijamin_payer,
            a.tarif_tindakan,
            daftartindakan_m.daftartindakan_kode AS tindakan_obat_kode,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            a.dokterpenanggungjawab_id AS pegawai_id,
            a.kelaspelayanan_id,
            daftartindakan_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_kode,
            groupinacbg_m.groupinacbg_nama,
            a.pendaftaran_id
           FROM tindakanpelayanan_t a
             JOIN ( SELECT a_1.daftartindakan_id,
                    a_1.daftartindakan_kode,
                    a_1.daftartindakan_nama,
                    a_1.groupinacbg_id
                   FROM daftartindakan_m a_1) daftartindakan_m ON a.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN ( SELECT a_1.groupinacbg_id,
                    a_1.groupinacbg_kode,
                    a_1.groupinacbg_nama
                   FROM groupinacbg_m a_1) groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE a.is_deleted = false) tagihan ON pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id AND pembayaran_t.pembayaran_id = tagihan.pembayaran_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.ruangan_singkatan
           FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.dokter_id
           FROM pegawai_m a) pegawai_m ON tagihan.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_kode
           FROM kelaspelayanan_m a) kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT a.carabayar_id
           FROM carabayar_m a
          WHERE a.groupcarabayar_id = 418) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.pasienpulang_id,
            a.tglpasienpulang
           FROM pasienpulang_t a
          WHERE a.is_deleted = false AND a.pasienbatalpulang_id IS NULL) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
  WHERE pasienadmisi_t.status_ranap <> 453 AND pasienadmisi_t.pasienbatalperiksa_id IS NULL
UNION ALL
 SELECT \'Obat RI\'::text AS jenis,
    \'RI\'::text AS tipe,
    pendaftaran_t.no_pendaftaran,
    pasienadmisi_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasienadmisi_t.pendaftaran_id,
    tagihan.tindakan_obat_kode,
    tagihan.tindakan_obat_nama,
    ruangan_m.ruangan_singkatan,
    ruangan_m.ruangan_nama,
    tagihan.no_tindakan_obat,
    tagihan.qty,
    tagihan.dijamin_payer AS layanan_tarif,
    tagihan.dijamin_payer AS jasa_rs,
    0 AS jasa_dokter,
    pegawai_m.dokter_id AS dokter_kode,
    pegawai_m.nama_pegawai,
    pasienpulang_t.tglpasienpulang,
    kelaspelayanan_m.kelaspelayanan_kode,
    pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
    pembayaran_t.no_pembayaran,
    NULL::text AS kode_nota,
    NULL::text AS kel_report,
    0 AS tarifrs_akt,
    NULL::text AS total_adjust,
    NULL::text AS kode_adjust,
    tagihan.groupinacbg_id,
    tagihan.groupinacbg_nama,
    tagihan.groupinacbg_kode
   FROM pasienadmisi_t
     JOIN ( SELECT a.pendaftaran_id,
            a.no_pendaftaran
           FROM pendaftaran_t a) pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik
           FROM pasien_m a) pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.pembayaran_id,
            a.pendaftaran_id,
            a.no_pembayaran
           FROM pembayaran_t a
          WHERE a.is_deleted = false) pembayaran_t ON pasienadmisi_t.pendaftaran_id = pembayaran_t.pendaftaran_id
     JOIN ( SELECT a.pembayaran_id,
            a.no_obatalkespasien AS no_tindakan_obat,
            a.qty_oa AS qty,
            a.dijamin_payer,
            a.hargajual_oa AS tarif_tindakan,
            obatalkes_m.obatalkes_kode AS tindakan_obat_kode,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            a.pegawai_id,
            a.kelaspelayanan_id,
            obatalkes_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_kode,
            groupinacbg_m.groupinacbg_nama,
            a.pendaftaran_id
           FROM obatalkespasien_t a
             JOIN ( SELECT a_1.pembayaran_id,
                    a_1.obatalkespasien_id
                   FROM obatsudahbayar_t a_1) obatsudahbayar_t ON a.obatalkespasien_id = obatsudahbayar_t.obatalkespasien_id
             JOIN ( SELECT a_1.obatalkes_id,
                    a_1.obatalkes_kode,
                    a_1.obatalkes_nama,
                    a_1.groupinacbg_id
                   FROM obatalkes_m a_1) obatalkes_m ON a.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN ( SELECT a_1.groupinacbg_id,
                    a_1.groupinacbg_kode,
                    a_1.groupinacbg_nama
                   FROM groupinacbg_m a_1) groupinacbg_m ON
                CASE
                    WHEN a.is_kronis THEN 17
                    ELSE obatalkes_m.groupinacbg_id
                END = groupinacbg_m.groupinacbg_id
          WHERE a.is_deleted = false) tagihan ON pendaftaran_t.pendaftaran_id = tagihan.pendaftaran_id AND pembayaran_t.pembayaran_id = tagihan.pembayaran_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.ruangan_singkatan
           FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.dokter_id
           FROM pegawai_m a) pegawai_m ON tagihan.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_kode
           FROM kelaspelayanan_m a) kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT a.carabayar_id
           FROM carabayar_m a
          WHERE a.groupcarabayar_id = 418) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.pasienpulang_id,
            a.tglpasienpulang
           FROM pasienpulang_t a
          WHERE a.is_deleted = false AND a.pasienbatalpulang_id IS NULL) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
  WHERE pasienadmisi_t.status_ranap <> 453 AND pasienadmisi_t.pasienbatalperiksa_id IS NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241002_040450_migrate_RPP1423_view_infokunjungantagihan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241002_040450_migrate_RPP1423_view_infokunjungantagihan_v cannot be reverted.\n";

        return false;
    }
    */
}
