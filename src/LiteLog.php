<?php
/**
 * LiteLog - Ultra-fast, Lightweight JSON Logger for PHP
 * 
 * Copyright (C) 2025 Lars Grove Mortensen. All rights reserved.
 * 
 * LiteLog is a simple, high-performance, and lightweight JSON logger
 * for PHP applications, supporting robust log rotation, strict error handling,
 * and PSR-4 compatibility.
 * 
 * LiteLog is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * LiteLog is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with LiteLog. If not, see <https://www.gnu.org/licenses/>.
 */


namespace LiteLog;


use RuntimeException;
use JsonException;


class LiteLog {

	/**
	 * Default directory for log files.
	 *
	 * @var string
	 */
	protected static string $defaultDir = '';
	
	
	/**
	 * Maximum size (in bytes) of a log file before rotation occurs.
	 *
	 * @var int
	 */
	protected static int $maxFileSize = 10485760; // 10 MB



	/**
	 * Sets the default directory for log files.
	 *
	 * @param string $dir Absolute or relative path to the log directory.
	 * @return void
	 */
	public static function setDefaultDir(string $dir): void {
		self::$defaultDir = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR;
	}


	/**
	 * Sets the maximum file size (in bytes) for log rotation.
	 *
	 * @param int $bytes Maximum file size before log rotation.
	 * @return void
	 */
	public static function setMaxFileSize(int $bytes): void {
		self::$maxFileSize = $bytes;
	}


	/**
	 * Log an event as a JSON line in the specified log file.
	 *
	 * @param string $file     Filename (e.g., "app.json")
	 * @param string $category Category (e.g., "error")
	 * @param mixed  $message  String or array
	 * @param array  $context  (Optional) Extra info, e.g., ['ip' => '1.2.3.4']
	 * @throws RuntimeException If log directory does not exist/is not writable, or rotation fails
	 */
	public static function log(
		string $file,
		string $category,
		$message,
		array $context = []
	): void {
	
		// Use the current default directory for logs
		$dir = self::$defaultDir;

		// Fail fast if log directory has not been set
		if (!$dir) {
			throw new RuntimeException("LiteLog: Log directory is not set. Please call setDefaultDir() before logging.");
		}

		// Ensure the log directory exists
		if (!is_dir($dir)) {
			throw new RuntimeException("LiteLog: Log directory '$dir' does not exist.");
		}
		
		// Ensure the log directory is writable
		if (!is_writable($dir)) {
			throw new RuntimeException("LiteLog: Log directory '$dir' is not writable.");
		}

		// Construct full log file path
		$filePath = $dir . $file;
		self::rotateIfNeeded($filePath);

		try {
		
			// Build the log entry array
			$logLine = [
				'timestamp' => date('Y-m-d H:i:s'),
				'category'  => $category,
				'message'   => is_array($message)
					? json_encode($message, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
					: (string)$message,
			];
			
			// Include context only if provided
			if (!empty($context)) {
				$logLine['context'] = $context;
			}
			
			// Encode the entire log entry as a JSON line
			$json = json_encode($logLine, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL;
			
			
		} catch (JsonException $e) {
		
			// Fail loudly if JSON encoding fails
			throw new RuntimeException('LiteLog: Failed to encode log entry as JSON - ' . $e->getMessage());
		}

		
		// Write the JSON log entry to file (atomic append with exclusive lock)
		if (file_put_contents($filePath, $json, FILE_APPEND | LOCK_EX) === false) {	
			throw new RuntimeException("LiteLog: Failed to write to log file '$filePath'.");
		}
	}


	/**
	 * Rotates the log file if it exceeds the maximum allowed size.
	 *
	 * Generates a new filename with a microsecond-based suffix to avoid collisions,
	 * then atomically renames (moves) the original file. Throws an exception if the
	 * rotation fails.
	 *
	 * @param string $filePath Absolute path to the log file.
	 * @throws RuntimeException If the log file cannot be renamed/moved.
	 * @return void
	 */
	protected static function rotateIfNeeded(string $filePath): void {
	
		// Refresh file status cache to ensure we get up-to-date file size
		clearstatcache(true, $filePath);

		// Only rotate if file exists AND exceeds the max allowed size
		if (file_exists($filePath) && filesize($filePath) >= self::$maxFileSize) {
			$info = pathinfo($filePath);
			
			// Generate a unique filename suffix using microseconds
			$micro = str_replace('.', '_', number_format(microtime(true), 3, '.', ''));
			$newName = sprintf(
				'%s_%s.json',
				$info['filename'],
				$micro
			);
			$newPath = $info['dirname'] . DIRECTORY_SEPARATOR . $newName;

			// Atomically move (rename) the old log file to the new rotated filename
			if (!rename($filePath, $newPath)) {
				$error = error_get_last();
				
				// Fail loudly if the rotation (move) operation does not succeed
				throw new RuntimeException(
					"LiteLog: Failed to rotate log file to '$newPath'. Error: " . ($error['message'] ?? 'Unknown')
				);
			}
		}
	}
}
