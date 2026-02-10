<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['web', 'auth'],
    'prefix'     => \Helper::getSubdirectory(),
    'namespace'  => 'Modules\AdamTicketAgingColors\Http\Controllers'
], function () {
    // Use mailbox settings namespace to avoid conflict with core /mailbox/{id}/{folder_id} route.
    Route::get('/mailbox/settings/{id}/ticket-aging-colors', 'MailboxSettingsController@edit')
        ->name('adamticketagingcolors.mailboxes.settings');

    Route::post('/mailbox/settings/{id}/ticket-aging-colors', 'MailboxSettingsController@save')
        ->name('adamticketagingcolors.mailboxes.settings.save');
});
