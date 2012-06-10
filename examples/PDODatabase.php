<?php
/**
 * Example of a database class that uses the profiler.
 * 
 * @author David Rochwerger <catch.dave@gmail.com>
 */
class PDODatabase extends PDO implements ProfileObservable
{
	/**
	 * @var QuickProfiler
	 */
	static $profiler;
	
	const PROFILE_TYPE = 'mysql';
	
	/**
	 * An example of how you can wrap database calls for profiling.
	 * 
	 * This example only wraps PDO::query(). For your own implementation,
	 * you'll probably want to wrap PDO::prepare/exec and PDOStatement::execute.
	 * 
	 * @see PDO::query()
	 */
	public function query($sql)
	{
		$startTime = microtime(true);
		$startMem = memory_get_usage(true);
		
		$results = parent::query($sql);
		
		$this->addProfileEvent(array('sql' => $sql), $startTime, $startMem);
		
		return $results;
	}
	
	/**
	 * @see ProfileObservable::attachProfiler()
	 */
	public function attachProfiler(QuickProfiler $profiler)
	{
		self::$profiler = $profiler;
		self::$profiler->increment(self::PROFILE_TYPE, 'duplicates', 0); // initialize duplicate count
	}
	
	/**
	 * @see ProfileObservable::addProfileEvent()
	 */
	public function addProfileEvent(array $event, $timeStart, $memoryStart)
	{
		static $duplicates = array();
		
		if (self::$profiler === null) {
			return; // no profiler attached
		}
		
		$timeUsed = microtime(true) - $memoryStart;
		$memoryUsed = memory_get_usage(true) - $memoryStart;
		$hash = md5($event['sql']);
		
		// Add explain details and time/memory metrics
		$event = $this->explainQuery($event);
		$event['time'] = $timeUsed;
		$event['memory'] = $memoryUsed;
		$event['duplicate'] = isset($duplicates[$hash]);
		
		// Keep track of current query to mark as duplicate
		$duplicates[$hash] = true;
		
		if ($event['duplicate']) {
		  self::$profiler->increment(self::PROFILE_TYPE, 'duplicates');
		}
		self::$profiler->addEvent(self::PROFILE_TYPE, $event);
	}
	
	/**
	 * Adds query plan to event details.
	 * 
	 * @param array $event - Event details
	 */
	protected function explainQuery(array $event)
	{
		try {
			$query = 'EXPLAIN ' . $event['sql'];
			$results = parent::query($query);
			
			if (count($results) > 0) {
				$event['explain'] = $results[0];
			}
		}
		catch (PDOException $e) {
			// do nothing--it's only extra information
		}
		
		return $event;
	}
}