<?php


class dbConnector {

    private $connection;
    public $profiling;
    public $profilingTotalTime;

    function __construct($host=null, $user=null, $pwd=null, $db=null) {
        if (!$host && !$user && !$pwd && !$db) {
            $host 	= Settings::$dbWriteHost;
            $user 	= Settings::$dbUser;
            $pwd 	= Settings::$dbPassword;
            $db 	= Settings::$dbName;
        }
        $this->connectDb($host, $user, $pwd, $db);
    }

    function connectDb($host, $user, $pwd, $db) {
        
        //Logger::log("[connectDb] CONNECT: $host, $user, $pwd, $db");
    	
        $port = 3306;
        if (preg_match('/(.*):(\d*)/', $host, $matches)) {
            $port = $matches[2];
            $host = $matches[1];
        }
        $retries = 0;
        
        while ($retries++ < Settings::$DB_CONNECTION_RETRIES) {
            $this->connection = @new mysqli($host, $user, $pwd, $db, $port);
            if (mysqli_connect_errno()) {
                if ($retries == 1) {
                    $msg = 'No db connection'.' '.$host
                    .' #'.mysqli_connect_errno()
                    .' '.mysqli_connect_error();
                    Logger::log(Logger::SEVERE, $msg);
                }
                $this->connection = null;
            } else {
            	$this->connection->query("SET NAMES utf8");
            	$this->connection->query("SET CHARACTER SET utf8");
            	$this->connection->query("SET CHARACTER_SET_RESULTS=utf8");
                break;
            }
        }
    }

    function isConnected() {
        return $this->connection != null;
    }

    function __destruct() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    function runSQL($sql) {
    	
    	if (!trim($sql)) return null;
// Logger::log("[runSQL] SQL=$sql");  	
        $t = microtime(true);
        $result = $this->connection->query($sql);

        $delta = microtime(true) - $t;
        if ($delta > Settings::$MAX_DB_EXEC_TIME) {
            $secs = (intval($delta*10000)/10000);
            $message = "SQL takes long time to execute. Timing: $secs sql: $sql\n";
            Logger::log(Logger::WARNING, $message);
        }
        if (Settings::$PROFILING) {            
            $this->profiling .= "Timing: ".(intval($delta*10000)/10000)." sql: $sql\n";
            $this->profilingTotalTime += $delta;
        }

        if (!$result) {
            $msg = $this->connection->error;
            Logger::log(Logger::SEVERE, $sql."\n".$msg);
            throw new Exception();
        }
        return $result;
    }

    function getGeneratedId() {
        return $this->connection->insert_id;
    }

    function getAffectedRowsNumber() {
        return $this->connection->affected_rows;
    }

    public function escapeString($str) {
        return $this->connection->real_escape_string($str);
    }
    
    public function getProfiling() {
        return array('total' =>  $this->profilingTotalTime, 'details' => $this->profiling);
    }

    public function escapeArray($to_escape) {
        $escaped = array();
        foreach($to_escape as $k => $v) {
            $escaped[$this->connection->real_escape_string($k)] = strip_tags(trim($this->connection->real_escape_string($v)));
        }
        return $escaped;
    }
    
}
?>
