<?php

namespace FriendsOfBotble\GeoDataDetector\Providers;

use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Setting\PanelSections\SettingOthersPanelSection;

class GeoDataDetectorServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/fob-geo-data-detector')
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishTranslations()
            ->publishAssets()
            ->loadRoutes();

        $this->app->booted(function (): void {
            PanelSectionManager::beforeRendering(function (): void {
                PanelSectionManager::default()
                    ->registerItem(
                        SettingOthersPanelSection::class,
                        fn () => PanelSectionItem::make('geo-data-detector-settings')
                            ->setTitle(trans('plugins/fob-geo-data-detector::fob-geo-data-detector.name'))
                            ->withIcon('ti ti-world-pin')
                            ->withDescription(trans('plugins/fob-geo-data-detector::fob-geo-data-detector.description'))
                            ->withPriority(500)
                            ->withRoute('geo-data-detector.settings')
                    );
            });

            add_filter(THEME_FRONT_BODY, [$this, 'injectScript'], 15);
        });
    }

    public function injectScript(?string $html): string
    {
        return $html . '<script>
            (function() {
                const storedCurrency = localStorage.getItem("user_currency");
                const storedLanguage = localStorage.getItem("user_language");

                let url = "' . route('geo-data-detector.detect') . '";
                if (storedCurrency || storedLanguage) {
                    const params = new URLSearchParams();
                    if (storedCurrency) params.append("stored_currency", storedCurrency);
                    if (storedLanguage) params.append("stored_language", storedLanguage);
                    url += "?" + params.toString();
                }

                fetch(url, {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json"
                        }
                    })
                    .then(response => response.json())
                    .then(response => {
                        if (!response || response.error || !response.data) {
                            return;
                        }

                        let shouldReload = false;

                        if (response.data.detected) {
                            const newCurrency = response.data.currency || "USD";
                            const newLanguage = response.data.language || "en";

                            if (storedCurrency !== newCurrency) {
                                localStorage.setItem("user_currency", newCurrency);
                                shouldReload = true;
                            }
                            if (storedLanguage !== newLanguage) {
                                localStorage.setItem("user_language", newLanguage);
                                shouldReload = true;
                            }

                            if (response.data.next_url) {
                                window.location.href = response.data.next_url;
                                return;
                            }
                        }

                        if (shouldReload || response.data.session_restored) {
                            window.location.reload();
                        }
                    })
                    .catch(error => console.error("GeoDataDetector API Error: ", error));
            })();
        </script>';
    }
}
