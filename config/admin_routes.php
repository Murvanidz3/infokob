<?php

declare(strict_types=1);

/**
 * Admin panel routes (matched against path after /admin prefix).
 * Specific routes before parameterized ones.
 */
return [
    ['GET', '/', 'AdminController@dashboard'],
    ['GET', '/dashboard', 'AdminController@dashboard'],
    ['GET', '/properties', 'AdminController@propertyIndex'],
    ['GET', '/properties/{id}', 'AdminController@propertyShow'],
    ['POST', '/properties/{id}/approve', 'AdminController@propertyApprove'],
    ['POST', '/properties/{id}/reject', 'AdminController@propertyReject'],
    ['POST', '/properties/{id}/feature', 'AdminController@propertyToggleFeature'],
    ['GET', '/users', 'AdminController@users'],
    ['POST', '/users/{id}/active', 'AdminController@userToggleActive'],
    ['GET', '/settings', 'AdminController@settingsForm'],
    ['POST', '/settings', 'AdminController@settingsSave'],
];
