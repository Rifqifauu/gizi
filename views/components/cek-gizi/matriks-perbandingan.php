<?php
include '../koneksi.php';

// Ambil semua alternatif
$alternatif = $koneksi->query("SELECT * FROM alternatif ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);

// Ambil alternatif yang dipilih
$selected_id = isset($_GET['selected']) ? (int)$_GET['selected'] : 0;

// Ambil semua nilai konversi
$konversi = [];
$result = $koneksi->query("SELECT * FROM konversi_nilai ORDER BY id ASC");
while($row = $result->fetch_assoc()) {
    $konversi[$row['alternatif_id']] = $row; // keyed by alternatif_id
}

// Ambil nama kriteria
$kriteria = ['k1','k2','k3','k4'];
?>

<!-- Dropdown Pilih Alternatif -->
<form method="GET" class="px-3 my-3">
    <input type="hidden" name="tab" value="matriks">
    <select name="selected" class="form-select w-auto d-inline">
        <option value="">-- Pilih Alternatif --</option>
        <?php foreach($alternatif as $alt): ?>
            <option value="<?= $alt['id'] ?>" <?= $alt['id'] == $selected_id ? 'selected' : '' ?>>
                <?= $alt['nama'] ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-success">Tampilkan</button>
</form>
 <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
    <h3 class="m-0">Matriks perbandingan antar alternatif pada setiap kriteria</h3>
</div>
<div class="table-responsive px-3">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Kriteria</th>
                <?php 
                // Hanya tampilkan kolom alternatif lain (selain yang dipilih)
                foreach($alternatif as $alt):
                    if($selected_id && $alt['id'] == $selected_id) continue;
                    echo "<th>{$alt['nama']}</th>";
                endforeach;
                ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($kriteria as $k): ?>
                <tr>
                    <td><?= strtoupper($k) ?></td>
                    <?php
                    if($selected_id && isset($konversi[$selected_id])):
                        $base = $konversi[$selected_id][$k];
                        foreach($alternatif as $alt):
                            if($alt['id'] == $selected_id) continue;
                            $val = $konversi[$alt['id']][$k] ?? 0;
                            $selisih = $base - $val;
                            echo "<td>".number_format($selisih,2)."</td>";
                        endforeach;
                    else:
                        echo "<td colspan='".(count($alternatif)-1)."'>Pilih alternatif terlebih dahulu</td>";
                    endif;
                    ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

