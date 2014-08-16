<?php namespace Clumsy\Locale;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;

class Locale {

    public function detect()
    {
        // TODO?: Check cookies
        // TODO?: Check session

        if (function_exists('geoip_country_code_by_name'))
        {
            if ($country = strtolower(geoip_country_code_by_name(Request::instance()->getClientIp())))
            {
                return $country;
            }
        }
    }

    public function codes($locale = false)
    {
        if (!$locale)
        {
            $locale = Config::get('app.locale');
        }

        $codes[] = $locale;

        $composite = $this->composite($locale);

        $codes[] = $composite;
        
        $codes[] = $composite . ".utf8";
        
        $languages = $this->languages();

        $language = $this->localeToLanguage($locale);
        
        $codes[] = isset($languages[$language]) ? $languages[$language] : '';

        return array_filter($codes);
    }

    public function composite($locale = false)
    {
        if (!$locale)
        {
            $locale = Config::get('app.locale');
        }

        $language = $this->localeToLanguage($locale);

        if ($language !== '')
        {
            return $language . '_' . strtoupper($locale);
        }
        
        return '';
    }

    public function localeToLanguage($locale)
    {
        $language_codes = array_keys($this->languages());

        $languages = array_merge(
            array_combine($language_codes, $language_codes),
            (array)Config::get('locale::locale_to_language')
        );

        return isset($languages[$locale]) ? $languages[$locale] : '';
    }

    public function all()
    {
        return Config::get('locale::locales');
    }

    public function languages()
    {   
        return Config::get('locale::languages');
    }
}