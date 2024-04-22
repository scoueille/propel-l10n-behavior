<?php
declare(strict_types=1);

namespace Gossi\Propel\Behavior\L10n\Tests\Model;

use Book;
use BookI18nQuery;
use BookQuery;
use Gossi\Propel\Behavior\L10n\PropelL10n;
use PHPUnit\Framework\TestCase;
use Propel\Generator\Util\QuickBuilder;

/**
 *
 */
class BookQueryTest extends TestCase
{
    /**
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        if (!class_exists('Book')) {
            $schema = <<<EOF
<database name="l10n_behavior" defaultIdMethod="native">
	<table name="book">
		<column name="id" required="true" primaryKey="true" autoIncrement="true" type="integer" />
		<column name="title" type="varchar" required="true" />
		<column name="author" type="varchar" />
	
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
    protected function setUp(): void
    {
        // reset db contents
        BookQuery::create()->deleteAll();
        BookI18nQuery::create()->deleteAll();

        // fill in some dummy data

        // lord of the rings
        $b = new Book();
        $b->setTitle('Lord of the Rings');
        $b->setTitle('Herr der Ringe', 'de');
        $b->setTitle('Yubiwa Monogatari', 'ja-latn-JP');
        $b->save();

        // harry potter
        $b = new Book();
        $b->setTitle('Harry Potter and the Philosopher\'s Stone');
        $b->setTitle('Harry Potter und der Stein der Weisen', 'de');
        $b->setTitle('Harī Pottā to kenja no ishi', 'ja-latn-JP');
        $b->save();

        $b = new Book();
        $b->setTitle('Harry Potter and the Prisoner of Azkaban');
        $b->setTitle('Harry Potter und der Gefangene von Askaban', 'de');
        $b->setTitle('Harī Pottā to Azukaban no shūjin', 'ja-latn-JP');
        $b->save();
    }

    /**
     * @return void
     */
    public function testFilter()
    {
        $q = BookQuery::create();
        $q->filterByTitle('Lord of the Rings');
        $b = $q->findOne();

        static::assertNotNull($b);
        static::assertEquals('Herr der Ringe', $b->getTitle('de'));
    }

    /**
     * @return void
     */
    public function testFind()
    {
        $q = BookQuery::create();
        $books = $q->findByTitle('Harry Potter%');

        static::assertCount(2, $books);
    }

    /**
     * @return void
     */
    public function testFindOne()
    {
        $q = BookQuery::create();
        $b = $q->findOneByTitle('Harry Potter%');

        static::assertNotNull($b);
        static::assertEquals('Harry Potter und der Stein der Weisen', $b->getTitle('de'));
    }

    /**
     * @return void
     */
    public function testLocales()
    {
        $q = BookQuery::create();
        $q->setLocale('de');
        $q->filterByTitle('Herr der Ringe');
        $b = $q->findOne();

        static::assertNotNull($b);

        $q = BookQuery::create();
        $q->setLocale('de');
        $q->filterByTitle('Yubiwa Monogatari', null, 'ja-latn-JP');
        $b = $q->findOne();

        static::assertNotNull($b);
    }
}
