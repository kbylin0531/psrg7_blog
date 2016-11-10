#!/usr/bin/env php
<?php
require __DIR__.'/../../../Sharin/console.inc';

$console = new Shacon([
    'params'    => 'a:b/i:c'
]);

\Sharin\dumpout(

    $console->parseInput()


);