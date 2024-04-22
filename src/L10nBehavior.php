<?php
declare(strict_types=1);

namespace Gossi\Propel\Behavior\L10n;

use Propel\Generator\Behavior\I18n\I18nBehavior;
use Propel\Generator\Builder\Om\AbstractOMBuilder;

/**
 *
 */
class L10nBehavior extends I18nBehavior
{
    /**
     * Default parameters value
     *
     * @var array<string, mixed>
     */
    protected $parameters = [
        'i18n_table' => '%TABLE%_i18n',
        'i18n_phpname' => '%PHPNAME%I18n',
        'i18n_columns' => '',
        'i18n_pk_column' => null,
        'locale_column' => 'locale',
        'locale_length' => 5,
        'locale_alias' => '',
    ];

    /**
     * @var string|null
     */
    protected $templateDirnameBackup;

    /**
     * @var L10nBehaviorObjectBuilderModifier|null
     */
    protected $objectBuilderModifier;

    /**
     * @var L10nBehaviorQueryBuilderModifier|null
     */
    protected $queryBuilderModifier;

    public function __construct()
    {
        $this->dirname = __DIR__ . DIRECTORY_SEPARATOR;
    }

    /**
     * @return void
     */
    public function modifyDatabase(): void
    {
    }

    /**
     * @return string
     */
    public function getDefaultLocale(): string
    {
        return PropelL10n::getLocale();
    }

    /**
     * @return L10nBehaviorObjectBuilderModifier
     */
    public function getObjectBuilderModifier(): ?L10nBehaviorObjectBuilderModifier
    {
        if ($this->objectBuilderModifier === null) {
            $this->objectBuilderModifier = new L10nBehaviorObjectBuilderModifier($this);
        }

        return $this->objectBuilderModifier;
    }

    /**
     * @return L10nBehaviorQueryBuilderModifier
     */
    public function getQueryBuilderModifier(): ?L10nBehaviorQueryBuilderModifier
    {
        if ($this->queryBuilderModifier === null) {
            $this->queryBuilderModifier = new L10nBehaviorQueryBuilderModifier($this);
        }

        return $this->queryBuilderModifier;
    }

    /**
     * @param AbstractOMBuilder $builder
     *
     * @return string
     */
    public function staticAttributes(AbstractOMBuilder $builder): string
    {
        return $this->renderTemplate('staticAttributes');
    }

    /**
     * @return void
     */
    public function backupTemplatesDirname()
    {
        $this->templateDirnameBackup = $this->dirname;

        $this->dirname = __DIR__ . DIRECTORY_SEPARATOR;
    }

    /**
     * @return void
     */
    public function restoreTemplatesDirname()
    {
        $this->dirname = (string)$this->templateDirnameBackup;
    }
}
