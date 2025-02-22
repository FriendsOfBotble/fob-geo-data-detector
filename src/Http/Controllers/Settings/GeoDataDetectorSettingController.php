<?php

namespace FriendsOfBotble\GeoDataDetector\Http\Controllers\Settings;

use FriendsOfBotble\GeoDataDetector\Forms\Settings\GeoDataDetectorSettingForm;
use FriendsOfBotble\GeoDataDetector\Http\Requests\Settings\GeoDataDetectorSettingRequest;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Http\Controllers\SettingController;

class GeoDataDetectorSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/fob-geo-data-detector::fob-geo-data-detector.name'));

        return GeoDataDetectorSettingForm::create()->renderForm();
    }

    public function update(GeoDataDetectorSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate($request->validated());
    }
}
