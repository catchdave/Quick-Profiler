<?php

/**
 * Quick Profiler Console Class.
 * This class serves as a wrapper around a global php variable, pqp_logs, that we have created.
 * 
 * @author Ryan Campbell. April 22, 2009
 * @author Modifed by David Rochwerger <catch.dave@gmail.com>
 *
 */
class Console
{
	/**
	 * @var Console
	 */
	static $instance = null;

	/**
	 * @var array
	 */
	protected $logs = array();

	/**
	 * Singleton
	 */
	static public function get()
	{
		if (static::$instance === null) {
			static::$instance = new static();
		}
		
		return static::$instance;
	}
	
	/**
	 * For unit testing - clears the singleton instance
	 */
	static public function reset()
	{
		static::$instance = null;
	}
	
	public function __construct()
	{
		$this->logs = array(
			'console'     => array(),
			'logCount'    => 0,
			'memoryCount' => 0,
			'errorCount'  => 0,
			'speedCount'  => 0 
		);
	}
	
	/**
	 * Log a variable to console
	 * 
	 * @param unknown_type $data        	
	 */
	public function log($data)
	{
		$logItem = array(
			'data' => $data,
			'type' => 'log' 
		);
		self::addToConsoleAndIncrement('logCount', $logItem);
	}
	
	/**
	 * Log memory usage of variable or entire script
	 *
	 * @param unknown_type $object        	
	 * @param unknown_type $name        	
	 */
	public function logMemory($object = false, $name = 'PHP')
	{
		$memory = memory_get_usage();
		if ($object) {
			$memory = strlen(serialize($object));
		}
		$logItem = array(
			'data' => $memory,
			'type' => 'memory',
			'name' => $name,
			'dataType' => is_object($object) ? get_class($object) : gettype($object) 
		);
		self::addToConsoleAndIncrement('memoryCount', $logItem);
	}
	
	/**
	 * Log a php exception object
	 *
	 * @param Exception $exception        	
	 * @param string $message        	
	 */
	public function logError(Exception $exception, $message)
	{
		$logItem = array(
			'data' => $message,
			'type' => 'error',
			'file' => $exception->getFile(),
			'line' => $exception->getLine() 
		);
		self::addToConsoleAndIncrement('errorCount', $logItem);
	}
	
	/**
	 * Point in time speed snapshot
	 * 
	 * @param unknown_type $name        	
	 */
	public function logSpeed($name = 'Point in Time')
	{
		$logItem = array(
			'data' => microtime(true),
			'type' => 'speed',
			'name' => $name 
		);
		self::addToConsoleAndIncrement('speedCount', $logItem);
	}
	
	/**
	 * Return & modify logs
	 *
	 * @param unknown_type $log        	
	 * @param unknown_type $item        	
	 */
	public function addToConsoleAndIncrement($log, $item)
	{
		$this->logs['console'][] = $item;
		$this->logs[$log] += 1;
	}
	
	/**
	 * Returns the logs
	 */
	public function getLogs()
	{
		return $this->logs;
	}

}

?>