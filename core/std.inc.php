<?php

$core = "/core/";
$project = "/project/";
$config = "/core/config/";
$setup = $project."setup/";

require_once $_SERVER['DOCUMENT_ROOT'].$config.'Settings.php';
require_once $_SERVER['DOCUMENT_ROOT'].$setup.'Setup.php';

require_once $_SERVER['DOCUMENT_ROOT'].$core.'logger.php';
require_once $_SERVER['DOCUMENT_ROOT'].$core.'communicator.php';
require_once $_SERVER['DOCUMENT_ROOT'].$core.'c_class.php';
require_once $_SERVER['DOCUMENT_ROOT'].$core.'url.php';
require_once $_SERVER['DOCUMENT_ROOT'].$core.'dbconnector.php';
require_once $_SERVER['DOCUMENT_ROOT'].$core.'formvalidation.php';
require_once $_SERVER['DOCUMENT_ROOT'].$core.'genericlist.php';
require_once $_SERVER['DOCUMENT_ROOT'].$core.'core.php';

require_once $_SERVER['DOCUMENT_ROOT'].$project.'cmap.php';
require_once $_SERVER['DOCUMENT_ROOT'].$project.'API.php';
require_once $_SERVER['DOCUMENT_ROOT'].$core.'func.php';

require_once $_SERVER['DOCUMENT_ROOT'].$project.'root.php'; // should be included after Core class


error_reporting(E_ALL | E_STRICT);
//error_reporting(E_ALL);


function exceptions_error_handler($severity, $message, $filename, $lineno) {
  if (error_reporting() == 0) {
    return;
  }
  if (error_reporting() & $severity) {
    throw new ErrorException($message, 0, $severity, $filename, $lineno);
  }
}
// This is a default exceptions-handler. For debugging, it's practical to get a readable
// trace dumped out at the top level, rather than just a blank screen.
// If you use something like Xdebug, you may want to skip this part, since it already gives
// a similar output.
// For production, you should replace this handler with something, which logs the error,
// and doesn't dump a trace. Failing to do so could be a security risk.
function debug_exception_handler($ex) {
//	Logger::log('[EXCEPTION] error:'.$ex->getMessage().', code: '.$ex->getCode().', file:'.$ex->getFile().', line:'.$ex->getLine());
//	header("Location:/404");

  if (php_sapi_name() == 'cli') {
    echo "Error (code:".$ex->getCode().") :".$ex->getMessage()."\n at line ".$ex->getLine()." in file ".$ex->getFile()."\n";
    echo $ex->getTraceAsString()."\n";
  } else {
    echo "<p style='font-family:helvetica,sans-serif'>\n";
    echo "<b>Error :</b>".$ex->getMessage()."<br />\n";
    echo "<b>Code :</b>".$ex->getCode()."<br />\n";
    echo "<b>File :</b>".$ex->getFile()."<br />\n";
    echo "<b>Line :</b>".$ex->getLine()."</p>\n";
    echo "<div style='font-family:garamond'>".nl2br(htmlspecialchars($ex->getTraceAsString()))."</div>\n";
  }

  exit -1;
}
set_exception_handler('debug_exception_handler');
