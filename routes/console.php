<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use DefStudio\Telegraph\Models\TelegraphBot;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/** @var TelegraphBot $telegraphBot */
Artisan::command('tester', function () {
    $telegraphBot = TelegraphBot::find(1);

    $telegraphBot->registerCommands([
        'start' => 'Привітання',
        'startquiz' => 'Почати тест на 5 мін',
    ])->send();
});
