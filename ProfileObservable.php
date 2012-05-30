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
   * @param array $details
   * @param float $time
   * @param integer $memory
   */
  public function addProfileEvent(array $details, $time, $memory);
}
