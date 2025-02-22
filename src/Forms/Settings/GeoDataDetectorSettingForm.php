<?php

namespace FriendsOfBotble\GeoDataDetector\Forms\Settings;

use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\TextField;
use FriendsOfBotble\GeoDataDetector\Http\Requests\Settings\GeoDataDetectorSettingRequest;
use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Setting\Forms\SettingForm;

class GeoDataDetectorSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/fob-geo-data-detector::fob-geo-data-detector.name'))
            ->setSectionDescription(trans('plugins/fob-geo-data-detector::fob-geo-data-detector.description'))
            ->setValidatorClass(GeoDataDetectorSettingRequest::class)
            ->add(
                'fob_geo_data_detector_enabled',
                OnOffCheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('plugins/fob-geo-data-detector::fob-geo-data-detector.enable'))
                    ->value($CurrencyDetectorEnabled = setting('fob_geo_data_detector_enabled', false))
            )
            ->addOpenCollapsible('fob_geo_data_detector_enabled', '1', $CurrencyDetectorEnabled == '1')
            ->add(
                'fob_geo_data_detector_ipdata_api_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/fob-geo-data-detector::fob-geo-data-detector.api_key'))
                    ->helperText(trans('plugins/fob-geo-data-detector::fob-geo-data-detector.api_key_helper'))
                    ->value(setting('fob_geo_data_detector_ipdata_api_key'))
            )
            ->add(
                'fob_geo_data_currency_detector_enabled',
                OnOffCheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('plugins/fob-geo-data-detector::fob-geo-data-detector.currency_detector_enabled'))
                    ->helperText(trans('plugins/fob-geo-data-detector::fob-geo-data-detector.currency_detector_enabled_helper'))
                    ->value(setting('fob_geo_data_currency_detector_enabled', false))
            )
            ->add(
                'fob_geo_data_language_detector_enabled',
                OnOffCheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('plugins/fob-geo-data-detector::fob-geo-data-detector.language_detector_enabled'))
                    ->helperText(trans('plugins/fob-geo-data-detector::fob-geo-data-detector.language_detector_enabled_helper'))
                    ->value(setting('fob_geo_data_language_detector_enabled', false))
            )
            ->addCloseCollapsible('fob_geo_data_detector_enabled', '1');
    }
}
