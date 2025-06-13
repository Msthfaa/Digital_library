<?php

require_once __DIR__ . '/../config/db.php';

class LibrarySystem {
    private $db;

    public function __construct() {
        $this->db = new Database();
        
        if (!$this->db->checkTables()) {
            // Memanggil metode initializeDatabase yang sudah didefinisikan di bawah
            $this->initializeDatabase();
        }
    }
    
    /**
     * Inisialisasi database dari file SQL jika tabel belum ada.
     * Ini adalah metode yang hilang dan menyebabkan error sebelumnya.
     */
    private function initializeDatabase() {
        $sqlFile = __DIR__ . '/../sql/init_database.sql';
        
        if (file_exists($sqlFile)) {
            $queries = file_get_contents($sqlFile);
            $this->db->getConnection()->multi_query($queries);
            
            // Membersihkan hasil dari multi_query
            while ($this->db->getConnection()->next_result()) {
                if ($result = $this->db->getConnection()->store_result()) {
                    $result->free();
                }
            }
        } else {
            throw new Exception("File inisialisasi database tidak ditemukan: " . $sqlFile);
        }
    }
    
    /**
     * Menambahkan buku baru ke database.
     */
    public function addBook($title, $author, $year, $category, $synopsis, $cover_image_url) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("SELECT id FROM books WHERE title = ? AND author = ?");
        $stmt->bind_param("ss", $title, $author);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'Buku dengan judul dan penulis ini sudah ada.'];
        }
        
        $stmt = $conn->prepare("INSERT INTO books (title, author, year, category, synopsis, cover_image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisss", $title, $author, $year, $category, $synopsis, $cover_image_url);
        
        if ($stmt->execute()) {
            $insertId = $stmt->insert_id;
            $stmt->close();
            return ['success' => true, 'id' => $insertId];
        } else {
            $stmt->close();
            return ['success' => false, 'message' => 'Gagal menambahkan buku ke database.'];
        }
    }

    /**
     * Mengambil semua buku dari database.
     * @return Book[]
     */
    public function getAllBooks() {
        $conn = $this->db->getConnection();
        $books = [];
        $query = "SELECT * FROM books ORDER BY id DESC";
        
        $result = $conn->query($query);
        
        while ($row = $result->fetch_assoc()) {
            $books[] = new Book(
                $row['id'], $row['title'], $row['author'],
                $row['year'], $row['category'], $row['created_at'],
                $row['synopsis'], $row['cover_image_url']
            );
        }
        
        return $books;
    }
    
    /**
     * Mencari buku berdasarkan query pada judul, penulis, atau kategori.
     */
    public function searchBooks($query) {
        $conn = $this->db->getConnection();
        $books = [];
        
        $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR category LIKE ?");
        $searchParam = "%" . $conn->real_escape_string($query) . "%";
        $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $books[] = new Book(
                $row['id'], $row['title'], $row['author'],
                $row['year'], $row['category'], $row['created_at'],
                $row['synopsis'], $row['cover_image_url']
            );
        }
        
        $stmt->close();
        return $books;
    }
    
    /**
     * Mendapatkan satu buku berdasarkan ID.
     */
    public function getBookById($id) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $stmt->close();
            return new Book(
                $row['id'], $row['title'], $row['author'],
                $row['year'], $row['category'], $row['created_at'],
                $row['synopsis'], $row['cover_image_url']
            );
        }
        
        $stmt->close();
        return null;
    }
    
    /**
     * Memperbarui data buku yang ada.
     */
    public function editBook($id, $title, $author, $year, $category, $synopsis, $cover_image_url) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, year = ?, category = ?, synopsis = ?, cover_image_url = ? WHERE id = ?");
        $stmt->bind_param("ssisssi", $title, $author, $year, $category, $synopsis, $cover_image_url, $id);
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }
    
    /**
     * Menghapus buku dari database.
     */
    public function deleteBook($id) {
        $conn = $this->db->getConnection();
        
        $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }
    
    /**
     * Mendapatkan data analitik untuk ditampilkan di dashboard.
     */
    public function getAnalyticsData() {
        $conn = $this->db->getConnection();
        $analytics = [
            'totalBooks' => 0,
            'totalAuthors' => 0,
            'totalCategories' => 0,
            'booksPerCategory' => [],
            'booksPerYear' => []
        ];

        $result = $conn->query("SELECT COUNT(*) as total FROM books");
        $analytics['totalBooks'] = $result->fetch_assoc()['total'];

        $result = $conn->query("SELECT COUNT(DISTINCT author) as total FROM books");
        $analytics['totalAuthors'] = $result->fetch_assoc()['total'];
        
        $result = $conn->query("SELECT COUNT(DISTINCT category) as total FROM books");
        $analytics['totalCategories'] = $result->fetch_assoc()['total'];

        $result = $conn->query("SELECT category, COUNT(*) as count FROM books GROUP BY category ORDER BY count DESC");
        while ($row = $result->fetch_assoc()) {
            $analytics['booksPerCategory'][$row['category']] = (int)$row['count'];
        }

        $result = $conn->query("SELECT year, COUNT(*) as count FROM books GROUP BY year ORDER BY year DESC LIMIT 10");
        $yearCounts = [];
        while ($row = $result->fetch_assoc()) {
            $yearCounts[$row['year']] = (int)$row['count'];
        }
        ksort($yearCounts);
        $analytics['booksPerYear'] = $yearCounts;

        return $analytics;
    }

    /**
     * Mengambil daftar kategori unik.
     */
    public function getCategories() {
        $conn = $this->db->getConnection();
        $categories = [];
        
        $result = $conn->query("SELECT DISTINCT category FROM books ORDER BY category ASC");
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row['category'];
        }
        
        return $categories;
    }
    
    /**
     * Mengambil daftar penulis unik.
     */
    public function getAuthors() {
        $conn = $this->db->getConnection();
        $authors = [];
        
        $result = $conn->query("SELECT DISTINCT author FROM books ORDER BY author ASC");
        while ($row = $result->fetch_assoc()) {
            $authors[] = $row['author'];
        }
        
        return $authors;
    }

    /**
     * Menutup koneksi database saat objek dihancurkan.
     */
    public function __destruct() {
        $this->db->closeConnection();
    }

    
}

