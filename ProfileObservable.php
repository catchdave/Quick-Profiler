<?php
/**
 * Classes that want to be monitored by the QuickProfiler
 * should implement this interface.
 * 
 * @author David Rochwerger <catch.dave@gmail.com>
 */
interface ProfileObservable
{
	
	/**
	 *
	 * @param QuickProfiler $profiler        	
	 */
	public function attachProfiler(QuickProfiler $profiler);
	
	/**
	 * Add an event to the profiler
	 *
	 * @param array $event - Array of event details
	 * @param float $time - Start time of event
	 * @param integer $memory - Start memory usage of event
	 */
	public function addProfileEvent(array $event, $timeStart, $memoryStart);
}
