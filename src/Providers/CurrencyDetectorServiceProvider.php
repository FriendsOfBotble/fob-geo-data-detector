<?php

namespace FriendsOfBotble\CurrencyDetector\Providers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\Helper;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Setting\PanelSections\SettingOthersPanelSection;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Http;
use Throwable;

class CurrencyDetectorServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/fob-currency-detector')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishTranslations()
            ->publishAssets()
            ->loadRoutes();

        $this->app->booted(function () {
            $this->app['events']->listen(RouteMatched::class, function () {
                if (! function_exists('cms_currency')) {
                    return;
                }

                if (! session('currency')) {
                    $currencyCode = $this->currencyByIP();

                    session(['currency' => $currencyCode]);
                }
            });

            PanelSectionManager::beforeRendering(function (): void {
                PanelSectionManager::default()
                    ->registerItem(
                        SettingOthersPanelSection::class,
                        fn () => PanelSectionItem::make('currency-detector-settings')
                            ->setTitle(trans('plugins/fob-currency-detector::fob-currency-detector.name'))
                            ->withIcon('ti ti-currency')
                            ->withDescription(trans('plugins/fob-currency-detector::fob-currency-detector.description'))
                            ->withPriority(500)
                            ->withRoute('currency-detector.settings')
                    );
            });

        });
    }

    protected function currencyByIP()
    {
        $apiKey = setting('fob_currency_detector_ipdata_api_key');

        if (! $apiKey) {
            return null;
        }

        $ip = Helper::getIpFromThirdParty();


        $url = "https://api.ipdata.co/{$ip}?api-key={$apiKey}";

        try {
            return Http::withoutVerifying()->timeout(5)->get($url)->json('currency.code');
        } catch (Throwable $exception) {
            BaseHelper::logError($exception);
        }

        return null;
    }
}
