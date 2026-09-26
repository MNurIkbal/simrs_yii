<?php

use yii\db\Migration;

/**
 * Class m220725_045428_migrate_MHG1801_view_laporanwaktutunggulab_v
 */
class m220725_045428_migrate_MHG1801_view_laporanwaktutunggulab_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanwaktutunggulab_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporanwaktutunggulab_v" AS  
            SELECT \'NON-PAKET\'::text AS tipe,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                tindakanpelayanan_t.tindakanpelayanan_id,
                tindakanpelayanan_t.daftartindakan_id, 
                pemeriksaanlab_m.pemeriksaanlab_id,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasienmasukpenunjang_t.pegawai_id AS dokter_id,
                dokter.nama_pegawai AS dokter,
                ambilsample_t.samplelab_id,
                samplelab_m.nama_sample,
                pemeriksaanlab_m.pemeriksaanlab_nama,
                COALESCE(pasienkirimkeunitlain_t.tgl_kirimpasien, pendaftaran_t.tgl_pendaftaran) AS tgl_dirujuk,
                pasienmasukpenunjang_t.tglmasukpenunjang,
                ambilsample_t.tgl_ambilsample,
                ambilsample_t.jam_ambilsample,
                hasilpemeriksaanlab_t.tgl_hasilpemeriksaanlab,
                hasilpemeriksaanlab_t.tgl_expertise,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                permintaankepenunjang_t.permintaankepenunjang_id,
                COALESCE(pasienmasukpenunjang_t.no_masukpenunjang, rujukan_t.no_rujukan) AS no_rujukan,
                    CASE
                        WHEN permintaankepenunjang_t.rumahsakit_rujukan IS NOT NULL THEN 4
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = false THEN 1
                        WHEN pendaftaran_t.rujukan_id IS NOT NULL THEN 2
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = true THEN 3
                        ELSE NULL::integer
                    END AS jenis_rujukan_id,
                    CASE
                        WHEN permintaankepenunjang_t.rumahsakit_rujukan IS NOT NULL THEN \'Rujukan Keluar\'::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = false THEN \'Rujukan RS\'::text
                        WHEN pendaftaran_t.rujukan_id IS NOT NULL THEN \'Rujukan Masuk\'::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = true THEN \'APS\'::text
                        ELSE NULL::text
                    END AS jenis_rujukan,
                    CASE
                        WHEN permintaankepenunjang_t.rumahsakit_rujukan IS NOT NULL THEN \'Rujukan Keluar \'::text || COALESCE(permintaankepenunjang_t.rumahsakit_rujukan, \'\'::character varying)::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = false THEN ((\'Rujukan dari instalasi \'::text || instalasi_asal.instalasi_nama::text) || \' ruangan \'::text) || asal_ruangan.ruangan_nama::text
                        WHEN pendaftaran_t.rujukan_id IS NOT NULL THEN ((\'Rujukan dari \'::text || COALESCE(perujuk_m.namaperujuk, \'\'::character varying)::text) || \' \'::text) || COALESCE(asalrujukan_m.asalrujukan_nama, \'\'::character varying)::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = true THEN ((\'Rujukan dari instalasi \'::text || instalasi_asal.instalasi_nama::text) || \' ruangan \'::text) || asal_ruangan.ruangan_nama::text
                        ELSE NULL::text
                    END AS rujukan,
                    CASE
                        WHEN permintaankepenunjang_t.rumahsakit_rujukan IS NOT NULL THEN asal_ruangan.ruangan_nama::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = false THEN instalasi_asal.instalasi_nama::text
                        WHEN pendaftaran_t.rujukan_id IS NOT NULL THEN asalrujukan_m.asalrujukan_nama::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = true THEN \'\'::text
                        ELSE NULL::text
                    END AS asalrujukan_nama,
                    CASE
                        WHEN permintaankepenunjang_t.rumahsakit_rujukan IS NOT NULL THEN permintaankepenunjang_t.rumahsakit_rujukan::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = false THEN asal_ruangan.ruangan_nama::text
                        WHEN pendaftaran_t.rujukan_id IS NOT NULL THEN perujuk_m.namaperujuk::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = true THEN \'\'::text
                        ELSE NULL::text
                    END AS rujukandari_nama,
                lookup_gender.lookup_name AS jeniskelamin,
                pasien_m.tanggal_lahir
               FROM pasienmasukpenunjang_t
                 JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
                 JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 JOIN pemeriksaanlab_m ON daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id AND tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                 JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 LEFT JOIN pegawai_m dokter ON pasienmasukpenunjang_t.pegawai_id = dokter.pegawai_id
                 JOIN ambilsample_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = ambilsample_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id AND tindakanpelayanan_t.daftartindakan_id = ambilsample_t.tindakanpaket_id
                 JOIN samplelab_m ON ambilsample_t.samplelab_id = samplelab_m.samplelab_id
                 JOIN hasilpemeriksaanlab_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id AND ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id
                 LEFT JOIN ( SELECT a.rujukan_id,
                        a.asalrujukan_id,
                        a.rujukandari_id,
                        a.no_rujukan,
                        a.tanggal_rujukan
                       FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
                 LEFT JOIN ( SELECT a.asalrujukan_id,
                        a.asalrujukan_nama
                       FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
                 LEFT JOIN ( SELECT a.perujuk_id,
                        a.namaperujuk
                       FROM perujuk_m a) perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
                 LEFT JOIN ( SELECT a.rujukandari_id,
                        a.nama_perujuk
                       FROM rujukandari_m a) rujukandari_m ON rujukan_t.rujukandari_id = rujukandari_m.rujukandari_id
                 LEFT JOIN ( SELECT a.tgl_kirimpasien,
                        a.no_orderkeunitlain,
                        a.pasienkirimkeunitlain_id,
                        a.instalasi_id
                       FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
                 LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                        rujukankeluar_m.rumahsakit_rujukan,
                        rujukankeluar_m.asalrujukan_nama,
                        a.daftartindakan_id,
                        a.permintaankepenunjang_id
                       FROM permintaankepenunjang_t a
                         LEFT JOIN ( SELECT a_1.permintaankepenunjang_id,
                                a_1.rujukankeluar_id
                               FROM pasiendirujukkeluar_t a_1) pasiendirujukkeluar_t ON a.permintaankepenunjang_id = pasiendirujukkeluar_t.permintaankepenunjang_id
                         LEFT JOIN ( SELECT a_1.rujukankeluar_id,
                                a_1.rumahsakit_rujukan,
                                asalrujukan_m_1.asalrujukan_nama
                               FROM rujukankeluar_m a_1
                                 LEFT JOIN ( SELECT a_2.asalrujukan_id,
                                        a_2.asalrujukan_nama
                                       FROM asalrujukan_m a_2) asalrujukan_m_1 ON a_1.asalrujukan_id = asalrujukan_m_1.asalrujukan_id) rujukankeluar_m ON pasiendirujukkeluar_t.rujukankeluar_id = rujukankeluar_m.rujukankeluar_id) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id AND tindakanpelayanan_t.daftartindakan_id = permintaankepenunjang_t.daftartindakan_id
                 LEFT JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama,
                        a.instalasi_id
                       FROM ruangan_m a) asal_ruangan ON pasienmasukpenunjang_t.ruanganasal_id = asal_ruangan.ruangan_id
                 LEFT JOIN ( SELECT a.instalasi_id,
                        a.instalasi_nama
                       FROM instalasi_m a) instalasi_asal ON asal_ruangan.instalasi_id = instalasi_asal.instalasi_id
                 LEFT JOIN ( SELECT lookup_m.lookup_id,
                        lookup_m.lookup_name
                       FROM lookup_m) lookup_gender ON pasien_m.jeniskelamin::integer = lookup_gender.lookup_id
            UNION ALL
             SELECT \'PAKET\'::text AS tipe,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                tindakanpelayanan_t.tindakanpelayanan_id,
                paketpelayanan_mp.daftartindakan_id,
                pemeriksaanlab_m.pemeriksaanlab_id,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasienmasukpenunjang_t.pegawai_id AS dokter_id,
                dokter.nama_pegawai AS dokter,
                ambilsample_t.samplelab_id,
                samplelab_m.nama_sample,
                pemeriksaanlab_m.pemeriksaanlab_nama,
                COALESCE(pasienkirimkeunitlain_t.tgl_kirimpasien, pendaftaran_t.tgl_pendaftaran) AS tgl_dirujuk,
                pasienmasukpenunjang_t.tglmasukpenunjang,
                ambilsample_t.tgl_ambilsample,
                ambilsample_t.jam_ambilsample,
                hasilpemeriksaanlab_t.tgl_hasilpemeriksaanlab,
                hasilpemeriksaanlab_t.tgl_expertise,
                pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                permintaankepenunjang_t.permintaankepenunjang_id,
                COALESCE(pasienmasukpenunjang_t.no_masukpenunjang, rujukan_t.no_rujukan) AS no_rujukan,
                    CASE
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = false THEN 1
                        WHEN pendaftaran_t.rujukan_id IS NOT NULL THEN 2
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = true THEN 3
                        WHEN permintaankepenunjang_t.pasiendirujukkeluar_id IS NOT NULL THEN 4
                        ELSE NULL::integer
                    END AS jenis_rujukan_id,
                    CASE
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = false THEN \'Rujukan RS\'::text
                        WHEN pendaftaran_t.rujukan_id IS NOT NULL THEN \'Rujukan Masuk\'::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = true THEN \'APS\'::text
                        WHEN permintaankepenunjang_t.pasiendirujukkeluar_id IS NOT NULL THEN \'Rujukan Keluar\'::text
                        ELSE NULL::text
                    END AS jenis_rujukan,
                ((\'Rujukan dari instalasi \'::text || instalasi_asal.instalasi_nama::text) || \' ruangan \'::text) || asal_ruangan.ruangan_nama::text AS rujukan,
                    CASE
                        WHEN permintaankepenunjang_t.is_referred IS TRUE THEN \'\'::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = false THEN instalasi_asal.instalasi_nama::text
                        WHEN pendaftaran_t.rujukan_id IS NOT NULL THEN \'\'::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = true THEN instalasi_asal.instalasi_nama::text
                        ELSE NULL::text
                    END AS asalrujukan_nama,
                    CASE
                        WHEN permintaankepenunjang_t.is_referred IS TRUE THEN \'\'::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = false THEN asal_ruangan.ruangan_nama::text
                        WHEN pendaftaran_t.rujukan_id IS NOT NULL THEN \'\'::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = true THEN asal_ruangan.ruangan_nama::text
                        ELSE NULL::text
                    END AS rujukandari_nama,
                lookup_gender.lookup_name AS jeniskelamin,
                pasien_m.tanggal_lahir
               FROM pasienmasukpenunjang_t
                 JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
                 JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                 JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 JOIN pemeriksaanlab_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id AND daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                 JOIN pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 JOIN pegawai_m dokter ON pasienmasukpenunjang_t.pegawai_id = dokter.pegawai_id
                 JOIN ambilsample_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = ambilsample_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id AND paketpelayanan_mp.daftartindakan_id = ambilsample_t.tindakanpaket_id
                 JOIN samplelab_m ON ambilsample_t.samplelab_id = samplelab_m.samplelab_id
                 JOIN hasilpemeriksaanlab_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id AND ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id
                 LEFT JOIN ( SELECT a.rujukan_id,
                        a.asalrujukan_id,
                        a.rujukandari_id,
                        a.no_rujukan,
                        a.tanggal_rujukan
                       FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
                 LEFT JOIN ( SELECT a.asalrujukan_id,
                        a.asalrujukan_nama
                       FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
                 LEFT JOIN ( SELECT a.rujukandari_id,
                        a.nama_perujuk
                       FROM rujukandari_m a) rujukandari_m ON rujukan_t.rujukandari_id = rujukandari_m.rujukandari_id
                 LEFT JOIN ( SELECT a.tgl_kirimpasien,
                        a.no_orderkeunitlain,
                        a.pasienkirimkeunitlain_id,
                        a.instalasi_id
                       FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
                 LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                        a.daftartindakan_id,
                        a.is_deleted,
                        a.is_referred,
                        a.permintaankepenunjang_id,
                        pasiendirujukkeluar_t.pasiendirujukkeluar_id
                       FROM permintaankepenunjang_t a
                         JOIN ( SELECT a_1.permintaankepenunjang_id,
                                a_1.rujukankeluar_id,
                                a_1.pasiendirujukkeluar_id
                               FROM pasiendirujukkeluar_t a_1) pasiendirujukkeluar_t ON a.permintaankepenunjang_id = pasiendirujukkeluar_t.permintaankepenunjang_id) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
                 LEFT JOIN rujukankeluar_m ON permintaankepenunjang_t.pasiendirujukkeluar_id = rujukankeluar_m.rujukankeluar_id
                 LEFT JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama,
                        a.instalasi_id
                       FROM ruangan_m a) asal_ruangan ON pasienmasukpenunjang_t.ruanganasal_id = asal_ruangan.ruangan_id
                 LEFT JOIN ( SELECT a.instalasi_id,
                        a.instalasi_nama
                       FROM instalasi_m a) instalasi_asal ON asal_ruangan.instalasi_id = instalasi_asal.instalasi_id
                 LEFT JOIN ( SELECT lookup_m.lookup_id,
                        lookup_m.lookup_name
                       FROM lookup_m) lookup_gender ON pasien_m.jeniskelamin::integer = lookup_gender.lookup_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220725_045428_migrate_MHG1801_view_laporanwaktutunggulab_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220725_045428_migrate_MHG1801_view_laporanwaktutunggulab_v cannot be reverted.\n";

        return false;
    }
    */
}
