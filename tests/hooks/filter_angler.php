<?php

return function ($data, $angler) {
    $data[0] = $angler;
    return $data;
};