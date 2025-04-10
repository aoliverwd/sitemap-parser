<?php

namespace AOWD\SitemapParser;

use AOWD\SitemapParser\Exceptions\ParserException;

class Parser
{
    /** @var array<mixed> */
    private static array $entries = [];

    /**
     * Parses a sitemap file or URL.
     *
     * @param string $sitemap_location The path to the sitemap file or URL.
     * @return array<mixed> The parsed entries.
     */
    public static function parse(string $sitemap_location): array
    {
        // Get the extension of the sitemap file
        $extension = pathinfo($sitemap_location, PATHINFO_EXTENSION);

        // Check if the provided sitemap location is a valid URL or a local file
        if (filter_var($sitemap_location, FILTER_VALIDATE_URL) && $extension === "xml") {
            self::processSiteMapURL($sitemap_location);
        } elseif ($extension === "xml") {
            self::processSitemapFile($sitemap_location);
        }

        // Return the parsed entries
        return self::$entries;
    }

    /**
     * Returns the parsed entries.
     *
     * @return array<mixed> The parsed entries.
     */
    public static function getEntries(): array
    {
        return self::$entries;
    }

    /**
     * Fetches the contents of a URL using cURL.
     *
     * @param string $sitemap_url The URL to fetch.
     * @return string The contents of the URL or false on error.
     */
    private static function fetchUrlContents(string $sitemap_url): string
    {
        // Check if the URL is empty
        if (empty($sitemap_url) || !filter_var($sitemap_url, FILTER_VALIDATE_URL)) {
            return "";
        }

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $sitemap_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        // Execute the request
        $response = curl_exec($ch);

        // Response code
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check response code
        if ($responseCode >= 300) {
            return "";
        }

        // Check for errors
        if (curl_errno($ch)) {
            echo "cURL Error: " . curl_error($ch);
            return "";
        }

        // Close cURL session
        curl_close($ch);
        return is_string($response) ? $response : "";
    }

    /**
     * Processes a sitemap URL.
     *
     * @param string $sitemap_url The URL of the sitemap.
     */
    private static function processSiteMapURL(string $sitemap_url): void
    {
        try {
            $xml = new \SimpleXMLElement(self::fetchUrlContents($sitemap_url));
            self::processXMLEntries($xml);
        } catch (\Exception $e) {
            throw new ParserException("Error processing sitemap: " . $e->getMessage());
        }
    }

    /**
     * Processes a sitemap file.
     *
     * @param string $sitemap_file The path to the sitemap file.
     */
    private static function processSitemapFile(string $sitemap_file): void
    {
        if (!file_exists($sitemap_file)) {
            throw new ParserException("Sitemap file does not exist");
        }

        try {
            $xml = new \SimpleXMLElement((string) file_get_contents($sitemap_file));
            self::processXMLEntries($xml);
        } catch (\Exception $e) {
            throw new ParserException("Error processing sitemap file: " . $e->getMessage());
        }
    }

    /**
     * Processes XML entries.
     *
     * @param \SimpleXMLElement $xml The XML element.
     */
    private static function processXMLEntries(\SimpleXMLElement $xml): void
    {
        $locations = property_exists($xml, "url") ? $xml->url : (property_exists($xml, "sitemap") ? $xml->sitemap : []);
        $type = property_exists($xml, "url") ? "url" : "sitemap";

        if (!empty($locations)) {
            foreach ($locations as $location) {
                $loc = (string) $location->loc;
                $updated = (string) $location->lastmod;

                // Check if the location is a sitemap URL
                if ($type === "sitemap" && pathinfo($loc, PATHINFO_EXTENSION) === "xml") {
                    self::processSiteMapURL($loc);
                } else {
                    self::$entries[] = [
                        "location" => $loc,
                        "updated" => $updated,
                    ];
                }
            }
        }
    }
}
