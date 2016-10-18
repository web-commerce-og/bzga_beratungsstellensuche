<?php

namespace BZgA\BzgaBeratungsstellensuche;

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

/**
 * @package TYPO3
 * @subpackage bzga_beratungsstellensuche
 * @author Sebastian Schreiber
 */
final class Events
{

    /**
     * @var string
     */
    const PRE_IMPORT_SIGNAL = 'preImport';

    /**
     * @var string
     */
    const POST_IMPORT_SIGNAL = 'postImport';

    /**
     * @var string
     */
    const SIGNAL_MapNames = 'mapNames';

    /**
     * @var string
     */
    const LIST_ACTION_SIGNAL = 'entry.list.action';

    /**
     * @var string
     */
    const SHOW_ACTION_SIGNAL = 'entry.show.action';

    /**
     * @var string
     */
    const FORM_ACTION_SIGNAL = 'entry.form.action';

    /**
     * @var string
     */
    const INITIALIZE_ACTION_SIGNAL = 'entry.initialize.action';

    /**
     * @var string
     */
    const ADDITIONAL_NORMALIZERS_SIGNAL = 'serializer.normalizers';

    /**
     * @var string
     */
    const DENORMALIZE_CALLBACKS_SIGNAL = 'denormalizer.callbacks';


    /**
     * @var string
     */
    const TABLE_TRUNCATE_ALL_SIGNAL = 'repository.truncateall';




}