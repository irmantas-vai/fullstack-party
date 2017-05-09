<?php
return [
    'displayErrorDetails' => true,
    'twig' => [
        'tplPpath' => realpath(dirname(__FILE__) . '/../templates'),
        'settings' => [
            //'cache' => realpath(dirname(__FILE__) . '/../cache/templates'),
        ],
    ],
    'github' => [
        'clientId' => '49de1f9976d9500786f5',
        'clientSecret' => 'dbdd68b4db340a2465455319297768f0f6afcc15',
    ],
    'issue' => [
        'issuesPerPage' => 4,
    ],
];
