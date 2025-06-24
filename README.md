# LiteLog - Ultra-fast, Lightweight JSON Logger for PHP

LiteLog is an ultra-fast, lightweight logger for PHP applications. It writes single-line JSON entries to log files, supports safe log rotation, and throws exceptions on all errors for maximum robustness.

---

## Features

✅ Super-fast and minimal overhead\
✅ Atomic writes with exclusive lock\
✅ Automatic log rotation on file size\
✅ Strict error handling (never fails silently)\
✅ PSR-4 compatible, no external dependencies\
✅ Works with PHP 7.4 and newer

---

## Installation

### Manual installation

Copy `LiteLog.php` into your project (for example: `src/LiteLog/LiteLog.php`). Set up PSR-4 autoloading in your `composer.json`:

```json
"autoload": {
    "psr-4": {
        "LiteLog\\": "src/LiteLog/"
    }
}
```

Then run:

```
composer dump-autoload
```

*LiteLog will be available via autoload.*

---

## Usage

### Basic logging

```php
use LiteLog\LiteLog;

// Set the log directory (must exist and be writable)
LiteLog::setDefaultDir(__DIR__ . '/logs/');

// (Optional) Set max file size for log rotation (in bytes)
LiteLog::setMaxFileSize(5 * 1024 * 1024); // 5 MB

// Log a simple message
LiteLog::log('app.json', 'info', 'Application started');
```

### Log with context (e.g. IP address, user id)

```php
LiteLog::log('app.json', 'auth', 'User login', ['ip' => '203.0.113.1', 'user_id' => 42]);
```

### Log an array as message

```php
LiteLog::log('events.json', 'debug', ['event' => 'test', 'value' => 1234]);
```

---

## Example log entry

```json
{
  "timestamp": "2025-06-24 14:31:05",
  "category": "auth",
  "message": "User login",
  "context": {
    "ip": "203.0.113.1",
    "user_id": 42
  }
}
```

---

## How it works

- **Directory must exist:** LiteLog never creates directories. If the log directory does not exist or is not writable, an exception is thrown.
- **JSON log format:** Each log entry is a single JSON line, including timestamp, category, message, and optional context.
- **Log rotation:** When a log file exceeds the maximum size, it is atomically renamed (with a unique microsecond suffix) and a new file is started.
- **Strict error handling:** Any failure (file, directory, JSON, etc.) throws an exception—let your global error handler or framework handle it.

---

## 💡 Why LiteLog?

Unlike heavy logging frameworks, LiteLog is built for speed, reliability, and developer control. No magic, no global state, just clear, safe, high-performance logging. Use it as the backbone for custom frameworks, microservices, or any PHP app where you want full logging control.

---

## Recommended setup

- Set the log directory in your application's bootstrap or kernel before any logging is performed.
- Use absolute paths for the log directory (recommended for CLI and webserver consistency).
- Handle exceptions from LiteLog with your global error handler.

---

## License

LiteLog is released under the **GNU General Public License v3.0**. See [LICENSE](LICENSE) for details.

---

## Contributing

Contributions are welcome! Feel free to fork this repository, submit issues, or open a pull request.

---

## Author

Developed by **Lars Grove Mortensen** © 2025. Feel free to reach out or contribute!

---

🌟 **If you find this library useful, give it a star on GitHub!** 🌟

