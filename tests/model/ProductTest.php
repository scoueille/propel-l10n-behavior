<?php
declare(strict_types=1);

namespace model;

use Gossi\Propel\Behavior\L10n\PropelL10n;
use PHPUnit\Framework\TestCase;
use Product;
use ProductI18nQuery;
use ProductQuery;
use Propel\Generator\Util\QuickBuilder;

/**
 *
 */
class ProductTest extends TestCase
{
    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        if (!class_exists('Product')) {
            $schema = <<<EOF
<database name="l10n_behavior">
	<table name="product">
		<column name="id" required="true" primaryKey="true" autoIncrement="true" type="integer" />
		<column name="title" type="varchar" required="true" />
		
		<behavior name="l10n">
			<parameter name="i18n_columns" value="title" />
		</behavior>
	</table>
</database>
EOF;

            QuickBuilder::buildSchema($schema);
        }

        PropelL10n::setLocale('en'); // just reset, may changed in other tests
        PropelL10n::setFallback('en');
        PropelL10n::setDependencies([
            'de-CH' => 'de-DE',
            'de-AT' => 'de-DE',
            'de-DE' => 'en-US',
            'ja' => 'en-US'
        ]);
    }

    /**
     * @return void
     */
    public function testDefaultLocale()
    {
        $p = new Product();

        static::assertNull($p->getLocale());

        $p->setTitle('delicious');
        static::assertEquals('delicious', $p->getTitle());
        static::assertEquals('en', $p->getCurrentTranslation()->getLocale());
    }

    /**
     * @return void
     */
    public function testDependency()
    {
        $p = new Product();
        $p->setLocale('de-DE');
        $p->setTitle('lecker');

        static::assertEquals('lecker', $p->getTitle('de-CH'));
    }

    /**
     * @return void
     */
    public function testPrimaryLanguage()
    {
        $p = new Product();
        $p->setLocale('ja');
        $p->setTitle('おいしい');

        static::assertEquals('おいしい', $p->getTitle('ja-JP'));
    }

    /**
     * @return void
     */
    public function testFallback()
    {
        $p = new Product();
        $p->setLocale('en');
        $p->setTitle('delicious');
        $p->setLocale('de');
        $p->setTitle('lecker');

        static::assertEquals('delicious', $p->getTitle('it'));
    }

    /**
     * @return void
     */
    public function testSetterLocale()
    {
        $p = new Product();
        $p->setTitle('delicious', 'en');
        $p->setTitle('bene', 'it');

        static::assertEquals('delicious', $p->getTitle('en-US'));
        static::assertEquals('bene', $p->getTitle('it-IT'));
    }

    /**
     * @return void
     */
    public function testLocaleTagChain()
    {
        PropelL10n::addDependency('it-IT', 'en');
        $p = new Product();
        $p->setTitle('bene', 'it');
        $p->setTitle('good', 'en');

        static::assertEquals('bene', $p->getTitle('it-IT'));
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        ProductQuery::create()->deleteAll();
        ProductI18nQuery::create()->deleteAll();
    }
}
