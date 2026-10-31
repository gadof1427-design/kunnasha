<?php

it('shows the branded page when a page does not exist', function () {
    $response = $this->get('/this-page-does-not-exist');

    $response->assertNotFound()->assertSee('الصفحة غير موجودة');
});
