<?php

it('shows the site in Arabic by default', function () {
    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('<html lang="ar" dir="rtl">', false)
        ->assertSee('ابدأ كُنّاشتك')
        ->assertDontSee('Start your notebook');
});

it('translates the page content after switching to English', function () {
    $this->get(route('language.switch', 'en'));

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSee('<html lang="en" dir="ltr">', false)
        ->assertSee('Start your notebook')
        ->assertSee('Discover quotes')
        ->assertDontSee('ابدأ كُنّاشتك');
});

it('shows error pages in the language the visitor chose', function () {
    $response = $this->withSession(['locale' => 'en'])->get(route('quotes.show', 999));

    $response->assertNotFound()->assertSee('Page not found');
});
