<?php

use yii\db\Migration;

/**
 * Class m210124_144951_hotfix_view_show_group_lab
 */
class m210124_144951_hotfix_view_show_group_lab extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."nilaipemeriksaanlabdetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."nilaipemeriksaanlabdetail_v" AS  SELECT pemeriksaanlab_m.pemeriksaanlab_id,
                pemeriksaanlab_m.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                nilairujukan_m.nilairujukan_id,
                nilairujukan_m.nama_rujukan, 
                nilairujukan_m.jenis_kelamin,
                fgetnamalookup(nilairujukan_m.jenis_kelamin) AS jenis_kelamin_nama,
                nilairujukan_m.golonganumur_id,
                golonganumurlab_m.gol_umurlab_nama,
                COALESCE(nilairujukan_m.umur_awal, 0) AS gol_umurlab_minimal,
                COALESCE(nilairujukan_m.umur_akhir, 54750) AS gol_umurlab_maksimal,
                nilairujukan_m.nilai_rujukan,
                nilairujukan_m.nilai_min,
                nilairujukan_m.nilai_max,
                nilairujukan_m.satuan_hasillab AS satuanlab_nama,
                nilairujukan_m.keterangan,
                hasilpemeriksaanlabdetail_t.hasil,
                hasilpemeriksaanlabdetail_t.petugaslab_id,
                hasilpemeriksaanlabdetail_t.hasilpemeriksaanlabdetail_id,
                petugaslab.nama_pegawai AS petugaslab_nama,
                ambilsample_t.samplelab_id,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
                nilairujukan_m.is_deleted,
                hasilpemeriksaanlabdetail_t.metode,
                jenispemeriksaanlab_m.jenispemeriksaanlab_id,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama
               FROM ((((((((((ambilsample_t
                 JOIN pemeriksaanlab_m ON ((ambilsample_t.tindakanpaket_id = pemeriksaanlab_m.daftartindakan_id)))
                 JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
                 JOIN pasienmasukpenunjang_t ON ((ambilsample_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
                 JOIN daftartindakan_m ON ((pemeriksaanlab_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN nilairujukan_m ON ((pemeriksaanlab_m.pemeriksaanlab_id = nilairujukan_m.pemeriksaanlab_id)))
                 LEFT JOIN golonganumurlab_m ON ((nilairujukan_m.golonganumur_id = golonganumurlab_m.golonganumurlab_id)))
                 LEFT JOIN hasilpemeriksaanlab_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id) AND (ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id))))
                 LEFT JOIN hasilpemeriksaanlabdetail_t ON (((pemeriksaanlab_m.pemeriksaanlab_id = hasilpemeriksaanlabdetail_t.pemeriksaanlab_id) AND (nilairujukan_m.nilairujukan_id = hasilpemeriksaanlabdetail_t.nilairujukan_id) AND (hasilpemeriksaanlab_t.hasilpemeriksaanlab_id = hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id))))
                 LEFT JOIN pegawai_m petugaslab ON ((hasilpemeriksaanlabdetail_t.petugaslab_id = petugaslab.pegawai_id)))
                 LEFT JOIN samplelab_m ON ((hasilpemeriksaanlabdetail_t.samplelab_id = samplelab_m.samplelab_id)))
              WHERE ((ambilsample_t.is_deleted = false) AND (ambilsample_t.is_active = true) AND (pemeriksaanlab_m.is_deleted = false) AND (pemeriksaanlab_m.is_active = true) AND (daftartindakan_m.is_deleted = false) AND (daftartindakan_m.is_active = true))
            UNION ALL
             SELECT pemeriksaanlab_m.pemeriksaanlab_id,
                paketpelayanan_mp.daftartindakan_id,
                daftartindakan_m.daftartindakan_nama,
                pemeriksaanlab_m.tipepaket_id,
                tipepaket_m.tipepaket_nama,
                nilairujukan_m.nilairujukan_id,
                nilairujukan_m.nama_rujukan,
                nilairujukan_m.jenis_kelamin,
                fgetnamalookup(nilairujukan_m.jenis_kelamin) AS jenis_kelamin_nama,
                nilairujukan_m.golonganumur_id,
                golonganumurlab_m.gol_umurlab_nama,
                COALESCE(nilairujukan_m.umur_awal, 0) AS gol_umurlab_minimal,
                COALESCE(nilairujukan_m.umur_akhir, 54750) AS gol_umurlab_maksimal,
                nilairujukan_m.nilai_rujukan,
                nilairujukan_m.nilai_min,
                nilairujukan_m.nilai_max,
                nilairujukan_m.satuan_hasillab AS satuanlab_nama,
                nilairujukan_m.keterangan,
                hasilpemeriksaanlabdetail_t.hasil,
                hasilpemeriksaanlabdetail_t.petugaslab_id,
                hasilpemeriksaanlabdetail_t.hasilpemeriksaanlabdetail_id,
                petugaslab.nama_pegawai AS petugaslab_nama,
                ambilsample_t.samplelab_id,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
                nilairujukan_m.is_deleted,
                hasilpemeriksaanlabdetail_t.metode,
                jenispemeriksaanlab_m.jenispemeriksaanlab_id,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama
               FROM ((((((((((((ambilsample_t
                 JOIN pemeriksaanlab_m ON ((ambilsample_t.tindakanpaket_id = pemeriksaanlab_m.tipepaket_id)))
                 JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
                 JOIN pasienmasukpenunjang_t ON ((ambilsample_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
                 JOIN tipepaket_m ON ((pemeriksaanlab_m.tipepaket_id = tipepaket_m.tipepaket_id)))
                 JOIN paketpelayanan_mp ON ((pemeriksaanlab_m.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
                 JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 LEFT JOIN nilairujukan_m ON ((pemeriksaanlab_m.pemeriksaanlab_id = nilairujukan_m.pemeriksaanlab_id)))
                 LEFT JOIN golonganumurlab_m ON ((nilairujukan_m.golonganumur_id = golonganumurlab_m.golonganumurlab_id)))
                 LEFT JOIN hasilpemeriksaanlab_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id) AND (ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id))))
                 LEFT JOIN hasilpemeriksaanlabdetail_t ON (((pemeriksaanlab_m.pemeriksaanlab_id = hasilpemeriksaanlabdetail_t.pemeriksaanlab_id) AND (nilairujukan_m.nilairujukan_id = hasilpemeriksaanlabdetail_t.nilairujukan_id) AND (hasilpemeriksaanlab_t.hasilpemeriksaanlab_id = hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id))))
                 LEFT JOIN pegawai_m petugaslab ON ((hasilpemeriksaanlabdetail_t.petugaslab_id = petugaslab.pegawai_id)))
                 LEFT JOIN samplelab_m ON ((hasilpemeriksaanlabdetail_t.samplelab_id = samplelab_m.samplelab_id)))
              WHERE ((ambilsample_t.is_deleted = false) AND (ambilsample_t.is_active = true) AND (pemeriksaanlab_m.is_deleted = false) AND (pemeriksaanlab_m.is_active = true) AND (daftartindakan_m.is_deleted = false) AND (daftartindakan_m.is_active = true));
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."rincianpasienlab_v";
        ');

        $this->execute('
            CREATE VIEW "public"."rincianpasienlab_v" AS  SELECT \'ORDER\'::text AS tipe_pasien,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                pasienmasukpenunjang_t.pendaftaran_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik, 
                pasien_m.nama_pasien,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                pegawai_m.nama_pegawai AS dokter,
                ruangan_m.ruangan_nama,
                kelaspelayanan_m.kelaspelayanan_nama,
                penjamin_m.penjamin_nama,
                carabayar_m.carabayar_nama,
                fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
                sum(tindakanpelayanan_t.tarif_tindakan) AS total_tagihan,
                sum(tindakansudahbayar_t.jmliur_biaya) AS total_sdh_bayar,
                (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya) AS total_sisa_tagihan,
                pemakaianuangmuka_t.total_uangmuka,
                pasien_m.tanggal_lahir
               FROM ((((((((((((((pasienmasukpenunjang_t
                 JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                 JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN pasienadmisi_t ON ((pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                 JOIN pegawai_m ON ((COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id)))
                 JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
                 JOIN ruangan_m ON ((COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
                 JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 JOIN kelaspelayanan_m ON ((COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN penjamin_m ON ((COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) = penjamin_m.penjamin_id)))
                 JOIN carabayar_m ON ((COALESCE(pasienadmisi_t.carabayar_id, pendaftaran_t.carabayar_id) = carabayar_m.carabayar_id)))
                 JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
                 LEFT JOIN pemakaianuangmuka_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id)))
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 4)
              GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, pasienmasukpenunjang_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kelaspelayanan_m.kelaspelayanan_nama, penjamin_m.penjamin_nama, carabayar_m.carabayar_nama, pendaftaran_t.status_bayar, (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya), pemakaianuangmuka_t.total_uangmuka, pasien_m.tanggal_lahir
            UNION ALL
             SELECT \'RUJUKAN RS\'::text AS tipe_pasien,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                pasienmasukpenunjang_t.pendaftaran_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                pegawai_m.nama_pegawai AS dokter,
                ruangan_m.ruangan_nama,
                kelaspelayanan_m.kelaspelayanan_nama,
                penjamin_m.penjamin_nama,
                carabayar_m.carabayar_nama,
                fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
                sum(tindakanpelayanan_t.tarif_tindakan) AS total_tagihan,
                sum(tindakansudahbayar_t.jmliur_biaya) AS total_sdh_bayar,
                (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya) AS total_sisa_tagihan,
                pemakaianuangmuka_t.total_uangmuka,
                pasien_m.tanggal_lahir
               FROM ((((((((((((((pasienmasukpenunjang_t
                 JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                 JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
                 JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
                 JOIN rujukandari_m ON ((rujukan_t.rujukandari_id = rujukandari_m.rujukandari_id)))
                 JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
                 LEFT JOIN pemakaianuangmuka_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id)))
              WHERE (pendaftaran_t.instalasi_id = 4)
              GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, pasienmasukpenunjang_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kelaspelayanan_m.kelaspelayanan_nama, penjamin_m.penjamin_nama, carabayar_m.carabayar_nama, pendaftaran_t.status_bayar, (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya), pemakaianuangmuka_t.total_uangmuka, pasien_m.tanggal_lahir
            UNION ALL
             SELECT \'APS\'::text AS tipe_pasien,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                pasienmasukpenunjang_t.pendaftaran_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                pegawai_m.nama_pegawai AS dokter,
                ruangan_m.ruangan_nama,
                kelaspelayanan_m.kelaspelayanan_nama,
                penjamin_m.penjamin_nama,
                carabayar_m.carabayar_nama,
                fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
                sum(tindakanpelayanan_t.tarif_tindakan) AS total_tagihan,
                sum(tindakansudahbayar_t.jmliur_biaya) AS total_sdh_bayar,
                (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya) AS total_sisa_tagihan,
                pemakaianuangmuka_t.total_uangmuka,
                pasien_m.tanggal_lahir
               FROM (((((((((((pasienmasukpenunjang_t
                 JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                 JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
                 LEFT JOIN pemakaianuangmuka_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id)))
              WHERE ((pendaftaran_t.instalasi_id = 4) AND (pendaftaran_t.is_aps = true))
              GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, pasienmasukpenunjang_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kelaspelayanan_m.kelaspelayanan_nama, penjamin_m.penjamin_nama, carabayar_m.carabayar_nama, pendaftaran_t.status_bayar, (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya), pemakaianuangmuka_t.total_uangmuka, pasien_m.tanggal_lahir;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210124_144951_hotfix_view_show_group_lab cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210124_144951_hotfix_view_show_group_lab cannot be reverted.\n";

        return false;
    }
    */
}
