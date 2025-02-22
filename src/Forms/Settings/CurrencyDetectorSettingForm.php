<?php

namespace FriendsOfBotble\CurrencyDetector\Forms\Settings;

use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\TextField;
use FriendsOfBotble\CurrencyDetector\Http\Requests\Settings\CurrencyDetectorSettingRequest;
use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Setting\Forms\SettingForm;

class CurrencyDetectorSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/fob-currency-detector::fob-currency-detector.name'))
            ->setSectionDescription(trans('plugins/fob-currency-detector::fob-currency-detector.description'))
            ->setValidatorClass(CurrencyDetectorSettingRequest::class)
            ->add(
                'fob_currency_detector_enabled',
                OnOffCheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('plugins/fob-currency-detector::fob-currency-detector.enable'))
                    ->value($CurrencyDetectorEnabled = setting('fob_currency_detector_enabled', false))
            )
            ->addOpenCollapsible('fob_currency_detector_enabled', '1', $CurrencyDetectorEnabled == '1')
            ->add(
                'fob_currency_detector_ipdata_api_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/fob-currency-detector::fob-currency-detector.api_key'))
                    ->helperText(trans('plugins/fob-currency-detector::fob-currency-detector.api_key_helper'))
                    ->value(setting('fob_currency_detector_ipdata_api_key'))
            )
            ->addCloseCollapsible('fob_currency_detector_enabled', '1');
    }
}
