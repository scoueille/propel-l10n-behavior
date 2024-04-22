
/**
 * Sets the locale for translations
 *
 * @param string $locale Locale to use for the translation, e.g. 'fr_FR'
 *
 * @return $this|<?php echo $objectClassName ?> The current object (for fluent API support)
 */
public function set<?php echo $localeColumnName ?>($locale)
{
    $this->currentLocale = PropelL10n::normalize($locale);

    return $this;
}
