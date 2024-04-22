<?php
declare(strict_types=1);

namespace Gossi\Propel\Behavior\L10n\Tests;

use Gossi\Propel\Behavior\L10n\PropelL10n;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class PropelL10nTest extends TestCase
{
    /**
     * @return void
     */
    public function testAddDependency()
    {
        PropelL10n::addDependency('de-DE', 'en-US');

        static::assertTrue(PropelL10n::hasDependency('de-DE'));
        static::assertEquals(['de-DE' => 'en-US'], PropelL10n::getDependencies());
    }

    /**
     * @return void
     */
    public function testRemoveDepedency()
    {
        PropelL10n::addDependency('de-DE', 'en-US');
        PropelL10n::removeDependency('de-DE');

        static::assertCount(0, PropelL10n::getDependencies());
    }

    /**
     * @return void
     */
    public function testSetDependencies()
    {
        $deps = [
            'de-DE' => 'en-US',
            'de-CH' => 'de-DE',
            'ja-JP' => 'en-US'
        ];

        PropelL10n::setDependencies($deps);

        static::assertEquals(2, PropelL10n::countDependencies('de-CH'));
        static::assertEquals(0, PropelL10n::countDependencies('it-IT'));
        static::assertEquals($deps, PropelL10n::getDependencies());
    }

    /**
     * @return void
     */
    public function testCurrentLocale()
    {
        static::assertEquals('en', PropelL10n::getLocale());

        PropelL10n::setLocale('de-DE');
        static::assertEquals('de-DE', PropelL10n::getLocale());
    }
}
