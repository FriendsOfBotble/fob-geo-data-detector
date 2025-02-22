<?php

namespace FriendsOfBotble\CurrencyDetector\Http\Requests\Settings;

use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;

class CurrencyDetectorSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'fob_currency_detector_enabled' => new OnOffRule(),
            'fob_currency_detector_ipdata_api_key' => ['required', 'string', 'size:56'],
        ];
    }

    public function attributes(): array
    {
        return [
            'fob_currency_detector_enabled' => trans('plugins/fob-currency-detector::fob-currency-detector.enable'),
            'fob_currency_detector_ipdata_api_key' => trans('plugins/fob-currency-detector::fob-currency-detector.api_key'),
        ];
    }
}
