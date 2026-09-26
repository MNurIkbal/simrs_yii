<?php

use yii\db\Migration;

/**
 * Class m210518_151037_migrate_20210518_infoorderanlab_v
 */
class m210518_151037_migrate_20210518_infoorderanlab_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute('DROP VIEW if exists "public"."infoorderanlab_v";');

       $this->execute("
        CREATE VIEW \"public\".\"infoorderanlab_v\" AS  SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pasienkirimkeunitlain_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    pendaftaran_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    COALESCE(pasienmasukpenunjang_t.is_bayar, false) AS is_bayar,
    pasienmasukpenunjang_t.status_periksa,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
        CASE COALESCE(pasienmasukpenunjang_t.is_bayar, false)
            WHEN true THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar,
    pasienkirimkeunitlain_t.is_rujukan,
    COALESCE(pemeriksaan.jml_pemeriksaan, 0::bigint) AS jml_pemeriksaan,
    COALESCE(pemeriksaan_approve.jml_pemeriksaan_approve, 0::bigint) AS jml_pemeriksaan_approve,
    COALESCE(tindakanpelayanan.jumlah_tagihan, 0::double precision) AS jumlah_tagihan,
    COALESCE(tindakan_bayar.jumlah_bayar, 0::double precision) AS jumlah_bayar,
    fgetkodelookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin_kode,
    COALESCE(cyto_tindakan.is_cyto, false) AS is_cyto
   FROM pasienkirimkeunitlain_t
     JOIN pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            count(*) AS jml_pemeriksaan
           FROM permintaankepenunjang_t
          WHERE permintaankepenunjang_t.is_deleted IS FALSE
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            count(*) AS jml_pemeriksaan_approve
           FROM permintaankepenunjang_t
          WHERE permintaankepenunjang_t.is_deleted IS FALSE AND permintaankepenunjang_t.is_approve IS TRUE
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan_approve ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan_approve.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_tagihan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_bayar
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakan_bayar ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_bayar.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT DISTINCT ON (permintaankepenunjang_t.pasienkirimkeunitlain_id, permintaankepenunjang_t.is_cyto) permintaankepenunjang_t.pasienkirimkeunitlain_id,
            permintaankepenunjang_t.is_cyto
           FROM permintaankepenunjang_t
          WHERE permintaankepenunjang_t.is_cyto = true AND permintaankepenunjang_t.is_deleted = false) cyto_tindakan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = cyto_tindakan.pasienkirimkeunitlain_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4
UNION ALL
 SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pendaftaran_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer) AS stat_penunjang,
    pasienadmisi_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasienadmisi_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    COALESCE(pasienmasukpenunjang_t.is_bayar, false) AS is_bayar,
    pasienmasukpenunjang_t.status_periksa,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
        CASE COALESCE(pasienmasukpenunjang_t.is_bayar, false)
            WHEN true THEN 'Sudah Bayar'::text
            ELSE 'Belum Bayar'::text
        END AS status_bayar,
    pasienkirimkeunitlain_t.is_rujukan,
    COALESCE(pemeriksaan.jml_pemeriksaan, 0::bigint) AS jml_pemeriksaan,
    COALESCE(pemeriksaan_approve.jml_pemeriksaan_approve, 0::bigint) AS jml_pemeriksaan_approve,
    COALESCE(tindakanpelayanan.jumlah_tagihan, 0::double precision) AS jumlah_tagihan,
    COALESCE(tindakan_bayar.jumlah_bayar, 0::double precision) AS jumlah_bayar,
    fgetkodelookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin_kode,
    COALESCE(cyto_tindakan.is_cyto, false) AS is_cyto
   FROM pasienkirimkeunitlain_t
     JOIN pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
     JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            count(*) AS jml_pemeriksaan
           FROM permintaankepenunjang_t
          WHERE permintaankepenunjang_t.is_deleted IS FALSE
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
            count(*) AS jml_pemeriksaan_approve
           FROM permintaankepenunjang_t
          WHERE permintaankepenunjang_t.is_deleted IS FALSE AND permintaankepenunjang_t.is_approve IS TRUE
          GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) pemeriksaan_approve ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan_approve.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_tagihan
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, 0::double precision)) AS jumlah_bayar
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL AND tindakanpelayanan_t.is_deleted = false
          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakan_bayar ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_bayar.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT DISTINCT ON (permintaankepenunjang_t.pasienkirimkeunitlain_id, permintaankepenunjang_t.is_cyto) permintaankepenunjang_t.pasienkirimkeunitlain_id,
            permintaankepenunjang_t.is_cyto
           FROM permintaankepenunjang_t
          WHERE permintaankepenunjang_t.is_cyto = true AND permintaankepenunjang_t.is_deleted = false) cyto_tindakan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = cyto_tindakan.pasienkirimkeunitlain_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4;");

       $this->execute('ALTER TABLE "public"."infoorderanlab_v" OWNER TO "postgres";');
       
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210518_151037_migrate_20210518_infoorderanlab_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210518_151037_migrate_20210518_infoorderanlab_v cannot be reverted.\n";

        return false;
    }
    */
}
