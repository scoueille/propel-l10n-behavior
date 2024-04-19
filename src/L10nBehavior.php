<?php
declare(strict_types=1);

namespace gossi\propel\behavior\l10n;

use Propel\Generator\Behavior\I18n\I18nBehavior;
use Propel\Generator\Behavior\I18n\I18nBehaviorObjectBuilderModifier;
use Propel\Generator\Behavior\I18n\I18nBehaviorQueryBuilderModifier;
use Propel\Generator\Builder\Om\AbstractOMBuilder;
use ReflectionObject;

/**
 *
 */
class L10nBehavior extends I18nBehavior
{

    // default parameters value
    protected $parameters = [
        'i18n_table' => '%TABLE%_i18n',
        'i18n_phpname' => '%PHPNAME%I18n',
        'i18n_columns' => '',
        'i18n_pk_column' => null,
        'locale_column' => 'locale',
        'locale_length' => 76,
        'locale_alias' => '',
    ];

    protected string $templateDirnameBackup;

    public function __construct()
    {
//        parent::__construct();

        $r = new ReflectionObject(new I18nBehavior());
        $this->dirname = dirname((string)$r->getFileName());
    }

    /**
     * @return void
     */
    public function modifyDatabase(): void
    {
        // override parent behavior... but do nothing
    }

    /**
     * @param AbstractOMBuilder $builder
     *
     * @return string
     */
    public function staticAttributes(AbstractOMBuilder $builder): string
    {
        // override parent behavior... but do nothing
    }

    /**
     * @return string
     */
    public function getDefaultLocale(): string
    {
        return PropelL10n::getLocale();
    }

    /**
     * @return I18nBehaviorObjectBuilderModifier
     */
    public function getObjectBuilderModifier()
    {
        if (null === $this->objectBuilderModifier) {
            $this->objectBuilderModifier = new L10nBehaviorObjectBuilderModifier($this);
        }

        return $this->objectBuilderModifier;
    }

    /**
     * @return I18nBehaviorQueryBuilderModifier
     */
    public function getQueryBuilderModifier()
    {
        if (null === $this->queryBuilderModifier) {
            $this->queryBuilderModifier = new L10nBehaviorQueryBuilderModifier($this);
        }

        return $this->queryBuilderModifier;
    }

    /**
     * @return void
     */
    public function backupTemplatesDirname()
    {
        $this->templateDirnameBackup = $this->dirname;

        $r = new ReflectionObject($this);
        $this->dirname = dirname((string)$r->getFileName());
    }

    /**
     * @return void
     */
    public function restoreTemplatesDirname()
    {
        $this->dirname = $this->templateDirnameBackup;
    }
}
