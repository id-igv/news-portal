<?php
    namespace Controller;
    
    use \Library\Request;
    use \Library\Controller;
    use \Model\NewsManager;
    use \Library\Response;
    use \Library\JsonResponse;
    
    class NewsController extends Controller {
        
        public function indexAction() {
            $view = str_replace(['\\', 'Controller'], '', get_class($this));
            
            $data = [];
            $container = $this->get_container();
            $newsManager = new NewsManager();
            
            $newsManager->set_pdo($container->get('pdo_connection'));
            $category_id = $container
                                ->get('router')->getCurrentRoute()
                                ->get_requestedParams()['id'];
            
            $data['news'] = $newsManager->findNewsByID($category_id);
            
            $viewFile = "{$view}Layout.phtml";
            return $this->render($viewFile, $data);
        }
    }
?>