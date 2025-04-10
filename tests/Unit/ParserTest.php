<?php

test("it parses a valid XML sitemap file", function () {
    $result = \AOWD\SitemapParser\Parser::parse(getTestDataLocation());
    expect($result)->toBeArray();
});

test("it parses a valid XML sitemap URL", function () {
    $result = \AOWD\SitemapParser\Parser::parse("http://localhost:3000/index.xml");
    expect($result)->toBeArray();
});

test("Its throws an exception when the file is not found", function () {
    try {
        \AOWD\SitemapParser\Parser::parse(__DIR__ . "/fixtures/nonexistent.xml");
    } catch (\AOWD\SitemapParser\Exceptions\ParserException $e) {
        expect($e->getMessage())->toBe("Sitemap file does not exist");
    }
});
