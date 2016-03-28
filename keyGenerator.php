<?php

var_dump(
    preg_replace(
        '~(..)~',
        '\x$1',
        bin2hex(random_bytes(16))
    )
);