<?php

return function ($data, $angler) {
    $data[0] = $data[0] * $data[0];
    return $data;
};