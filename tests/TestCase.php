<?php

namespace SHTayeb\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    public function __construct(string $name)
    {
        parent::__construct($name);
    }


    public function getEnvironmentSetUp($app)
    {
        config([
            'words_per_minute' =>200,
            'codewords_per_minute' => 200,
            'seconds_per_image' => 12
        ]);
    }

    protected array $words = [
        'consectetur',
        'vestibulum',
        'Lorem',
        'amet',
        'sit',
        'at',
        'a',
    ];

    /*
     * create a text that has $words words
     */
    public function words($words = 200): string
    {
        $text = "";
        $wordCount = count($this->words) - 1;

        for ($i = 0; $i < $words; $i++) {
            $text .= $this->words[rand(0, $wordCount)] . ' ';
        }

        return $text;
    }
    /*
     * create a text that takes $readingTime minutes to read at a speed of $wordsPerMinute
     */
    public function minutesOfText($readingTime = 1, $wordsPerMinute = 200): string
    {
        $totalWords = $readingTime * $wordsPerMinute;

        return $this->words($totalWords);
    }
}
