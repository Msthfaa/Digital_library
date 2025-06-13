<?php
// index.php (Versi FINAL V3 - Dengan Panel Statistik Eksekusi)

require_once __DIR__ . '/classes/LibrarySystem.php';
require_once __DIR__ . '/classes/Book.php';

$library = new LibrarySystem();

// Logika penanganan form (add, edit, delete)
$action = $_POST['action'] ?? '';
$message = '';
$messageType = '';

switch ($action) {
    case 'add':
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $year = (int)($_POST['year'] ?? 0);
        $category = trim($_POST['category'] ?? '');
        $synopsis = trim($_POST['synopsis'] ?? '');
        $cover_image_url = trim($_POST['cover_image_url'] ?? '');
        if (!empty($title) && !empty($author) && $year != 0 && !empty($category)) {
            $result = $library->addBook($title, $author, $year, $category, $synopsis, $cover_image_url);
            if ($result['success']) {
                $message = "Buku '{$title}' berhasil ditambahkan!";
                $messageType = 'success';
            } else {
                $message = $result['message'];
                $messageType = 'error';
            }
        } else {
            $message = "Harap isi semua kolom wajib.";
            $messageType = 'error';
        }
        break;
    case 'edit':
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $year = (int)($_POST['year'] ?? 0);
        $category = trim($_POST['category'] ?? '');
        $synopsis = trim($_POST['synopsis'] ?? '');
        $cover_image_url = trim($_POST['cover_image_url'] ?? '');
        if ($id > 0 && !empty($title) && !empty($author) && $year != 0 && !empty($category)) {
            if ($library->editBook($id, $title, $author, $year, $category, $synopsis, $cover_image_url)) {
                $message = "Buku berhasil diperbarui!";
                $messageType = 'success';
            } else {
                $message = "Gagal memperbarui buku.";
                $messageType = 'error';
            }
        } else {
            $message = "Data tidak valid untuk diedit.";
            $messageType = 'error';
        }
        break;
    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $book = $library->getBookById($id);
            if ($book && $library->deleteBook($id)) {
                $message = "Buku '{$book->title}' berhasil dihapus!";
                $messageType = 'success';
            } else {
                $message = "Gagal menghapus buku.";
                $messageType = 'error';
            }
        } else {
            $message = "ID buku tidak valid.";
            $messageType = 'error';
        }
        break;
}

// Logika untuk menentukan tampilan utama dan sub-tampilan
$view = $_GET['view'] ?? 'dashboard';
$display = $_GET['display'] ?? 'cards';
$searchQuery = trim($_GET['search'] ?? '');
$editingId = (int)($_GET['edit'] ?? 0);

if ($editingId) $view = 'edit_form';
if (!empty($searchQuery)) $view = 'list';
if (isset($_GET['view']) && $_GET['view'] === 'add_form') $view = 'add_form';

// Pengambilan data sesuai kebutuhan
$analyticsData = ($view === 'dashboard') ? $library->getAnalyticsData() : null;
$editingBook = ($view === 'edit_form' && $editingId > 0) ? $library->getBookById($editingId) : null;
$bookList = ($view === 'list') ? $library->getAllBooks() : [];
$booksFromSearch = !empty($searchQuery) ? $library->searchBooks($searchQuery) : $bookList;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Library Ultimate</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/heroicons/2.1.3/24/solid/css/heroicons.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        .nav-link.active { border-bottom-color: #3b82f6; color: #2563eb; font-weight: 600; }
        .view-toggle.active { background-color: #3b82f6; color: white; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .sorting-active { background-color: #fef3c7 !important; }
        .sorting-pivot { background-color: #dcfce7 !important; }
        .sorting-swapped { background-color: #fce7f3 !important; }
        .search-boundary { background-color: #e0e7ff !important; }
        .search-mid { background-color: #fef9c3 !important; }
        .search-found { background-color: #a7f3d0 !important; font-weight: bold; }
        .search-fail { background-color: #fee2e2 !important; }
    </style>
</head>
<body class="text-gray-800">
<div class="container mx-auto p-4 sm:p-6 lg:p-8">
    <header class="bg-white shadow-lg rounded-xl p-6 mb-8">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <img src="https://www.svgrepo.com/show/475524/library.svg" alt="Library Icon" class="h-12 w-12">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Digital Library Ultimate</h1>
                    <p class="text-gray-500 mt-1">Manajemen, Analitik, dan Visualisasi Algoritma.</p>
                </div>
            </div>
            <form action="index.php" method="get" class="w-full sm:w-1/3">
                <input type="hidden" name="view" value="list">
                <input type="text" name="search" placeholder="Cari buku..." class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" value="<?= htmlspecialchars($searchQuery) ?>">
            </form>
        </div>
        <nav class="mt-6 border-b border-gray-200">
            <ul class="flex space-x-6 text-gray-600">
                <li><a href="?" class="nav-link pb-3 inline-block <?= $view === 'dashboard' ? 'active' : '' ?>">Dashboard</a></li>
                <li><a href="?view=list" class="nav-link pb-3 inline-block <?= $view === 'list' ? 'active' : '' ?>">Koleksi Buku</a></li>
                <li><a href="?view=add_form" class="nav-link pb-3 inline-block <?= in_array($view, ['add_form', 'edit_form']) ? 'active' : '' ?>"><?= $editingBook ? 'Edit Buku' : 'Tambah Buku' ?></a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php if (!empty($message)): ?>
            <div class="p-4 mb-6 rounded-md <?= $messageType === 'success' ? 'bg-green-100 border-green-500 text-green-800' : 'bg-red-100 border-red-500 text-red-800' ?>">
                <p><?= htmlspecialchars($message) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($view === 'dashboard'): ?>
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Dashboard Analitik</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="stat-card bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow"><h3 class="text-gray-500 text-lg">Total Buku</h3><p class="text-4xl font-bold text-blue-600 mt-2"><?= $analyticsData['totalBooks'] ?></p></div>
                <div class="stat-card bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow"><h3 class="text-gray-500 text-lg">Jumlah Kategori</h3><p class="text-4xl font-bold text-green-600 mt-2"><?= $analyticsData['totalCategories'] ?></p></div>
                <div class="stat-card bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow"><h3 class="text-gray-500 text-lg">Jumlah Penulis</h3><p class="text-4xl font-bold text-purple-600 mt-2"><?= $analyticsData['totalAuthors'] ?></p></div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6"><div class="lg:col-span-3 bg-white p-6 rounded-lg shadow-md"><h3 class="text-xl font-semibold mb-4">Buku per Tahun Terbit</h3><canvas id="booksPerYearChart"></canvas></div><div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md"><h3 class="text-xl font-semibold mb-4">Distribusi Kategori</h3><canvas id="booksPerCategoryChart"></canvas></div></div>

        <?php elseif ($view === 'list'): ?>
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Koleksi Buku (<?= count($booksFromSearch) ?>)</h2>
                <div class="flex items-center border border-gray-300 rounded-lg p-1 bg-gray-200">
                    <a href="?view=list&display=cards" class="view-toggle px-4 py-1 rounded-md text-sm font-medium flex items-center gap-2 <?= $display == 'cards' ? 'active' : '' ?>"><i class="hi-solid hi-squares-2x2"></i> Kartu</a>
                    <a href="?view=list&display=table" class="view-toggle px-4 py-1 rounded-md text-sm font-medium flex items-center gap-2 <?= $display == 'table' ? 'active' : '' ?>"><i class="hi-solid hi-queue-list"></i> Algoritma</a>
                </div>
            </div>

            <?php if ($display === 'table'): ?>
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-semibold mb-3 text-center">Panel Kontrol Algoritma</h3>
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                            <div class="flex items-center gap-3 p-2 rounded-lg bg-gray-50">
                                <label class="font-medium whitespace-nowrap">Visualisasi Sorting:</label>
                                <select id="sortMethodSelect" class="flex-grow border-gray-300 rounded-md shadow-sm"><option value="">Pilih Algoritma</option><option value="insertion">Insertion Sort</option><option value="quick">Quick Sort</option></select>
                                <button onclick="triggerSortAndVisualization()" class="px-3 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">Mulai</button>
                                <button onclick="resetTableOrder()" class="px-3 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">Reset</button>
                            </div>
                             <div class="flex items-center gap-3 p-2 rounded-lg bg-gray-50">
                                <label class="font-medium whitespace-nowrap">Binary Search:</label>
                                <input type="text" id="binarySearchInput" placeholder="Judul buku eksak..." class="flex-grow border-gray-300 rounded-md shadow-sm">
                                <button onclick="triggerBinarySearch()" class="px-3 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700">Cari</button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="algorithmPanel" class="mt-4 hidden">
                        <h4 class="font-semibold mb-2 text-gray-700">Log Proses Algoritma:</h4>
                        <div id="algorithmLogContainer" class="h-48 overflow-y-auto bg-gray-800 text-white font-mono text-sm p-4 rounded-lg mb-4 shadow-inner"></div>
                        <h4 class="font-semibold mb-2 text-gray-700">Hasil Akhir:</h4>
                        <div id="searchResultOutput" class="p-4 bg-gray-100 rounded-lg font-medium">Menunggu pencarian dimulai...</div>

                        <h4 class="font-semibold mb-2 text-gray-700 mt-6">Statistik Eksekusi:</h4>
                        <div id="executionStats" class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-center">
                            <div class="bg-gray-100 p-4 rounded-lg shadow">
                                <div class="flex justify-center items-center text-blue-500 mb-2">
                                    <i class="hi-solid hi-clock h-6 w-6"></i>
                                    <h5 class="font-semibold ml-2">Waktu Eksekusi</h5>
                                </div>
                                <p id="stat-execution-time" class="text-2xl font-bold text-gray-800">- ms</p>
                            </div>

                            <div class="bg-gray-100 p-4 rounded-lg shadow">
                                <div class="flex justify-center items-center text-purple-500 mb-2">
                                    <i class="hi-solid hi-eye h-6 w-6"></i>
                                    <h5 class="font-semibold ml-2">Waktu Visualisasi</h5>
                                </div>
                                <p id="stat-visualization-time" class="text-2xl font-bold text-gray-800">- s</p>
                            </div>

                            <div class="bg-gray-100 p-4 rounded-lg shadow">
                                <div class="flex justify-center items-center text-green-500 mb-2">
                                    <i class="hi-solid hi-scale h-6 w-6"></i>
                                    <h5 class="font-semibold ml-2">Total Perbandingan</h5>
                                </div>
                                <p id="stat-comparisons" class="text-2xl font-bold text-gray-800">0</p>
                            </div>

                            <div class="bg-gray-100 p-4 rounded-lg shadow">
                                <div class="flex justify-center items-center text-red-500 mb-2">
                                    <i class="hi-solid hi-arrows-right-left h-6 w-6"></i>
                                    <h5 class="font-semibold ml-2">Total Penukaran</h5>
                                </div>
                                <p id="stat-swaps" class="text-2xl font-bold text-gray-800">0</p>
                            </div>
                        </div>
                        </div>

                    <div class="overflow-x-auto mt-6">
                        <table class="min-w-full"><thead class="bg-gray-50"><tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penulis</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tahun</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr></thead>
                        <tbody id="bookTableBody" class="bg-white divide-y divide-gray-200">
                            <?php foreach ($booksFromSearch as $book): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap font-medium" data-book-title="<?= htmlspecialchars($book->title) ?>"><?= htmlspecialchars($book->title) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600"><?= htmlspecialchars($book->author) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600"><?= $book->year ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="?edit=<?= $book->id ?>" class="text-blue-600 hover:text-blue-800">Edit</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody></table>
                    </div>
                </div>
            <?php else: // Tampilan Kartu ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    <?php if (empty($booksFromSearch)): ?>
                        <p class="col-span-full text-center text-gray-500 py-10">Tidak ada buku ditemukan.</p>
                    <?php else: ?>
                        <?php foreach ($booksFromSearch as $book): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col group transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                            <img src="<?= htmlspecialchars($book->cover_image_url ?: 'https://via.placeholder.com/400x500.png?text=No+Cover') ?>" alt="Cover" class="w-full h-64 object-cover">
                            <div class="p-4 flex flex-col flex-grow">
                                <h3 class="text-lg font-bold text-gray-800 group-hover:text-blue-600"><?= htmlspecialchars($book->title) ?></h3>
                                <p class="text-sm text-gray-600">oleh <?= htmlspecialchars($book->author) ?></p>
                                <p class="text-xs text-gray-500 mt-1 mb-2"><?= htmlspecialchars($book->category) ?> &bull; <?= $book->year ?></p>
                                <p class="text-sm text-gray-700 flex-grow h-20 overflow-hidden"><?= htmlspecialchars($book->synopsis) ?></p>
                                <div class="mt-4 pt-4 border-t flex justify-end gap-3">
                                    <a href="?edit=<?= $book->id ?>" class="text-sm font-medium text-blue-600 hover:underline">Edit</a>
                                    <form method="post" onsubmit="return confirm('Yakin?');" class="inline"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $book->id ?>"><button type="submit" class="text-sm font-medium text-red-600 hover:underline">Hapus</button></form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php elseif (in_array($view, ['add_form', 'edit_form'])): ?>
            <div class="bg-white p-8 rounded-xl shadow-lg max-w-4xl mx-auto">
                <h2 class="text-2xl font-bold text-gray-800 mb-8"><?= $editingBook ? 'Edit Detail Buku' : 'Tambah Buku Baru' ?></h2>
                <form method="post" action="index.php"><input type="hidden" name="action" value="<?= $editingBook ? 'edit' : 'add' ?>"><?php if ($editingBook): ?><input type="hidden" name="id" value="<?= $editingBook->id ?>"><?php endif; ?><div class="grid grid-cols-1 md:grid-cols-2 gap-6"><div><label for="title" class="block text-sm font-medium text-gray-700">Judul <span class="text-red-500">*</span></label><input type="text" name="title" required class="mt-1 w-full p-2 border rounded-md" value="<?= htmlspecialchars($editingBook->title ?? '') ?>"></div><div><label for="author" class="block text-sm font-medium text-gray-700">Penulis <span class="text-red-500">*</span></label><input type="text" name="author" required class="mt-1 w-full p-2 border rounded-md" value="<?= htmlspecialchars($editingBook->author ?? '') ?>"></div><div><label for="year" class="block text-sm font-medium text-gray-700">Tahun <span class="text-red-500">*</span></label><input type="number" name="year" required class="mt-1 w-full p-2 border rounded-md" value="<?= htmlspecialchars($editingBook->year ?? '') ?>"></div><div><label for="category" class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label><input type="text" name="category" required class="mt-1 w-full p-2 border rounded-md" value="<?= htmlspecialchars($editingBook->category ?? '') ?>"></div><div class="md:col-span-2"><label for="cover_image_url" class="block text-sm font-medium text-gray-700">URL Gambar Sampul</label><input type="url" name="cover_image_url" class="mt-1 w-full p-2 border rounded-md" value="<?= htmlspecialchars($editingBook->cover_image_url ?? '') ?>"></div><div class="md-col-span-2"><label for="synopsis" class="block text-sm font-medium text-gray-700">Sinopsis</label><textarea name="synopsis" rows="4" class="mt-1 w-full p-2 border rounded-md"><?= htmlspecialchars($editingBook->synopsis ?? '') ?></textarea></div></div><div class="mt-8 flex justify-end space-x-4"><a href="?view=list" class="px-6 py-2 border rounded-md hover:bg-gray-100">Batal</a><button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"><?= $editingBook ? 'Perbarui' : 'Simpan' ?></button></div></form>
            </div>
        <?php endif; ?>
    </main>
</div>

<script>
    // Logika Chart.js untuk Dashboard
    <?php if ($view === 'dashboard' && $analyticsData): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryData = <?= json_encode($analyticsData['booksPerCategory'] ?? []); ?>;
        const yearData = <?= json_encode($analyticsData['booksPerYear'] ?? []); ?>;
        if (Object.keys(categoryData).length > 0) { const ctxCategory = document.getElementById('booksPerCategoryChart').getContext('2d'); new Chart(ctxCategory, { type: 'doughnut', data: { labels: Object.keys(categoryData), datasets: [{ data: Object.values(categoryData), backgroundColor: ['#3B82F6', '#10B981', '#8B5CF6', '#F59E0B', '#EF4444'], hoverOffset: 4 }] }, options: { responsive: true, plugins: { legend: { position: 'top' } } } }); }
        if (Object.keys(yearData).length > 0) { const ctxYear = document.getElementById('booksPerYearChart').getContext('2d'); new Chart(ctxYear, { type: 'bar', data: { labels: Object.keys(yearData), datasets: [{ label: 'Jumlah Buku', data: Object.values(yearData), backgroundColor: 'rgba(59, 130, 246, 0.6)', borderColor: 'rgba(59, 130, 246, 1)', borderWidth: 1 }] }, options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }, plugins: { legend: { display: false } } } }); }
    });
    <?php endif; ?>

    // ===================================================================
    // BAGIAN LENGKAP: Logika Visualisasi Algoritma dengan Statistik
    // ===================================================================
    let originalTableHTML = '';
    let isVisualizing = false;
    const delayTime = 300; 

    // Variabel global untuk statistik sorting
    let sortComparisons = 0;
    let sortSwaps = 0;

    document.addEventListener('DOMContentLoaded', function() {
        const bookTableBody = document.getElementById('bookTableBody');
        if(bookTableBody) originalTableHTML = bookTableBody.innerHTML;
    });

    function clearAllHighlights() {
        document.querySelectorAll('#bookTableBody tr').forEach(row => {
            row.classList.remove('sorting-active', 'sorting-pivot', 'sorting-swapped', 'search-boundary', 'search-mid', 'search-found', 'search-fail');
        });
    }

    function resetUI() {
        clearAllHighlights();
        const panel = document.getElementById('algorithmPanel');
        if (panel) panel.classList.add('hidden');
    }

    async function setVisualizationStatus(status) {
        isVisualizing = status;
        const elementsToDisable = document.querySelectorAll('.view-toggle, .p-4 button, .p-4 select, .p-4 input, th button, td a, td button');
        elementsToDisable.forEach(el => {
            el.disabled = status;
            if(status) el.classList.add('opacity-50', 'cursor-not-allowed');
            else el.classList.remove('opacity-50', 'cursor-not-allowed');
        });
    }

    function resetTableOrder() {
        if(isVisualizing) { alert('Harap tunggu visualisasi selesai.'); return; }
        resetUI();
        const bookTableBody = document.getElementById('bookTableBody');
        if (bookTableBody) {
            bookTableBody.innerHTML = originalTableHTML;
            alert('Urutan tabel dan status telah direset.');
        }
    }

    function triggerSortAndVisualization() {
        if (isVisualizing) { alert('Harap tunggu visualisasi selesai.'); return; }
        const panel = document.getElementById('algorithmPanel');
        panel.classList.remove('hidden');
        const method = document.getElementById('sortMethodSelect').value;
        if (method) visualizeSort(method);
        else alert('Silakan pilih algoritma sorting terlebih dahulu.');
    }

    function triggerBinarySearch() {
        if (isVisualizing) { alert('Harap tunggu visualisasi selesai.'); return; }
        const targetTitle = document.getElementById('binarySearchInput').value.trim().toLowerCase();
        if (!targetTitle) { alert('Silakan masukkan judul buku yang ingin dicari.'); return; }
        
        resetUI(); 
        const panel = document.getElementById('algorithmPanel');
        const logContainer = document.getElementById('algorithmLogContainer');
        const resultOutput = document.getElementById('searchResultOutput');
        
        panel.classList.remove('hidden');
        logContainer.innerHTML = '';
        resultOutput.innerHTML = 'Mempersiapkan pencarian...';
        
        visualizeBinarySearch(targetTitle);
    }
    
    async function visualizeBinarySearch(target) {
        await setVisualizationStatus(true);
        const tbody = document.getElementById('bookTableBody');
        const logContainer = document.getElementById('algorithmLogContainer');
        const resultOutput = document.getElementById('searchResultOutput');
        let rows = Array.from(tbody.querySelectorAll('tr'));
        let step = 1;

        // --- BARU: Inisialisasi statistik ---
        let comparisons = 0;
        const startTime = performance.now(); // Mulai timer
        // ------------------------------------

        function log(message, colorClass = 'text-gray-300') {
            const p = document.createElement('p');
            p.innerHTML = `<span class="${colorClass}">></span> ${message}`;
            logContainer.appendChild(p);
            logContainer.scrollTop = logContainer.scrollHeight;
        }
        
        log('Memulai Binary Search...');
        rows.sort((a, b) => a.querySelector('td:first-child').dataset.bookTitle.toLowerCase().localeCompare(b.querySelector('td:first-child').dataset.bookTitle.toLowerCase()));
        rows.forEach(row => tbody.appendChild(row));
        log('Langkah 1: Tabel diurutkan berdasarkan judul.', 'text-yellow-300');
        await new Promise(r => setTimeout(r, 1000));

        let low = 0, high = rows.length - 1, foundRow = null;

        while(low <= high) {
            clearAllHighlights();
            rows[low].classList.add('search-boundary');
            rows[high].classList.add('search-boundary');
            
            let mid = Math.floor(low + (high - low) / 2);
            rows[mid].classList.add('search-mid');
            
            const midTitle = rows[mid].querySelector('td:first-child').dataset.bookTitle;
            const midTitleLower = midTitle.toLowerCase();
            
            // --- BARU: Hitung perbandingan ---
            comparisons++;
            // ---------------------------------
            
            log(`Langkah ${step++}: Batas [${low}, ${high}]. Cek tengah (index=${mid}).`);
            log(`--> Membandingkan: "${target}" dengan "${midTitleLower}"`);
            await new Promise(r => setTimeout(r, delayTime * 4));

            if (midTitleLower === target) {
                log(`--> Hasil: SAMA! Buku ditemukan.`, 'text-green-400');
                rows[mid].classList.add('search-found');
                foundRow = rows[mid];
                resultOutput.innerHTML = `<span class="text-green-600"><strong>Status: Ditemukan!</strong> Buku "${midTitle}" berada di baris ke-${mid + 1} (setelah diurutkan).</span>`;
                break;
            } else if (midTitleLower < target) {
                log(`--> Hasil: Target > Tengah. Batas bawah baru = ${mid + 1}.`, 'text-red-400');
                low = mid + 1;
            } else {
                log(`--> Hasil: Target < Tengah. Batas atas baru = ${mid - 1}.`, 'text-blue-400');
                high = mid - 1;
            }
        }
        
        if (!foundRow) {
            log(`Pencarian selesai. Buku tidak ditemukan.`, 'text-red-400');
            resultOutput.innerHTML = `<span class="text-red-600"><strong>Status: Tidak Ditemukan.</strong> Buku yang cocok dengan "${target}" tidak ada dalam daftar.</span>`;
            tbody.querySelectorAll('tr').forEach(row => row.classList.add('search-fail'));
            await new Promise(r => setTimeout(r, 1000));
        }

        // --- BARU: Finalisasi dan tampilkan statistik ---
        const endTime = performance.now();
        const visualizationTime = ((endTime - startTime) / 1000).toFixed(2);
        const executionTime = (endTime - startTime).toFixed(2); 

        document.getElementById('stat-execution-time').textContent = `${executionTime} ms`;
        document.getElementById('stat-visualization-time').textContent = `${visualizationTime} s`;
        document.getElementById('stat-comparisons').textContent = comparisons;
        document.getElementById('stat-swaps').textContent = 'N/A';
        // -----------------------------------------------

        tbody.querySelectorAll('tr').forEach(row => {
            row.classList.remove('search-boundary', 'search-mid', 'search-fail');
        });
        
        await setVisualizationStatus(false);
    }

    async function visualizeSort(sortMethod) {
        await setVisualizationStatus(true);
        resetUI();
        document.getElementById('algorithmPanel').classList.remove('hidden');

        // --- BARU: Reset statistik sebelum mulai ---
        sortComparisons = 0;
        sortSwaps = 0;
        const startTime = performance.now();
        // -------------------------------------------

        const tbody = document.getElementById('bookTableBody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        if (rows.length <= 1) { await setVisualizationStatus(false); return; }
        const booksToSort = rows.map(row => ({ title: row.querySelector('td:first-child').dataset.bookTitle.toLowerCase(), rowElement: row }));
        
        if (sortMethod === 'insertion') await insertionSortVisualize(booksToSort);
        else if (sortMethod === 'quick') await quickSortVisualize(booksToSort, 0, booksToSort.length - 1);
        
        booksToSort.forEach(book => tbody.appendChild(book.rowElement));

        // --- BARU: Finalisasi dan tampilkan statistik ---
        const endTime = performance.now();
        const visualizationTime = ((endTime - startTime) / 1000).toFixed(2);
        const executionTime = (endTime - startTime).toFixed(2);

        document.getElementById('stat-execution-time').textContent = `${executionTime} ms`;
        document.getElementById('stat-visualization-time').textContent = `${visualizationTime} s`;
        document.getElementById('stat-comparisons').textContent = sortComparisons;
        document.getElementById('stat-swaps').textContent = sortSwaps;
        // -----------------------------------------------

        await setVisualizationStatus(false);
        alert('Visualisasi sorting selesai!');
    }

    async function swapRows(arr, i, j) {
        // --- BARU: Hitung penukaran ---
        sortSwaps++;
        // ------------------------------
        arr[i].rowElement.classList.add('sorting-swapped');
        arr[j].rowElement.classList.add('sorting-swapped');
        await new Promise(r => setTimeout(r, delayTime * 1.5));
        [arr[i], arr[j]] = [arr[j], arr[i]];
        arr[i].rowElement.classList.remove('sorting-swapped');
        arr[j].rowElement.classList.remove('sorting-swapped');
    }

    async function insertionSortVisualize(arr) {
        for (let i = 1; i < arr.length; i++) {
            let key = arr[i];
            let j = i - 1;
            key.rowElement.classList.add('sorting-pivot');
            await new Promise(r => setTimeout(r, delayTime));
            
            while (j >= 0) {
                // --- BARU: Hitung perbandingan ---
                sortComparisons++;
                // ---------------------------------
                if(arr[j].title > key.title) {
                    arr[j].rowElement.classList.add('sorting-active');
                    await swapRows(arr, j + 1, j);
                    arr[j + 1].rowElement.classList.remove('sorting-active');
                    j = j - 1;
                } else {
                    break;
                }
            }
            key.rowElement.classList.remove('sorting-pivot');
        }
    }

    async function quickSortVisualize(arr, low, high) {
        if (low < high) {
            let pi = await partitionVisualize(arr, low, high);
            await quickSortVisualize(arr, low, pi - 1);
            await quickSortVisualize(arr, pi + 1, high);
        }
    }

    async function partitionVisualize(arr, low, high) {
        let pivot = arr[high];
        pivot.rowElement.classList.add('sorting-pivot');
        await new Promise(r => setTimeout(r, delayTime));
        let i = low - 1;
        for (let j = low; j < high; j++) {
            arr[j].rowElement.classList.add('sorting-active');
            await new Promise(r => setTimeout(r, delayTime));
            
            // --- BARU: Hitung perbandingan ---
            sortComparisons++;
            // ---------------------------------
            if (arr[j].title < pivot.title) { 
                i++; 
                await swapRows(arr, i, j); 
            }
            arr[j].rowElement.classList.remove('sorting-active');
        }
        await swapRows(arr, i + 1, high);
        pivot.rowElement.classList.remove('sorting-pivot');
        return i + 1;
    }

    // --- Finalisasi dan tampilkan statistik ---
    const endTime = performance.now();
    const visualizationTime = ((endTime - startTime) / 1000).toFixed(2);
    const executionTime = (endTime - startTime).toFixed(2); 

    document.getElementById('stat-execution-time').textContent = `${executionTime} ms`;
    document.getElementById('stat-visualization-time').textContent = `${visualizationTime} s`;
    document.getElementById('stat-comparisons').textContent = comparisons;
    document.getElementById('stat-swaps').textContent = 'N/A'; 
    // -----------------------------------------------
</script>
</body>
</html>