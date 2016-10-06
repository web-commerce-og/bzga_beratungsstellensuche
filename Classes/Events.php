<?php

namespace BZgA\BzgaBeratungsstellensuche;

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
    const INITIALIZE_ACTION_SIGNAL = 'entry.initialize.action';

    /**
     * @var string
     */
    const ADDITIONAL_NORMALIZERS_SIGNAL = 'serializer.normalizers';

    /**
     * @var string
     */
    const DENORMALIZE_CALLBACKS_SIGNAL = 'denormalizer.callbacks';




}