<?php

namespace Concept\Transit;

class Transit {

    var $msg;
    public function __construct(string $msg)
    {
        $this->msg = $msg;
    }
}