<?php
declare(strict_types=1);

namespace Gossi\Propel\Behavior\L10n;

use Propel\Generator\Behavior\I18n\I18nBehaviorObjectBuilderModifier;
use Propel\Generator\Builder\Om\ObjectBuilder;
use Propel\Generator\Model\Column;

/**
 *
 */
class L10nBehaviorObjectBuilderModifier extends I18nBehaviorObjectBuilderModifier
{
    use RenderTrait;

    /**
     * @var L10nBehavior
     */
    protected $behavior;

    /**
     * @param ObjectBuilder $builder
     *
     * @return string
     */
    public function objectAttributes(ObjectBuilder $builder): string
    {
        return $this->renderTemplate('objectAttributes', [
            'objectClassName' => $builder->getClassNameFromBuilder(
                $builder->getNewStubObjectBuilder($this->behavior->getI18nTable())
            ),
        ]);
    }

    /**
     * @param ObjectBuilder $builder
     *
     * @return string
     */
    public function objectClearReferences(ObjectBuilder $builder): string
    {
        return $this->renderTemplate('objectClearReferences');
    }

    /**
     * @param ObjectBuilder $builder
     *
     * @return string
     */
    public function objectMethods(ObjectBuilder $builder): string
    {
        $builder->declareClass('Gossi\\Propel\\Behavior\\L10n\\PropelL10n');
        $this->builder = $builder;

        $script = '';
        $script .= $this->addSetLocale();
        $script .= $this->addGetLocale();

        $alias = $this->behavior->getParameter('locale_alias');
        if ($alias) {
            $script .= $this->addGetLocaleAlias($alias);
            $script .= $this->addSetLocaleAlias($alias);
        }

        $script .= $this->addGetTranslation();
        $script .= $this->addRemoveTranslation();
        $script .= $this->addGetCurrentTranslation();

        foreach ($this->behavior->getI18nColumns() as $column) {
            $script .= $this->addTranslatedColumnGetter($column);
            $script .= $this->addTranslatedColumnSetter($column);
        }

        return $script;
    }

    /**
     * @return string
     */
    protected function addSetLocale(): string
    {
        return $this->renderTemplate('objectSetLocale', [
            'objectClassName' => $this->builder->getClassNameFromBuilder($this->builder->getStubObjectBuilder()),
            'localeColumnName' => $this->behavior->getLocaleColumn()->getPhpName(),
        ]);
    }

    /**
     * @return string
     */
    protected function addGetLocale(): string
    {
        return $this->renderTemplate('objectGetLocale', [
            'localeColumnName' => $this->behavior->getLocaleColumn()->getPhpName(),
        ]);
    }

    /**
     * @param string $alias
     *
     * @return string
     */
    protected function addSetLocaleAlias(string $alias): string
    {
        return $this->renderTemplate('objectSetLocaleAlias', [
            'objectClassName' => $this->builder->getClassNameFromBuilder($this->builder->getStubObjectBuilder()),
            'alias' => ucfirst($alias),
            'localeColumnName' => $this->behavior->getLocaleColumn()->getPhpName(),
        ]);
    }

    /**
     * @param string $alias
     *
     * @return string
     */
    protected function addGetLocaleAlias(string $alias): string
    {
        return $this->renderTemplate('objectGetLocaleAlias', [
            'alias' => ucfirst($alias),
            'localeColumnName' => $this->behavior->getLocaleColumn()->getPhpName(),
        ]);
    }

    /**
     * @return string
     */
    protected function addGetTranslation(): string
    {
        $plural = false;
        $i18nTable = $this->behavior->getI18nTable();
        $fk = $this->behavior->getI18nForeignKey();

        return $this->renderTemplate('objectGetTranslation', [
            'i18nTablePhpName' => $this->builder->getClassNameFromBuilder(
                $this->builder->getNewStubObjectBuilder($i18nTable)
            ),
            'i18nListVariable' => $this->builder->getRefFKCollVarName($fk),
            'localeColumnName' => $this->behavior->getLocaleColumn()->getPhpName(),
            'i18nQueryName' => $this->builder->getClassNameFromBuilder(
                $this->builder->getNewStubQueryBuilder($i18nTable)
            ),
            'i18nSetterMethod' => $this->builder->getRefFKPhpNameAffix($fk, $plural),
        ]);
    }

    /**
     * @return string
     */
    protected function addRemoveTranslation(): string
    {
        $i18nTable = $this->behavior->getI18nTable();
        $fk = $this->behavior->getI18nForeignKey();

        return $this->renderTemplate('objectRemoveTranslation', [
            'objectClassName' => $this->builder->getClassNameFromBuilder($this->builder->getStubObjectBuilder()),
            'i18nQueryName' => $this->builder->getClassNameFromBuilder(
                $this->builder->getNewStubQueryBuilder($i18nTable)
            ),
            'i18nCollection' => $this->builder->getRefFKCollVarName($fk),
            'localeColumnName' => $this->behavior->getLocaleColumn()->getPhpName(),
        ]);
    }

    /**
     * @return string
     */
    protected function addGetCurrentTranslation(): string
    {
        return $this->renderTemplate('objectGetCurrentTranslation', [
            'i18nTablePhpName' => $this->builder->getClassNameFromBuilder(
                $this->builder->getNewStubObjectBuilder($this->behavior->getI18nTable())
            ),
            'localeColumnName' => $this->behavior->getLocaleColumn()->getPhpName(),
        ]);
    }

    /**
     * @param Column $column
     *
     * @return string
     * @todo The connection used by getCurrentTranslation in the generated code cannot be specified by the user
     *
     */
    protected function addTranslatedColumnGetter(Column $column): string
    {
        $objectBuilder = $this->builder->getNewObjectBuilder($this->behavior->getI18nTable());
        $comment = '';
        $functionStatement = '';
        if ($this->isDateType($column->getType())) {
            $objectBuilder->addTemporalAccessorComment($comment, $column);
            $objectBuilder->addTemporalAccessorOpen($functionStatement, $column);
        } else {
            $objectBuilder->addDefaultAccessorComment($comment, $column);
            $objectBuilder->addDefaultAccessorOpen($functionStatement, $column);
        }
        $comment = preg_replace('/^\t/m', '', $comment);
        $functionStatement = preg_replace('/^\t/m', '', $functionStatement);
        preg_match_all('/\$[a-z]+/i', $functionStatement, $params);

        return $this->renderTemplate('objectTranslatedColumnGetter', [
            'comment' => $comment,
            'functionStatement' => $functionStatement,
            'columnPhpName' => $column->getPhpName(),
            'params' => implode(', ', $params[0]),
            'column' => $column,
            'localeColumnName' => $this->behavior->getLocaleColumn()->getPhpName()
        ]);
    }

    /**
     * @param Column $column
     *
     * @return string
     * @todo The connection used by getCurrentTranslation in the generated code cannot be specified by the user
     *
     */
    protected function addTranslatedColumnSetter(Column $column): string
    {
        $i18nTablePhpName = $this->builder->getClassNameFromBuilder(
            $this->builder->getNewStubObjectBuilder($this->behavior->getI18nTable())
        );
        $tablePhpName = $this->builder->getObjectClassName();
        $objectBuilder = $this->builder->getNewObjectBuilder($this->behavior->getI18nTable());
        $comment = '';
        $functionStatement = '';
        if ($this->isDateType($column->getType())) {
            $objectBuilder->addTemporalMutatorComment($comment, $column);
        } else {
            $objectBuilder->addMutatorComment($comment, $column);
        }
        $objectBuilder->addMutatorOpenOpen($functionStatement, $column);
        $comment = preg_replace('/^\t/m', '', $comment);
        $comment = str_replace(
            '@return     $this|' . $i18nTablePhpName,
            '@return     $this|' . $tablePhpName,
            $comment
        );
        $functionStatement = preg_replace('/^\t/m', '', $functionStatement);
        preg_match_all('/\$[a-z]+/i', $functionStatement, $params);

        return $this->renderTemplate('objectTranslatedColumnSetter', [
            'comment' => $comment,
            'functionStatement' => $functionStatement,
            'columnPhpName' => $column->getPhpName(),
            'params' => implode(', ', $params[0]),
            'localeColumnName' => $this->behavior->getLocaleColumn()->getPhpName(),
            'column' => $column,
        ]);
    }
}
