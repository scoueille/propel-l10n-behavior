/**
* Remove the translation for a given locale
*
* @param string $locale Locale to use for the translation, e.g. 'fr_FR'
* @param ConnectionInterface|null $con an optional connection object
*
* @return $this|<?php echo $objectClassName ?> The current object (for fluent API support)
*/
public function removeTranslation(string $locale = null, ?ConnectionInterface $con = null)
{
if ($locale === null) {
$locale = PropelL10n::getLocale();
}
if (!$this->isNew()) {
<?php echo $i18nQueryName ?>::create()
->filterByPrimaryKey(array($this->getPrimaryKey(), $locale))
->delete($con);
}
if (isset($this->currentTranslations[$locale])) {
unset($this->currentTranslations[$locale]);
}
foreach ($this-><?php echo $i18nCollection ?> as $key => $translation) {
if ($translation->get<?php echo $localeColumnName ?>() == $locale) {
unset($this-><?php echo $i18nCollection ?>[$key]);
break;
}
}

return $this;
}
