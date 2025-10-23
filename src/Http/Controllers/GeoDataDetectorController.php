<?php

namespace FriendsOfBotble\GeoDataDetector\Http\Controllers;

use Botble\Base\Facades\AdminHelper;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
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
            return $this->httpResponse()->setData(['data' => null]);
        }

        $apiKey = setting('fob_geo_data_detector_ipdata_api_key');

        if (! $apiKey) {
            return $this->httpResponse()->setData(['data' => null]);
        }

        $storedCurrency = request('stored_currency');
        $storedLanguage = request('stored_language');
        $sessionRestored = false;

        if ($storedCurrency && ! session('currency') && setting('fob_geo_data_currency_detector_enabled')) {
            if (function_exists('cms_currency')) {
                $currencyData = get_application_currency();
                if ($currencyData && isset($currencyData->currencies) && $currencyData->currencies) {
                    $availableCurrencies = $currencyData->currencies->pluck('title')->all();
                    if (in_array($storedCurrency, $availableCurrencies)) {
                        session(['currency' => $storedCurrency]);
                        $sessionRestored = true;
                    }
                }
            }
        }

        if ($storedLanguage && ! session('language') && setting('fob_geo_data_language_detector_enabled')) {
            if (in_array($storedLanguage, array_keys(Language::getAvailableLocales()))) {
                session(['language' => $storedLanguage]);
                $sessionRestored = true;
            }
        }

        $currencyEnabled = setting('fob_geo_data_currency_detector_enabled');
        $languageEnabled = setting('fob_geo_data_language_detector_enabled');

        $shouldReturnEarly = $sessionRestored && (
            ($currencyEnabled && $languageEnabled && session('currency') && session('language')) ||
            ($currencyEnabled && ! $languageEnabled && session('currency')) ||
            (! $currencyEnabled && $languageEnabled && session('language'))
        );

        if ($shouldReturnEarly) {
            return $this
                ->httpResponse()
                ->setData([
                    'detected' => false,
                    'session_restored' => true,
                    'currency' => session('currency'),
                    'language' => session('language'),
                ]);
        }

        $ip = request()->ip();

        $url = "https://api.ipdata.co/{$ip}?api-key={$apiKey}";

        try {
            $data = [];

            if (session()->has('fob_geo_data')) {
                $data = session('fob_geo_data', []);
            }

            if (! $data) {
                $response = Http::withoutVerifying()->timeout(5)->get($url);
                $data = $response->json();

                if (! $response->successful() || isset($data['error']) || ! is_array($data)) {
                    return $this->httpResponse()->setData(['data' => null]);
                }
            }

            session(['fob_geo_data' => $data]);

            $responseData = [
                'detected' => false,
                'currency' => null,
                'language' => null,
                'session_restored' => $sessionRestored,
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

        return $this->httpResponse()->setData(['data' => null]);
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
