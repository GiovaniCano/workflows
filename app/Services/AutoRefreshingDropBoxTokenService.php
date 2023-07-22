<?php

namespace App\Services;

class AutoRefreshingDropBoxTokenService
{
    public function getToken(string $key, string $secret, string $refreshToken): string|false
    {
        try {
            // GuzzleHttp: https://docs.guzzlephp.org/en/stable/request-options.html#form-params
            $client = new \GuzzleHttp\Client();
            
            $res = $client->request("POST", "https://{$key}:{$secret}@api.dropbox.com/oauth2/token", [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                ]
            ]);

            // dd(json_decode($res->getBody(), TRUE));

            if ($res->getStatusCode() == 200) {
                return json_decode($res->getBody(), TRUE)['access_token'];
            } else {
                info(json_decode($res->getBody(), TRUE));
                return false;
            }
        } catch (\Exception $e) {
            info($e->getMessage());
            return false;
        }
    }
}

// API docs: https://www.dropbox.com/developers/documentation/http/documentation

// The following is like the request above, but it's in the docs
// Example: refresh token request:
// curl https://api.dropbox.com/oauth2/token \
//     -d grant_type=refresh_token \
//     -d refresh_token=<REFRESH_TOKEN> \
//     -d client_id=<APP_KEY> \
//     -d client_secret=<APP_SECRET>

# The important: https://github.com/spatie/flysystem-dropbox/issues/86
# How to get credentials: https://gist.github.com/phuze/755dd1f58fba6849fbf7478e77e2896a

# Apps: https://www.dropbox.com/account/connected_apps?oref=e

/*
1. To get DROPBOX_ACCESS_CODE
    https://www.dropbox.com/oauth2/authorize?client_id=<YOUR_APP_KEY>&response_type=code&token_access_type=offline
2. To get DROPBOX_REFRESH_TOKEN
    curl https://api.dropbox.com/oauth2/token -d code=<ACCESS_CODE> -d grant_type=authorization_code -u <APP_KEY>:<APP_SECRET>

   // App key & secret are in app console
*/