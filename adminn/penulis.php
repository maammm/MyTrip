<?php
include_once 'ceksession.php';
include_once 'function.php';

// Query untuk mengambil semua data penulis dari database
$query_penulis = "SELECT id, nickname, email FROM author ORDER BY id DESC";
$result_penulis = mysqli_query($koneksi, $query_penulis);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Manajemen Penulis - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="index.html">Menu Utama</a>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto me-0 me-md-3 my-2 my-md-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="#!">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" href="index.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <div class="sb-sidenav-menu-heading">Opsi</div>
                            <a class="nav-link collapsed" href="artikel.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                                Artikel
                            </a>
                            <a class="nav-link" href="penulis.php">
                                <div class="sb-nav-link-icon"><i class="fa-sharp-duotone fa-solid fa-user"></i></div>
                                Penulis
                            </a>
                            <a class="nav-link" href="kategori.php">
                                <div class="sb-nav-link-icon"><i class="fa-sharp-duotone fa-solid fa-layer-group"></i></div>
                                Kategori
                            </a>
                            <a class="nav-link" href="logout.php">
                                <div class="sb-nav-link-icon"><i class="fa-sharp-duotone fa-solid fa-right-from-bracket"></i></div>
                                Logout
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        <?php 
                        // Menampilkan username dari sesi yang sedang login
                        // htmlspecialchars() digunakan untuk keamanan (mencegah XSS)
                        echo htmlspecialchars($_SESSION['admin']['username']); 
                        ?>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Penulis</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Penulis</li>
                        </ol>

                        <?php
                        if (isset($_GET['status'])) {
                            $status = $_GET['status'];
                            $pesan = '';
                            $tipe_alert = 'success';

                            if ($status == 'sukses') {
                                $pesan = '<strong>Berhasil!</strong> Data artikel baru telah ditambahkan.';
                            } elseif ($status == 'edit_sukses') {
                                $pesan = '<strong>Berhasil!</strong> Data artikel telah diperbarui.';
                            } elseif ($status == 'hapus_sukses') {
                                $pesan = '<strong>Berhasil!</strong> Data artikel telah dihapus.';
                            } elseif ($status == 'gagal' || $status == 'edit_gagal' || $status == 'hapus_gagal') {
                                $pesan = '<strong>Gagal!</strong> Terjadi kesalahan pada proses data.';
                                $tipe_alert = 'danger';
                            }

                            if ($pesan) {
                                echo "<div class='alert alert-{$tipe_alert} alert-dismissible fade show' role='alert'>
                                        {$pesan}
                                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                    </div>";
                            }
                        }
                        ?>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i> Data Penulis
                            <button type="button" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#tambahPenulisModal">Tambah Penulis</button>
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Panggilan</th>
                                        <th>Email</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; while ($penulis = mysqli_fetch_assoc($result_penulis)): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($penulis['nickname']); ?></td>
                                        <td><?php echo htmlspecialchars($penulis['email']); ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm edit-btn" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editPenulisModal"
                                                    data-id="<?php echo $penulis['id']; ?>"
                                                    data-nickname="<?php echo htmlspecialchars($penulis['nickname']); ?>"
                                                    data-email="<?php echo htmlspecialchars($penulis['email']); ?>">
                                                Edit
                                            </button>
                                            <a href="proses_hapus_penulis.php?id=<?php echo $penulis['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus penulis ini? Artikel yang ditulis oleh penulis ini juga mungkin terpengaruh.')">Hapus</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            </div>
    </div>

    <div class="modal fade" id="tambahPenulisModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Tambah Penulis Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form action="proses_tambah_penulis.php" method="post">
                    <div class="modal-body">
                        <div class="mb-3"><label for="nickname" class="form-label">Nama Panggilan</label><input type="text" name="nickname" class="form-control" required></div>
                        <div class="mb-3"><label for="email" class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                        <div class="mb-3"><label for="password" class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPenulisModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Data Penulis</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form action="proses_edit_penulis.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="mb-3"><label for="edit-nickname" class="form-label">Nama Panggilan</label><input type="text" name="nickname" id="edit-nickname" class="form-control" required></div>
                        <div class="mb-3"><label for="edit-email" class="form-label">Email</label><input type="email" name="email" id="edit-email" class="form-control" required></div>
                        <div class="mb-3"><label for="edit-password" class="form-label">Password Baru</label><input type="password" name="password" id="edit-password" class="form-control"><small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"></script>
    <script src="js/datatables-simple-demo.js"></script>
    <script>
        // Script untuk mengisi data ke modal edit secara dinamis
        document.addEventListener('DOMContentLoaded', function () {
            var editPenulisModal = document.getElementById('editPenulisModal');
            editPenulisModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget; // Tombol yang memicu modal
                var id = button.getAttribute('data-id');
                var nickname = button.getAttribute('data-nickname');
                var email = button.getAttribute('data-email');

                // Update nilai form di dalam modal
                var modal = this;
                modal.querySelector('#edit-id').value = id;
                modal.querySelector('#edit-nickname').value = nickname;
                modal.querySelector('#edit-email').value = email;
                modal.querySelector('#edit-password').value = ''; // Kosongkan field password setiap kali modal dibuka
            });
        });
    </script>
</body>
</html>