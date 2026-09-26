<?php

use yii\db\Migration;

/**
 * Class m230301_133244_migrate_gbd_182_infopermintaanmakan_v
 */
class m230301_133244_migrate_gbd_182_infopermintaanmakan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infopermintaanmakan_v";
        ');

        $this->execute("
        CREATE OR REPLACE VIEW public.infopermintaanmakan_v
            AS   SELECT permintaanmakan_t.permintaaanmakan_id,
    permintaanmakan_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    jenis_kelamin.lookup_name AS jenis_kelamin,
    jenis_kelamin.lookup_value AS jenis_kelamin_value,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    permintaanmakan_t.no_permintaanmakan,
    pegawai_m.nama_pegawai,
    kelaspelayanan_m.kelaspelayanan_nama,
    permintaanmakan_t.tgl_permintaanmakan,
    permintaanmakan_t.status AS status_permintaanmakan,
        CASE
            WHEN permintaanmakan_t.status = 1 THEN 'PROSES'::text
            ELSE 'BATAL'::text
        END AS status_permintaan,
    permintaanmakan_t.no_pembatalan,
    permintaanmakan_t.waktu_pembatalan,
    permintaanmakan_t.alasan_pembatalan,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    ruangan_m.ruangan_id,
    kamarruangan_m.kamarruangan_id,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    dok_dpjp.nama_pegawai AS dok_dpjp,
    kamartempattidur_m.no_tempattidur,
    pemesan.nama_pegawai AS nama_pemesan,
        CASE
            WHEN permintaanmakan_t.catatan_diet IS NULL THEN catatan_diet.keterangan
            ELSE permintaanmakan_t.catatan_diet
        END AS catatan_diet,
    pendaftaran_t.is_stopakomodasi,
        CASE
            WHEN pasienadmisi_t.pasienpulang_id IS NULL THEN false
            ELSE true
        END AS is_pulang,
    pendaftaran_t.status_bayar,
    catatan_diet.is_ditagihkan,
    agama.lookup_name AS agama
   FROM permintaanmakan_t
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.jeniskasuspenyakit_id,
            a.pasien_id,
            a.no_pendaftaran,
            a.tgl_pendaftaran,
            a.umur,
            a.is_stopakomodasi,
            a.status_bayar
           FROM pendaftaran_t a) pendaftaran_t ON permintaanmakan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pasienadmisi_id,
            a.kelaspelayanan_id,
            a.ruangan_id,
            a.kamarruangan_id,
            a.kamartempattidur_id,
            a.carabayar_id,
            a.penjamin_id,
            a.pegawai_id,
            a.pasienpulang_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT a.jeniskasuspenyakit_id,
            a.jeniskasuspenyakit_nama
           FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
           FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.jeniskelamin,
            a.no_rekam_medik,
            a.tanggal_lahir,
            a.agama
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON permintaanmakan_t.peg_pemesan_id = pegawai_m.pegawai_id
     JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dok_dpjp ON pasienadmisi_t.pegawai_id = dok_dpjp.pegawai_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pemesan ON permintaanmakan_t.peg_pemesan_id = pemesan.pegawai_id
     LEFT JOIN ( SELECT b.lookup_id,
            b.lookup_value,
            b.lookup_name
           FROM lookup_m b) jenis_kelamin ON pasien_m.jeniskelamin::integer = jenis_kelamin.lookup_id
     LEFT JOIN ( SELECT a.permintaanmakan_id,
            a.keterangan,
            a.is_ditagihkan
           FROM permintaanmakandetail_t a
          ORDER BY a.created_date DESC) catatan_diet ON permintaanmakan_t.permintaaanmakan_id = catatan_diet.permintaanmakan_id
     LEFT JOIN ( SELECT b.lookup_id,
            b.lookup_value,
            b.lookup_name
           FROM lookup_m b) agama ON pasien_m.agama::integer = agama.lookup_id
UNION ALL
 SELECT permintaanmakan_t.permintaaanmakan_id,
    permintaanmakan_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    jenis_kelamin.lookup_name AS jenis_kelamin,
    jenis_kelamin.lookup_value AS jenis_kelamin_value,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    permintaanmakan_t.no_permintaanmakan,
    pegawai_m.nama_pegawai,
    kelaspelayanan_m.kelaspelayanan_nama,
    permintaanmakan_t.tgl_permintaanmakan,
    permintaanmakan_t.status AS status_permintaanmakan,
        CASE
            WHEN permintaanmakan_t.status = 1 THEN 'PROSES'::text
            ELSE 'BATAL'::text
        END AS status_permintaan,
    permintaanmakan_t.no_pembatalan,
    permintaanmakan_t.waktu_pembatalan,
    permintaanmakan_t.alasan_pembatalan,
    ruangan_m.ruangan_nama,
    ruangan_m.ruangan_nama AS kamarruangan_nokamar,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_id AS kamarruangan_id,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    dok_dpjp.nama_pegawai AS dok_dpjp,
    ''::character varying AS no_tempattidur,
    pemesan.nama_pegawai AS nama_pemesan,
        CASE
            WHEN permintaanmakan_t.catatan_diet IS NULL THEN catatan_diet.keterangan
            ELSE permintaanmakan_t.catatan_diet
        END AS catatan_diet,
    pendaftaran_t.is_stopakomodasi,
        CASE
            WHEN pendaftaran_t.pasienpulang_id IS NULL THEN false
            ELSE true
        END AS is_pulang,
    pendaftaran_t.status_bayar,
    catatan_diet.is_ditagihkan,
    agama.lookup_name AS agama
   FROM permintaanmakan_t
     JOIN ( SELECT b.pendaftaran_id,
            b.pasienadmisi_id,
            b.jeniskasuspenyakit_id,
            b.kelaspelayanan_id,
            b.pasien_id,
            b.no_pendaftaran,
            b.tgl_pendaftaran,
            b.umur,
            b.is_stopakomodasi,
            b.status_bayar,
            b.ruangan_id,
            b.carabayar_id,
            b.penjamin_id,
            b.pegawai_id,
            b.pasienpulang_id,
            b.instalasi_id
           FROM pendaftaran_t b) pendaftaran_t ON permintaanmakan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT b.jeniskasuspenyakit_id,
            b.jeniskasuspenyakit_nama
           FROM jeniskasuspenyakit_m b) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ( SELECT b.kelaspelayanan_id,
            b.kelaspelayanan_nama
           FROM kelaspelayanan_m b) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT b.ruangan_id,
            b.ruangan_nama
           FROM ruangan_m b) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT b.pasien_id,
            b.nama_pasien,
            b.no_rekam_medik,
            b.jeniskelamin,
            b.tanggal_lahir,
            b.agama
           FROM pasien_m b) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
           FROM pegawai_m b) pegawai_m ON permintaanmakan_t.peg_pemesan_id = pegawai_m.pegawai_id
     JOIN ( SELECT b.carabayar_id,
            b.carabayar_nama
           FROM carabayar_m b) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ( SELECT b.penjamin_id,
            b.penjamin_nama
           FROM penjamin_m b) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
           FROM pegawai_m b) dok_dpjp ON pendaftaran_t.pegawai_id = dok_dpjp.pegawai_id
     JOIN ( SELECT b.pegawai_id,
            b.nama_pegawai
           FROM pegawai_m b) pemesan ON permintaanmakan_t.peg_pemesan_id = pemesan.pegawai_id
     LEFT JOIN ( SELECT b.lookup_id,
            b.lookup_value,
            b.lookup_name
           FROM lookup_m b) jenis_kelamin ON pasien_m.jeniskelamin::integer = jenis_kelamin.lookup_id
     LEFT JOIN ( SELECT a.permintaanmakan_id,
            a.keterangan,
            a.is_ditagihkan
           FROM permintaanmakandetail_t a
          ORDER BY a.created_date DESC) catatan_diet ON permintaanmakan_t.permintaaanmakan_id = catatan_diet.permintaanmakan_id
     LEFT JOIN ( SELECT b.lookup_id,
            b.lookup_value,
            b.lookup_name
           FROM lookup_m b) agama ON pasien_m.agama::integer = agama.lookup_id
  WHERE (pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2])) AND pendaftaran_t.pasienadmisi_id IS NULL
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230301_133244_migrate_gbd_182_infopermintaanmakan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230301_133244_migrate_gbd_182_infopermintaanmakan_v cannot be reverted.\n";

        return false;
    }
    */
}
