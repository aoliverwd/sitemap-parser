# Sitemap Parser Documentation

## Overview
The Sitemap Parser is a PHP class designed to parse XML sitemaps, whether they are local files or hosted URLs. It can handle both standard sitemaps and sitemap indexes, extracting location URLs and their last modification dates.

## Class: `Parser`
Namespace: `AOWD\SitemapParser`

## Features
- Parse XML sitemaps from local files or URLs
- Support for nested sitemaps (sitemap index files)
- Extract URL locations and last modification dates
- Built-in URL validation
- cURL-based URL content fetching
- Error handling with custom exceptions

## Public Methods

### `parser(string $sitemap_location): array`
The main method to parse a sitemap.

#### Parameters:
- `$sitemap_location`: String containing either a file path or URL to the sitemap

#### Returns:
- Array of parsed entries, each containing:
  - `location`: The URL from the sitemap
  - `updated`: The last modification date

#### Example:
```php
$entries = Parser::parser('https://example.com/sitemap.xml');
// or
$entries = Parser::parser('/path/to/local/sitemap.xml');
```

## Error Handling
The class uses a custom `ParserException` class for error handling. Exceptions are thrown for:
- Invalid XML content
- File reading errors
- URL fetching errors

## Requirements
- PHP 8.3 or higher
- cURL extension
- SimpleXML extension

## Example Usage

```php
use AOWD\SitemapParser\Parser;
use AOWD\Exceptions\ParserException;

try {
    // Parse a sitemap
    $entries = Parser::parser('https://example.com/sitemap.xml');

    // Access the parsed entries
    foreach ($entries as $entry) {
        echo "URL: " . $entry['location'] . "\n";
        echo "Last Updated: " . $entry['updated'] . "\n";
    }
} catch (ParserException $e) {
    echo "Error: " . $e->getMessage();
}
```

## Technical Details

### Entry Format
Each parsed entry is stored as an associative array with the following structure:
```php
[
    'location' => 'https://example.com/page',
    'updated' => '2023-01-01T12:00:00+00:00'
]
```

### Supported Sitemap Types
- Standard XML sitemaps
- Sitemap index files
- Nested sitemaps

### Validation
- URL validation using `filter_var()`
- File extension checking (.xml)
- File existence verification for local files

## Best Practices
1. Always wrap parser calls in try-catch blocks
2. Verify sitemap file extensions
3. Ensure proper file permissions for local files
4. Handle potential network issues for remote sitemaps

## Limitations
- Only supports XML format sitemaps
- Requires proper XML formatting
- Network dependent for remote sitemaps

This parser provides a robust solution for handling XML sitemaps in PHP applications, with built-in error handling and support for both local and remote sitemap files.
