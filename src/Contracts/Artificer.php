<?php

namespace SanthoshKorukonda\Artificer\Contracts;

use stdClass;

interface Artificer
{
    /**
     * Send a new message using a text.
     *
     * @param  stdClass  $text
     * @return void
     */
    public function build(stdClass $schema);
}
