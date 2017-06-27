<?php
    namespace Controller;
    
    use \Library\Request;
    use \Library\Controller;
    use \Model\NewsManager;
    use \Library\Pagination\Pagination;
    use \Library\Response;
    use \Library\JsonResponse;
    
    class SearchController extends Controller {
        const NEWS_PER_PAGE = 5;
        
        public function indexAction() {
            $data = []; // will send to render
            $params = []; // will be used as db search params
            $container = $this->get_container();
            $request = $container->get('request');
            $newsManager = new NewsManager();
            $newsManager->set_pdo($container->get('pdo_connection'));
            $view = str_replace(['\\', 'Controller'], '', get_class($this));
            
            $data['categories'] = $newsManager->findAllCategories();
            $data['tags'] = $this->tagPerCell($newsManager->findAllTags());
            
            $data['getParams'] = null;
            if ($request->isGet()) {
                if (!is_null($request->get('categories'))) {
                    $params['category_set'] = explode('|', $request->get('categories'));
                    $data['getParams'] .= "categories=" . $request->get('categories') . "&";
                    $data['params']['category_set'] = explode('|', $request->get('categories'));
                }
                
                if (!is_null($request->get('tags'))) {
                    $params['tag_set'] = explode('|', $request->get('tags'));
                    $data['getParams'] .= "tags=" . $request->get('tags') . "&";
                    $data['params']['tag_set'] = explode('|', $request->get('tags'));
                }
                
                // db contains dates in unix timestamps
                // strtotime: normal date -> unix timestamp
                if (!is_null($request->get('dfrom'))) {
                    $params['dfrom'] = strtotime($request->get('dfrom'));
                    $data['getParams'] .= "dfrom=" . $request->get('dfrom') . "&";
                    $data['params']['dfrom'] = $request->get('dfrom');
                }
                
                if (!is_null($request->get('dto'))) {
                    $params['dto'] = strtotime($request->get('dto'));
                    $data['getParams'] .="dto" . $request->get('dto') . "&";
                    $data['params']['dto'] = $request->get('dto');
                }
            }
            
            $page = $request->get('page');
            $page = is_null($page) ? 1 : $page;
            
            $newsCount = $newsManager->filterCount($params);
            $data['newsList'] = $newsManager->filter(self::NEWS_PER_PAGE, ($page - 1) * self::NEWS_PER_PAGE, $params);
            // echo count($data['newsList']); die;
            $pagination = new Pagination([
                'itemsCount' => $newsCount,
                'itemsPerPage' => self::NEWS_PER_PAGE,
                'currentPage' => $page
            ]);
            
            $data['pagination'] = $pagination;
            
            $viewFile = "{$view}Layout.phtml";
            return $this->render($viewFile, $data);
        }
        
        private function newsByTagAction() {
            $container = $this->get_container();
            $view = str_replace(['\\', 'Controller'], '', get_class($this));
            $requested = $container->get('request')->get('tag');

            $newsManager = new NewsManager();
            $newsManager->set_pdo($container->get('pdo_connection'));
            
            $page = $container->get('request')->get('page');
            $page = is_null($page) ? 1 : $page;
            // number of news in chosen category
            $newsCount = $newsManager->countNewsByTag($requested);
            $data['tag'] = $requested;
            $data['newsList'] = $newsManager->findNewsByTag($requested, 5, ($page - 1) * 5);
            
            $pagination = new Pagination([
                'itemsCount' => $newsCount,
                'itemsPerPage' => 5,
                'currentPage' => $page
            ]);
            
            $data['pagination'] = $pagination;
            
            $viewFile = "{$view}Layout.phtml";
            return $this->render($viewFile, $data);
        }
        
        /**
         * Used to answer on search by tag field request
         */
        public function tagListAction() {
            $requested = $this->get_container()->get('request')->post('searchtag');
            $responseData = [];
            $status = 400;
            
            if ($requested) {
                $newsManager = new NewsManager();
                $newsManager->set_pdo($this->get_container()->get('pdo_connection'));
                
                $tagSetList = $newsManager->findAllTags();
                $tags = $this->tagPerCell($tagSetList);
                
                foreach ($tags as $tag) {
                    if (mb_stripos($tag, $requested) > -1) {
                        $responseData[] = $tag;
                    }
                }
                
                $status = 200;
                if (empty($responseData)) {
                    $status = 404;
                }
                
                $responseData = json_encode($responseData);
            }
            
            $response = new JsonResponse($responseData, $status);
            $response->send();
            return $response;
        }
        
        /**
         * Recieves list of tag sets from all news in DB
         * Returns array where each cell contains only one tag
         * Used for search by tags
         */
        private function tagPerCell($tagSetList) {
            $res = implode(' ', $tagSetList);
            $res = explode(' ', $res);
            return array_unique($res);
            // echo '<pre>';print_r($res);echo '</pre>';echo 'end';
        }
    }
?>