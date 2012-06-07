<?php

/**
 * Quick Profiler Console Class.
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
	 * 
	 * @var array
	 */
	protected $logs;
	
	/**
	 * @var float
	 */
	protected $currentTime;
	
	/**#@+
	 * Log types
	 * 
	 * @var string
	 */
	const LOG    = 'log';
	const ERROR  = 'error';
	const TIME  = 'time';
	const MEMORY = 'memory';
	/**#@-*/
	
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
		$this->setCurrentTime(microtime(true));
		$this->logs = array(
			'console' => array(),
			'counts'  => array(
				self::LOG    => 0,
				self::ERROR  => 0,
				self::MEMORY => 0,
				self::TIME   => 0,
			)
		);
	}
	
	/**
	 * Set the time
	 * 
	 * @param float $time
	 */
	public function setCurrentTime($time)
	{
		$this->currentTime = $time;
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
			'type' => self::LOG
		);
		self::addToConsoleAndIncrement($logItem);
	}
	
	/**
	 * Log memory usage of variable or entire script
	 *
	 * @param mixed $object
	 * @param string $name
	 */
	public function logMemory($object = false, $name = 'PHP')
	{
		$type = null;
	    if (is_null($object)) {
	      $memory = memory_get_usage(true);
	    }
	    elseif (is_string($object)) {
	      $memory = strlen($object);
	      $type = 'string';
	    }
	    else {
	      $memory = strlen(serialize($object));
	      $type = is_object($object) ? get_class($object) : gettype($object);
	    }
		$logItem = array(
			'data' => $memory,
			'type' => self::MEMORY,
			'name' => $name,
			'dataType' => $type
		);
		self::addToConsoleAndIncrement($logItem);
	}
	
	/**
	 * Log an error, optionally with an exception.
	 *
	 * @param string $message
	 * @param Exception $exception - If not provided will use debug backtrace
	 */
	public function logError($message, Exception $exception = null)
	{
		if ($exception) {
			$line = $exception->getLine();
			$file = $exception->getFile();
		}
		else {
			$backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
			$line = $backtrace[0]['line'];
			$file = $backtrace[0]['file'];
		}
		$logItem = array(
			'data' => $message,
			'type' => self::ERROR,
			'file' => $file,
			'line' => $line 
		);
		self::addToConsoleAndIncrement($logItem);
	}
	
	/**
	 * Point in time snapshot. Uses last point in time
	 * or optionally you can specify amount.
	 * 
	 * @param string $name
	 * @param float $timeTaken - Optional amount of time taken        	
	 */
	public function logTime($name = 'Point in Time', $timeTaken = null)
	{
		if (is_null($timeTaken)) {
			$timeTaken = microtime(true) - $this->currentTime;
		}
		$this->currentTime = microtime(true);
		$logItem = array(
			'data' => microtime(true),
			'type' => self::TIME,
			'name' => $name 
		);
		self::addToConsoleAndIncrement($logItem);
	}
	
	/**
	 * Return & modify logs
	 *
	 * @param array $item
	 */
	public function addToConsoleAndIncrement(array $item)
	{
		$this->logs['console'][] = $item;
		$this->logs['counts'][$item['type']] += 1;
	}
	
	/**
	 * Returns the logs
	 */
	public function getLogs()
	{
		return $this->logs;
	}

}

