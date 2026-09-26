<?php

use yii\db\Migration;

/**
 * Class m211125_031304_migrate_addalamat_inforeseptur_v
 */
class m211125_031304_migrate_addalamat_inforeseptur_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute('DROP VIEW if exists public.inforeseptur_v;');
        $this->execute("
          CREATE VIEW \"public\".\"inforeseptur_v\" AS
          SELECT reseptur_t.reseptur_id,
          reseptur_t.pasien_id,
          reseptur_t.pendaftaran_id,
          reseptur_t.pasienadmisi_id,
          CASE
          WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.carabayar_id
          ELSE penjamin_ri.carabayar_id
          END AS carabayar_id,
          CASE
          WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.penjamin_id
          ELSE pasienadmisi_t.penjamin_id
          END AS penjamin_id,
          CASE
          WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_m.carabayar_nama
          ELSE carabayar_ri.carabayar_nama
          END AS carabayar_nama,
          CASE
          WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_m.penjamin_nama
          ELSE penjamin_ri.penjamin_nama
          END AS penjamin_nama,
          pendaftaran_t.umur,
          reseptur_t.ruangan_id,
          reseptur_t.ruanganreseptur_id,
          reseptur_t.tglreseptur,
          reseptur_t.noresep,
          reseptur_t.penjualanresep_id,
          pendaftaran_t.no_pendaftaran,
          pasien_m.no_rekam_medik,
          concat(fgetnamalookup((pasien_m.namadepan)::integer), ' ', pasien_m.nama_pasien) AS nama_pasien,
          pasien_m.tanggal_lahir,
          fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
          ruangan_tujuan.ruangan_nama AS ruangan_tujuan,
          ruangan_reseptur.ruangan_nama AS ruangan_reseptur,
          fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur,
          reseptur_t.pegawai_id,
          pegawai_m.nama_pegawai,
          ruangan_reseptur.instalasi_id AS instalasi_reseptur_id,
          instalasi_reseptur.instalasi_nama AS instalasi_reseptur,
          ruangan_tujuan.instalasi_id AS instalasi_tujuan_id,
          instalasi_tujuan.instalasi_nama AS instalasi_tujuan,
          sum(obatalkes_m.harganetto) AS total_harganetto,
          antrian_t.no_antrian,
          reseptur_t.status_reseptur AS status_reseptur_id,
          reseptur_t.is_hamil,
          reseptur_t.berat_badan,
          reseptur_t.tinggi_badan,
          reseptur_t.luas_tubuh,
          reseptur_t.diagnosa_id,
          concat(diagnosa_m.diagnosa_kode, '-', diagnosa_m.diagnosa_nama) AS diagnosa_nama,
          reseptur_t.instruksi_id,
          reseptur_t.antrian_id,
          string_agg((resepturdetail_t.racikan_id)::text, '-'::text) AS antrian_racikan,
          penjualanresep_t.catatan,
          resepturdetail_t.iter,
          penjualanresep_t.noresep AS noresep_penjualan,
          resepturdetail_t.iter AS iter_penjualan,
          CASE
          WHEN (ruangan_reseptur.instalasi_id = 1) THEN (anamnesa_t.riwayat_alergiobat)::character varying
          WHEN (ruangan_reseptur.instalasi_id = 2) THEN (asesmenperawatrd_t.alergi_obat)::character varying
          ELSE asesmenawal_t.nama_alergi
          END AS riwayat_alergi,
          CASE
          WHEN (ruangan_reseptur.instalasi_id = 1) THEN (pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text)
          WHEN (ruangan_reseptur.instalasi_id = 2) THEN cppt_rd.diagnosa_utama
          WHEN (ruangan_reseptur.instalasi_id = 3) THEN cppt_rd.diagnosa_utama
          ELSE NULL::text
          END AS diagnosa_text,
          sum(resepturdetail_t.hargajual_reseptur) AS total_tagihan,
          CASE
          WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.kelaspelayanan_id
          ELSE pasienadmisi_t.kelaspelayanan_id
          END AS kelaspelayanan_id,
          CASE
          WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelaspelayanan_m.kelaspelayanan_nama
          ELSE kelas_ri.kelaspelayanan_nama
          END AS kelaspelayanan_nama,
          reseptur_t.status_worklist,
          COALESCE(reseptur_t.biaya_administrasi, (0)::double precision) AS biaya_administrasi,
          fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
          penjualanresep_t.is_approve,
          pasien_m.alamat_pasien
          FROM (((((((((((((((((((((((((reseptur_t
          JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
          JOIN pasien_m ON ((reseptur_t.pasien_id = pasien_m.pasien_id)))
          JOIN ruangan_m ruangan_tujuan ON ((reseptur_t.ruangan_id = ruangan_tujuan.ruangan_id)))
          JOIN ruangan_m ruangan_reseptur ON ((reseptur_t.ruanganreseptur_id = ruangan_reseptur.ruangan_id)))
          JOIN pegawai_m ON ((reseptur_t.pegawai_id = pegawai_m.pegawai_id)))
          JOIN instalasi_m instalasi_reseptur ON ((ruangan_reseptur.instalasi_id = instalasi_reseptur.instalasi_id)))
          JOIN instalasi_m instalasi_tujuan ON ((ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id)))
          LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
          LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
          LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
          LEFT JOIN penjamin_m penjamin_ri ON ((pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id)))
          LEFT JOIN carabayar_m carabayar_ri ON ((penjamin_ri.penjamin_id = carabayar_ri.carabayar_id)))
          LEFT JOIN resepturdetail_t ON (((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id) AND (resepturdetail_t.is_deleted = false))))
          LEFT JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
          LEFT JOIN antrian_t ON ((reseptur_t.antrian_id = antrian_t.antrian_id)))
          LEFT JOIN diagnosa_m ON ((reseptur_t.diagnosa_id = diagnosa_m.diagnosa_id)))
          LEFT JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
          LEFT JOIN kelaspelayanan_m kelas_ri ON ((pasienadmisi_t.kelaspelayanan_id = kelas_ri.kelaspelayanan_id)))
          LEFT JOIN penjualanresep_t ON ((reseptur_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
          LEFT JOIN ( SELECT resepturdetail_t_1.reseptur_id,
          resepturdetail_t_1.iter
          FROM resepturdetail_t resepturdetail_t_1
          WHERE (resepturdetail_t_1.is_deleted = false)
          GROUP BY resepturdetail_t_1.reseptur_id, resepturdetail_t_1.iter) iter ON ((iter.reseptur_id = reseptur_t.reseptur_id)))
          LEFT JOIN anamnesa_t ON ((pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id)))
          LEFT JOIN asesmenperawatrd_t ON ((pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id)))
          LEFT JOIN asesmenawal_t ON ((pendaftaran_t.pendaftaran_id = asesmenawal_t.pendaftaran_id)))
          LEFT JOIN pasienmorbiditas_t ON (((pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false) AND (pasienmorbiditas_t.kelompokdiagnosa_id = 2))))
          LEFT JOIN ( SELECT instruksi_t.instruksi_id,
          cppt_t.cppt_id,
          cppt_t.pendaftaran_id,
          (cppt_t.a_diag_utama ->> 'text'::text) AS diagnosa_utama
          FROM (instruksi_t
          JOIN cppt_t ON (((instruksi_t.cppt_id = cppt_t.cppt_id) AND (cppt_t.is_deleted = false) AND (cppt_t.is_active = true))))
          WHERE ((instruksi_t.is_deleted = false) AND (instruksi_t.is_active = true))) cppt_rd ON (((pendaftaran_t.pendaftaran_id = cppt_rd.pendaftaran_id) AND (reseptur_t.instruksi_id = cppt_rd.instruksi_id))))
          WHERE ((reseptur_t.is_deleted = false) AND (reseptur_t.is_active = true))
          GROUP BY reseptur_t.instruksi_id, pendaftaran_t.umur, pasien_m.tanggal_lahir, (fgetnamalookup((pasien_m.jeniskelamin)::integer)), diagnosa_m.diagnosa_namalainnya, reseptur_t.reseptur_id, reseptur_t.pasien_id, reseptur_t.pendaftaran_id, reseptur_t.pasienadmisi_id, reseptur_t.ruangan_id, reseptur_t.ruanganreseptur_id, reseptur_t.tglreseptur, reseptur_t.noresep, reseptur_t.penjualanresep_id, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.*, ruangan_tujuan.ruangan_nama, ruangan_reseptur.ruangan_nama, reseptur_t.status_reseptur, reseptur_t.pegawai_id, pegawai_m.nama_pegawai, ruangan_reseptur.instalasi_id, instalasi_reseptur.instalasi_nama, ruangan_tujuan.instalasi_id, instalasi_tujuan.instalasi_nama, antrian_t.no_antrian, reseptur_t.is_hamil, reseptur_t.berat_badan, reseptur_t.tinggi_badan, reseptur_t.luas_tubuh, reseptur_t.diagnosa_id, penjualanresep_t.catatan, resepturdetail_t.iter, penjualanresep_t.noresep, penjualanresep_t.is_approve, pasien_m.alamat_pasien,
          CASE
          WHEN (ruangan_reseptur.instalasi_id = 1) THEN (anamnesa_t.riwayat_alergiobat)::character varying
          WHEN (ruangan_reseptur.instalasi_id = 2) THEN (asesmenperawatrd_t.alergi_obat)::character varying
          ELSE asesmenawal_t.nama_alergi
          END,
          CASE
          WHEN (ruangan_reseptur.instalasi_id = 1) THEN (pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text)
          WHEN (ruangan_reseptur.instalasi_id = 2) THEN cppt_rd.diagnosa_utama
          WHEN (ruangan_reseptur.instalasi_id = 3) THEN cppt_rd.diagnosa_utama
          ELSE NULL::text
          END, (concat(diagnosa_m.diagnosa_kode, '-', diagnosa_m.diagnosa_nama)), pendaftaran_t.kelaspelayanan_id, reseptur_t.status_worklist, reseptur_t.biaya_administrasi, pasien_m.namadepan,
          CASE
          WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.kelaspelayanan_id
          ELSE pasienadmisi_t.kelaspelayanan_id
          END,
          CASE
          WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.carabayar_id
          ELSE penjamin_ri.carabayar_id
          END,
          CASE
          WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.penjamin_id
          ELSE pasienadmisi_t.penjamin_id
          END,
          CASE
          WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_m.carabayar_nama
          ELSE carabayar_ri.carabayar_nama
          END,
          CASE
          WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_m.penjamin_nama
          ELSE penjamin_ri.penjamin_nama
          END,
          CASE
          WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN kelaspelayanan_m.kelaspelayanan_nama
          ELSE kelas_ri.kelaspelayanan_nama
          END
            ;");
        $this->execute('
            ALTER TABLE public.inforeseptur_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211125_031304_migrate_addalamat_inforeseptur_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211125_031304_migrate_addalamat_inforeseptur_v cannot be reverted.\n";

        return false;
    }
    */
}
