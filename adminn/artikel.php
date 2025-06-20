<?php
// Memastikan sesi telah dimulai dan pengguna sudah login
include_once 'ceksession.php';
include_once 'function.php';

// Query untuk mengambil semua data artikel, diurutkan dari yang terbaru
$query = "SELECT id, title, date FROM article ORDER BY id DESC";
$query_kategori = "SELECT * FROM category ORDER BY name ASC";
$query = "SELECT 
            a.id, 
            a.title, 
            a.last_modified, 
            a.picture,
            c.name AS category_name
          FROM 
            article a
          LEFT JOIN 
            article_category ac ON a.id = ac.article_id
          LEFT JOIN 
            category c ON ac.category_id = c.id
          ORDER BY 
            a.id DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard Admin</title>
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
                        <h1 class="mt-4">Artikel</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                            <li class="breadcrumb-item active">Artikel</li>
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
                                <i class="fas fa-table me-1"></i>
                                Data Artikel
                                <button type="button" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#tambahArtikelModal">
                                    Tambah Artikel
                                </button>
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Judul Artikel</th>
                                            <th>Kategori</th>
                                            <th>Tanggal Terbit</th>
                                            <th>Gambar</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                                <td>
                                                    <?php 
                                                        // Tampilkan nama kategori atau 'Tanpa Kategori' jika tidak ada
                                                        echo !empty($row['category_name']) ? htmlspecialchars($row['category_name']) : '<span class="badge bg-secondary">Tanpa Kategori</span>';
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($row['last_modified']); ?></td>
                                                <td>
                                                    <?php 
                                                        if (!empty($row['picture'])) {
                                                            // Langsung tampilkan nama filenya sebagai teks
                                                            echo htmlspecialchars($row['picture']); 
                                                        } else {
                                                            // Tampilkan pesan jika tidak ada gambar
                                                            echo '<span class="text-muted">Tidak ada gambar</span>';
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="edit_artikel.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                    <a href="hapus_artikel.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
                <div class="modal fade" id="tambahArtikelModal" tabindex="-1" aria-labelledby="tambahArtikelModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="tambahArtikelModalLabel">Artikel Baru</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="proses_tambah_artikel.php" method="post" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="judul" class="form-label">Judul Artikel</label>
                                        <input type="text" class="form-control" id="judul" name="title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="category_name" class="form-label">Kategori</label>
                                        <input class="form-control" list="datalistOptions" id="category_name" name="category_name" placeholder="Ketik untuk mencari atau membuat baru..." required>
                                        <datalist id="datalistOptions">
                                            <?php
                                            mysqli_data_seek($result, 0);
                                            while($kategori = mysqli_fetch_assoc($result)){
                                                echo "<option value='" . htmlspecialchars($kategori['name']) . "'>";
                                            }
                                            ?>
                                        </datalist>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editor" class="form-label">Konten Artikel</label>
                                        <textarea class="form-control" id="editor" name="content" rows="10"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="gambar" class="form-label">Gambar Utama (Opsional)</label>
                                        <input type="file" class="form-control" id="gambar" name="picture" accept="image/*">
                                    </div>
                                
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Artikel</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2023</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
        <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
        <script>
            // Inisialisasi CKEditor pada textarea dengan id 'editor'
            ClassicEditor
                .create(document.querySelector('#editor'))
                .catch(error => {
                    console.error(error);
                });
        </script>
    </body>
</html>
