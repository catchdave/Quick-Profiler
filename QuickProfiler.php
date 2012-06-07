<?php

/**
 * Profile time and memory and provides a console.
 * 
 * @author Ryan Campbell. April 22, 2009
 * @author Modifed by David Rochwerger <catch.dave@gmail.com>
 * @see http://particletree.com
 */

class QuickProfiler
{
	/**
	 * Profile data
	 * @var array
	 */
	protected $data = array();
	
	/**
	 * Totals of data
	 * @var array
	 */
	protected $totals = array();
	
	/**
	 * @param float $startTime        	
	 */
	public function __construct($startTime = null)
	{
		$this->startTime = $startTime ?: microtime(true);
	}
	
	/**
	 * Display to the screen -- call when code terminating.
	 */
	public function render()
	{
		$this->gatherConsoleData();
		$this->gatherFileData();
		$this->gatherMemoryData();
		$this->gatherTimeData();
		
		// TODO: Use Smarty to render $this->data
	}
	
	/**
	 * Add a profile event from an external class that is being observed
	 * by this profiler.
	 * 
	 * @param string $type
	 * @param array $event
	 */
	public function addEvent($type, array $event)
	{
		if (!empty($event['memory'])) {
			$event['memory'] = $this->getReadableFileSize($event['memory']);
		}
		if (!empty($event['time'])) {
			$event['time'] = $this->getReadableTime($event['time']);
		}
		
		$this->data[$type][] = $event;
		$this->increment($type, 'count');
	}
	
	/**
	 * Increments an arbitray total.
	 * 
	 * @param string $type
	 * @param string $key
	 * @param integer $value
	 */
	public function increment($type, $key, $value = 1)
	{
	  if (isset($this->totals[$type][$key])) {
	    $this->totals[$type][$key] += $value;
	  } else {
	    $this->totals[$type][$key] = $value;
	  }
	}
	
	/**
	 * Format the different types of logs
	 */
	protected function gatherConsoleData()
	{
		$logs = Console::getLogs();
		if ($logs['console']) {
			foreach ( $logs['console'] as $key => $log ) {
				if ($log['type'] == 'log') {
					$logs['console'][$key]['data'] = print_r($log['data'], true);
				} elseif ($log['type'] == 'memory') {
					$logs['console'][$key]['memory'] = $this->getReadableFileSize($log['data']);
				} elseif ($log['type'] == 'time') {
					$logs['console'][$key]['time'] = $this->getReadableTime($log['data'] - $this->startTime);
				}
			}
		}
		$this->data['logs'] = $logs;
	}
	
	/**
	 * Aggregate data on the files included
	 */
	protected function gatherFileData()
	{
		$fileList = array();
		$largest = $totalSize = 0;
	
		$files = get_included_files();
		foreach ($files as $curFile) {
			$size = filesize($curFile);
			$fileList[] = array(
				'name' => $curFile,
				'size' => $this->getReadableFileSize($size)
			);

			$totalSize += $size;
			$largest = max($largest, $size);
		}
	
		$this->data['files'] = $fileList;
		$this->totals['files'] = array(
			'size'    => $this->getReadableFileSize($totalSize),
			'largest' => $this->getReadableFileSize($largest),
			'count'   => count($files)
		);
	}

	/**
	 * Aggregate classes defined
	 */
	protected function gatherClassData()
	{
		$classList = array();
		$largest = $totalLines = $count = 0;
	
		// Get user defined classes
		foreach (get_defined_classes() as $className) {
			$reflect = new ReflectionClass($className);
			if (!$reflect->isUserDefined()) {
				continue; // skip built-in classes
			}

			// Get approx lines of code (includes comments)
			$lines = $reflect->getEndLine() - $reflect->getStartLine();
			$largest = max($largest, $lines);
			$totalLines += $lines;	
			$count++;

			$classList[] = array(
				'name'  => $className,
				'lines' => number_format($lines)
			);
		}

		$this->data['classes'] = $classList;
		$this->totals['classes'] = array(
			'largest' => number_format($largest),
			'lines'   => number_format($totalLines),
			'count'   => $count
		);
	}
	
	/**
	 * Memory usage and memory available
	 */
	protected function gatherMemoryData()
	{
		$memoryTotals = array();
		$memoryTotals['used'] = $this->getReadableFileSize(memory_get_peak_usage());
		$memoryTotals['total'] = ini_get('memory_limit');
		$this->totals['memory'] = $memoryTotals;
	}
	
	/**
	 * Time data for entire page load
	 */
	protected function gatherTimeData()
	{
		$timeTotals = array();
		$timeTotals['total'] = $this->getReadableTime(microtime(true) - $this->startTime);
		$timeTotals['allowed'] = ini_get('max_execution_time');
		$this->totals['time'] = $timeTotals;
	}

	/**
	 * Adapted from code at
	 * http://aidanlister.com/repos/v/function.size_readable.php
	 *
	 * @param integer $size
	 * @param unknown_type $retString
	 */
	protected function getReadableFileSize($size, $retString = null)
	{
		$sizes = array('bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
		$lastSizeString = end($sizes);
	
		foreach ($sizes as $sizeString) {
			if ($size < 1024) {
				break;
			}
			if ($sizeString != $lastSizeString) {
				$size /= 1024;
			}
		}
		if ($sizeString == $sizes[0]) {
			$retString = '%01d %s'; // Bytes aren't normally fractional
		} else {
			$retString = '%01.2f %s';
		}
		return sprintf($retString, $size, $sizeString);
	}
	
	/**
	 * Formats time
	 *
	 * @param float $time
	 */
	protected function getReadableTime($time)
	{
		$formats = array('ms', 's', 'm');
		if (abs($time) >= 60) {
			$formatter = 2;
			$time /= 60;
		} elseif (abs($time) > 1) {
			$formatter = 1;
		} else {
			$formatter = 0;
			$time *= 1000;
		}
	
		return number_format($time, 3, '.', '') . ' ' . $formats[$formatter];
	}
}

?>
