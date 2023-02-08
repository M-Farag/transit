<?php

namespace Concept\Transit;

class Transit {

    var $msg;
    public function __construct(string $msg)
    {
        $this->msg = $msg;
    }


    /**
     * Call
     * Perform parallel API calls using CURL Multi
     *
     * @param array $request
     * @return array $results
     */
    public function call(array $request) :array
    {

        return [];
    }

}