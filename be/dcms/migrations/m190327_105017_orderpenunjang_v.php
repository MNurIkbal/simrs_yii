<?php

use yii\db\Migration;

/**
 * Class m190327_105017_orderpenunjang_v
 */
class m190327_105017_orderpenunjang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW orderpenunjang_v;
        ');

        $this->execute('
                CREATE OR REPLACE VIEW orderpenunjang_v AS 
                 SELECT
                        CASE
                            WHEN gabung.pendaftaran_id IS NULL THEN pasienadmisi_t.pendaftaran_id
                            ELSE gabung.pendaftaran_id
                        END AS pendaftaran_id,
                        CASE
                            WHEN pendaftaran_t.no_pendaftaran IS NULL THEN pasienadmisi_t.no_pendaftaran
                            ELSE pendaftaran_t.no_pendaftaran
                        END AS no_pendaftaran,
                        CASE
                            WHEN pendaftaran_t.instalasi_nama IS NULL THEN pasienadmisi_t.instalasi_nama
                            ELSE pendaftaran_t.instalasi_nama
                        END AS instalasi_nama,
                        CASE
                            WHEN pendaftaran_t.ruangan_nama IS NULL THEN pasienadmisi_t.ruangan_nama
                            ELSE pendaftaran_t.ruangan_nama
                        END AS ruangan_nama,
                        CASE
                            WHEN pendaftaran_t.no_rekam_medik IS NULL THEN pasienadmisi_t.no_rekam_medik
                            ELSE pendaftaran_t.no_rekam_medik
                        END AS no_rekam_medik,
                        CASE
                            WHEN pendaftaran_t.nama_pasien IS NULL THEN pasienadmisi_t.nama_pasien
                            ELSE pendaftaran_t.nama_pasien
                        END AS nama_pasien,
                    gabung.instalasi_nama AS instalasi_penunjang,
                    gabung.ruangan_nama AS ruangan_penunjang,
                        CASE
                            WHEN pendaftaran_t.tanggal_lahir IS NULL THEN pasienadmisi_t.tanggal_lahir
                            ELSE pendaftaran_t.tanggal_lahir
                        END AS tanggal_lahir,
                        CASE
                            WHEN pendaftaran_t.jenis_kelamin IS NULL THEN pasienadmisi_t.jenis_kelamin
                            ELSE pendaftaran_t.jenis_kelamin
                        END AS jenis_kelamin,
                        CASE
                            WHEN pendaftaran_t.carabayar_nama IS NULL THEN pasienadmisi_t.carabayar_nama
                            ELSE pendaftaran_t.carabayar_nama
                        END AS carabayar_nama,
                        CASE
                            WHEN pendaftaran_t.penjamin_nama IS NULL THEN pasienadmisi_t.penjamin_nama
                            ELSE pendaftaran_t.penjamin_nama
                        END AS penjamin_nama,
                    gabung.tgl_kirimpasien,
                    gabung.no_orderkeunitlain,
                    gabung.nama_pegawai AS dokter_perujuk,
                        CASE
                            WHEN gabung.pemeriksaanrad_id IS NOT NULL THEN jenispemeriksaanrad_m.jenispemeriksaanrad_nama::text
                            WHEN gabung.pemeriksaanlab_id IS NOT NULL THEN jenispemeriksaanlab_m.jenispemeriksaanlab_nama::text
                            WHEN gabung.operasi_id IS NOT NULL THEN kegiatanoperasi_m.kegiatanoperasi_nama::text
                            ELSE gabung.pemeriksaan::text
                        END AS jenis_periksa,
                    gabung.pemeriksaan_nama,
                    gabung.tarif_pelayanan,
                    gabung.is_cyto,
                    gabung.tarif_cytotindakan,
                    pasienadmisi_t.pasienadmisi_id,
                    gabung.instruksi_id,
                    gabung.pasienkirimkeunitlain_id
                   FROM ( SELECT \'NON_PAKET\'::text AS jenis,
                            pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                            pasienkirimkeunitlain_t.tgl_kirimpasien,
                            pasienkirimkeunitlain_t.no_orderkeunitlain,
                            pasienkirimkeunitlain_t.ruangan_id,
                            ruangan_m.ruangan_nama,
                            daftartindakan_m.daftartindakan_nama AS pemeriksaan,
                            permintaankepenunjang_t.qtypermintaan,
                            pasienkirimkeunitlain_t.instalasi_id,
                            pasienkirimkeunitlain_t.pendaftaran_id,
                            pasienkirimkeunitlain_t.pasienadmisi_id,
                            instalasi_m.instalasi_nama,
                            pasienkirimkeunitlain_t.pegawai_id,
                            pegawai_m.nama_pegawai,
                            pasienkirimkeunitlain_t.catatan_dokterpengirim,
                            pasienmasukpenunjang_t.catatan,
                            lookstatus.lookup_name AS status,
                            permintaankepenunjang_t.pemeriksaanrad_id,
                            permintaankepenunjang_t.pemeriksaanlab_id,
                            permintaankepenunjang_t.operasi_id,
                            pasienkirimkeunitlain_t.instruksi_id,
                            permintaankepenunjang_t.tarif_pelayanan,
                            permintaankepenunjang_t.is_cyto,
                            permintaankepenunjang_t.tarif_cytotindakan,
                            daftartindakan_m.daftartindakan_nama AS pemeriksaan_nama,
                            batalorderpenunjang_t.alasan AS alasan_batal
                           FROM pasienkirimkeunitlain_t
                             JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
                             JOIN ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
                             JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                             JOIN instalasi_m ON pasienkirimkeunitlain_t.instalasi_id = instalasi_m.instalasi_id
                             JOIN pegawai_m ON pasienkirimkeunitlain_t.pegawai_id = pegawai_m.pegawai_id
                             LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
                             JOIN lookup_m lookstatus ON lookstatus.lookup_id = pasienkirimkeunitlain_t.status_penunjang::integer
                             LEFT JOIN batalorderpenunjang_t ON batalorderpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
                        UNION ALL
                         SELECT \'PAKET\'::text AS jenis,
                            pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                            pasienkirimkeunitlain_t.tgl_kirimpasien,
                            pasienkirimkeunitlain_t.no_orderkeunitlain,
                            pasienkirimkeunitlain_t.ruangan_id,
                            ruangan_m.ruangan_nama,
                            tipepaket_m.tipepaket_nama AS pemeriksaan,
                            permintaankepenunjang_t.qtypermintaan,
                            pasienkirimkeunitlain_t.instalasi_id,
                            pasienkirimkeunitlain_t.pendaftaran_id,
                            pasienkirimkeunitlain_t.pasienadmisi_id,
                            instalasi_m.instalasi_nama,
                            pasienkirimkeunitlain_t.pegawai_id,
                            pegawai_m.nama_pegawai,
                            pasienkirimkeunitlain_t.catatan_dokterpengirim,
                            pasienmasukpenunjang_t.catatan,
                            lookstatus.lookup_name AS status,
                            permintaankepenunjang_t.pemeriksaanrad_id,
                            permintaankepenunjang_t.pemeriksaanlab_id,
                            permintaankepenunjang_t.operasi_id,
                            pasienkirimkeunitlain_t.instruksi_id,
                            permintaankepenunjang_t.tarif_pelayanan,
                            permintaankepenunjang_t.is_cyto,
                            permintaankepenunjang_t.tarif_cytotindakan,
                            ( SELECT string_agg(tindakan.daftartindakan_nama::text, \', \'::text) AS string_agg
                                   FROM paketpelayanan_mp mp
                                     JOIN daftartindakan_m tindakan ON mp.daftartindakan_id = tindakan.daftartindakan_id
                                  WHERE mp.tipepaket_id = permintaankepenunjang_t.tipepaket_id) AS pemeriksaan_nama,
                            batalorderpenunjang_t.alasan AS alasan_batal
                           FROM pasienkirimkeunitlain_t
                             JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
                             JOIN ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
                             JOIN tipepaket_m ON permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id
                             JOIN instalasi_m ON pasienkirimkeunitlain_t.instalasi_id = instalasi_m.instalasi_id
                             JOIN pegawai_m ON pasienkirimkeunitlain_t.pegawai_id = pegawai_m.pegawai_id
                             LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
                             JOIN lookup_m lookstatus ON lookstatus.lookup_id = pasienkirimkeunitlain_t.status_penunjang::integer
                             LEFT JOIN batalorderpenunjang_t ON batalorderpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id) gabung
                     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                            pendaftaran_t_1.no_pendaftaran,
                            pasien_m.no_rekam_medik,
                            pasien_m.nama_pasien,
                            ruangan_m.ruangan_nama,
                            instalasi_m.instalasi_nama,
                            pasien_m.tanggal_lahir,
                            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
                            carabayar_m.carabayar_nama,
                            penjamin_m.penjamin_nama
                           FROM pendaftaran_t pendaftaran_t_1
                             JOIN pasien_m ON pendaftaran_t_1.pasien_id = pasien_m.pasien_id
                             JOIN ruangan_m ON pendaftaran_t_1.ruangan_id = ruangan_m.ruangan_id
                             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                             JOIN carabayar_m ON pendaftaran_t_1.carabayar_id = carabayar_m.carabayar_id
                             JOIN penjamin_m ON pendaftaran_t_1.penjamin_id = penjamin_m.penjamin_id) pendaftaran_t ON gabung.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                            pendaftaran_t_1.no_pendaftaran,
                            pasienadmisi_t_1.pasienadmisi_id,
                            pasien_m.no_rekam_medik,
                            pasien_m.nama_pasien,
                            ruangan_m.ruangan_nama,
                            instalasi_m.instalasi_nama,
                            pasien_m.tanggal_lahir,
                            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
                            carabayar_m.carabayar_nama,
                            penjamin_m.penjamin_nama
                           FROM pasienadmisi_t pasienadmisi_t_1
                             JOIN pendaftaran_t pendaftaran_t_1 ON pasienadmisi_t_1.pasienadmisi_id = pendaftaran_t_1.pasienadmisi_id
                             JOIN pasien_m ON pendaftaran_t_1.pasien_id = pasien_m.pasien_id
                             JOIN ruangan_m ON pendaftaran_t_1.ruangan_id = ruangan_m.ruangan_id
                             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                             JOIN carabayar_m ON pendaftaran_t_1.carabayar_id = carabayar_m.carabayar_id
                             JOIN penjamin_m ON pendaftaran_t_1.penjamin_id = penjamin_m.penjamin_id) pasienadmisi_t ON gabung.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     LEFT JOIN pemeriksaanlab_m ON gabung.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id
                     LEFT JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                     LEFT JOIN pemeriksaanrad_m ON gabung.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id
                     LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                     LEFT JOIN operasi_m ON gabung.operasi_id = operasi_m.operasi_id
                     LEFT JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
                  ORDER BY
                        CASE
                            WHEN gabung.pendaftaran_id IS NULL THEN pasienadmisi_t.pendaftaran_id
                            ELSE gabung.pendaftaran_id
                        END;
        ');
        
        $this->execute('
            ALTER TABLE orderpenunjang_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_105017_orderpenunjang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_105017_orderpenunjang_v cannot be reverted.\n";

        return false;
    }
    */
}
