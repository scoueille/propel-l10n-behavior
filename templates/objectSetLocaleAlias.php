/**
* Sets the locale for translations.
* Alias for setLocale(), for BC purpose.
*
* @param string $locale Locale to use for the translation, e.g. 'fr_FR'
*
* @return $this|<?php echo $objectClassName ?> The current object (for fluent API support)
*/
public function set<?php echo $alias ?>($locale)
{
return $this->set<?= $localeColumnName ?>($locale);
}
