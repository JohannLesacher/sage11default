<?php

namespace App\View\Composers\Blocks;

use App\View\BlockComposer;

/**
 * Expose $wrapperAttributes() à toutes les vues de blocs, y compris celles sans composer dédié.
 */
class Common extends BlockComposer
{
    protected static $views = ['blocks.*'];
}
