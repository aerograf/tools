<?php namespace XoopsModules\Tools;

use Xmf\Request;
use XoopsModules\Tools;
use XoopsModules\Tools\Common;

/**
 * Class Utility
 */
class Utility
{
    use Common\VersionChecks; //checkVerXoops, checkVerPhp Traits

    use Common\ServerStats; // getServerStats Trait

    use Common\FilesManagement; // Files Management Trait

    //--------------- Custom module methods -----------------------------
}
