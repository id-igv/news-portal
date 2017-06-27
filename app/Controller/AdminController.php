<?php
    namespace Controller;
    
    use \Library\Request;
    use \Library\Controller;
    use \Model\NewsManager;
    use \Library\Response;
    use \Library\JsonResponse;
    
    class AdminController extends Controller {
        
        public function indexAction(Request $request = null) {
            if (!\Library\Session::has('admin')) {
                $this->get_container()->get('router')->redirect('/');
            }
            $view = str_replace(['\\', 'Controller'], '', get_class($this));
            
            $viewFile = "{$view}Layout.phtml";
            return $this->render($viewFile);
        }
        
        public function categoriesAction() {
            if (!\Library\Session::has('admin')) {
                $this->get_container()->get('router')->redirect('/');
            }
            $view = str_replace(['\\', 'Controller'], '', get_class($this));
            $viewFile = "{$view}Layout.phtml";
            $controlLayout = "{$view}Layout.categories.phtml";
            $container = $this->get_container();
            $data = [];
            
            $newsManager = new NewsManager();
            $newsManager->set_pdo($container->get('pdo_connection'));
            $categories = $newsManager->findAllCategories();
            
            $data['controlLayout'] = $controlLayout;
            $data['categories'] = $categories;
            
            return $this->render($viewFile, $data);
        }
        
        public function upCategoryAction() {
            $container = $this->get_container();
            $id = $container->get('request')->post('id');
            $name = $container->get('request')->post('name');
            $responseData = [];
            $status = 400;
            
            
            if (!is_null($id) && !is_null($name)) {
                $newsManager = new NewsManager();
                $newsManager->set_pdo($container->get('pdo_connection'));
                
                $updated = $newsManager->updateCategory($id, $name);
                $responseData['updated'] = (array) $newsManager->findCategoryByID($id);
                $status = $updated ? 200 : 400;
                
                $responseData = json_encode($responseData);
            }
            
            $response = new JsonResponse($responseData, $status);
            $response->send();
            return $response;
        }
        
        public function addCategoryAction() {
            $container = $this->get_container();
            $name = $container->get('request')->post('name');
            $responseData = [];
            $status = 400;
            
            
            if (!is_null($name)) {
                $newsManager = new NewsManager();
                $newsManager->set_pdo($container->get('pdo_connection'));
                
                $updated = $newsManager->addCategory($name);
                $responseData['updated'] = (array) $newsManager->findCategoryByName($name);
                $status = 200;
                
                $responseData = json_encode($responseData);
            }
            
            $response = new JsonResponse($responseData, $status);
            $response->send();
            return $response;
        }
        
        public function rmCategoryAction() {
            $container = $this->get_container();
            $id = $container->get('request')->post('id');
            // $name = $container->get('request')->post('name');
            $responseData = [];
            $status = 400;
            
            
            if (!is_null($id)) {
                $newsManager = new NewsManager();
                $newsManager->set_pdo($container->get('pdo_connection'));
                
                $updated = $newsManager->rmCategory($id);
                $responseData['updated'] = $id;
                $status = 200;
                
                $responseData = json_encode($responseData);
            }
            
            $response = new JsonResponse($responseData, $status);
            $response->send();
            return $response;
        }
        
        public function newsAction() {
            if (!\Library\Session::has('admin')) {
                $this->get_container()->get('router')->redirect('/');
            }
            
            $view = str_replace(['\\', 'Controller'], '', get_class($this));
            $viewFile = "{$view}Layout.phtml";
            $controlLayout = "{$view}Layout.news.phtml";
            $container = $this->get_container();
            $data = [];
            
            $newsManager = new NewsManager();
            $newsManager->set_pdo($container->get('pdo_connection'));
            $categories = $newsManager->findAllNews();
            
            $data['controlLayout'] = $controlLayout;
            $data['newsList'] = $categories;
            
            return $this->render($viewFile, $data);
        }
        
        public function addNewsAction() {
            
        }
        
        public function rmNewsAction() {
            
        }
    }
?>