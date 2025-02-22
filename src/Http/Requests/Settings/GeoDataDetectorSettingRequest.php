<?php

namespace FriendsOfBotble\GeoDataDetector\Http\Requests\Settings;

use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;

class GeoDataDetectorSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'fob_geo_data_detector_enabled' => new OnOffRule(),
            'fob_geo_data_detector_ipdata_api_key' => ['required', 'string', 'size:56'],
            'fob_geo_data_currency_detector_enabled' => new OnOffRule(),
            'fob_geo_data_language_detector_enabled' => new OnOffRule(),
        ];
    }

    public function attributes(): array
    {
        return [
            'fob_geo_data_detector_enabled' => trans('plugins/fob-geo-data-detector::fob-geo-data-detector.enable'),
            'fob_geo_data_detector_ipdata_api_key' => trans('plugins/fob-geo-data-detector::fob-geo-data-detector.api_key'),
            'fob_geo_data_currency_detector_enabled' => trans('plugins/fob-geo-data-detector::fob-geo-data-detector.currency_detector_enabled'),
            'fob_geo_data_language_detector_enabled' => trans('plugins/fob-geo-data-detector::fob-geo-data-detector.language_detector_enabled'),
        ];
    }
}
