<?php

namespace FriendsOfBotble\CurrencyDetector\Http\Controllers\Settings;

use FriendsOfBotble\CurrencyDetector\Forms\Settings\CurrencyDetectorSettingForm;
use FriendsOfBotble\CurrencyDetector\Http\Requests\Settings\CurrencyDetectorSettingRequest;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Http\Controllers\SettingController;

class CurrencyDetectorSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/fob-currency-detector::fob-currency-detector.name'));

        return CurrencyDetectorSettingForm::create()->renderForm();
    }

    public function update(CurrencyDetectorSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate($request->validated());
    }
}
