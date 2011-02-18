<?php

class Pagination {

    public static $currentPage;
    public static $totalPagesCount;
    public static $itemsPerPage;
    public static $pageControlsCount;
    public static $baseUrl;

    public static function getBaseUrl() {
        $pageParameterName = Setup::$PAGE_ARG;
        $baseUrl = $_SERVER['REQUEST_URI'];
        if (preg_match("/\?$pageParameterName=\d+/", $baseUrl)) {
            $baseUrl = preg_replace("/\?$pageParameterName=\d+&?/", '?', $baseUrl);
        } else {
            $baseUrl = preg_replace("/&$pageParameterName=\d+/", '', $baseUrl);
        }
        $paramsPos = strpos($baseUrl, '?');
        if ($paramsPos === false) {
            $baseUrl .= '?';
        } else if ($paramsPos != strlen($baseUrl) - 1) {
            $baseUrl .= '&';
        }
        return $baseUrl;
    }
    public static function calculatePages($totalItems) {
        return ($totalItems ? ceil($totalItems/Setup::$POSTS_PER_PAGE) : false);
    }

    public static function getCurrentPage(){
        return isset(URL::$GET[Setup::$PAGE_ARG])?URL::$GET[Setup::$PAGE_ARG]:1;
    }

    public static function get($context,$totalPagesCount) {

        // general case
        //
        // 1 .. 6  7  8  9  10 .. 100
        //
        // firstPage = 1
        // lastPage = 100
        // currentPage = 8
        // firstControlPage = 6
        // lastControlPage = 10
        // newerPage = 7
        // olderPage = 9
        // $this->pageControlsCount = 5

        $currentPage = self::getCurrentPage();
        $firstPage = 1;
        $lastPage = $currentPage;
        $firstControlPage = 1;
        $lastControlPage = 1;
        $pageControlsCount = 5;
        
        if ($totalPagesCount) {

            $lastPage = $totalPagesCount;

            $firstControlPage = ($currentPage - floor($pageControlsCount/2));
            if ($firstControlPage < 1) {
                $firstControlPage = 1;
            }

            $lastControlPage = $firstControlPage + $pageControlsCount - 1;
            if ($lastControlPage > $totalPagesCount) {
                $lastControlPage = $totalPagesCount;
                if ($lastControlPage > $pageControlsCount) {
                    $firstControlPage = $lastControlPage - $pageControlsCount + 1;
                }
            }
        }

        $newerPage = ($currentPage > 1 ? $currentPage - 1 : false);
        $olderPage = ($currentPage < $totalPagesCount ? $currentPage + 1 : false);

        $view = $_SERVER['DOCUMENT_ROOT'].Settings::$PROJECT_TPL_PATH.'/generic/'.'_pagination.tpl.php';
        return $context->render($view,
                                array(
                                        'baseUrl'           => htmlspecialchars(self::getBaseUrl()),
                                        'firstPage'         => $firstPage,
                                        'lastPage'          => $lastPage,
                                        'currentPage'       => $currentPage,
                                        'newerPage'         => $newerPage,
                                        'olderPage'         => $olderPage,
                                        'totalPages'        => $totalPagesCount,
                                        'parameterName'     => Setup::$PAGE_ARG,
                                        'firstControlPage'  => $firstControlPage,
                                        'lastControlPage'   => $lastControlPage
                                        )
                                );
    }

}