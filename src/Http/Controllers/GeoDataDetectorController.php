<?php

namespace FriendsOfBotble\GeoDataDetector\Http\Controllers;

use Botble\Base\Facades\AdminHelper;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Helper;
use Botble\Base\Supports\Language;
use Botble\Language\Facades\Language as LanguageFacade;
use Botble\Setting\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Throwable;

class GeoDataDetectorController extends SettingController
{
    public function detect(): BaseHttpResponse
    {
        if (AdminHelper::isInAdmin() || $this->isRequestFromBot()) {
            return $this->httpResponse();
        }

        $apiKey = setting('fob_geo_data_detector_ipdata_api_key');

        if (! $apiKey) {
            return $this->httpResponse();
        }

        $ip = request()->ip();

        $url = "https://api.ipdata.co/{$ip}?api-key={$apiKey}";

        try {
            $data = [];

            if (session()->has('fob_geo_data')) {
                $data = session('fob_geo_data', []);
            }

            if (! $data) {
                $data = Http::withoutVerifying()->timeout(5)->get($url)->json();
            }

            session(['fob_geo_data' => $data]);

            $responseData = [
                'detected' => false,
                'currency' => null,
                'language' => null,
            ];

            if (setting('fob_geo_data_currency_detector_enabled')) {
                $currencyCode = $data['currency']['code'] ?? null;

                if ($currencyCode && function_exists('cms_currency') && ! session('currency')) {
                    session(['currency' => $currencyCode]);
                }

                $responseData['detected'] = true;

                $responseData['currency'] = $currencyCode;
            }

            if (setting('fob_geo_data_language_detector_enabled')) {
                $languageCode = $data['languages'][0]['code'] ?? null;

                if (
                    $languageCode
                    && ! session('language')
                    && in_array($languageCode, array_keys(Language::getAvailableLocales()))
                ) {
                    session(['language' => $languageCode]);
                }

                $responseData['detected'] = true;

                $responseData['language'] = $languageCode;

                if (is_plugin_active('language')) {
                    session()->reflash();

                    $redirection = LanguageFacade::getLocalizedURL($languageCode, URL::previous(), [], false);
                    $responseData['next_url'] = $redirection;
                }
            }

            return $this
                ->httpResponse()
                ->setData($responseData);
        } catch (Throwable $exception) {
            BaseHelper::logError($exception);
        }

        return $this->httpResponse();
    }

    protected function isRequestFromBot(): bool
    {
        $ignoredBots = config('core.base.general.error_reporting.ignored_bots', []);
        $agent = strtolower(request()->userAgent());

        if (empty($agent)) {
            return false;
        }

        foreach ($ignoredBots as $bot) {
            if (str_contains($agent, $bot)) {
                return true;
            }
        }

        return false;
    }
}
