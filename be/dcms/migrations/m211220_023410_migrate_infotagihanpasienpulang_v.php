<?php

use yii\db\Migration;

/**
 * Class m211220_023410_migrate_infotagihanpasienpulang_v
 */
class m211220_023410_migrate_infotagihanpasienpulang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infotagihanpasienpulang_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infotagihanpasienpulang_v\" AS  SELECT gabung.pendaftaran_id,
    gabung.pasienpulang_id,
    gabung.tglpasienpulang,
    gabung.no_pendaftaran,
    gabung.instalasi_id,
    gabung.instalasi_nama,
    gabung.ruanganakhir_id AS ruangan_id,
    gabung.ruangan_nama,
    gabung.no_rekam_medik,
    gabung.nama_pasien,
    gabung.carabayar_id,
    gabung.carabayar_nama,
    gabung.penjamin_id,
    gabung.penjamin_nama,
    gabung.jeniskasuspenyakit_nama,
    gabung.status_bayar,
    gabung.kelaspelayanan_nama,
    gabung.nama_pegawai AS dokter,
    gabung.total_tindakan,
    gabung.total_obat,
    COALESCE(gabung.total_tindakan, 0::numeric) + COALESCE(gabung.total_obat, 0::numeric) AS total_tagihan,
    gabung.pegawai_id,
    gabung.photopasien,
    gabung.tanggal_lahir,
    gabung.umur,
    gabung.jeniskelamin,
    gabung.jenis_kelamin,
    gabung.tgl_pendaftaran,
    gabung.is_stopakomodasi,
    gabung.tgl_stopakomodasi,
    gabung.no_sep,
    gabung.status_pulang
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienpulang_id,
            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
            COALESCE(pulang_ri.tglpasienpulang, pendaftaran_t.tgl_stopakomodasi, pulang_rjrd.tglpasienpulang) AS tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            COALESCE(ruangan_ri.instalasi_id, ruangan_rjrd.instalasi_id) AS instalasi_id,
            COALESCE(instalasi_ri.instalasi_nama, instalasi_rjrd.instalasi_nama) AS instalasi_nama,
            COALESCE(ruangan_ri.ruangan_id, ruangan_rjrd.ruangan_id) AS ruanganakhir_id,
            COALESCE(ruangan_ri.ruangan_nama, ruangan_rjrd.ruangan_nama) AS ruangan_nama,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            COALESCE(penjamin_ri.carabayar_id, penjamin_rjrd.carabayar_id) AS carabayar_id,
            COALESCE(carabayar_ri.carabayar_nama, carabayar_rjrd.carabayar_nama) AS carabayar_nama,
            COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) AS penjamin_id,
            COALESCE(penjamin_ri.penjamin_nama, penjamin_rjrd.penjamin_nama) AS penjamin_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            COALESCE(kelaspelayanan_ri.kelaspelayanan_nama, kelaspelayanan_rjrd.kelaspelayanan_nama) AS kelaspelayanan_nama,
            COALESCE(pegawai_ri.nama_pegawai, pegawai_rjrd.nama_pegawai) AS nama_pegawai,
            tagihan_tindakan.tarif_tindakan AS total_tindakan,
            tagihan_obat.hargajual_oa AS total_obat,
            pendaftaran_t.pegawai_id,
            pasien_m.photopasien,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            pasien_m.jeniskelamin,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            pendaftaran_t.tgl_pendaftaran,
            NULL::text AS sudah_bayar,
            pendaftaran_t.is_stopakomodasi,
            pendaftaran_t.tgl_stopakomodasi,
            bpjs_t.nosep AS no_sep,
            COALESCE(fgetnamalookup(pasienadmisi_t.status_ranap), fgetnamalookup(pendaftaran_t.status_periksa::integer)) AS status_pulang,
            pendaftaran_t.pasienadmisi_id
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    COALESCE(round(sum(a.tarif_tindakan)::numeric, 2), 0::numeric) AS tarif_tindakan
                   FROM tindakanpelayanan_t a
                  WHERE a.is_deleted = false AND a.tindakansudahbayar_id IS NULL
                  GROUP BY a.pendaftaran_id) tagihan_tindakan ON pendaftaran_t.pendaftaran_id = tagihan_tindakan.pendaftaran_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    COALESCE(round(sum(a.hargajual_oa)::numeric, 2), 0::numeric) AS hargajual_oa
                   FROM obatalkespasien_t a
                  WHERE a.is_deleted = false AND a.obatsudahbayar_id IS NULL
                  GROUP BY a.pendaftaran_id) tagihan_obat ON pendaftaran_t.pendaftaran_id = tagihan_obat.pendaftaran_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang,
                    a.ruanganakhir_id
                   FROM pasienpulang_t a) pulang_rjrd ON pendaftaran_t.pasienpulang_id = pulang_rjrd.pasienpulang_id
             LEFT JOIN ( SELECT a.pasienpulang_id,
                    a.tglpasienpulang,
                    a.ruanganakhir_id
                   FROM pasienpulang_t a) pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik,
                    a.photopasien,
                    a.tanggal_lahir,
                    a.jeniskelamin
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_rjrd ON pendaftaran_t.ruangan_id = ruangan_rjrd.ruangan_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.instalasi_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_ri ON pasienadmisi_t.ruangan_id = ruangan_ri.ruangan_id
             LEFT JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_rjrd ON ruangan_rjrd.instalasi_id = instalasi_rjrd.instalasi_id
             LEFT JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_ri ON ruangan_ri.instalasi_id = instalasi_ri.instalasi_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.carabayar_id
                   FROM penjamin_m a) penjamin_rjrd ON pendaftaran_t.penjamin_id = penjamin_rjrd.penjamin_id
             LEFT JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama,
                    a.carabayar_id
                   FROM penjamin_m a) penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_rjrd ON penjamin_rjrd.carabayar_id = carabayar_rjrd.carabayar_id
             LEFT JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_ri ON penjamin_ri.carabayar_id = carabayar_ri.carabayar_id
             LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_rjrd ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_rjrd.kelaspelayanan_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_ri ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_ri.kelaspelayanan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_rjrd ON pendaftaran_t.pegawai_id = pegawai_rjrd.pegawai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_ri ON pasienadmisi_t.pegawai_id = pegawai_ri.pegawai_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.nosep
                   FROM bpjs_t a) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) gabung
  WHERE gabung.pasienpulang_id IS NOT NULL AND gabung.pasienadmisi_id IS NULL OR gabung.is_stopakomodasi = true AND gabung.pasienadmisi_id IS NOT NULL;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211220_023410_migrate_infotagihanpasienpulang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211220_023410_migrate_infotagihanpasienpulang_v cannot be reverted.\n";

        return false;
    }
    */
}
