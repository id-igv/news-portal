<?php
    namespace Model;
    
    use \Library\Manager;
    use Model\Entity\Category;
    use Model\Entity\News;
    
    class NewsManager extends Manager {
        
        public function updateCategory($id, $name) {
            $stmt = $this->pdo->prepare("UPDATE categories SET name='{$name}' WHERE id={$id}");
            return $stmt->execute();
        }
        
        public function addCategory($name) {
            $this->pdo->exec("INSERT INTO categories (name) VALUES ('{$name}')");
        }
        
        public function rmCategory($id) {
            $this->pdo->exec("DELETE FROM categories WHERE id={$id}");
        }
        
        public function findAllNews() {
            $newsList = [];
            $stmt = $this->pdo->query("SELECT * FROM news");
            
            while ($res = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $news = new News();
                $news->id = $res['id'];
                $news->category_id = $res['category_id'];
                $news->title = $res['title'];
                $news->title_image = $res['title_image'];
                $news->content = $res['content'];
                $news->total_views = $res['total_views'];
                $news->created = $res['created'];
                $news->author_id = $res['author_id'];
                $news->tag_set = $res['tag_set'];
                
                // echo '<pre>';print_r($bike);echo '</pre>';
                // echo 'end';
                $newsList[] = $news;
            }
            
            return $newsList;
        }
        
        public function findAllCategories() {
            $categories = [];
            $stmt = $this->pdo->query("SELECT * FROM categories");
            
            while ($res = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $category = new Category();
                $category->id = $res['id'];
                $category->name = $res['name'];
                // echo '<pre>';print_r();echo '</pre>';
                // echo 'end';
                $categories[] = $category;
            }
            
            return $categories;
        }
        
        public function findLastNews($count, $category_id = null) {
            $newsList = [];
            $query = "SELECT id, title, title_image FROM news";
            
            if (!is_null($category_id)) {
                $query .= " WHERE category_id={$category_id}";
            }
            $query .= " ORDER BY created LIMIT {$count}";
            
            $stmt = $this->pdo->query($query);
                
            
            while ($res = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $news = new News();
                $news->id = $res['id'];
                $news->title = $res['title'];
                $news->title_image = $res['title_image'];
                // echo '<pre>';print_r($bike);echo '</pre>';
                // echo 'end';
                $newsList[] = $news;
            }
            
            return $newsList;
        }
        
        public function findNewsByID($id) {
            $news = null;
            $query = "SELECT * FROM news WHERE id={$id}";
            
            $stmt = $this->pdo->query($query);
            
            while ($res = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $news = new News();
                $news->id = $res['id'];
                $news->title = $res['title'];
                $news->title_image = $res['title_image'];
                $news->content = $res['content'];
                $news->total_views = $res['total_views'];
                $news->created = $res['created'];
                $news->author_id = $res['author_id'];
                $news->tag_set = $res['tag_set'];
                
                // echo '<pre>';print_r($bike);echo '</pre>';
                // echo 'end';
            }
            
            return $news;
        }
        
        public function findCategoryByID($id) {
            $category = null;
            $query = "SELECT * FROM categories WHERE id={$id}";
            
            $stmt = $this->pdo->query($query);
            
            while ($res = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $category = new Category();
                $category->id = $res['id'];
                $category->name = $res['name'];
                
                // echo '<pre>';print_r($bike);echo '</pre>';
                // echo 'end';
            }
            
            return $category;
        }
        
        public function findCategoryByName($name) {
            $category = null;
            $query = "SELECT * FROM categories WHERE name='{$name}'";
            
            $stmt = $this->pdo->query($query);
            
            while ($res = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $category = new Category();
                $category->id = $res['id'];
                $category->name = $res['name'];
                
                // echo '<pre>';print_r($bike);echo '</pre>';
                // echo 'end';
            }
            
            return $category;
        }
        
        public function findNewsByTag($tagname, $count, $offset) {
            $newsList = [];
            $stmt = $this->pdo->query("SELECT id, title FROM news WHERE tag_set LIKE '%" . $tagname . "%'  LIMIT {$offset}, {$count}");
            
            while ($res = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $news = new News();
                $news->id = $res['id'];
                $news->title = $res['title'];
                // echo '<pre>';print_r($bike);echo '</pre>';
                // echo 'end';
                $newsList[] = $news;
            }
            
            return $newsList;
        }
        
        public function findAllNewsByCategory($category_id, $count, $offset) {
            $result = [];
            $query = "SELECT id, title FROM news WHERE category_id={$category_id} LIMIT {$offset}, {$count}";
            $stmt = $this->pdo->query($query);
            
            while ($res = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $news = new News();
                $news->id = $res['id'];
                $news->title = $res['title'];
                
                // echo '<pre>';print_r($bike);echo '</pre>';
                // echo 'end';
                $result[] = $news;
            }
            
            return $result;
        }
        
        public function countNewsByCategory($category_id)
        {
            $stmt = $this->pdo->query("SELECT COUNT(id) AS count FROM news WHERE category_id={$category_id}");
            return $stmt->fetchColumn();
        }
        
        public function countNewsByTag($tag)
        {
            $stmt = $this->pdo->query("SELECT COUNT(id) AS count FROM news WHERE tag_set LIKE '%{$tag}%'");
            return $stmt->fetchColumn();
        }
        
        public function findAllTags() {
            $tagSetList = [];
            $stmt = $this->pdo->query("SELECT tag_set FROM news");
            
            while ($res = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $tagSetList[] = $res['tag_set'];
            }
            
            // echo '<pre>';print_r($tagSetList);echo '</pre>';echo 'end';
            
            return $tagSetList;
        }
        
        /**
         * Possible array keys names
         * Array @params = [
         *      'dfrom' => @value,
         *      'dto' => @value,
         *      'tag_set' => @array,
         *      'category_set' => @array
         * ]
         */
        
        public function filter($count, $offset, Array $params = []) {
            $newsList = [];
            $query = "SELECT id, title, category_id, tag_set, created FROM news";
            $limit = " LIMIT {$offset},{$count}";
            
            if (empty($params)) {
                $query .= $limit;
            }
            else {
                $filter = " WHERE ";
                extract($params);
                
                if (isset($dfrom)) {
                    $filter .= (isset($dto) ? "(" : "") . "created > {$dfrom}";
                }
                
                if (isset($dto)) {
                    if (isset($dfrom)) {
                        $filter .= " AND ";
                    }
                    
                    $filter .= "created < {$dto}" . (isset($dfrom) ? ")" : "");
                }
                
                if (isset($tag_set)) {
                    if (isset($dfrom) || isset($dto)) {
                        $filter .= " AND ";
                    }
                    
                    $filter .= "(";
                    $length = sizeof($tag_set);
                    for ($i = 0; $i < $length; $i++ ) {
                        $filter .= "tag_set LIKE '%" . $tag_set[$i] . "%'";
                        if ($i !== ($length - 1)) {
                            $filter .= " OR ";
                        }
                    }
                    $filter .= ")";
                }
                
                if (isset($category_set)) {
                    if (isset($dfrom) || isset($dto) || isset($tag_set)) {
                        $filter .= " AND ";
                    }
                    
                    $filter .= "(";
                    $length = sizeof($category_set);
                    for ($i = 0; $i < $length; $i++ ) {
                        $filter .= "category_id=" . $category_set[$i];
                        if ($i !== ($length - 1)) {
                            $filter .= " OR ";
                        }
                    }
                    $filter .= ")";
                }
                
                $query .= $filter . $limit;
            }
            
            // return $query;
            
            $stmt = $this->pdo->query($query);
            
            while ($res = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $news = new News();
                $news->id = $res['id'];
                $news->title = $res['title'];
                $news->category_id = $res['category_id'];
                $news->tag_set = $res['tag_set'];
                $news->created = $res['created'];
                // echo '<pre>';print_r($bike);echo '</pre>';
                // echo 'end';
                $newsList[] = $news;
            }
            
            return $newsList;
        }
        
        public function filterCount(Array $params = []) {
            $newsList = [];
            $query = "SELECT COUNT(id) FROM news";
            
            if (empty($params)) {
                $stmt = $this->pdo->query($query);
                return $stmt->fetchColumn();
            }
            
            $filter = " WHERE ";
            extract($params);
            
            if (isset($dfrom)) {
                $filter .= (isset($dto) ? "(" : "") . "created > {$dfrom}";
            }
            
            if (isset($dto)) {
                if (isset($dfrom)) {
                    $filter .= " AND ";
                }
                
                $filter .= "created < {$dto}" . (isset($dfrom) ? ")" : "");
            }
            
            if (isset($tag_set)) {
                if (isset($dfrom) || isset($dto)) {
                    $filter .= " AND ";
                }
                
                $filter .= "(";
                $length = sizeof($tag_set);
                for ($i = 0; $i < $length; $i++ ) {
                    $filter .= "tag_set LIKE '%" . $tag_set[$i] . "%'";
                    if ($i !== ($length - 1)) {
                        $filter .= " OR ";
                    }
                }
                $filter .= ")";
            }
            
            if (isset($category_set)) {
                if (isset($dfrom) || isset($dto) || isset($tag_set)) {
                    $filter .= " AND ";
                }
                
                $filter .= "(";
                $length = sizeof($category_set);
                for ($i = 0; $i < $length; $i++ ) {
                    $filter .= "category_id=" . $category_set[$i];
                    if ($i !== ($length - 1)) {
                        $filter .= " OR ";
                    }
                }
                $filter .= ")";
            }
            
            $query .= $filter;
            $stmt = $this->pdo->query($query);
            
            return $stmt->fetchColumn();
        }
    }
?>