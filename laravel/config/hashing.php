<?php

return [

    'driver' => 'bcrypt',

    'bcrypt' => [
        'rounds' => 10,
    ],

    'argon' => [
        'memory' => 1024,
        'time' => 2,
        'threads' => 2,
    ],

];