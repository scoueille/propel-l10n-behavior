<?php
declare(strict_types=1);

namespace gossi\propel\behavior\l10n;

/**
 *
 */
trait RenderTrait
{

    /**
     * @param $name
     * @param array $vars
     * @return string
     */
    protected function renderTemplate($name, array $vars = []): string
    {
        $this->behavior->backupTemplatesDirname();
        $template = $this->behavior->renderTemplate($name, $vars);
        $this->behavior->restoreTemplatesDirname();
        return $template;
    }
}
