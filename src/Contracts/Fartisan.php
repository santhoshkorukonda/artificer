<?php

namespace SanthoshKorukonda\Fartisan\Contracts;

use stdClass;

interface Fartisan
{
    /**
     * Send a new message using a text.
     *
     * @param  stdClass  $text
     * @return void
     */
    public function build(stdClass $schema);
}
