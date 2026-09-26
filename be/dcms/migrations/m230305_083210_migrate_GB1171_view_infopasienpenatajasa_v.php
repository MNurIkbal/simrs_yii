<?php

use yii\db\Migration;

/**
 * Class m230305_083210_migrate_GB1171_view_infopasienpenatajasa_v
 */
class m230305_083210_migrate_GB1171_view_infopasienpenatajasa_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienpenatajasa_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienpenatajasa_v" AS  SELECT pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    look_jenisidentitas.lookup_name AS jenisidentitas,
    pasien_m.no_identitas_pasien, 
    look_namadepan.lookup_name AS namadepan,
    concat(look_namadepan.lookup_name, \' \', pasien_m.nama_pasien) AS nama_pasien,
    look_jeniskelamin.lookup_name AS jenis_kelamin,
    pasien_m.tanggal_lahir,
    look_statusperkawinan.lookup_name AS statusperkawinan,
    look_golongandarah.lookup_name AS golongandarah,
    look_agama.lookup_name AS agama,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    look_warganegara.lookup_name AS warga_negara,
    pasien_m.alamat_pasien,
    pendaftaran_t.umur,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    pendaftaran_t.ruangan_id,
    ruangan_m.ruangan_nama,
    (instalasi_m.instalasi_nama::text || \' - \'::text) || ruangan_m.ruangan_nama::text AS instalasi_ruangan,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    (carabayar_m.carabayar_nama::text || \' - \'::text) || penjamin_m.penjamin_nama::text AS carabayar_penjamin,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.status_periksa::integer AS status_periksa_id,
    look_statusperiksa.lookup_name AS status_periksa,
    pendaftaran_t.status_bayar AS status_bayar_id,
    look_statusbayar.lookup_name AS status_bayar,
    bpjs_t.klsrawat AS hak_kelas,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_dpjp,
    asuransipasien.nokartuasuransi,
    pendaftaran_t.catatan_penatajasa,
    pendaftaran_t.is_stopakomodasi,
    pasienpulang_t.tglpasienpulang,
    carabayar_m.groupcarabayar_id,
    NULL::integer AS kamarruangan_id,
    NULL::integer AS kamartempattidur_id,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    penunjang.pasienmasukpenunjang_id,
    NULL::boolean AS is_pasientitipan,
    NULL::integer AS kelas_ditagihkan_id,
    \'-\'::character varying AS kelas_ditagihkan_nama,
    penunjang.no_masukpenunjang
   FROM pendaftaran_t
     JOIN ( SELECT a.no_rekam_medik,
            a.no_identitas_pasien,
            a.nama_pasien,
            a.tanggal_lahir,
            a.no_telepon_pasien,
            a.no_mobile_pasien,
            a.alamat_pasien,
            a.pasien_id,
            a.jenisidentitas,
            a.namadepan,
            a.jeniskelamin,
            a.statusperkawinan,
            a.golongandarah,
            a.agama,
            a.warga_negara
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT a.jeniskasuspenyakit_id,
            a.jeniskasuspenyakit_nama
           FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama,
            a.groupcarabayar_id
           FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.pasienpulang_id,
            a.tglpasienpulang
           FROM pasienpulang_t a) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN ( SELECT a.bpjs_id,
            a.klsrawat
           FROM bpjs_t a) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT asuransipasien_m.asuransipasien_id,
            asuransipasien_m.pasien_id,
            asuransipasien_m.penjamin_id,
            asuransipasien_m.carabayar_id,
            asuransipasien_m.nokartuasuransi
           FROM asuransipasien_m
             LEFT JOIN ( SELECT min(asuransipasien_m_1.asuransipasien_id) AS asuransipasien_id,
                    asuransipasien_m_1.carabayar_id,
                    asuransipasien_m_1.penjamin_id
                   FROM asuransipasien_m asuransipasien_m_1
                  GROUP BY asuransipasien_m_1.carabayar_id, asuransipasien_m_1.penjamin_id) asuransipasien_min ON asuransipasien_m.asuransipasien_id = asuransipasien_min.asuransipasien_id) asuransipasien ON pendaftaran_t.asuransipasien_id = asuransipasien.asuransipasien_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_jenisidentitas ON pasien_m.jenisidentitas::integer = look_jenisidentitas.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_namadepan ON pasien_m.namadepan::integer = look_namadepan.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statusperkawinan ON pasien_m.statusperkawinan::integer = look_statusperkawinan.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_golongandarah ON pasien_m.golongandarah::integer = look_golongandarah.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_agama ON pasien_m.agama::integer = look_agama.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_warganegara ON pasien_m.warga_negara::integer = look_warganegara.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statusperiksa ON pendaftaran_t.status_periksa::integer = look_statusperiksa.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statusbayar ON pendaftaran_t.status_bayar = look_statusbayar.lookup_id
     LEFT JOIN ( SELECT pasienmasukpenunjang_t.pendaftaran_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.no_masukpenunjang
           FROM pasienmasukpenunjang_t
          WHERE pasienmasukpenunjang_t.is_deleted = false AND pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL) penunjang ON pendaftaran_t.pendaftaran_id = penunjang.pendaftaran_id
  WHERE pendaftaran_t.pasienadmisi_id IS NULL
UNION ALL
 SELECT pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    look_jenisidentitas.lookup_name AS jenisidentitas,
    pasien_m.no_identitas_pasien,
    look_namadepan.lookup_name AS namadepan,
    pasien_m.nama_pasien,
    look_jeniskelamin.lookup_name AS jenis_kelamin,
    pasien_m.tanggal_lahir,
    look_statusperkawinan.lookup_name AS statusperkawinan,
    look_golongandarah.lookup_name AS golongandarah,
    look_agama.lookup_name AS agama,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    look_warganegara.lookup_name AS warga_negara,
    pasien_m.alamat_pasien,
    pendaftaran_t.umur,
    pendaftaran_t.pendaftaran_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pasienadmisi_t.ruangan_id,
    ruangan_m.ruangan_nama,
    (instalasi_m.instalasi_nama::text || \' - \'::text) || ruangan_m.ruangan_nama::text AS instalasi_ruangan,
    pasienadmisi_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.penjamin_id,
    penjamin_m.penjamin_nama,
    (carabayar_m.carabayar_nama::text || \' - \'::text) || penjamin_m.penjamin_nama::text AS carabayar_penjamin,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasienadmisi_t.status_ranap AS status_periksa_id,
    look_statusranap.lookup_name AS status_periksa,
    pendaftaran_t.status_bayar AS status_bayar_id,
    look_statusbayar.lookup_name AS status_bayar,
    bpjs_t.klsrawat AS hak_kelas,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_dpjp,
    asuransipasien.nokartuasuransi,
    pendaftaran_t.catatan_penatajasa,
    pendaftaran_t.is_stopakomodasi,
    pasienpulang_t.tglpasienpulang,
    carabayar_m.groupcarabayar_id,
    pasienadmisi_t.kamarruangan_id,
    pasienadmisi_t.kamartempattidur_id,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    NULL::integer AS pasienmasukpenunjang_id,
    pasienadmisi_t.is_pasientitipan,
    pasienadmisi_t.kelas_ditagihkan_id,
    kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
    \'\'::text AS no_masukpenunjang
   FROM pendaftaran_t
     JOIN ( SELECT a.pasienadmisi_id,
            a.tgl_admisi,
            a.ruangan_id,
            a.kelaspelayanan_id,
            a.carabayar_id,
            a.penjamin_id,
            a.status_ranap,
            a.pasien_id,
            a.pasienpulang_id,
            a.pegawai_id,
            a.bpjs_id,
            a.kamarruangan_id,
            a.kamartempattidur_id,
            a.is_pasientitipan,
            a.kelas_ditagihkan_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT a.no_rekam_medik,
            a.no_identitas_pasien,
            a.nama_pasien,
            a.tanggal_lahir,
            a.no_telepon_pasien,
            a.no_mobile_pasien,
            a.alamat_pasien,
            a.pasien_id,
            a.jenisidentitas,
            a.namadepan,
            a.jeniskelamin,
            a.statusperkawinan,
            a.golongandarah,
            a.agama,
            a.warga_negara
           FROM pasien_m a) pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ( SELECT a.jeniskasuspenyakit_id,
            a.jeniskasuspenyakit_nama
           FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama,
            a.groupcarabayar_id
           FROM carabayar_m a) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.pasienpulang_id,
            a.tglpasienpulang
           FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN ( SELECT a.bpjs_id,
            a.klsrawat
           FROM bpjs_t a) bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) kamarruangan_m ON kamarruangan_m.kamarruangan_id = pasienadmisi_t.kamarruangan_id
     LEFT JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
           FROM kamartempattidur_m a) kamartempattidur_m ON kamartempattidur_m.kamartempattidur_id = pasienadmisi_t.kamartempattidur_id
     LEFT JOIN ( SELECT asuransipasien_m.asuransipasien_id,
            asuransipasien_m.pasien_id,
            asuransipasien_m.penjamin_id,
            asuransipasien_m.carabayar_id,
            asuransipasien_m.nokartuasuransi
           FROM asuransipasien_m
             LEFT JOIN ( SELECT min(asuransipasien_m_1.asuransipasien_id) AS asuransipasien_id,
                    asuransipasien_m_1.carabayar_id,
                    asuransipasien_m_1.penjamin_id
                   FROM asuransipasien_m asuransipasien_m_1
                  GROUP BY asuransipasien_m_1.carabayar_id, asuransipasien_m_1.penjamin_id) asuransipasien_min ON asuransipasien_m.asuransipasien_id = asuransipasien_min.asuransipasien_id) asuransipasien ON pendaftaran_t.asuransipasien_id = asuransipasien.asuransipasien_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_jenisidentitas ON pasien_m.jenisidentitas::integer = look_jenisidentitas.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_namadepan ON pasien_m.namadepan::integer = look_namadepan.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statusperkawinan ON pasien_m.statusperkawinan::integer = look_statusperkawinan.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_golongandarah ON pasien_m.golongandarah::integer = look_golongandarah.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_agama ON pasien_m.agama::integer = look_agama.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_warganegara ON pasien_m.warga_negara::integer = look_warganegara.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statusranap ON pasienadmisi_t.status_ranap = look_statusranap.lookup_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_statusbayar ON pendaftaran_t.status_bayar = look_statusbayar.lookup_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230305_083210_migrate_GB1171_view_infopasienpenatajasa_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230305_083210_migrate_GB1171_view_infopasienpenatajasa_v cannot be reverted.\n";

        return false;
    }
    */
}
