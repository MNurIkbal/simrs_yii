<?php

use yii\db\Migration;

/**
 * Class m250409_095454_migrate_optimasi_view_infotagihanpenunjang_v
 */
class m250409_095454_migrate_optimasi_view_infotagihanpenunjang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS infotagihanpenunjang_v;
        ');

        $this->execute('
            CREATE VIEW "public"."infotagihanpenunjang_v" AS  SELECT tagihanpenunjang.pendaftaran_id,
    tagihanpenunjang.tgl_pendaftaran,
    tagihanpenunjang.tglmasukpenunjang, 
    tagihanpenunjang.no_pendaftaran,
    tagihanpenunjang.jeniskasuspenyakit_nama,
    tagihanpenunjang.kelaspelayanan_nama,
    tagihanpenunjang.dokter,
    tagihanpenunjang.ruang_pendaftaran,
    tagihanpenunjang.instalasi_nama,
    tagihanpenunjang.ruangan_nama,
    tagihanpenunjang.no_rekam_medik,
    tagihanpenunjang.nama_pasien,
    tagihanpenunjang.carabayar_nama,
    tagihanpenunjang.penjamin_nama,
    tagihanpenunjang.total_tindakan + tagihanpenunjang.total_obat AS jumlah_tagihan,
    tagihanpenunjang.pasienmasukpenunjang_id,
    tagihanpenunjang.jeniskelamin,
    tagihanpenunjang.jenis_kelamin,
    tagihanpenunjang.umur,
    tagihanpenunjang.tanggal_lahir,
    tagihanpenunjang.no_masukpenunjang,
    tagihanpenunjang.ruangan_id,
    tagihanpenunjang.carabayar_kode_warna,
    tagihanpenunjang.is_close_bill
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pasienmasukpenunjang_t.tglmasukpenunjang,
            pendaftaran_t.no_pendaftaran,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            kelaspelayanan_m.kelaspelayanan_nama,
            pegawai_m.nama_pegawai AS dokter,
            ruang_pendaftaran.ruangan_nama AS ruang_pendaftaran,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM tindakanpelayanan_t
                      WHERE tindakanpelayanan_t.is_deleted IS FALSE AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                     LIMIT 1)) THEN ( SELECT COALESCE(round(sum(a.tarif_tindakan)::numeric, 2), 0::numeric) AS tarif_tindakan
                       FROM tindakanpelayanan_t a
                      WHERE a.is_deleted IS FALSE AND a.tindakansudahbayar_id IS NULL AND a.pendaftaran_id IS NOT NULL AND pasienmasukpenunjang_t.pasienmasukpenunjang_id = a.pasienmasukpenunjang_id AND pendaftaran_t.pendaftaran_id = a.pendaftaran_id)
                    ELSE 0::numeric
                END AS total_tindakan,
                CASE
                    WHEN (EXISTS ( SELECT 1
                       FROM obatalkespasien_t
                      WHERE obatalkespasien_t.is_deleted IS FALSE AND obatalkespasien_t.obatsudahbayar_id IS NULL AND pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
                     LIMIT 1)) THEN ( SELECT COALESCE(round(sum(a.hargajual_oa)::numeric, 2), 0::numeric) AS "coalesce"
                       FROM obatalkespasien_t a
                      WHERE a.is_deleted IS FALSE AND a.obatsudahbayar_id IS NULL AND a.pendaftaran_id IS NOT NULL AND pasienmasukpenunjang_t.pasienmasukpenunjang_id = a.pasienmasukpenunjang_id AND pendaftaran_t.pendaftaran_id = a.pendaftaran_id)
                    ELSE 0::numeric
                END AS total_obat,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasien_m.jeniskelamin,
            lkp_jk.lookup_name AS jenis_kelamin,
            pendaftaran_t.umur,
            pasien_m.tanggal_lahir,
            pasienmasukpenunjang_t.no_masukpenunjang,
            pasienmasukpenunjang_t.ruangan_id,
            carabayar_m.carabayar_kode_warna,
            pendaftaran_t.is_close_bill
           FROM pasienmasukpenunjang_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.tgl_pendaftaran,
                    a.no_pendaftaran,
                    a.ruangan_id,
                    a.pasien_id,
                    a.carabayar_id,
                    a.penjamin_id,
                    a.jeniskasuspenyakit_id,
                    a.kelaspelayanan_id,
                    a.pegawai_id,
                    a.umur,
                    a.is_close_bill,
                    a.status_bayar,
                    a.status_periksa
                   FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.ruangan_id
                   FROM pasienadmisi_t a) pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruang_pendaftaran ON COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) = ruang_pendaftaran.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.jeniskelamin,
                    a.tanggal_lahir
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) lkp_jk ON pasien_m.jeniskelamin::integer = lkp_jk.lookup_id
          WHERE pendaftaran_t.status_bayar = 349 AND (pendaftaran_t.status_periksa::text <> ALL (ARRAY[\'402\'::character varying::text, \'628\'::character varying::text])) AND NOT (pendaftaran_t.pendaftaran_id IN ( SELECT gabungpelayanandetail_t.pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted IS FALSE))
          GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, ruang_pendaftaran.ruangan_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, pendaftaran_t.pendaftaran_id, pasienmasukpenunjang_t.tglmasukpenunjang, pendaftaran_t.no_pendaftaran, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, pendaftaran_t.tgl_pendaftaran, pasien_m.jeniskelamin, pasien_m.tanggal_lahir, pasienmasukpenunjang_t.no_masukpenunjang, carabayar_m.carabayar_kode_warna, pendaftaran_t.is_close_bill, pendaftaran_t.umur, lkp_jk.lookup_name) tagihanpenunjang;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250409_095454_migrate_optimasi_view_infotagihanpenunjang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250409_095454_migrate_optimasi_view_infotagihanpenunjang_v cannot be reverted.\n";

        return false;
    }
    */
}
