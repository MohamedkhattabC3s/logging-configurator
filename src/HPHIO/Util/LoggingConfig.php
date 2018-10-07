<?php
/**
 * {CLASS SUMMARY}
 *
 * Date: 10/7/18
 * Time: 4:02 PM
 * @author Michael Munger <mj@hph.io>
 */

namespace HPHIO\Util;


class LoggingConfig
{
    public $name     = null;
    public $filename = null;
    public $enabled  = null;
    public $path     = null;

    public function importJSON($obj) {
        $this->filename = $obj->filename;
        $this->enabled  = $obj->enabled;
        $this->name     = $obj->name;
        $this->path     = $obj->path;
    }

    public function logPath() {
        return $this->path . "/" . $this->filename;
    }
}