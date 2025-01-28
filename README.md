# LiteLog - Lightweight Logging Utility for PHP  

LiteLog is a simple, efficient logging function for PHP applications. It supports JSON-formatted log entries, automatic log rotation, and IP tracking, making it easy to maintain structured logs for debugging and monitoring.  

## Features  
✅ Simple and lightweight logging function  
✅ Logs messages in **JSON format** for easy parsing  
✅ **Supports structured logs** with categories and contexts  
✅ **Automatic log rotation** when file size exceeds limit  
✅ **IP tracking** for identifying request sources  
✅ Compatible with **PHP 7.4+**  

## Installation  
Simply include `LiteLog.php` in your project:  

```php
require_once 'LiteLog.php';
```

## Usage  

### Basic Logging  
Log a simple string message:  

```php
LiteLog("User login successful", "auth");
```

### Structured Logging  
Log a structured array message:  

```php
LiteLog([
	"response" => 500,
	"db_result" => ["error_msg" => ["Database connection timeout"]],
	"mail_result" => ["error_msg" => ["Could not connect to mail server"]]
], "system_error");
```

### Custom Log File and Directory  
Specify a custom log file, directory, and file size limit:  

```php
LiteLog("System started", "system", "custom_log.json", "/var/logs", [], "192.168.1.10", 5 * 1024 * 1024);
```

### Log Rotation Test  
Ensure log files are rotated when reaching the size limit:  

```php
for ($i = 0; $i < 100; $i++) {
	LiteLog("Log entry #" . $i, "debug", "test_log.json", "logs/", [], "192.168.1.102", 102400);
}
```

### JSON Validity Check  
Ensure logs are valid JSON:  

```php
$log_files = glob("logs/*.json");

foreach ($log_files as $file) {
	$log_content = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	foreach ($log_content as $line) {
		if (json_decode($line, true) === null && json_last_error() !== JSON_ERROR_NONE) {
			echo "JSON Decode Error in $file: " . json_last_error_msg() . "\n";
		}
	}
}
```

## Why LiteLog?  
Unlike complex logging libraries, LiteLog is designed to be **fast and efficient** with minimal overhead. It provides **structured JSON logging**, log rotation, and IP tracking in a simple, **single-file** PHP function.  

## License  
LiteLog is released under the **GNU General Public License v3.0**. See [LICENSE](LICENSE) for details.  

## Contributing  
Contributions are welcome! Feel free to fork this repository, submit issues, or open a pull request.  

## Author  
Developed by **Lars Grove Mortensen** © 2025.  

---  

🌟 **If you find this library useful, give it a star on GitHub!** 🌟  

---
