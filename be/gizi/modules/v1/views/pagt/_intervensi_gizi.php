<ol type="a">
    <li>
        <b>Cara Intervensi: </b> <?=ucfirst(str_replace('_', ' ', $cppt['cara_intervensi']))?>
    </li>
    <li>
        <b>Diet yang diberikan: </b> <?=$cppt['diet_diberikan']?>
    </li>
    <li>
        <b>Nilai Zat Gizi: </b> <br>
        <b>Energi: </b> <?=$cppt['energi']?>kal, <b>Lemak: </b> <?=$cppt['lemak']?>gr, <b>Protein: </b> <?=$cppt['protein']?>gr, <b>KH: </b> <?=$cppt['kh']?>gr,
    </li>
    <li>
        <b>Tujuan Diet: </b> <?=$cppt['tujuan_diet']?>
    </li>
    <li>
        <b>Bentuk Makan: </b> <?=str_replace(',', ', ', str_replace('_', ' ', $cppt['bentuk_makanan']))?> <b>Cair/Sonde: </b> <?=$cppt['sonde_voeding_saji']?> cc/saji, <?=$cppt['sonde_voeding_hari']?> kali/hari
    </li>
    <li>
        <?php $cara_pemberian = explode(',', $cppt['cara_pemberian']) ?>
        <b>Cara Pemberian: </b> <?=str_replace(',', ', ', $cppt['cara_pemberian'])?> <?=!empty($cara_pemberian) && in_array('vitamin/mineral', $cara_pemberian) ? ' : '.str_replace(',', ', ', $cppt['cara_pemberian_vitamin']) : ''?>
    </li>
    <li>
        <b>Pembagian Makan Sehari: </b> <br>
        <b>Makan Pagi: </b> <br>
        <b>Nasi/Penukar: </b> <?=$cppt['bagi_makan_pagi_nasi']?>, <b>Hewani: </b> <?=$cppt['bagi_makan_pagi_hewani']?>, <b>Nabati: </b> <?=$cppt['bagi_makan_pagi_nabati']?>, <b>Sayur: </b> <?=$cppt['bagi_makan_pagi_sayur']?>, <b>Buah: </b> <?=$cppt['bagi_makan_pagi_buah']?> <br><br>

        <b>Selingan Pagi: </b> <br>
        <b>Nasi/Penukar: </b> <?=$cppt['bagi_selingan_pagi_nasi']?>, <b>Hewani: </b> <?=$cppt['bagi_selingan_pagi_hewani']?>, <b>Nabati: </b> <?=$cppt['bagi_selingan_pagi_nabati']?>, <b>Sayur: </b> <?=$cppt['bagi_selingan_pagi_sayur']?>, <b>Buah: </b> <?=$cppt['bagi_selingan_pagi_buah']?> <br><br>

        <b>Makan Siang: </b> <br>
        <b>Nasi/Penukar: </b> <?=$cppt['bagi_makan_siang_nasi']?>, <b>Hewani: </b> <?=$cppt['bagi_makan_siang_hewani']?>, <b>Nabati: </b> <?=$cppt['bagi_makan_siang_nabati']?>, <b>Sayur: </b> <?=$cppt['bagi_makan_siang_sayur']?>, <b>Buah: </b> <?=$cppt['bagi_makan_siang_buah']?> <br><br>

        <b>Selingan Sore: </b> <br>
        <b>Nasi/Penukar: </b> <?=$cppt['bagi_selingan_sore_nasi']?>, <b>Hewani: </b> <?=$cppt['bagi_selingan_sore_hewani']?>, <b>Nabati: </b> <?=$cppt['bagi_selingan_sore_nabati']?>, <b>Sayur: </b> <?=$cppt['bagi_selingan_sore_sayur']?>, <b>Buah: </b> <?=$cppt['bagi_selingan_sore_buah']?> <br><br>

        <b>Makan Malam: </b> <br>
        <b>Nasi/Penukar: </b> <?=$cppt['bagi_makan_malam_nasi']?>, <b>Hewani: </b> <?=$cppt['bagi_makan_malam_hewani']?>, <b>Nabati: </b> <?=$cppt['bagi_makan_malam_nabati']?>, <b>Sayur: </b> <?=$cppt['bagi_makan_malam_sayur']?>, <b>Buah: </b> <?=$cppt['bagi_makan_malam_buah']?> <br><br>

        <b>Selingan Malam: </b> <br>
        <b>Nasi/Penukar: </b> <?=$cppt['bagi_selingan_malam_nasi']?>, <b>Hewani: </b> <?=$cppt['bagi_selingan_malam_hewani']?>, <b>Nabati: </b> <?=$cppt['bagi_selingan_malam_nabati']?>, <b>Sayur: </b> <?=$cppt['bagi_selingan_malam_sayur']?>, <b>Buah: </b> <?=$cppt['bagi_selingan_malam_buah']?> <br><br>
    </li>
</ol>
