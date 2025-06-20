<aside class="sidebar">
    <div class="widget">
        <h3 class="widget-title">Pencarian</h3>
        <form class="search-form" action="cari.php" method="get">
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
                <li><a href="kategori.php?category_id=<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
            <?php endwhile; ?>
        </ul>
    </div>
    <div class="widget">
        <h3 class="widget-title">Tentang</h3>
        <p>Blog ini berisi catatan dan ulasan dari berbagai tempat wisata menarik, khususnya di sekitar Malang Raya.</p>
    </div>
</aside>