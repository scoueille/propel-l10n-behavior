
<?php echo $comment ?>
<?php echo $functionStatement ?>

    return $this->getCurrentTranslation()->get<?php echo $columnPhpName ?>(<?php echo $params ?>);

<?php echo $column->getAccessorVisibility() ?> function get<?php echo $columnPhpName?>($locale = null<?php
if ($column->isLazyLoad()) {
 	echo ", ConnectionInterface \$con = null";
}
?>)
{
	$getTranslatedLocale = function($locale) <?php if ($column->isLazyLoad()) echo 'use ($con)';?> {
		$trans = $this->getTranslation($locale);
		return $trans->get<?php echo $columnPhpName ?>(<?php if ($column->isLazyLoad()) echo '$con';?>);
	};
	$workDownLanguageTag = function($locale) use($getTranslatedLocale) {
		// check if the locale has more than one subtag to work down
		if (strpos($locale, '-') === false) {
			return null;
		}

		// drop the last subtag
		$locale = implode('-', array_slice(explode('-', $locale), 0, -1));
		$value = $getTranslatedLocale($locale);
		if ($value === null) {
			$value = $workDownLanguageTag($locale);
		}
		return $value;
	};
	$value = null;
	if ($locale === null) {
		$locale = $this->get<?= $localeColumnName ?>();
	}
	if ($locale === null) {
		$locale = PropelL10n::getLocale();
	}

	// try default locale
	$value = $getTranslatedLocale($locale);

	if ($value === null) {
		// try dependency chain
		while (PropelL10n::hasDependency($locale) && $value === null) {
			$newLocale = PropelL10n::getDependency($locale);

			// if primary language of dependency is different than current, work down language-tag-chain
			if (\Locale::getPrimaryLanguage($newLocale) != \Locale::getPrimaryLanguage($locale)) {
				$value = $workDownLanguageTag($locale);
			}

			// proceed with dependency if still nothing is found
			if ($value === null) {
				$locale = $newLocale;
				$value = $getTranslatedLocale($locale);
			}
		}

		// work down language-tag-chain
		if ($value === null) {
			$value = $workDownLanguageTag($locale);

			// try fallback language
			if ($value === null) {
				$locale = PropelL10n::getFallback();
				$value = $getTranslatedLocale($locale);
			}
		}
	}
    return $value;
}
