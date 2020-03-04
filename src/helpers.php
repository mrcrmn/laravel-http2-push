<?php

if (! function_exists('preload')) {
    function preload($resource, $silent = true) {
        return resolve('http2push')->add($resource, $silent);
    }
}
