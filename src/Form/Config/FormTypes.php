<?php

namespace App\Form\Config;

use Symfony\Component\Form\Extension\Core\Type as Type;

enum FormTypes: string
{
    case BIRTHDAY = Type\BirthdayType::class;
    case CHECKBOX = Type\CheckboxType::class;
    case CHOICE = Type\ChoiceType::class;
    case COLLECTION = Type\CollectionType::class;
    case COUNTRY = Type\CountryType::class;
    case DATE_INTERVAL = Type\DateIntervalType::class;
    case DATE = Type\DateType::class;
    case DATE_TIME = Type\DateTimeType::class;
    case EMAIL = Type\EmailType::class;
    case HIDDEN = Type\HiddenType::class;
    case INTEGER = Type\IntegerType::class;
    case LANGUAGE = Type\LanguageType::class;
    case LOCALE = Type\LocaleType::class;
    case MONEY = Type\MoneyType::class;
    case NUMBER = Type\NumberType::class;
    case PASSWORD = Type\PasswordType::class;
    case PERCENT = Type\PercentType::class;
    case RADIO = Type\RadioType::class;
    case RANGE = Type\RangeType::class;
    case REPEATED = Type\RepeatedType::class;
    case SEARCH = Type\SearchType::class;
    case TEXTAREA = Type\TextareaType::class;
    case TEXT = Type\TextType::class;
    case TIME = Type\TimeType::class;
    case TIMEZONE = Type\TimezoneType::class;
    case URL = Type\UrlType::class;
    case FILE = Type\FileType::class;
    case BUTTON = Type\ButtonType::class;
    case SUBMIT = Type\SubmitType::class;
    case RESET = Type\ResetType::class;
    case CURRENCY = Type\CurrencyType::class;
    case TEL = Type\TelType::class;
    case COLOR = Type\ColorType::class;
    case WEEK = Type\WeekType::class;
}
