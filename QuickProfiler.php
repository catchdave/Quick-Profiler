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
		$this->gatherSpeedData();
		
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
		
		// TODO: Standardise types and separate totals from data
		$this->data[$type.'Totals']++;
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
					$logs['console'][$key]['data'] = $this->getReadableFileSize($log['data']);
				} elseif ($log['type'] == 'speed') {
					$logs['console'][$key]['data'] = $this->getReadableTime($log['data'] - $this->startTime);
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
		$files = get_included_files();
		$fileList = array();
		$fileTotals = array(
			'count' => count($files),
			'size' => 0,
			'largest' => 0
		);
	
		foreach ($files as $key => $file) {
			$size = filesize($file);
			$fileList[] = array(
				'name' => $file,
				'size' => $this->getReadableFileSize($size)
			);
			$fileTotals['size'] += $size;
			if ($size > $fileTotals['largest'])
				$fileTotals['largest'] = $size;
		}
	
		$fileTotals['size'] = $this->getReadableFileSize($fileTotals['size']);
		$fileTotals['largest'] = $this->getReadableFileSize($fileTotals['largest']);
		$this->data['files'] = $fileList;
		$this->data['fileTotals'] = $fileTotals;
	}
	
	/**
	 * Memory usage and memory available
	 */
	protected function gatherMemoryData()
	{
		$memoryTotals = array();
		$memoryTotals['used'] = $this->getReadableFileSize(memory_get_peak_usage());
		$memoryTotals['total'] = ini_get('memory_limit');
		$this->data['memoryTotals'] = $memoryTotals;
	}
	
	/**
	 * Speed data for entire page load
	 */
	protected function gatherSpeedData()
	{
		$speedTotals = array();
		$speedTotals['total'] = $this->getReadableTime(microtime(true) - $this->startTime);
		$speedTotals['allowed'] = ini_get('max_execution_time');
		$this->data['speedTotals'] = $speedTotals;
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