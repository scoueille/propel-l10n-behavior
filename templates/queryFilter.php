/**
* Filters the query with the
*
* Example usage:
* <code>
    * $query->filterBy<?php echo $columnPhpName ?>('fooValue'); //
    WHERE <?php echo $columnName ?> = 'fooValue'
    * $query->filterBy<?php echo $columnPhpName ?>('%fooValue%'); //
    WHERE <?php echo $columnName ?> LIKE '%fooValue%'
    * </code>
*
* @param     string $<?php echo $columnName ?> The value to use as filter.
*              Accepts wildcards (* and % trigger a LIKE)
* @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
* @param     string $locale Overwrites the locale for this filter
*
* @return    <?php echo $queryClass ?> The current query, for fluid interface
*/
public function filterBy<?php echo $columnPhpName ?>($<?php echo $columnName ?> , $comparison = null, $locale = null)
{
if ($locale === null) {
$locale = $this->get<?= $localeColumnName ?>();
}
if ($locale === null) {
$locale = PropelL10n::getLocale();
}

if (null === $comparison) {
if (is_array($<?php echo $columnName ?>)) {
$comparison = Criteria::IN;
} elseif (preg_match('/[\%\*]/', $<?php echo $columnName ?>)) {
$token = str_replace('*', '%', $<?php echo $columnName ?>);
$comparison = Criteria::LIKE;
}
}

return $this->useI18nQuery($locale)
->filterBy<?php echo $columnPhpName ?>($<?php echo $columnName ?> , $comparison)
->endUse();
}
