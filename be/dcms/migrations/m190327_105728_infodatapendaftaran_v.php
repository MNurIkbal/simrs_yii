<?php

use yii\db\Migration;

/**
 * Class m190327_105728_infodatapendaftaran_v
 */
class m190327_105728_infodatapendaftaran_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW infodatapendaftaran_v;');

        $this->execute('
            CREATE OR REPLACE VIEW infodatapendaftaran_v AS 
             SELECT data_info.pendaftaran_id,
                data_info.instalasi_id AS ins_id,
                data_info.ruangan_id AS rua_id,
                data_info.pasien_id,
                data_info.penjamin_id AS pen_id,
                data_info.carabayar_id AS car_id,
                data_info.kelaspelayanan_id,
                data_info.pasienpulang_id,
                data_info.no_pendaftaran,
                data_info.tgl_pendaftaran,
                data_info.no_rekam_medik,
                data_info.nama_pasien,
                data_info.no_mobile_pasien,
                data_info.instalasi_nama AS ins_nama,
                data_info.ruangan_nama AS rua_nama,
                data_info.carabayar_nama AS car,
                data_info.penjamin_nama AS pen,
                data_info.kelaspelayanan_nama,
                data_info.jumlah_uangmuka,
                data_info.pasienpulangri_id,
                data_info.pasienadmisi_id,
                data_info.status_pasien,
                data_info.pasienmasukpenunjang_id,
                    CASE
                        WHEN data_info.tglpasienpulang IS NULL THEN data_info.tglpasienpulang_ri
                        ELSE data_info.tglpasienpulang
                    END AS tglpasienpulang,
                data_info.nama_dok_rj_rd,
                data_info.nama_dok_ri,
                data_info.jeniskasuspenyakit_nama,
                data_info.umur,
                    CASE
                        WHEN data_info.pasienadmisi_id IS NULL THEN data_info.carabayar_id
                        ELSE data_info.carabayarri_id
                    END AS carabayar_id,
                    CASE
                        WHEN data_info.pasienadmisi_id IS NULL THEN data_info.penjamin_id
                        ELSE data_info.penjaminri_id
                    END AS penjamin_id,
                    CASE
                        WHEN data_info.pasienadmisi_id IS NULL THEN data_info.carabayar_nama
                        ELSE data_info.carabayar_nama_ri
                    END AS carabayar_nama,
                    CASE
                        WHEN data_info.pasienadmisi_id IS NULL THEN data_info.penjamin_nama
                        ELSE data_info.penjamin_nama_ri
                    END AS penjamin_nama,
                    CASE
                        WHEN data_info.pasienadmisi_id IS NULL THEN data_info.instalasi_id
                        ELSE data_info.instalasiri_id
                    END AS instalasi_id,
                    CASE
                        WHEN data_info.pasienadmisi_id IS NULL THEN data_info.ruangan_id
                        ELSE data_info.ruanganri_id
                    END AS ruangan_id,
                    CASE
                        WHEN data_info.pasienadmisi_id IS NULL THEN data_info.instalasi_nama
                        ELSE data_info.instalasi_nama_ri
                    END AS instalasi_nama,
                    CASE
                        WHEN data_info.pasienadmisi_id IS NULL THEN data_info.ruangan_nama
                        ELSE data_info.ruangan_nama_ri
                    END AS ruangan_nama,
                data_info.status_bayar,
                data_info.jeniskasuspenyakit_id,
                data_info.tanggal_lahir,
                data_info.penjualanresep_id,
                data_info.jasa,
                data_info.administrasi,
                data_info.obat,
                data_info.totalharga_jual
               FROM ( SELECT pendaftaran_t.pendaftaran_id,
                        pendaftaran_t.instalasi_id,
                        pendaftaran_t.ruangan_id,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.penjamin_id,
                        pendaftaran_t.carabayar_id,
                        pendaftaran_t.kelaspelayanan_id,
                        pendaftaran_t.pasienpulang_id,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.tgl_pendaftaran,
                        pasien_m.no_rekam_medik,
                        pasien_m.nama_pasien,
                        pasien_m.no_mobile_pasien,
                        instalasi_m.instalasi_nama,
                        ruangan_m.ruangan_nama,
                        carabayar_m.carabayar_nama,
                        penjamin_m.penjamin_nama,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        pasienpulang_t.tglpasienpulang,
                        pulang_ri.tglpasienpulang AS tglpasienpulang_ri,
                        COALESCE(bayaruangmuka_t.jumlah_uangmuka, 0::double precision) - COALESCE(pemakaianuangmuka_t.pemakaian_uangmuka, 0::double precision) - COALESCE(pengembalianuangmuka_t.total_pengembalian, 0::double precision) AS jumlah_uangmuka,
                        pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
                        pasienadmisi_t.pasienadmisi_id,
                        pendaftaran_t.status_pasien,
                        pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                        dok_rj_rd.nama_pegawai AS nama_dok_rj_rd,
                        dok_ri.nama_pegawai AS nama_dok_ri,
                        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                        pendaftaran_t.umur,
                        pasienadmisi_t.carabayar_id AS carabayarri_id,
                        carabayar_ri.carabayar_nama AS carabayar_nama_ri,
                        pasienadmisi_t.penjamin_id AS penjaminri_id,
                        penjamin_ri.penjamin_nama AS penjamin_nama_ri,
                        pasienadmisi_t.ruangan_id AS ruanganri_id,
                        ruang_ri.instalasi_id AS instalasiri_id,
                        ruang_ri.ruangan_nama AS ruangan_nama_ri,
                        ins_ri.instalasi_nama AS instalasi_nama_ri,
                        pendaftaran_t.status_bayar,
                        jeniskasuspenyakit_m.jeniskasuspenyakit_id,
                        pasien_m.tanggal_lahir,
                        NULL::integer AS penjualanresep_id,
                        0 AS jasa,
                        0 AS administrasi,
                        0 AS obat,
                        0 AS totalharga_jual
                       FROM pendaftaran_t
                         LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                         JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                         JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
                         JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                         LEFT JOIN ruangan_m ruang_ri ON pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id
                         LEFT JOIN instalasi_m ins_ri ON ruang_ri.instalasi_id = ins_ri.instalasi_id
                         JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
                         JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
                         LEFT JOIN carabayar_m carabayar_ri ON pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id
                         LEFT JOIN penjamin_m penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
                         JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                         JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                         LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
                         LEFT JOIN pasienpulang_t pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
                         LEFT JOIN ( SELECT bayaruangmuka_t_1.pendaftaran_id,
                                sum(bayaruangmuka_t_1.jumlah_uangmuka) AS jumlah_uangmuka
                               FROM bayaruangmuka_t bayaruangmuka_t_1
                              WHERE bayaruangmuka_t_1.is_deleted = false
                              GROUP BY bayaruangmuka_t_1.pendaftaran_id) bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
                         LEFT JOIN pasienmasukpenunjang_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                         LEFT JOIN ( SELECT pengembalianuangmuka_t_1.pendaftaran_id,
                                sum(pengembalianuangmuka_t_1.total_pengembalian) AS total_pengembalian
                               FROM pengembalianuangmuka_t pengembalianuangmuka_t_1
                              WHERE pengembalianuangmuka_t_1.is_deleted = false
                              GROUP BY pengembalianuangmuka_t_1.pendaftaran_id) pengembalianuangmuka_t ON pendaftaran_t.pendaftaran_id = pengembalianuangmuka_t.pendaftaran_id
                         LEFT JOIN ( SELECT pemakaianuangmuka_t_1.pendaftaran_id,
                                sum(pemakaianuangmuka_t_1.pemakaian_uangmuka) AS pemakaian_uangmuka
                               FROM pemakaianuangmuka_t pemakaianuangmuka_t_1
                              WHERE pemakaianuangmuka_t_1.is_deleted = false
                              GROUP BY pemakaianuangmuka_t_1.pendaftaran_id) pemakaianuangmuka_t ON pendaftaran_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id
                         LEFT JOIN pegawai_m dok_rj_rd ON pendaftaran_t.pegawai_id = dok_rj_rd.pegawai_id
                         LEFT JOIN pegawai_m dok_ri ON pasienadmisi_t.pegawai_id = dok_ri.pegawai_id
                      GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.instalasi_id, pendaftaran_t.ruangan_id, pendaftaran_t.pasien_id, pendaftaran_t.penjamin_id, pendaftaran_t.carabayar_id, pendaftaran_t.kelaspelayanan_id, pendaftaran_t.pasienpulang_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.no_mobile_pasien, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, kelaspelayanan_m.kelaspelayanan_nama, pasienpulang_t.tglpasienpulang, pasienadmisi_t.pasienpulang_id, pasienadmisi_t.pasienadmisi_id, pasienmasukpenunjang_t.pasienmasukpenunjang_id, pendaftaran_t.status_pasien, pulang_ri.tglpasienpulang, dok_rj_rd.nama_pegawai, dok_ri.nama_pegawai, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pasienadmisi_t.carabayar_id, carabayar_ri.carabayar_nama, pasienadmisi_t.penjamin_id, penjamin_ri.penjamin_nama, pasienadmisi_t.ruangan_id, ruang_ri.instalasi_id, ruang_ri.ruangan_nama, ins_ri.instalasi_nama, bayaruangmuka_t.jumlah_uangmuka, pemakaianuangmuka_t.pemakaian_uangmuka, pengembalianuangmuka_t.total_pengembalian, pendaftaran_t.status_bayar, jeniskasuspenyakit_m.jeniskasuspenyakit_id, pasien_m.tanggal_lahir
                    UNION ALL
                     SELECT NULL::integer AS pendaftaran_id,
                        ruangan_m.instalasi_id,
                        penjualanresep_t.ruangan_id,
                        penjualanresep_t.pasien_id,
                        penjualanresep_t.penjamin_id,
                        penjualanresep_t.carabayar_id,
                        penjualanresep_t.kelaspelayanan_id,
                        0 AS pasienpulang_id,
                        penjualanresep_t.noresep AS no_pendaftaran,
                        penjualanresep_t.tglpenjualan AS tgl_pendaftaran,
                        pasien_m.no_rekam_medik,
                        penjualanresep_t.nama_pembeli AS nama_pasien,
                        pasien_m.no_mobile_pasien,
                        instalasi_m.instalasi_nama,
                        ruangan_m.ruangan_nama,
                        carabayar_m.carabayar_nama,
                        penjamin_m.penjamin_nama,
                        NULL::character varying AS kelaspelayanan_nama,
                        penjualanresep_t.tglresep AS tglpasienpulang,
                        penjualanresep_t.tglresep AS tglpasienpulang_ri,
                        0 AS jumlah_uangmuka,
                        0 AS pasienpulangri_id,
                        0 AS pasienadmisi_id,
                        NULL::character varying AS status_pasien,
                        0 AS pasienmasukpenunjang_id,
                        pegawai_m.nama_pegawai AS nama_dok_rj_rd,
                        pegawai_m.nama_pegawai AS nama_dok_ri,
                        NULL::character varying AS jeniskasuspenyakit_nama,
                        NULL::character varying AS umur,
                        penjualanresep_t.carabayar_id AS carabayarri_id,
                        carabayar_m.carabayar_nama AS carabayar_nama_ri,
                        penjualanresep_t.penjamin_id AS penjaminri_id,
                        penjamin_m.penjamin_nama AS penjamin_nama_ri,
                        penjualanresep_t.ruangan_id AS ruanganri_id,
                        ruangan_m.instalasi_id AS instalasiri_id,
                        ruangan_m.ruangan_nama AS ruangan_nama_ri,
                        instalasi_m.instalasi_nama AS instalasi_nama_ri,
                        penjualanresep_t.status_bayar,
                        0 AS jeniskasuspenyakit_id,
                        pasien_m.tanggal_lahir,
                        penjualanresep_t.penjualanresep_id,
                        COALESCE(penjualanresep_t.totaltarifservice, 0::double precision) AS jasa,
                        COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS administrasi,
                        COALESCE(penjualanresep_t.totalhargajual, 0::double precision) AS obat,
                        COALESCE(penjualanresep_t.totalhargajual, 0::double precision) + COALESCE(penjualanresep_t.totaltarifservice, 0::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS totalharga_jual
                       FROM penjualanresep_t
                         LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
                         LEFT JOIN ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
                         LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                         LEFT JOIN carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
                         LEFT JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
                         LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
                      WHERE penjualanresep_t.jenispenjualan::text = \'343\'::text AND penjualanresep_t.is_deleted = false) data_info;
        ');
        
        $this->execute('
            ALTER TABLE infodatapendaftaran_v
              OWNER TO postgres;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_105728_infodatapendaftaran_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_105728_infodatapendaftaran_v cannot be reverted.\n";

        return false;
    }
    */
}
