<?php

class Book {
    public $id;
    public $title;
    public $author;
    public $year;
    public $category;
    public $created_at;
    
    // Properti baru ditambahkan
    public $synopsis;
    public $cover_image_url;

    /**
     * Konstruktor untuk kelas Book.
     *
     * @param int $id
     * @param string $title
     * @param string $author
     * @param int $year
     * @param string $category
     * @param string|null $created_at
     * @param string|null $synopsis         // Parameter baru
     * @param string|null $cover_image_url  // Parameter baru
     */
    public function __construct($id, $title, $author, $year, $category, $created_at = null, $synopsis = null, $cover_image_url = null) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->year = $year;
        $this->category = $category;
        $this->created_at = $created_at;
        
        $this->synopsis = $synopsis;
        $this->cover_image_url = $cover_image_url;
    }
}