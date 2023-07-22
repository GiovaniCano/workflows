<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;

use App\Services\AutoRefreshingDropBoxTokenService;
use Spatie\FlysystemDropbox\DropboxAdapter;
use Spatie\Dropbox\Client as DropboxClient;

class DropboxServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Storage::extend('dropbox', function (Application $app, array $config) {
            $token = new AutoRefreshingDropBoxTokenService;
            
            $adapter = new DropboxAdapter(new DropboxClient(
                $token->getToken($config['appKey'], $config['appSecret'], $config['refreshToken'])
            ));

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
    }
}
