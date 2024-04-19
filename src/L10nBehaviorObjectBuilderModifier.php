<?php
declare(strict_types=1);

namespace gossi\propel\behavior\l10n;

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
     * @param ObjectBuilder $builder
     *
     * @return string
     */
    public function objectMethods(ObjectBuilder $builder): string
    {
        $builder->declareClass('gossi\\propel\\behavior\\l10n\\PropelL10n');
        return parent::objectMethods($builder);
    }

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
            'i18nSetterMethod' => $this->builder->getRefFKPhpNameAffix($fk),
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
        if ($this->isDateType($column->getType())) {
            $objectBuilder->addTemporalAccessorComment($comment, $column);
        } else {
            $objectBuilder->addDefaultAccessorComment($comment, $column);
        }
        $comment = preg_replace('/^\t/m', '', $comment);

        return $this->renderTemplate('objectTranslatedColumnGetter', [
            'comment' => $comment,
            'column' => $column,
            'columnPhpName' => $column->getPhpName(),
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
        $visibility = $column->getTable()->isReadOnly() ? 'protected' : $column->getMutatorVisibility();

        $typeHint = '';
        $null = '';

        if ($column->getTypeHint()) {
            $typeHint = $column->getTypeHint();
            if ('array' !== $typeHint) {
                $typeHint = $this->declareClass($typeHint);
            }

            $typeHint .= ' ';

            if (!$column->isNotNull()) {
                $null = ' = null';
            }
        }

        $typeHint = "$typeHint\$v$null";


        $i18nTablePhpName = $this->builder->getClassNameFromBuilder(
            $this->builder->getNewStubObjectBuilder($this->behavior->getI18nTable())
        );
        $tablePhpName = $this->builder->getObjectClassName();
        $objectBuilder = $this->builder->getNewObjectBuilder($this->behavior->getI18nTable());
        $comment = '';
        if ($this->isDateType($column->getType())) {
            $objectBuilder->addTemporalMutatorComment($comment, $column);
        } else {
            $objectBuilder->addMutatorComment($comment, $column);
        }
        $comment = preg_replace('/^\t/m', '', $comment);
        $comment = str_replace(
            '@return     $this|' . $i18nTablePhpName,
            '@return     $this|' . $tablePhpName,
            $comment
        );

        return $this->renderTemplate('objectTranslatedColumnSetter', [
            'comment' => $comment,
            'column' => $column,
            'visibility' => $visibility,
            'typeHint' => $typeHint,
            'columnPhpName' => $column->getPhpName(),
            'localeColumnName' => $this->behavior->getLocaleColumn()->getPhpName()
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
}
