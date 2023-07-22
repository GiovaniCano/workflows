<?php

namespace Tests\Feature\workflows;

interface FormTestInterface
{
    /**
     * Submit the workflow expecting to be invalid
     * @param array $data Request's body as an associative array
     * @param array $errors Array of expected error keys
     * @return void
     */
    public function assertInvalidSubmit(array $data, array $errors): void;
}