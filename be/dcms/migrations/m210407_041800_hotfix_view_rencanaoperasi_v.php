<?php

use yii\db\Migration;

/**
 * Class m210407_041800_hotfix_view_rencanaoperasi_v
 */
class m210407_041800_hotfix_view_rencanaoperasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."rencanaoperasi_v";
        ');

        $this->execute('
            CREATE VIEW "public"."rencanaoperasi_v" AS  SELECT rencanaoperasi_t.rencanaoperasi_id,
                rencanaoperasi_t.pasienkirimkeunitlain_id,
                rencanaoperasi_t.pendaftaran_id,
                rencanaoperasi_t.pasienadmisi_id,
                pasienkirimkeunitlain_t.no_orderkeunitlain,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasien_m.jeniskelamin,
                pasien_m.tanggal_lahir, 
                pasien_m.photopasien,
                pendaftaran_t.umur,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
                COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) AS kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                pendaftaran_t.carabayar_id,
                carabayar_m.carabayar_nama,
                pendaftaran_t.penjamin_id,
                penjamin_m.penjamin_nama,
                rencanaoperasi_t.ruangan_id,
                ruangan_m.ruangan_nama,
                rencanaoperasi_t.tgl_permintaan,
                rencanaoperasi_t.jam_rencana_mulai,
                rencanaoperasi_t.jam_rencana_selesai,
                pasienkirimkeunitlain_t.status_penunjang,
                fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer) AS status,
                fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer) AS status_operasi,
                rencanaoperasi_t.dr_operator_id,
                dr_operator.nama_pegawai AS dok_operator,
                rencanaoperasi_t.dr_anastesi_id,
                dr_anastesi.nama_pegawai AS dok_anastesi,
                pasienkirimkeunitlain_t.pegawai_id AS dok_perujuk_id,
                dr_perujuk.nama_pegawai AS dok_perujuk,
                pasienkirimkeunitlain_t.catatan_dokterpengirim,
                    CASE
                        WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dr_pemeriksa.nama_pegawai
                        ELSE dr_pemeriksa_admisi.nama_pegawai
                    END AS dok_pemeriksa,
                inpostoperasi_t.mulai_operasi,
                inpostoperasi_t.selesai_operasi,
                app.nama_pegawai AS pegawai_approve,
                pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_approve,
                jenis_operasi.kegiatanoperasi_nama,
                pasienmasukpenunjang_t.kamarruangan_id,
                kamarruangan_m.kamarruangan_nokamar
               FROM ((((((((((((((((((((rencanaoperasi_t
                 JOIN pasienkirimkeunitlain_t ON ((rencanaoperasi_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                 JOIN pendaftaran_t ON ((rencanaoperasi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 JOIN ruangan_m ON ((rencanaoperasi_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN pegawai_m dr_operator ON ((rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id)))
                 JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 JOIN kelaspelayanan_m ON ((COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN pegawai_m dr_anastesi ON ((rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id)))
                 LEFT JOIN pegawai_m dr_perujuk ON ((pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id)))
                 LEFT JOIN pegawai_m dr_pemeriksa ON ((pendaftaran_t.pegawai_id = dr_pemeriksa.pegawai_id)))
                 LEFT JOIN pegawai_m dr_pemeriksa_admisi ON ((pasienadmisi_t.pegawai_id = dr_pemeriksa_admisi.pegawai_id)))
                 LEFT JOIN inpostoperasi_t ON ((inpostoperasi_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id)))
                 LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
                 LEFT JOIN kamarruangan_m ON ((pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
                 LEFT JOIN loginpemakai_k ON ((pasienmasukpenunjang_t.created_by = loginpemakai_k.loginpemakai_id)))
                 LEFT JOIN pegawai_m app ON ((loginpemakai_k.pegawai_id = app.pegawai_id)))
                 LEFT JOIN ( SELECT permintaankepenunjang_t.pasienkirimkeunitlain_id,
                        string_agg((kegiatanoperasi_m.kegiatanoperasi_nama)::text, \',\'::text) AS kegiatanoperasi_nama
                       FROM ((((permintaankepenunjang_t
                         JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                         JOIN operasi_m ON ((permintaankepenunjang_t.operasi_id = operasi_m.operasi_id)))
                         JOIN golonganoperasi_m ON ((operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id)))
                         JOIN kegiatanoperasi_m ON ((operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id)))
                      WHERE (permintaankepenunjang_t.is_deleted IS FALSE)
                      GROUP BY permintaankepenunjang_t.pasienkirimkeunitlain_id) jenis_operasi ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = jenis_operasi.pasienkirimkeunitlain_id)));
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210407_041800_hotfix_view_rencanaoperasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210407_041800_hotfix_view_rencanaoperasi_v cannot be reverted.\n";

        return false;
    }
    */
}
