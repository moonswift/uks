<?php

include "../pasien/koneksi.php";
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$limit = 5; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$query = "SELECT dp.*, 
           COALESCE(do.nama_obat, 'Data dihapus') AS nama_obat, 
           COALESCE(dpg.nama_pengurus, 'Data dihapus') AS nama_pengurus 
    FROM data_pasien dp 
    LEFT JOIN data_obat do ON dp.id_obat = do.id_obat 
    LEFT JOIN data_pengurus dpg ON dp.id_pengurus = dpg.id_pengurus
";

if ($search) {
    $query .= " WHERE dp.nama LIKE '%$search%' 
                OR do.nama_obat LIKE '%$search%' 
                OR dpg.nama_pengurus LIKE '%$search%' 
                OR dp.profesi LIKE '%$search%' 
                OR dp.gejala LIKE '%$search%'";
}

$query .= " LIMIT $start, $limit";

$result = mysqli_query($koneksi, $query);
if (!$result) {
    die('Query Error: ' . mysqli_error($koneksi)); 
}

$total_query = "SELECT COUNT(*) as total 
                FROM data_pasien dp 
                LEFT JOIN data_obat do ON dp.id_obat = do.id_obat 
                LEFT JOIN data_pengurus dpg ON dp.id_pengurus = dpg.id_pengurus";
if ($search) {
    $total_query .= " WHERE dp.nama LIKE '%$search%' 
                      OR do.nama_obat LIKE '%$search%' 
                      OR dpg.nama_pengurus LIKE '%$search%' 
                      OR dp.profesi LIKE '%$search%' 
                      OR dp.gejala LIKE '%$search%'";
}
$total_result = mysqli_query($koneksi, $total_query);
$total_data = mysqli_fetch_assoc($total_result)['total'];
$total_page = ceil($total_data / $limit);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pasien</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="nafigasi">
    <div class="logo">
        <h2>UKS_SMK</h2>
    </div>
    <div class="menu">
            <ul>
                <li><a href="../index/index.html"><b>Home</b></a></li>
                <li><a href="../obat/data_obat.php"><b>Data Obat</b></a></li>
                <li><a href="../pasien/pasien.php"><b>Data Pasien</b></a>
                <li><a href="../pengurus/pengurus.php"><b>Data Pengurus</b></a></li>
            </ul>
    </div>
</div>

            <div class="mt-3" style="padding-top: 80px">
                <h1 class="text-center">Data Pasien</h1>
            </div>
            <div class="container ms-5">
                <div class="card">
                    <div class="card-header text-white" style="background: radial-gradient(circle, #191fb3, #00268f, #002467, #091d3f, #141518); color: white;">Input Pasien</div>
                    <div class="card-body">
                        
            <form action="pasien.php" method="GET" class="form-inline mb-3 d-flex">
                <input class="form-control me-2" type="search" name="search" placeholder="Cari Pasien, obat, pengurus..." value="<?= htmlspecialchars($search)?>" aria-label="Search">
                <button class="btn btn-primary" type="submit">Search</button>
            </form>
                        
                    <button type="button" class="btn btn-success mb-3 mt-2" data-bs-toggle="modal" data-bs-target="#modaltambah">
                     Tambah Data
                    </button>
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped-table-hover">
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama</th>
                            <th>Status</th>
                            <th>Keluhan</th>
                            <th>profesi</th>
                            <th>Obat</th>
                            <th>Jumlah</th>
                            <th>Pengurus</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                        <?php  
                            $result = mysqli_query($koneksi, $query);
                            $no = 1;while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                            <?php if (!empty($row['gambar'])): ?>
                                <img src="uploads/<?= $row['gambar'] ?>" alt="Gambar Pasien" style="width: 50px; height: 50px;">
                            <?php else: ?>
                                <span>Tidak ada gambar</span>
                            <?php endif; ?>
                        </td>
                                        <td><?= $row['nama'] ?></td>
                                        <td><?= isset($row['status']) ? $row['status'] : 'Tidak ada status' ?></td>
                                        <td><?= $row['gejala'] ?></td>
                                        <td><?= $row['profesi'] ?></td>
                                        <td><?= $row['nama_obat'] ?></td>
                                        <td><?= $row['jumlah'] ?></td>
                                        <td><?= $row['nama_pengurus'] ?></td>
                                        <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>          
                            <td><a href="" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $no ?>"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
  <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
</svg></a>

<a href="action.php?hapus=<?= $row['id_pasien'] ?>" class="btn btn-danger" onclick="return confirm('Apakah kamu yakin ingin menghapus data ini?')"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
  <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
</svg></a>
</td>
</tr>
                    
                        <div class="modal fade modal-lg" id="modalEdit<?= $no ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Data Pasien</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="action.php">
                                        <div class="mb-3">
                                            <input type="hidden" class="form-control" name="id_pasien" value="<?=$row['id_pasien'] ?>"
                                             id="exampleFormControlInput1" placeholder="Masukkan ID_Pasien">
                                        </div>
                            <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" name="nama" value="<?=$row['nama'] ?>"
                             placeholder="Masukkan Nama Pasien">
                        </div>
                        <div class="mb-3">
                        <label for="profesi" class="form-label">Profesi</label>
                        <select class="form-select" name="profesi" id="profesi" required>
                            <option value="" disabled selected>Pilih profesi</option>
                            <option value="pegawai">Pegawai</option>
                            <option value="siswa">Siswa</option>
                        </select>
                    </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option disabled selected value="<?=$row['status'] ?>"><?=$row['status'] ?></option>
                                <option value="tetap di UKS">Tetap di UKS</option>
                                <option value="pulang">Pulang</option>
                                <option value="dirujuk">Dirujuk</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlTextarea1" class="form-label">Gejala</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="gejala" required><?=$row['gejala'] ?></textarea>
                       </div>
                       <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Obat</label>
                                <select class="form-select" name="id_obat" required>
                                    <option disabled selected value="<?=$row['nama_obat'] ?>"><?=$row['nama_obat'] ?></option>
                                        <?php
                                            $obatQuery = mysqli_query($koneksi, "SELECT * FROM data_obat");
                                            while ($obat = mysqli_fetch_assoc($obatQuery)) {
                                                echo "<option value='" . $obat['id_obat'] . "'>" . $obat['nama_obat'] . "</option>";
                                            }?></select>
                        </div>
                        <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah Obat</label>
                                <input type="number" class="form-control" id="jumlah" name="jumlah" value="<?=$row['jumlah'] ?>" placeholder="Masukkan Jumlah Obat" required>
                            </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Pengurus</label>
                            <select class="form-select" name="id_pengurus" required>
                            <option disabled selected value="<?=$row['nama_pengurus'] ?>"><?=$row['nama_pengurus'] ?></option>
                                <?php
                                $pengurusQuery = mysqli_query($koneksi, "SELECT * FROM data_pengurus");
                                while ($pengurus = mysqli_fetch_assoc($pengurusQuery)) {
                                echo "<option value='" . $pengurus['id_pengurus'] . "'>" . $pengurus['nama_pengurus'] . "</option>";
                                }?>                
                           </select>
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="exampleFormControlInput1" name="tanggal" value="<?=$row['tanggal'] ?>" required >
                        </div>
                        <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary" name="edit">Edit</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                        <?php
                            }
                            ?></table>
                    </div>
                    <nav>
      <ul class="pagination">
        <?php for ($i = 1; $i <= $total_page; $i++): ?>
          <li class="page-item <?= $i == $page ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>&search=<?= $search ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
                    
                    <div class="modal fade modal-lg" id="modaltambah" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Data Pasien</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                
                                <div class="modal-body">
                                    <form method="POST" action="action.php">
                                        <div class="mb-3">
                                            <input type="hidden" class="form-control"  id="exampleFormControlInput1" name="id_pasien" placeholder="Masukkan ID_Pasien">
                                        </div>
                                <div class="mb-3">
                                    <label for="gambar" class="form-label">Upload Gambar</label>
                                    <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*" required>
                                </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" name="nama" placeholder="Masukkan Nama Pasien" required>
                        </div>
                        
                        <div class="mb-3">
                        <label for="profesi" class="form-label">Profesi</label>
                        <select class="form-select" name="profesi" id="profesi" required>
                            <option value="" disabled selected>Pilih profesi</option>
                            <option value="pegawai">Pegawai</option>
                            <option value="siswa">Siswa</option>
                        </select>
                    </div>

                            <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                            <option disabled selected value="<?= isset($row['status']) ? $row['status'] : '' ?>">
                                <?= isset($row['status']) ? $row['status'] : 'Pilih Status' ?>
                            </option>
                            <option value="tetap di UKS">Tetap di UKS</option>
                            <option value="pulang">Pulang</option>
                            <option value="dirujuk">Dirujuk</option>
                            </select>

                        <div class="mb-3">
                            <label for="exampleFormControlTextarea1" class="form-label">Gejala</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="gejala" required></textarea>
                       </div>

                       <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Obat</label>
                                <select class="form-select" name="id_obat" required>
                                    <option disabled selected>Pilih</option>
                                        <?php
                                            $obatQuery = mysqli_query($koneksi, "SELECT * FROM data_obat");
                                            while ($obat = mysqli_fetch_assoc($obatQuery)) {
                                                echo "<option value='" . $obat['id_obat'] . "'>" . $obat['nama_obat'] . "</option>";
                                            }
                                            ?>
                                </select>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah Obat</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" placeholder="Masukkan Jumlah Obat" required>
                        </div>

                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Pengurus</label>
                            <select class="form-select" name="id_pengurus" required>
                            <option disabled selected>Pilih Pengurus</option>
                                <?php
                                $pengurusQuery = mysqli_query($koneksi, "SELECT * FROM data_pengurus");
                                while ($pengurus = mysqli_fetch_assoc($pengurusQuery)) {
                                echo "<option value='" . $pengurus['id_pengurus'] . "'>" . $pengurus['nama_pengurus'] . "</option>";
                                }
                                ?>                
                           </select>
                        </div>

                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="exampleFormControlInput1" name="tanggal" required >
                        </div>
                        <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary" name="simpan">Simpan</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>