<?php

namespace Concept\Transit\Validator;

class Validator {

    /**
     * Request data
     *
     * @var [array]
     */
    protected $data;

    /**
     * Init the validator object
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
}