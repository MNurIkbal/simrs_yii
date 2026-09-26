<?php

use yii\db\Migration;

/**
 * Class m220725_044527_migrate_MHG1802_view_laporanwaktutunggurad_v
 */
class m220725_044527_migrate_MHG1802_view_laporanwaktutunggurad_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanwaktutunggurad_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporanwaktutunggurad_v" AS  
            SELECT \'NON-PAKET\'::text AS tipe,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id, 
                tindakanpelayanan_t.tindakanpelayanan_id,
                tindakanpelayanan_t.daftartindakan_id,
                pemeriksaanrad_m.pemeriksaanradiologi_id,
                pasienkirimkeunitlain_t.tgl_kirimpasien,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasienmasukpenunjang_t.pegawai_id AS dokter_id,
                dokter.nama_pegawai AS dokter,
                pemeriksaanrad_m.jenispemeriksaanrad_id,
                jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
                pemeriksaanrad_m.pemeriksaanrad_nama,
                pasienmasukpenunjang_t.tglmasukpenunjang,
                hasilpemeriksaanrad_t.tgl_ambilfoto,
                hasilpemeriksaanrad_t.tgl_hasilrad,
                daftartindakan_m.daftartindakan_nama,
                COALESCE(pasienkirimkeunitlain_t.tglpersetujuan, pasienmasukpenunjang_t.tglmasukpenunjang) AS tglpersetujuan,
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
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = true THEN \'\'::text
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
                 LEFT JOIN ( SELECT a.tgl_kirimpasien,
                        a.no_orderkeunitlain,
                        a.pasienkirimkeunitlain_id,
                        a.instalasi_id,
                        a.tglpersetujuan
                       FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
                 JOIN ( SELECT a.tindakanpelayanan_id,
                        a.daftartindakan_id,
                        a.pasienmasukpenunjang_id
                       FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
                 JOIN ( SELECT a.daftartindakan_nama,
                        a.daftartindakan_id
                       FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 JOIN ( SELECT a.pemeriksaanradiologi_id,
                        a.jenispemeriksaanrad_id,
                        a.pemeriksaanrad_nama,
                        a.daftartindakan_id,
                        a.is_deleted
                       FROM pemeriksaanrad_m a) pemeriksaanrad_m ON daftartindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id AND tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                 JOIN ( SELECT a.jenispemeriksaanrad_nama,
                        a.jenispemeriksaanrad_id
                       FROM jenispemeriksaanrad_m a) jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                 JOIN ( SELECT a.no_pendaftaran,
                        a.pendaftaran_id,
                        a.pasien_id,
                        a.rujukan_id,
                        a.is_aps
                       FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN ( SELECT a.no_rekam_medik,
                        a.nama_pasien,
                        a.pasien_id,
                        a.jeniskelamin,
                        a.tanggal_lahir
                       FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 LEFT JOIN ( SELECT a.nama_pegawai,
                        a.pegawai_id
                       FROM pegawai_m a) dokter ON pasienmasukpenunjang_t.pegawai_id = dokter.pegawai_id
                 JOIN ( SELECT a.tgl_ambilfoto,
                        a.tgl_hasilrad,
                        a.pasienmasukpenunjang_id,
                        a.pemeriksaanrad_id,
                        a.tindakanpelayanan_id
                       FROM hasilpemeriksaanrad_t a) hasilpemeriksaanrad_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id AND pemeriksaanrad_m.pemeriksaanradiologi_id = hasilpemeriksaanrad_t.pemeriksaanrad_id AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id
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
              WHERE pemeriksaanrad_m.is_deleted = false
            UNION ALL
             SELECT \'PAKET\'::text AS tipe,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                tindakanpelayanan_t.tindakanpelayanan_id,
                tindakanpelayanan_t.daftartindakan_id,
                pemeriksaanrad_m.pemeriksaanradiologi_id,
                pasienkirimkeunitlain_t.tgl_kirimpasien,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasienmasukpenunjang_t.pegawai_id AS dokter_id,
                dokter.nama_pegawai AS dokter,
                pemeriksaanrad_m.jenispemeriksaanrad_id,
                jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
                pemeriksaanrad_m.pemeriksaanrad_nama,
                pasienmasukpenunjang_t.tglmasukpenunjang,
                hasilpemeriksaanrad_t.tgl_ambilfoto,
                hasilpemeriksaanrad_t.tgl_hasilrad,
                daftartindakan_m.daftartindakan_nama,
                COALESCE(pasienkirimkeunitlain_t.tglpersetujuan, pasienmasukpenunjang_t.tglmasukpenunjang) AS tglpersetujuan,
                    CASE
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = false THEN 1
                        WHEN pendaftaran_t.rujukan_id IS NOT NULL THEN 2
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = true THEN 3
                        WHEN permintaankepenunjang_t.permintaankepenunjang_id IS NOT NULL THEN 4
                        ELSE NULL::integer
                    END AS jenis_rujukan_id,
                    CASE
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = false THEN \'Rujukan RS\'::text
                        WHEN pendaftaran_t.rujukan_id IS NOT NULL THEN \'Rujukan Masuk\'::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = true THEN \'APS\'::text
                        WHEN permintaankepenunjang_t.permintaankepenunjang_id IS NOT NULL THEN \'Rujukan Keluar\'::text
                        ELSE NULL::text
                    END AS jenis_rujukan,
                    CASE
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = false THEN ((\'Rujukan dari instalasi \'::text || instalasi_asal.instalasi_nama::text) || \' ruangan \'::text) || asal_ruangan.ruangan_nama::text
                        WHEN pendaftaran_t.rujukan_id IS NOT NULL THEN ((\'Rujukan dari \'::text || COALESCE(perujuk_m.namaperujuk, \'\'::character varying)::text) || \' \'::text) || COALESCE(asalrujukan_m.asalrujukan_nama, \'\'::character varying)::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = true THEN ((\'Rujukan dari instalasi \'::text || instalasi_asal.instalasi_nama::text) || \' ruangan \'::text) || asal_ruangan.ruangan_nama::text
                        WHEN permintaankepenunjang_t.pasienkirimkeunitlain_id IS NOT NULL THEN \'Rujukan Keluar \'::text || COALESCE(permintaankepenunjang_t.rumahsakit_rujukan, \'\'::character varying)::text
                        ELSE NULL::text
                    END AS rujukan,
                    CASE
                        WHEN permintaankepenunjang_t.is_referred IS TRUE THEN \'\'::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = false THEN instalasi_asal.instalasi_nama::text
                        WHEN pendaftaran_t.rujukan_id IS NOT NULL THEN asalrujukan_m.asalrujukan_nama::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = true THEN \'\'::text
                        ELSE NULL::text
                    END AS asalrujukan_nama,
                    CASE
                        WHEN permintaankepenunjang_t.is_referred IS TRUE THEN \'\'::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = false THEN asal_ruangan.ruangan_nama::text
                        WHEN pendaftaran_t.rujukan_id IS NOT NULL THEN perujuk_m.namaperujuk::text
                        WHEN pendaftaran_t.rujukan_id IS NULL AND pendaftaran_t.is_aps = true THEN \'\'::text
                        ELSE NULL::text
                    END AS rujukandari_nama,
                lookup_gender.lookup_name AS jeniskelamin,
                pasien_m.tanggal_lahir
               FROM pasienmasukpenunjang_t
                 LEFT JOIN ( SELECT a.tgl_kirimpasien,
                        a.no_orderkeunitlain,
                        a.pasienkirimkeunitlain_id,
                        a.instalasi_id,
                        a.tglpersetujuan
                       FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
                 JOIN ( SELECT a.tindakanpelayanan_id,
                        a.daftartindakan_id,
                        a.pasienmasukpenunjang_id,
                        a.tipepaket_id
                       FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
                 JOIN ( SELECT a.tipepaket_id,
                        a.daftartindakan_id
                       FROM paketpelayanan_mp a) paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                 JOIN ( SELECT a.daftartindakan_nama,
                        a.daftartindakan_id
                       FROM daftartindakan_m a) daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 JOIN ( SELECT a.pemeriksaanradiologi_id,
                        a.jenispemeriksaanrad_id,
                        a.pemeriksaanrad_nama,
                        a.daftartindakan_id,
                        a.is_deleted
                       FROM pemeriksaanrad_m a) pemeriksaanrad_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id AND daftartindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                 JOIN ( SELECT a.jenispemeriksaanrad_nama,
                        a.jenispemeriksaanrad_id
                       FROM jenispemeriksaanrad_m a) jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                 JOIN ( SELECT a.no_pendaftaran,
                        a.pendaftaran_id,
                        a.pasien_id,
                        a.rujukan_id,
                        a.is_aps
                       FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN ( SELECT a.no_rekam_medik,
                        a.nama_pasien,
                        a.pasien_id,
                        a.jeniskelamin,
                        a.tanggal_lahir
                       FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 JOIN ( SELECT a.nama_pegawai,
                        a.pegawai_id
                       FROM pegawai_m a) dokter ON pasienmasukpenunjang_t.pegawai_id = dokter.pegawai_id
                 JOIN ( SELECT a.tgl_ambilfoto,
                        a.tgl_hasilrad,
                        a.pasienmasukpenunjang_id,
                        a.pemeriksaanrad_id,
                        a.tindakanpelayanan_id
                       FROM hasilpemeriksaanrad_t a) hasilpemeriksaanrad_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id AND pemeriksaanrad_m.pemeriksaanradiologi_id = hasilpemeriksaanrad_t.pemeriksaanrad_id AND tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id
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
                 LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                        rujukankeluar_m.rumahsakit_rujukan,
                        rujukankeluar_m.asalrujukan_nama,
                        a.daftartindakan_id,
                        a.permintaankepenunjang_id,
                        a.is_referred
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
              WHERE pemeriksaanrad_m.is_deleted = false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220725_044527_migrate_MHG1802_view_laporanwaktutunggurad_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220725_044527_migrate_MHG1802_view_laporanwaktutunggurad_v cannot be reverted.\n";

        return false;
    }
    */
}
