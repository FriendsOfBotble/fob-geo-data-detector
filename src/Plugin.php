<?php

namespace FriendsOfBotble\GeoDataDetector;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Facades\Setting;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Setting::delete([
            'fob_geo_data_detector_enabled',
            'fob_geo_data_detector_ipdata_api_key',
            'fob_geo_data_currency_detector_enabled',
            'fob_geo_data_language_detector_enabled',
        ]);
    }
}
