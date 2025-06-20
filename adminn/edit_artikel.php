<?php
// PHP di bagian atas untuk mengambil data artikel tetap sama
include_once 'ceksession.php';
include_once 'function.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: artikel.php");
    exit;
}
$id = $_GET['id'];

// Ambil data artikel utama dan relasinya
$query = "SELECT a.*, c.id as category_id, au.id as author_id
          FROM article a
          LEFT JOIN article_category ac ON a.id = ac.article_id
          LEFT JOIN category c ON ac.category_id = c.id
          LEFT JOIN article_author aa ON a.id = aa.article_id
          LEFT JOIN author au ON aa.author_id = au.id
          WHERE a.id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$artikel = mysqli_fetch_assoc($result);

if (!$artikel) {
    header("Location: artikel.php?status=id_tidak_ditemukan");
    exit;
}

// Ambil semua list kategori dan penulis untuk dropdown
$kategori_list = mysqli_query($koneksi, "SELECT id, name FROM category ORDER BY name");
$penulis_list = mysqli_query($koneksi, "SELECT id, nickname FROM author ORDER BY nickname");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Edit Artikel - Admin</title>
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
                            <li class="breadcrumb-item"><a href="artikel.php">Artikel</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header"><i class="fas fa-edit me-1"></i> Form Edit Artikel</div>
                            <div class="card-body">
                                <form id="edit-form" action="proses_edit_artikel.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="id" value="<?php echo $artikel['id']; ?>">
                                    <input type="hidden" name="gambar_lama" value="<?php echo htmlspecialchars($artikel['picture']); ?>">

                                    <div class="mb-3"><label for="title" class="form-label">Judul Artikel</label><input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($artikel['title']); ?>" required></div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="category_id" class="form-label">Kategori</label>
                                            <select class="form-select" id="category_id" name="category_id" required>
                                                <?php mysqli_data_seek($kategori_list, 0); while($kategori = mysqli_fetch_assoc($kategori_list)): ?>
                                                    <option value="<?php echo $kategori['id']; ?>" <?php if($kategori['id'] == $artikel['category_id']) echo 'selected'; ?>><?php echo htmlspecialchars($kategori['name']); ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="author_id" class="form-label">Penulis</label>
                                            <select class="form-select" id="author_id" name="author_id" required>
                                                <?php mysqli_data_seek($penulis_list, 0); while($penulis = mysqli_fetch_assoc($penulis_list)): ?>
                                                    <option value="<?php echo $penulis['id']; ?>" <?php if($penulis['id'] == $artikel['author_id']) echo 'selected'; ?>><?php echo htmlspecialchars($penulis['nickname']); ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3"><label for="editor" class="form-label">Konten Artikel</label><textarea class="form-control" id="editor" name="content"><?php echo htmlspecialchars($artikel['content']); ?></textarea></div>
                                        <?php if (!empty($artikel['picture'])): ?>
                                            <div class="mb-2"><img src="assets/img/<?php echo htmlspecialchars($artikel['picture']); ?>" alt="Gambar saat ini" style="max-width: 200px;"></div>
                                        <?php endif; ?>
                                    <div class="mb-3"><label for="picture" class="form-label">Ganti Gambar</label><input type="file" class="form-control" id="picture" name="picture" accept="image/*"></div>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    <a href="artikel.php" class="btn btn-secondary">Batal</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
        <script>
            let editorInstance;
            CKEDITOR.ClassicEditor
                .create(document.querySelector('#editor'), {
                    // PERBAIKAN FINAL: Tambahkan plugin baru yang menyebabkan error ke dalam daftar ini
                    removePlugins: [
                        'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage',
                        'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges', 'RealTimeCollaborativeRevisionHistory',
                        'PresenceList', 'Comments', 'TrackChanges', 'TrackChangesData', 'RevisionHistory',
                        'Pagination', 'WProofreader', 'MathType', 'SlashCommand', 'Template', 'DocumentOutline',
                        'FormatPainter', 'TableOfContents', 'PasteFromOfficeEnhanced',
                        // Menambahkan plugin penyebab error baru
                        'MultiLevelList', 
                        'CaseChange'
                    ],
                    // Konfigurasi toolbar bisa Anda sesuaikan lagi jika perlu
                    toolbar: {
                        items: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'uploadImage', 'insertTable', '|', 'undo', 'redo' ]
                    },
                    simpleUpload: { uploadUrl: 'upload_gambar.php' }
                })
                .then(editor => {
                    editorInstance = editor;
                })
                .catch(error => {
                    console.error(error);
                });
            
            document.querySelector('#edit-form').addEventListener('submit', function() {
                if (editorInstance) {
                    document.querySelector('textarea#editor').value = editorInstance.getData();
                }
            });
        </script>
    </body>
</html>