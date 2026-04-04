<?php

declare(strict_types=1);

/**
 * Public front controller routes (admin is handled by admin/index.php).
 * More specific paths must appear before generic patterns.
 */
return [
    ['GET', '/', 'HomeController@index'],
    ['GET', '/listings', 'PropertyController@index'],
    ['GET', '/listings/{slug}', 'PropertyController@show'],
    ['GET', '/classifieds', 'GuideController@classifieds'],
    ['POST', '/classifieds', 'GuideController@classifiedsStore'],
    ['GET', '/vacancies', 'GuideController@vacancies'],
    ['POST', '/vacancies', 'GuideController@vacanciesStore'],
    ['GET', '/hotels', 'GuideController@hotels'],
    ['GET', '/restaurants', 'GuideController@restaurants'],
    ['GET', '/sights', 'GuideController@sights'],
    ['GET', '/events', 'GuideController@events'],
    ['GET', '/transport', 'GuideController@transport'],
    ['GET', '/banks', 'GuideController@banks'],
    ['GET', '/beauty', 'GuideController@beauty'],
    ['GET', '/kobuleti', 'PageController@kobuleti'],
    ['GET', '/contact', 'PageController@contact'],
    ['POST', '/contact', 'PageController@sendContact'],
    ['GET', '/register', 'AuthController@registerForm'],
    ['POST', '/register', 'AuthController@register'],
    ['GET', '/login', 'AuthController@loginForm'],
    ['POST', '/login', 'AuthController@login'],
    ['GET', '/logout', 'AuthController@logout'],
    ['GET', '/lang/{code}', 'LanguageController@setLang'],

    ['GET', '/my/dashboard', 'UserController@dashboard'],
    ['GET', '/my/listings/create', 'UserController@createForm'],
    ['POST', '/my/listings/create', 'UserController@create'],
    ['POST', '/my/listings/{id}/sold', 'UserController@markSold'],
    ['POST', '/my/listings/{id}/archive', 'UserController@archive'],
    ['GET', '/my/listings/{id}/edit', 'UserController@editForm'],
    ['POST', '/my/listings/{id}/edit', 'UserController@update'],
    ['POST', '/my/listings/{id}/delete', 'UserController@delete'],
    ['GET', '/my/listings', 'UserController@listings'],
    ['GET', '/my/profile', 'UserController@profileForm'],
    ['POST', '/my/profile', 'UserController@updateProfile'],
];
