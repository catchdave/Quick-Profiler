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
	 * Output buffer
	 * @var array
	 */
	public $output = array();
	
	/**
	 * @param float $startTime        	
	 */
	public function __construct($startTime = null)
	{
		$this->startTime = $startTime ?: microtime(true);
	}
	
	/**
	 * Format the different types of logs
	 */
	public function gatherConsoleData()
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
		$this->output['logs'] = $logs;
	}
	
	/**
	 * Aggregate data on the files included
	 */
	public function gatherFileData()
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
		$this->output['files'] = $fileList;
		$this->output['fileTotals'] = $fileTotals;
	}
	
	/**
	 * Memory usage and memory available
	 */
	public function gatherMemoryData()
	{
		$memoryTotals = array();
		$memoryTotals['used'] = $this->getReadableFileSize(memory_get_peak_usage());
		$memoryTotals['total'] = ini_get('memory_limit');
		$this->output['memoryTotals'] = $memoryTotals;
	}
	
	/**
	 * Query data -- database object with logging required
	 */
	public function gatherQueryData()
	{
		$queryTotals = array();
		$queryTotals['count'] = 0;
		$queryTotals['time'] = 0;
		$queries = array();
		
		$queryTotals['time'] = $this->getReadableTime($queryTotals['time']);
		$this->output['queries'] = $queries;
		$this->output['queryTotals'] = $queryTotals;
	}
	
	/**
	 * Speed data for entire page load
	 */
	public function gatherSpeedData()
	{
		$speedTotals = array();
		$speedTotals['total'] = $this->getReadableTime(microtime(true) - $this->startTime);
		$speedTotals['allowed'] = ini_get('max_execution_time');
		$this->output['speedTotals'] = $speedTotals;
	}
	
	/**
	 * Adapted from code at
	 * http://aidanlister.com/repos/v/function.size_readable.php
	 *
	 * @param integer $size        	
	 * @param unknown_type $retString        	
	 */
	public function getReadableFileSize($size, $retString = null)
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
	public function getReadableTime($time)
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
	
	/**
	 * Display to the screen -- call when code terminating.
	 */
	public function render()
	{
		$this->gatherConsoleData();
		$this->gatherFileData();
		$this->gatherMemoryData();
		$this->gatherQueryData();
		$this->gatherSpeedData();
		
		// TODO: Use Smarty to render $this->output
	}

}

?>