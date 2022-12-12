<?php
    function redirect( $url, $statusCode = 303, $exit = true ) {
        header('Location: ' . $url, true, $statusCode );
        if ( $exit ) { die(); }
    }

    function dump($text) {
        throw new ErrorException($text);
    }