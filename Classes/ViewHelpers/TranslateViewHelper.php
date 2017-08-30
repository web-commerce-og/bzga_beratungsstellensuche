<?php


namespace Bzga\BzgaBeratungsstellensuche\ViewHelpers;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use Bzga\BzgaBeratungsstellensuche\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception\InvalidVariableException;

/**
 * @author Sebastian Schreiber
 */
class TranslateViewHelper extends AbstractViewHelper
{

    /**
     * Initializes arguments for Translate ViewHelper
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('key', 'string', 'Translation Key');
        $this->registerArgument('id', 'string', 'Translation Key compatible to TYPO3 Flow');
        $this->registerArgument('default', 'string',
            'if the given locallang key could not be found, this value is used. If this argument is not set, child nodes will be used to render the default');
        $this->registerArgument('htmlEscape', 'boolean',
            'TRUE if the result should be htmlescaped. This won\'t have an effect for the default value');
        $this->registerArgument('arguments', 'array', 'Arguments to be replaced in the resulting string');
        $this->registerArgument('extensionName', 'string', 'UpperCamelCased extension key (for example BlogExample)');
    }

    /**
     * Wrapper function including a compatibility layer for TYPO3 Flow Translation
     *
     * @throws InvalidVariableException
     *
     * @return string The translated key or tag body if key doesn't exist
     */
    public function render()
    {
        $id = $this->hasArgument('id') ? $this->arguments['id'] : $this->arguments['key'];

        if ('' === $id) {
            throw new InvalidVariableException('An argument "key" or "id" has to be provided', 1351584844);
        }

        $registeredExtensionKeys = ExtensionManagementUtility::getRegisteredExtensionKeys();
        if (! is_array($registeredExtensionKeys) && empty($registeredExtensionKeys)) {
            return $this->renderTranslation($id);
        }

        foreach ($registeredExtensionKeys as $extensionKey) {
            $this->arguments['extensionName'] = GeneralUtility::underscoredToLowerCamelCase($extensionKey);
            $value = $this->renderTranslation($id);
            if (! empty($value)) {
                return $value;
            }
        }

        return '';
    }

    /**
     * Translate a given key or use the tag body as default.
     *
     * @param string $id The locallang id
     *
     * @return string The translated key or tag body if key doesn't exist
     */
    protected function renderTranslation($id)
    {
        $request       = $this->controllerContext->getRequest();
        $extensionName = $this->arguments['extensionName'] === null ? $request->getControllerExtensionName() : $this->arguments['extensionName'];
        $value         = LocalizationUtility::translate($id, $extensionName, $this->arguments['arguments']);
        if ($value === null) {
            $value = $this->arguments['default'] !== null ? $this->arguments['default'] : $this->renderChildren();
            if (is_array($this->arguments['arguments'])) {
                $value = vsprintf($value, $this->arguments['arguments']);
            }
        } elseif ($this->arguments['htmlEscape']) {
            $value = htmlspecialchars($value);
        }

        return $value;
    }
}
