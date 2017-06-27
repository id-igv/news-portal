<?php
    namespace Controller;
    
    use \Library\Request;
    use \Library\Controller;
    use \Model\NewsManager;
    
    // controller for HOME PAGE
    class DefaultController extends Controller {
        
        public function indexAction(Request $request = null) {
            $view = lcfirst( str_replace(['\\', 'Controller'], '', get_class($this)));
            
            $data = [];
            $newsManager = new NewsManager();
            
            $newsManager->set_pdo($this->get_container()->get('pdo_connection'));
            
            // get last 4 news
            $lastNews = $newsManager->findLastNews(4);
            $data['lastNews'] = $lastNews;
            
            // get all cats
            $categories = $newsManager->findAllCategories();
            
            // get last 5 news from each cat
            foreach($categories as $cat) {
                $lastCatNews = $newsManager->findLastNews(5, $cat->id);
                
                $data['categories'][] = [
                    'category' => $cat,
                    'lastCatNews' => $lastCatNews
                    ];
            }
            
            // echo '<pre>';print_r($data);echo '</pre>';
            //     echo 'end'; die;
            
            // render
            $viewFile = ucfirst($view) . "Layout.phtml";
            return $this->render($viewFile, $data);
        }
    }
?>