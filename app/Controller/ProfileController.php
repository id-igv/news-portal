<?php
    namespace Controller;
    
    use \Library\Request;
    use \Library\Controller;
    use \Library\Session;
    use \Model\ProfileManager;
    use \Model\Form\RegistrationForm;
    use \Model\Form\LoginForm;
    
    class ProfileController extends Controller {
        
        public function indexAction() {
            
            $view = lcfirst( str_replace(['\\', 'Controller'], '', get_class($this)));
            $viewFile = ucfirst($view) . "Layout.phtml";
            
            /* 
                -avatar => steam
                -nickname => steam
                -steamid => db
                tradelink  !if exists => db
                -balance/withdrawable balanca => db
                claim bonus btn => expiration time from db
                redeem code field+btn
            */
            
            if (!Session::has('logedin')) {
                return $this->render($viewFile);
                // $this->loginAction();
            }
            
            // get profile data
            $steamid = Session::get('steamid');
            $url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" . self::$_STEAMAPI . "&steamids={$steamid}";
            $json_object= file_get_contents($url);
            $json_decoded = json_decode($json_object);
            
            // set profile data
            $userData = (array) $json_decoded->response->players[0];
            
            $profile = new ProfileManager();
            $profile->set_pdo($this->get_container()->get('pdo_connection'));
            $userAdditionalData = $profile->exists($steamid, true);
            
            $userData = array_merge($userData, (array)$userAdditionalData);
            return $this->render($viewFile, $userData);
        }
        
        public function registerAction() {
            $router = $this->get_container()->get('router');
            
            // get user data
            $regForm = new RegistrationForm($this->get_container()->get('request'));
            // procces them
            if (!$regForm->isValid()) {
                Session::setFlash('Incorrect form data');
                $router->redirect('/');
            }
            
            $profileManager = new ProfileManager();
            $profileManager->set_pdo($this->get_container()->get('pdo_connection'));
            // add to db
            if (!$profileManager->addUserToDB($regForm)) {
                Session::setFlash('Email has been already used');
                $router->redirect('/');
            }
            
            // redirect to login form
            Session::setFlash('Registration succed! Try to log in');
            $router->redirect('/');
        }
        
        public function loginAction() {
            $router = $this->get_container()->get('router');
            
            $profileManager = new ProfileManager();
            $profileManager->set_pdo($this->get_container()->get('pdo_connection'));
            $loginForm = new LoginForm($this->get_container()->get('request'));
            
            if (!$loginForm->isValid()) {
                Session::setFlash('Fill in all fields');
                $router->redirect('/');
            }
            
            $user = null;
            if (!$user = $profileManager->exists($loginForm)) {
                Session::setFlash('Incorrect login data');
                $router->redirect('/');
            }
            
            // print_r(serialize((array)$user));
            // die;
            // setup session
            Session::set('logedin', $user->email);
            Session::set('username', $user->username);
            Session::set($profileManager->getRole($user->role_id), '1');
            
            // redirect to profile page
            $router->redirect('/');
        }
        
        public function logoutAction() {
            
            Session::destroy();
            $this->get_container()->get('router')->redirect('/');
        }
        
        public function dailyBonusAction() {
            
            // probably better to load this value from some config. file
            $secondsInDay = 3600 *24;
            $bonusAmount = 200;
            
            
            // 1 - check last claim time
            // 2 - if ok => increase balance on amount of daily bonus
            
            $profile = new ProfileManager();
            $profile->set_pdo($this->get_container()->get('pdo_connection'));
            
            $userData = $profile->exists(Session::get('steamid'), true);
            if ($userData->bonus_last_claim == 0 || (time() - $userData->bonus_last_claim) > $secondsInDay) { // checking for claim block expiration time
                $profile->increaseBalance($bonusAmount);
            }
        }
    }
?>