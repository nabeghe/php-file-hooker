<?php

return function ($data, $angler) {
    throw new Exception($data['message']);
};