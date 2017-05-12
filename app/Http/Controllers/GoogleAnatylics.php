<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Analytics;

class GoogleAnatylics extends Controller
{
    function viewGA()
    {

        $client = new \Google_Client();
        $client->setApplicationName('Web FEEDY GA');
        $client->setClientId('1067702576881-dkootmgekevqm96sluet6jmu8odav4mi.apps.googleusercontent.com');
        $client->setClientSecret('Ik-cSAJuIAYbcekszghuaYi5');
        $client->setRedirectUri(url()->current());
        $client->setDeveloperKey('AIzaSyAsUD10QNTdBflKImc8a5iv-WU4hqKzVuw'); // API key
        if (session()->has('LAST_ACTIVITY') && (time() - session()->get('LAST_ACTIVITY') > 3600)) {
            // last request was more than 30 minutes ago = 1800
            //ạ session()->forget('token');
            session()->forget('LAST_ACTIVITY');     // unset $_SESSION variable for the run-time
        }
        if (request()->has('logout')) {
            //session()->forget('token');
        }
        $client->setScopes(array(
            'https://www.googleapis.com/auth/analytics.readonly'
        ));
        if (request()->has('code') && !session()->has('token')) { // we received the positive auth callback, get the token and store it in session
            $client->authenticate(request()->get('code'));
            session()->put('token', $client->getAccessToken());
            session()->put('LAST_ACTIVITY', time()); // update last activity time stamp
        }
        if (!$client->getAccessToken() && !session()->has('token')) {
            $authUrl = $client->createAuthUrl();
            return redirect($authUrl);
        } else {
            $token = json_decode(session()->get('token'));
            $access_token = $token->access_token;
            $url = "https://www.googleapis.com/analytics/v3/data/ga?ids=ga%3A124276340&start-date=2015-09-01&end-date=2018-11-05&metrics=ga%3Apageviews&dimensions=ga:pagePath&sort=-ga%3Apageviews&filters=ga:pagePath==/bai-viet/meo-vat/9-meo-nho-nhung-co-vo-giup-mon-nuong-cua-ban-ngon-bat-bai-lai-khong-lo-doc-hai_6275.html,ga:pagePath==/chi-tiet-cong-thuc/viet-nam/no-rung-ron-voi-bun-thai-chua-cay-kich-thich-vi-giac-ngon-me-man_6291.html,ga:pagePath==/chi-tiet-cong-thuc/cac-mon-chau-au-khac/an-hoai-khong-ngan-voi-mon-com-tron-sot-thit-bo-morocco-ngon-tuyet-cu-meo_6283.html,ga:pagePath==/bai-viet/tin-tuc/loai-qua-duoc-the-gioi-ca-ngoi-la-than-duoc-chua-bach-benh-viet-nam-co-san-nhung-it-nguoi-biet_6272.html&max-results=45&access_token=" . $access_token;
            echo $url ;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);
            curl_close($ch);
            dd(json_decode($output));
        }

    }
}
