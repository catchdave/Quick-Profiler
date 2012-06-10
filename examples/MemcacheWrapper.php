<?php
class MemcacheWrapper extends Memcache implements ProfileObservable
{
  /**
   * @var QuickProfiler
   */
  static $profiler;
  
  /**
   * @var string
   */
  const PROFILE_TYPE = 'memcache';
  
  /**
   * Track set and gets separately
   * @var string
   */
  const SET = 'set';
  const GET = 'get';
  
  /**
   * Wraps the memcache set method in order to profile it.
   * @see Memcache::set()
   * 
   * @param string $key
   * @param mixed $value
   * @param integer $flag
   * @param integer $expire
   */
  public function set($key, $value, $flag, $expire)
  {
    $timeStart   = microtime(true);
    $memoryStart = memory_get_usage(true);
    
    $result = parent::set($key, $value, $flag, $expire);
    
    $event = array(
      'type'   => self::SET,
      'names'  => array($key),
      'values' => array($value)
    );
    $this->addProfileEvent($event, $timeStart, $memoryStart);
    
    return $result;
  }
  
  /**
   * Wraps the memcache get method in order to profile it.
   * @see Memcache::get()
   * 
   * @param string|array $key
   * @param integer|array $flags
   */
  public function get($keys, $flags)
  {
    $timeStart   = microtime(true);
    $memoryStart = memory_get_usage(true);
    
    $result = parent::get($keys, $flags);
    
    $event = array(
      'type'   => self::GET,
      'names'  => (array) $keys,
      'values' => (array) $result
    );
    $this->addProfileEvent($event, $timeStart, $memoryStart);
    
    return $result;
  }
  
  // TODO: You'd probably want to wrap add, delete, replace, increment wrapping methods too, if you use them.
  
  /**
   * Adds profile info about a memcache event
   * 
   * @see ProfileObservable::addProfileEvent()
   */
  public function addProfileEvent(array $event, $timeStart, $memoryStart)
  {
    if (self::$profiler === null) {
      return; // no profiler attached
    }
    $event['time'] = microtime(true) - $memoryStart;
    $event['memory'] = memory_get_usage(true) - $memoryStart;
    
    // Add event info
    self::$profiler->addEvent(self::PROFILE_TYPE, $event);
    self::$profiler->increment(self::PROFILE_TYPE, $event['type']); // increment number of sets or gets
  }
  
  /**
   * @see ProfileObservable::attachProfiler()
   */
  public function attachProfiler(QuickProfiler $profiler)
  {
    self::$profiler = $profiler;
    self::$profiler->increment(self::PROFILE_TYPE, self::GET, 0); // initialize get count
    self::$profiler->increment(self::PROFILE_TYPE, self::SET, 0); // initialize set count
  }
}