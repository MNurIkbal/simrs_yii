<?php

use yii\db\Migration;

/**
 * Class m210407_113647_improvment_order_number_lab
 */
class m210407_113647_improvment_order_number_lab extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE nilairujukan_m ADD IF NOT EXISTS no_urut VARCHAR(30) ;
        ');

        $this->execute('
            ALTER TABLE hasilpemeriksaanlabdetail_t ADD IF NOT EXISTS no_urut VARCHAR(30) ;
        ');

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
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
                COALESCE(hasilpemeriksaanlabdetail_t.no_urut, nilairujukan_m.no_urut) AS no_urut
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
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
                COALESCE(hasilpemeriksaanlabdetail_t.no_urut, nilairujukan_m.no_urut) AS no_urut
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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210407_113647_improvment_order_number_lab cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210407_113647_improvment_order_number_lab cannot be reverted.\n";

        return false;
    }
    */
}
