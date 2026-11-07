<?php

it('redirects health endpoint successfully', function () {
    $this->get('/up')->assertOk();
});
