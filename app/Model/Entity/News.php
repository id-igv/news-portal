<?php
    namespace Model\Entity;
    
    class News {
        
        public $id;
        public $category_id;
        public $title;
        public $title_image;
        public $content;
        public $total_views;
        public $created;
        public $author_id;
        public $tag_set; 
    }
?>