
/**
 * Returns the current translation
 *
 * @param ConnectionInterface|null $con an optional connection object
 *
 * @return <?= $i18nTablePhpName ?>
 */
public function getCurrentTranslation($locale = null, ?ConnectionInterface $con = null)
{
	$locale = $this->get<?= $localeColumnName ?>();
	if ($locale === null) {
		$locale = PropelL10n::getLocale();
	}
    return $this->getTranslation($locale, $con);
}
