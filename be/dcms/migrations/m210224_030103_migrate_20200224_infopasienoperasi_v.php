<?php

use yii\db\Migration;

/**
 * Class m210224_030103_migrate_20200224_infopasienoperasi_v
 */
class m210224_030103_migrate_20200224_infopasienoperasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infopasienoperasi_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienoperasi_v\" AS  SELECT 'ORDER'::text AS jenis,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_m.instalasi_nama AS asalrujukan_nama,
    pasienmasukpenunjang_t.ruanganasal_id,
    ruangan_m.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer) AS status,
    pasienmasukpenunjang_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) AS kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienkirimkeunitlain_t.status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pasienkirimkeunitlain_t.pegawai_id AS dok_perujuk_id,
    dr_perujuk.nama_pegawai AS dok_perujuk,
    pasienmasukpenunjang_t.catatan AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
    cppt_t.a_diag_utama ->> 'text'::text AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama,
    login_pemakai.nama_pegawai AS created_by
   FROM pasienmasukpenunjang_t
     JOIN rencanaoperasi_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = rencanaoperasi_t.pasienmasukpenunjang_id
     JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
     JOIN instalasi_m ON pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
     LEFT JOIN pegawai_m dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN pegawai_m dr_perujuk ON pasienkirimkeunitlain_t.pegawai_id = dr_perujuk.pegawai_id
     LEFT JOIN cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id AND pasienadmisi_t.pegawai_id = cppt_t.pegawai_id AND cppt_t.is_deleted = false AND cppt_t.is_active = true AND cppt_t.is_instruksi_pulang = false
     LEFT JOIN kamarruangan_m ON pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
            pegawai_m_1.nama_pegawai
           FROM loginpemakai_k
             JOIN pegawai_m pegawai_m_1 ON loginpemakai_k.pegawai_id = pegawai_m_1.pegawai_id) login_pemakai ON pasienmasukpenunjang_t.created_by = login_pemakai.loginpemakai_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 12 AND pasienmasukpenunjang_t.status_periksa IS NOT NULL
UNION ALL
 SELECT 'APS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pendaftaran_t.tgl_pendaftaran AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer) AS status,
    NULL::character varying AS no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama,
    login_pemakai.nama_pegawai AS created_by
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m dok_perujuk ON pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id
     JOIN ruangan_m ruangan_asal ON pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id
     JOIN instalasi_m instalasi_asal ON pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN rencanaoperasi_t ON pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
     LEFT JOIN pegawai_m dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN pegawai_m dr_penunjang ON pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id
     LEFT JOIN kamarruangan_m ON pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
            pegawai_m.nama_pegawai
           FROM loginpemakai_k
             JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id) login_pemakai ON pasienmasukpenunjang_t.created_by = login_pemakai.loginpemakai_id
  WHERE pasienmasukpenunjang_t.status_periksa IS NOT NULL AND pendaftaran_t.instalasi_id = 12 AND pasienmasukpenunjang_t.is_bayar = true
UNION ALL
 SELECT 'PASIEN RS'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
    rencanaoperasi_t.rencanaoperasi_id,
    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
    pasienmasukpenunjang_t.no_masukpenunjang,
    rencanaoperasi_t.tgl_permintaan AS tgl_operasi,
    pasienmasukpenunjang_t.tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.photopasien,
    pasienmasukpenunjang_t.pegawai_id,
    dr_penunjang.nama_pegawai AS dokter_penunjang,
    pendaftaran_t.no_pendaftaran AS no_rujukan,
    pasienmasukpenunjang_t.instalasiasal_id,
    instalasi_asal.instalasi_nama AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_asal.ruangan_nama,
    pasienmasukpenunjang_t.status_periksa,
    fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer) AS status,
    NULL::character varying AS no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    pendaftaran_t.label_gelang::json ->> 'resiko_jatuh'::text AS kuning,
    pendaftaran_t.label_gelang::json ->> 'alergi'::text AS merah,
    pendaftaran_t.label_gelang::json ->> 'dnr'::text AS ungu,
    pendaftaran_t.label_gelang::json ->> 'duplikat'::text AS coklat,
    pasienmasukpenunjang_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    pasienmasukpenunjang_t.is_bayar,
    NULL::character varying AS status_penunjang,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    rencanaoperasi_t.dr_operator_id,
    dr_operator.nama_pegawai AS dok_operator,
    rencanaoperasi_t.dr_anastesi_id,
    dr_anastesi.nama_pegawai AS dok_anastesi,
    pendaftaran_t.pegawai_id AS dok_perujuk_id,
    dok_perujuk.nama_pegawai AS dok_perujuk,
    pendaftaran_t.keterangan_pendaftaran AS catatan_dokterpengirim,
    rencanaoperasi_t.jam_rencana_mulai,
    rencanaoperasi_t.jam_rencana_selesai,
    pendaftaran_t.jeniskasuspenyakit_id,
    NULL::text AS a_diag_utama,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamarruangan_nama,
    login_pemakai.nama_pegawai AS created_by
   FROM pasienmasukpenunjang_t
     JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m dok_perujuk ON pasienmasukpenunjang_t.pegawai_id = dok_perujuk.pegawai_id
     JOIN ruangan_m ruangan_asal ON pendaftaran_t.ruangan_id = ruangan_asal.ruangan_id
     JOIN instalasi_m instalasi_asal ON pendaftaran_t.instalasi_id = instalasi_asal.instalasi_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m ruangan_penunjang ON pasienmasukpenunjang_t.ruangan_id = ruangan_penunjang.ruangan_id
     LEFT JOIN rencanaoperasi_t ON pendaftaran_t.pendaftaran_id = rencanaoperasi_t.pendaftaran_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dr_operator ON rencanaoperasi_t.dr_operator_id = dr_operator.pegawai_id
     LEFT JOIN pegawai_m dr_anastesi ON rencanaoperasi_t.dr_anastesi_id = dr_anastesi.pegawai_id
     LEFT JOIN pegawai_m dr_penunjang ON pasienmasukpenunjang_t.pegawai_id = dr_penunjang.pegawai_id
     LEFT JOIN kamarruangan_m ON pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
            pegawai_m.nama_pegawai
           FROM loginpemakai_k
             JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id) login_pemakai ON pasienmasukpenunjang_t.created_by = login_pemakai.loginpemakai_id
  WHERE pasienmasukpenunjang_t.status_periksa IS NOT NULL AND ruangan_penunjang.instalasi_id = 12 AND pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL AND pendaftaran_t.instalasi_id <> 12;");

        $this->execute('ALTER TABLE "public"."infopasienoperasi_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210224_030103_migrate_20200224_infopasienoperasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210224_030103_migrate_20200224_infopasienoperasi_v cannot be reverted.\n";

        return false;
    }
    */
}
