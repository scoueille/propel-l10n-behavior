
/**
 * Finds objects in the query with the given filter 
 *
 * Example usage:
 * <code>
 * $query->findBy<?php echo $columnPhpName?>('fooValue');   // WHERE <?php echo $columnName ?> = 'fooValue'
 * $query->findBy<?php echo $columnPhpName?>('%fooValue%'); // WHERE <?php echo $columnName ?> LIKE '%fooValue%'
 * </code>
 *
 * @param     string $<?php echo $columnName ?> The value to use as filter.
 *              Accepts wildcards (* and % trigger a LIKE)
 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
 * @param     string $locale Overwrites the locale for this filter
 *
 * @return    <?php echo $objectClassName ?>[]|ObjectCollection The results
 */
public function findBy<?php echo $columnPhpName?>($<?php echo $columnName ?> , $comparison = null, $locale = null)
{
	return $this->filterBy<?php echo $columnPhpName?>($<?php echo $columnName ?> , $comparison, $locale)
		->find();
}
