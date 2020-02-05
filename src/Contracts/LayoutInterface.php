<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Contracts;

/**
 * @interface LayoutInterface
 * @package   Simianbv\JsonSchema\Contracts
 */
interface LayoutInterface extends ElementInterface
{
    const TYPE_GROUP = 'group';
    const TYPE_VERTICAL = 'vertical';
    const TYPE_HORIZONTAL = 'horizontal';
    const TYPE_TAB = 'tab';
    const TYPE_TABS = 'tabs';
    const TYPE_INLINE = 'inline';

    const ORIENTATION_HORIZONTAL = 'horizontal';
    const ORIENTATION_VERTICAL = 'vertical';

    const VISIBILITY_SHOW = 'show';
    const VISIBILITY_OPTIONAL = 'optional';
    const VISIBILITY_HIDE = 'hide';

}
