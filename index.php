<?php
require 'config.php'; // Sertakan file koneksi

function format_card_date($date_string) {
    if (!$date_string) {
        return '';
    }
    // Terjemahkan nama bulan ke Bahasa Inggris agar bisa dibaca oleh strtotime()
    $bulan_map = [
        'Januari' => 'January', 'Februari' => 'February', 'Maret' => 'March',
        'April' => 'April', 'Mei' => 'May', 'Juni' => 'June',
        'Juli' => 'July', 'Agustus' => 'August', 'September' => 'September',
        'Oktober' => 'October', 'November' => 'November', 'Desember' => 'December'
    ];
    $tanggal_en = str_replace(array_keys($bulan_map), array_values($bulan_map), $date_string);
    $timestamp = strtotime($tanggal_en);

    if ($timestamp === false) {
        return $date_string; // Kembalikan string asli jika format tidak dikenali
    }

    // Format tanggal ke format Indonesia lengkap (dengan nama hari)
    $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'Asia/Jakarta');
    return $formatter->format($timestamp);
}
// Tentukan zona waktu default
date_default_timezone_set('Asia/Jakarta');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    
    <?php
    // --- PENENTUAN MODE HALAMAN & JUDUL DINAMIS ---
    $is_detail_page = isset($_GET['id']) && !empty($_GET['id']);
    $is_category_page = isset($_GET['category_id']) && !empty($_GET['category_id']);
    $is_search_page = isset($_GET['q']) && !empty(trim($_GET['q']));

    if ($is_detail_page) {
        $article_id = (int)$_GET['id'];
        $query_title = $conn->prepare("SELECT title FROM article WHERE id = ?");
        $query_title->bind_param("i", $article_id);
        $query_title->execute();
        $result_title = $query_title->get_result();
        if ($result_title->num_rows > 0) {
            $article_title = $result_title->fetch_assoc()['title'];
            echo "<title>" . htmlspecialchars($article_title) . " - MyTrip</title>";
        } else {
            echo "<title>Artikel Tidak Ditemukan - Jalan Santai</title>";
        }
    } elseif ($is_category_page) {
        // Logika judul untuk halaman kategori
        $category_id = (int)$_GET['category_id'];
        $query_cat_title = $conn->prepare("SELECT name FROM category WHERE id = ?");
        $query_cat_title->bind_param("i", $category_id);
        $query_cat_title->execute();
        $result_cat_title = $query_cat_title->get_result();
        $category_title = ($result_cat_title->num_rows > 0) ? $result_cat_title->fetch_assoc()['name'] : "Kategori Tidak Ditemukan";
        echo "<title>Kategori: " . htmlspecialchars($category_title) . " - MyTrip</title>";
    } elseif ($is_search_page) {
        $search_term = trim($_GET['q']);
        echo "<title>Hasil Pencarian untuk '" . htmlspecialchars($search_term) . "'</title>";
    } else {
        echo "<title>MyTrip</title>";
    }
    ?>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <h1>Selamat Datang di Blog Kami!</h1>
            <p>Blog Catatan Wisata dan Jalan-Jalan</p>
        </div>
    </header>
    <nav class="main-nav">
        <div class="container">
            <ul>
                <li><a href="index.php">Beranda</a></li>
                <li><a href="tentang.php">Tentang</a></li>
                <li><a href="kontak.php">Kontak</a></li>
                <li><a href="adminn/index.php">Login/SignUp</a></li>
            </ul>
        </div>
    </nav>

    <div class="content-wrapper container">
        <?php if ($is_detail_page): ?>
            <?php
            // Kode untuk halaman detail (tidak berubah dari sebelumnya)
            $article_id = (int)$_GET['id'];
            $query = $conn->prepare("SELECT a.id, a.title, a.content, a.picture, a.last_modified AS creation_date, au.nickname AS author_name, c.name AS category_name, c.id AS category_id FROM article a LEFT JOIN article_author aa ON a.id = aa.article_id LEFT JOIN author au ON aa.author_id = au.id LEFT JOIN article_category ac ON a.id = ac.article_id LEFT JOIN category c ON ac.category_id = c.id WHERE a.id = ?");
            $query->bind_param("i", $article_id);
            $query->execute();
            $result = $query->get_result();
            $article = $result->fetch_assoc();

            if (!$article) {
                echo "<main class='main-content' style='text-align:center;'><p>Maaf, artikel tidak ditemukan.</p></main>";
            } else {
                $related_query = $conn->prepare("SELECT id, title FROM article WHERE id IN (SELECT article_id FROM article_category WHERE category_id = ?) AND id != ? ORDER BY RAND() LIMIT 3");
                $related_query->bind_param("ii", $article['category_id'], $article_id);
                $related_query->execute();
                $related_articles = $related_query->get_result();
            ?>
            <main class="main-content">
                <article class="post-detail">
                    <h1 class="post-detail-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                    <div class="post-detail-meta">
                        <?php
                            $bulan_map = ['Januari' => 'January','Februari' => 'February','Maret' => 'March','April' => 'April','Mei' => 'May','Juni' => 'June','Juli' => 'July','Agustus' => 'August','September' => 'September','Oktober' => 'October','November' => 'November','Desember' => 'December'];
                            $tanggal_en = str_replace(array_keys($bulan_map), array_values($bulan_map), $article['creation_date']);
                            $timestamp = strtotime($tanggal_en);
                            $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::FULL, IntlDateFormatter::NONE, 'Asia/Jakarta');
                            echo "Ditulis pada " . $formatter->format($timestamp) . " oleh <strong>" . htmlspecialchars($article['author_name']) . "</strong>";
                        ?>
                    </div>
                    <div class="post-detail-category"><?php echo htmlspecialchars($article['category_name']); ?></div>
                    <img src="images/<?php echo htmlspecialchars($article['picture']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="post-detail-image">
                    <div class="post-detail-content"><?php echo $article['content']; ?></div>
                    <a href="index.php" class="back-button">← Kembali ke Beranda</a>
                </article>
            </main>
            <aside class="sidebar">
                <div class="widget">
                    <h3 class="widget-title">Pencarian</h3>
                    <form class="search-form" action="index.php" method="get">
                        <input type="search" name="q" placeholder="Cari artikel...">
                        <button type="submit">Cari</button>
                    </form>
                </div>
                <div class="widget">
                    <h3 class="widget-title">Artikel Terkait</h3>
                    <div class="related-articles-container">
                        <?php if ($related_articles->num_rows > 0): while ($related = $related_articles->fetch_assoc()): ?>
                            <a href="index.php?id=<?php echo $related['id']; ?>" class="related-article-box"><?php echo htmlspecialchars($related['title']); ?></a>
                        <?php endwhile; else: ?>
                            <p>Tidak ada artikel terkait.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>
            <?php } ?>
            <?php elseif ($is_category_page): ?>
            <main class="main-content">
                <?php
                $category_id = (int)$_GET['category_id'];

                // Ambil nama kategori untuk judul
                $cat_name_query = $conn->prepare("SELECT name FROM category WHERE id = ?");
                $cat_name_query->bind_param("i", $category_id);
                $cat_name_query->execute();
                $cat_name_result = $cat_name_query->get_result();
                $category_name = ($cat_name_result->num_rows > 0) ? $cat_name_result->fetch_assoc()['name'] : "Tidak Dikenal";

                // Ambil semua artikel dalam kategori ini
                $cat_articles_query = $conn->prepare("
                    SELECT a.id, a.title, a.content, a.picture, a.last_modified 
                    FROM article a
                    JOIN article_category ac ON a.id = ac.article_id
                    WHERE ac.category_id = ? 
                    ORDER BY a.id DESC
                ");
                $cat_articles_query->bind_param("i", $category_id);
                $cat_articles_query->execute();
                $cat_articles_result = $cat_articles_query->get_result();
                ?>
                
                <?php if ($cat_articles_result->num_rows > 0): ?>
                    <?php while ($row = $cat_articles_result->fetch_assoc()):
                        $summary = strip_tags($row['content']);
                        $summary = substr($summary, 0, 150) . '...';
                    ?>
                        <article class="post-card">
                            <img src="images/<?php echo htmlspecialchars($row['picture']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="post-image">
                            <div class="post-content">
                                <p class="post-card-date"><?php echo format_card_date($row['last_modified']); ?></p>
                                <h2 class="post-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                                <p><?php echo htmlspecialchars($summary); ?></p>
                                <a href="index.php?id=<?php echo $row['id']; ?>" class="read-more">Selengkapnya →</a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Belum ada artikel dalam kategori ini. <a href="index.php">Kembali ke beranda</a>.</p>
                <?php endif; ?>
            </main>
            <aside class="sidebar">
                <div class="widget">
                    <h3 class="widget-title">Pencarian</h3>
                    <form class="search-form" action="index.php" method="get">
                        <input type="search" name="q" placeholder="Cari artikel...">
                        <button type="submit">Cari</button>
                    </form>
                </div>
                <div class="widget">
                    <h3 class="widget-title">Kategori</h3>
                    <ul class="category-list">
                        <?php
                        $query_categories = "SELECT id, name FROM category ORDER BY name ASC";
                        $result_categories = $conn->query($query_categories);
                        while ($cat = $result_categories->fetch_assoc()): ?>
                            <li><a href="index.php?category_id=<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <div class="widget">
                    <h3 class="widget-title">Tentang</h3>
                    <p>Blog ini berisi catatan dan ulasan dari berbagai tempat wisata menarik, khususnya di sekitar Malang Raya. Semoga tulisan kami bisa menjadi inspirasi untuk perjalanan Anda selanjutnya.</p>
                </div>
            </aside>
        <?php elseif ($is_search_page): ?>
            <main class="main-content">
                <?php
                $search_term = trim($_GET['q']);
                $search_pattern = "%" . $search_term . "%";

                $search_query = $conn->prepare("SELECT id, title, content, picture, last_modified FROM article WHERE title LIKE ? OR content LIKE ? ORDER BY id DESC");
                $search_query->bind_param("ss", $search_pattern, $search_pattern);
                $search_query->execute();
                $search_results = $search_query->get_result();
                ?>
                <?php if ($search_results->num_rows > 0): ?>
                    <?php while ($row = $search_results->fetch_assoc()):
                        $summary = strip_tags($row['content']);
                        $summary = substr($summary, 0, 150) . '...';
                    ?>
                        <article class="post-card">
                            <img src="images/<?php echo htmlspecialchars($row['picture']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="post-image">
                            <div class="post-content">
                                <p class="post-card-date"><?php echo format_card_date($row['last_modified']); ?></p>
                                <h2 class="post-title"><?php echo htmlspecialchars($row['title']); ?></h2>
                                <p><?php echo htmlspecialchars($summary); ?></p>
                                <a href="index.php?id=<?php echo $row['id']; ?>" class="read-more">Selengkapnya →</a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Maaf, tidak ada artikel yang cocok dengan kata kunci pencarian Anda. Coba kata kunci lain atau <a href="index.php">kembali ke beranda</a>.</p>
                <?php endif; ?>
            </main>
            <aside class="sidebar">
                 <div class="widget">
                    <h3 class="widget-title">Pencarian</h3>
                    <form class="search-form" action="index.php" method="get">
                        <input type="search" name="q" placeholder="Cari artikel lain...">
                        <button type="submit">Cari</button>
                    </form>
                </div>
                <div class="widget">
                    <h3 class="widget-title">Kategori</h3>
                    <ul class="category-list">
                        <?php
                        $query_categories = "SELECT id, name FROM category ORDER BY name ASC";
                        $result_categories = $conn->query($query_categories);
                        while ($cat = $result_categories->fetch_assoc()): ?>
                            <li><a href="index.php?category_id=<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </aside>

        <?php else: ?>
            <main class="main-content">
                <?php
                // Kode untuk homepage (tidak berubah dari sebelumnya)
                $query_articles = "SELECT id, title, content, picture, last_modified FROM article ORDER BY id DESC LIMIT 7";
                $result_articles = $conn->query($query_articles);
                $articles = $result_articles->fetch_all(MYSQLI_ASSOC);
                $featured_article = array_shift($articles);
                
                if ($featured_article):
                    $summary_featured = strip_tags($featured_article['content']);
                    $summary_featured = substr($summary_featured, 0, 200) . '...';
                ?>
                    <article class="post-card featured-post">
                        <img src="images/<?php echo htmlspecialchars($featured_article['picture']); ?>" alt="<?php echo htmlspecialchars($featured_article['title']); ?>" class="post-image">
                        <div class="post-content">
                            <p class="post-card-date"><?php echo format_card_date($featured_article['last_modified']); ?></p>
                            <h2 class="post-title"><?php echo htmlspecialchars($featured_article['title']); ?></h2>
                            <p><?php echo htmlspecialchars($summary_featured); ?></p>
                            <a href="index.php?id=<?php echo $featured_article['id']; ?>" class="read-more">Selengkapnya →</a>
                        </div>
                    </article>
                <?php endif; ?>
                <div class="post-grid">
                    <?php foreach ($articles as $article): 
                        $summary = strip_tags($article['content']);
                        $summary = substr($summary, 0, 100) . '...';
                    ?>
                        <article class="post-card">
                            <img src="images/<?php echo htmlspecialchars($article['picture']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="post-image">
                            <div class="post-content">
                                <p class="post-card-date"><?php echo format_card_date($article['last_modified']); ?></p>
                                <h2 class="post-title"><?php echo htmlspecialchars($article['title']); ?></h2>
                                <p><?php echo htmlspecialchars($summary); ?></p>
                                <a href="index.php?id=<?php echo $article['id']; ?>" class="read-more">Selengkapnya →</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </main>
            <aside class="sidebar">
                <div class="widget">
                    <h3 class="widget-title">Pencarian</h3>
                    <form class="search-form" action="index.php" method="get">
                        <input type="search" name="q" placeholder="Cari artikel...">
                        <button type="submit">Cari</button>
                    </form>
                </div>
                <div class="widget">
                    <h3 class="widget-title">Kategori</h3>
                    <ul class="category-list">
                        <?php
                        $query_categories = "SELECT id, name FROM category ORDER BY name ASC";
                        $result_categories = $conn->query($query_categories);
                        while ($cat = $result_categories->fetch_assoc()): ?>
                            <li><a href="index.php?category_id=<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                <div class="widget">
                    <h3 class="widget-title">Tentang</h3>
                    <p>Blog ini berisi catatan dan ulasan dari berbagai tempat wisata menarik, khususnya di sekitar Malang Raya. Semoga tulisan kami bisa menjadi inspirasi untuk perjalanan Anda selanjutnya.</p>
                </div>
            </aside>
        <?php endif; ?>
    </div>

    <footer class="main-footer">
        <div class="container">
            <p>Copyright © MyTrip 2025</p>
        </div>
    </footer>
</body>
</html>
<?php $conn->close(); ?>