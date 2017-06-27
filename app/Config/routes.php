<?php
    namespace Library;
    
    return array (
        new Route('default', '/', 'Default'),
        new Route('profile', '/profile', 'Profile'),
        new Route('registration', '/registration', 'Profile', 'register'),
        new Route('login', '/login', 'Profile', 'login'),
        new Route('logout', '/logout', 'Profile', 'logout'),
        new Route('news', '/news', 'News', 'filter'),
        new Route('newsinfo', '/news/{id}', 'News', 'index', array('id' => '[0-9]+')),
        new Route('category', '/category/{id}', 'Category', 'index', array('id' => '[0-9]+')),
        new Route('search', '/search', 'Search'),
        new Route('searchbar', '/search/bar', 'Search', 'tagList'),
        
        new Route('control', '/control', 'Admin'),
        new Route('control', '/control/categories', 'Admin', 'categories'),
        new Route('control', '/control/categories/update', 'Admin', 'upCategory'),
        new Route('control', '/control/categories/add', 'Admin', 'addCategory'),
        new Route('control', '/control/categories/rm', 'Admin', 'rmCategory'),
        new Route('control', '/control/news', 'Admin', 'news'),
        new Route('control', '/control/news/update', 'Admin', 'upNews'),
        new Route('control', '/control/news/add', 'Admin', 'addNews'),
        new Route('control', '/control/news/rm', 'Admin', 'rmNews'),
        
        new Route('error', '/error', 'Exception')
        );
?>