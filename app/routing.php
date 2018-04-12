<?php
/**
 * This file hold all routes definitions.
 *
 * PHP version 7
 *
 * @author   WCS <contact@wildcodeschool.fr>
 *
 * @link     https://github.com/WildCodeSchool/simple-mvc
 */


$routes = [

    'Homepage' => [
        ['index', '/', 'GET'],
    ],

    'Category' => [ // Controller
        ['list', '/admin/category', 'GET'], // action, url, method
    ],

    'Event' => [
        ['listEvent', '/event', 'GET'],
        ['addEvent', '/admin/addEvent', ['GET', 'POST']],
      ],

    'Recipe' => [ // Controller
        ['listRecipe', '/recipe', 'GET'], // action, url, method

    ],
];
