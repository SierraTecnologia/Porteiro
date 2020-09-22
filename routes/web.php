<?php

Route::group(
    ['middleware' => ['web']], function () {
        Route::prefix('porteiro')->group(
            function () {
                Route::group(
                    ['as' => 'porteiro.'], function () {
                    }
                );
            }
        );
    }
);
