<?php

/*
 * Copyright (c) Jeroen Visser <jeroenvisser101@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Feature;

use SHTayeb\Tests\TestCase;
use SHTayeb\Bookworm\Bookworm;

class BookwormTest extends TestCase
{
    public function __construct(string $name)
    {
        parent::__construct($name);
    }
    /**
     * Tests if the reading time doesn't reach 0 min.
     */
    public function testLessThanMinute()
    {
        $story = $this->words(116);

        $readingTime = (new Bookworm())->estimate($story);

        $this->assertEquals('1 minute', $readingTime, 'Text with less than a minute to read does not return 1 min.');
    }

    /**
     * Tests if bookworm can properly round to 1 min.
     */
    public function testMinute()
    {
        $story = $this->minutesOfText(1, 200);

        $readingTime = (new Bookworm())->estimate($story, ' min');

        $this->assertEquals('1 min', $readingTime, 'Text with a minute to read does not return 1 min.');
    }

    /**
     * Check rounding and large text.
     */
    public function testEightMinutes()
    {
        $story = $this->minutesOfText(8, 200);

        $lang = array(0 => ' minute', 1 => ' minutes');

        $readingTime = (new Bookworm())->estimate($story, $lang);

        $this->assertEquals('8 minutes', $readingTime, 'Text with 8 minutes to read does not return 8 min.');
    }

    /**
     * Tests if bookworm can properly work with markdown.
     */
    public function testMarkdown()
    {
        // 215 words
        $story = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus auctor leo mauris, quis rutrum mi vulputate vitae. Aliquam nec *lacus augue*. Ut diam nisl, porttitor sit amet mattis eget, vulputate nec mi. Curabitur mi augue, aliquam a fringilla in, sollicitudin vitae sem. Fusce at convallis orci. Curabitur commodo blandit nulla in dignissim. Sed tempus sagittis imperdiet. Nullam in purus nec nibh varius molestie. Pellentesque vel consequat urna. Sed tristique quam justo, vel vestibulum lorem porttitor et. Fusce laoreet, lorem et elementum aliquet, neque nulla imperdiet arcu, a ullamcorper libero leo quis turpis. Fusce feugiat, tellus sit amet varius **vehicula**, massa magna consectetur nulla, non ornare justo urna non velit. Praesent rutrum nisi dignissim enim eleifend egestas.
        <img src="http://test.com/image.jpg" />
        > Aliquam nec lacus augue. Ut diam nisl, porttitor sit amet mattis eget, vulputate nec mi. Curabitur mi augue, aliquam a fringilla in, sollicitudin vitae sem. Fusce at convallis orci. Curabitur commodo blandit nulla in dignissim. Sed tempus sagittis imperdiet.
        #Nullam in purus
        Nec nibh varius molestie. Pellentesque vel consequat urna. Sed tristique quam justo, vel vestibulum lorem porttitor et. Fusce laoreet, lorem et elementum aliquet, neque nulla imperdiet arcu, a ullamcorper libero leo quis turpis. Fusce feugiat, tellus sit amet varius vehicula. ![image](http://test.com/image.jpg)
        ```bash
        $ composer install
        $ composer update
        ```
        <img src="http://test.com/image.jpg" />
        <img src="http://test.com/image.jpg" />
        ## massa magna consectetur
        <picture>
            <source srcset="examples/images/extralarge.jpg" media="(min-width: 1000px)">
            <source srcset="examples/images/art-large.jpg" media="(min-width: 800px)">
            <img srcset="examples/images/art-medium.jpg" alt="…">
        </picture>
        Nulla, non ornare justo urna non velit. Praesent rutrum nisi dignissim enim eleifend egestas. Nulla, non ornare justo urna non velit. Praesent rutrum nisi dignissim enim eleifend egestas. Nulla, non ornare justo urna non velit. Praesent rutrum nisi dignissim enim eleifend egestas. Praesent rutrum nisi dignissim enim eleifend egestas. Nulla, non ornare justo urna non velit. Praesent rutrum nisi dignissim enim eleifend egestas. Praesent rutrum nisi dignissim enim eleifend egestas. Nulla, non ornare justo urna non velit. Praesent rutrum nisi dignissim enim eleifend egestas. Praesent rutrum nisi dignissim enim eleifend egestas. Nulla velit. Nulla, non ornare justo urna non velit. Praesent rutrum nisi dignissim enim eleifend egestas. Nulla, non ornare justo urna non velit. Praesent rutrum nisi dignissim enim eleifend egestas. Nulla, non ornare justo urna non velit.';

        $readingTime = (new Bookworm())->estimate($story);

        $this->assertEquals('3 minutes', $readingTime, 'Markdown does not return correct timing.');
    }

    /**
     * Tests if the reading time units is set to false.
     */
    public function testNoUnits()
    {
        $story = $this->minutesOfText(1, 200);

        $readingTime = (new Bookworm())->estimate($story, false);

        $this->assertEquals('1', $readingTime, 'Text with less than a minute & units set to false does not return 1.');
    }

    /**
     * Tests the image time config.
     */
    public function testImageTime()
    {
        // 116 words
        $story = '![image](http://test.com/image.jpg) <img src="http://test.com/image.jpg" />';

    
        $bookworm = new Bookworm();


        $readingTime = $bookworm->estimate($story, false);

        $this->assertEquals('1', $readingTime, 'Two images with time set to 1 min each do not return 2.');
    }
    /**
     * Tests the code time config.
     */
    public function testCodeTimeMarkdown()
    {
        // code
        $story = '`test` Lorem Ipsum ```html <svg id="rainsuncloud" xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"> <g id="sun" fill="#FFC300"> <path d="M28.4 40.2C28.4 34.5 33 30 38.7 30c3.7 0 7 2 8.7 5 .6-.5 1.7-1 3.4-1.5-2.4-4.3-7-7.2-12.2-7.2-7.7 0-14 6.2-14 14 0 1.5.4 3 1 4.5.4-.5 1.4-1.3 3-2l-.2-2.6zM38.7 23.7c1.2 0 2-1 2-2v-7.3c0-1.2-.8-2-2-2s-2 .8-2 2v7.2c0 1.2.8 2 2 2zM22.2 40.2c0-1.2-1-2-2-2h-7.3c-1.3 0-2.2.8-2.2 2s1 2 2 2h7.3c1.3 0 2.2-.8 2.2-2zM53.3 28.5l5-5c1-1 1-2.3 0-3-.7-1-2-1-3 0l-5 5c-.8.8-.8 2.2 0 3 1 1 2.2 1 3 0zM24 28.5c.8.8 2.2.8 3 0 .8-.8.8-2.2 0-3l-5-5c-1-1-2.3-1-3 0-1 .7-1 2 0 3l5 5z"/> </g> <path id="rain4" fill="#00B5E1" d="M31.8 79c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain3" fill="#00B5E1" d="M45.6 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.6 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain2" fill="#00B5E1" d="M65.2 84c0 1.7-1.5 3-3.2 3s-3.2-1.4-3.2-3 2.5-4.7 3.2-6.3c.6 1.5 3.2 4.4 3.2 6.2z"/> <path id="rain1" fill="#00B5E1" d="M80.8 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> </svg>``` ```<svg id="rainsuncloud" xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"> <g id="sun" fill="#FFC300"> <path d="M28.4 40.2C28.4 34.5 33 30 38.7 30c3.7 0 7 2 8.7 5 .6-.5 1.7-1 3.4-1.5-2.4-4.3-7-7.2-12.2-7.2-7.7 0-14 6.2-14 14 0 1.5.4 3 1 4.5.4-.5 1.4-1.3 3-2l-.2-2.6zM38.7 23.7c1.2 0 2-1 2-2v-7.3c0-1.2-.8-2-2-2s-2 .8-2 2v7.2c0 1.2.8 2 2 2zM22.2 40.2c0-1.2-1-2-2-2h-7.3c-1.3 0-2.2.8-2.2 2s1 2 2 2h7.3c1.3 0 2.2-.8 2.2-2zM53.3 28.5l5-5c1-1 1-2.3 0-3-.7-1-2-1-3 0l-5 5c-.8.8-.8 2.2 0 3 1 1 2.2 1 3 0zM24 28.5c.8.8 2.2.8 3 0 .8-.8.8-2.2 0-3l-5-5c-1-1-2.3-1-3 0-1 .7-1 2 0 3l5 5z"/> </g> <path id="rain4" fill="#00B5E1" d="M31.8 79c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain3" fill="#00B5E1" d="M45.6 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.6 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain2" fill="#00B5E1" d="M65.2 84c0 1.7-1.5 3-3.2 3s-3.2-1.4-3.2-3 2.5-4.7 3.2-6.3c.6 1.5 3.2 4.4 3.2 6.2z"/> <path id="rain1" fill="#00B5E1" d="M80.8 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> </svg>``` ```<svg id="rainsuncloud" xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"> <g id="sun" fill="#FFC300"> <path d="M28.4 40.2C28.4 34.5 33 30 38.7 30c3.7 0 7 2 8.7 5 .6-.5 1.7-1 3.4-1.5-2.4-4.3-7-7.2-12.2-7.2-7.7 0-14 6.2-14 14 0 1.5.4 3 1 4.5.4-.5 1.4-1.3 3-2l-.2-2.6zM38.7 23.7c1.2 0 2-1 2-2v-7.3c0-1.2-.8-2-2-2s-2 .8-2 2v7.2c0 1.2.8 2 2 2zM22.2 40.2c0-1.2-1-2-2-2h-7.3c-1.3 0-2.2.8-2.2 2s1 2 2 2h7.3c1.3 0 2.2-.8 2.2-2zM53.3 28.5l5-5c1-1 1-2.3 0-3-.7-1-2-1-3 0l-5 5c-.8.8-.8 2.2 0 3 1 1 2.2 1 3 0zM24 28.5c.8.8 2.2.8 3 0 .8-.8.8-2.2 0-3l-5-5c-1-1-2.3-1-3 0-1 .7-1 2 0 3l5 5z"/> </g> <path id="rain4" fill="#00B5E1" d="M31.8 79c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain3" fill="#00B5E1" d="M45.6 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.6 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain2" fill="#00B5E1" d="M65.2 84c0 1.7-1.5 3-3.2 3s-3.2-1.4-3.2-3 2.5-4.7 3.2-6.3c.6 1.5 3.2 4.4 3.2 6.2z"/> <path id="rain1" fill="#00B5E1" d="M80.8 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> </svg> <svg id="rainsuncloud" xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"> <g id="sun" fill="#FFC300"> <path d="M28.4 40.2C28.4 34.5 33 30 38.7 30c3.7 0 7 2 8.7 5 .6-.5 1.7-1 3.4-1.5-2.4-4.3-7-7.2-12.2-7.2-7.7 0-14 6.2-14 14 0 1.5.4 3 1 4.5.4-.5 1.4-1.3 3-2l-.2-2.6zM38.7 23.7c1.2 0 2-1 2-2v-7.3c0-1.2-.8-2-2-2s-2 .8-2 2v7.2c0 1.2.8 2 2 2zM22.2 40.2c0-1.2-1-2-2-2h-7.3c-1.3 0-2.2.8-2.2 2s1 2 2 2h7.3c1.3 0 2.2-.8 2.2-2zM53.3 28.5l5-5c1-1 1-2.3 0-3-.7-1-2-1-3 0l-5 5c-.8.8-.8 2.2 0 3 1 1 2.2 1 3 0zM24 28.5c.8.8 2.2.8 3 0 .8-.8.8-2.2 0-3l-5-5c-1-1-2.3-1-3 0-1 .7-1 2 0 3l5 5z"/> </g> <path id="rain4" fill="#00B5E1" d="M31.8 79c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain3" fill="#00B5E1" d="M45.6 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.6 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain2" fill="#00B5E1" d="M65.2 84c0 1.7-1.5 3-3.2 3s-3.2-1.4-3.2-3 2.5-4.7 3.2-6.3c.6 1.5 3.2 4.4 3.2 6.2z"/> <path id="rain1" fill="#00B5E1" d="M80.8 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> </svg> <path id="rain2" fill="#00B5E1" d="M65.2 84c0 1.7-1.5 3-3.2 3s-3.2-1.4-3.2-3 2.5-4.7 3.2-6.3c.6 1.5 3.2 4.4 3.2 6.2z"/> <path id="rain1" fill="#00B5E1" d="M80.8 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> </svg> <path id="rain3" fill="#00B5E1" d="M45.6 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.6 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain2" fill="#00B5E1" d="M65.2 84c0 1.7-1.5 3-3.2 3s-3.2-1.4-3.2-3 2.5-4.7 3.2-6.3c.6 1.5 3.2 4.4 3.2 6.2z"/> <path id="rain1" fill="#00B5E1" d="M80.8 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/></svg>``` `test` ```bash test test Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id.```';

        $bookworm = new Bookworm();
        $readingTime = $bookworm->estimate($story, false);

        $this->assertEquals('1', $readingTime, 'The code block should be 2 min but returned ' . $readingTime . ' min.');
    }

    /**
     * Tests the code time with html.
     */
    public function testCodeTimeHtml()
    {
        // code
        $story = '<pre><code>test</code></pre> Lorem Ipsum <pre class="test"><code data-language="html" class="code"><svg id="rainsuncloud" xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"> <g id="sun" fill="#FFC300"> <path d="M28.4 40.2C28.4 34.5 33 30 38.7 30c3.7 0 7 2 8.7 5 .6-.5 1.7-1 3.4-1.5-2.4-4.3-7-7.2-12.2-7.2-7.7 0-14 6.2-14 14 0 1.5.4 3 1 4.5.4-.5 1.4-1.3 3-2l-.2-2.6zM38.7 23.7c1.2 0 2-1 2-2v-7.3c0-1.2-.8-2-2-2s-2 .8-2 2v7.2c0 1.2.8 2 2 2zM22.2 40.2c0-1.2-1-2-2-2h-7.3c-1.3 0-2.2.8-2.2 2s1 2 2 2h7.3c1.3 0 2.2-.8 2.2-2zM53.3 28.5l5-5c1-1 1-2.3 0-3-.7-1-2-1-3 0l-5 5c-.8.8-.8 2.2 0 3 1 1 2.2 1 3 0zM24 28.5c.8.8 2.2.8 3 0 .8-.8.8-2.2 0-3l-5-5c-1-1-2.3-1-3 0-1 .7-1 2 0 3l5 5z"/> </g> <path id="rain4" fill="#00B5E1" d="M31.8 79c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain3" fill="#00B5E1" d="M45.6 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.6 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain2" fill="#00B5E1" d="M65.2 84c0 1.7-1.5 3-3.2 3s-3.2-1.4-3.2-3 2.5-4.7 3.2-6.3c.6 1.5 3.2 4.4 3.2 6.2z"/> <path id="rain1" fill="#00B5E1" d="M80.8 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> </svg></code></pre> <pre><code><svg id="rainsuncloud" xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"> <g id="sun" fill="#FFC300"> <path d="M28.4 40.2C28.4 34.5 33 30 38.7 30c3.7 0 7 2 8.7 5 .6-.5 1.7-1 3.4-1.5-2.4-4.3-7-7.2-12.2-7.2-7.7 0-14 6.2-14 14 0 1.5.4 3 1 4.5.4-.5 1.4-1.3 3-2l-.2-2.6zM38.7 23.7c1.2 0 2-1 2-2v-7.3c0-1.2-.8-2-2-2s-2 .8-2 2v7.2c0 1.2.8 2 2 2zM22.2 40.2c0-1.2-1-2-2-2h-7.3c-1.3 0-2.2.8-2.2 2s1 2 2 2h7.3c1.3 0 2.2-.8 2.2-2zM53.3 28.5l5-5c1-1 1-2.3 0-3-.7-1-2-1-3 0l-5 5c-.8.8-.8 2.2 0 3 1 1 2.2 1 3 0zM24 28.5c.8.8 2.2.8 3 0 .8-.8.8-2.2 0-3l-5-5c-1-1-2.3-1-3 0-1 .7-1 2 0 3l5 5z"/> </g> <path id="rain4" fill="#00B5E1" d="M31.8 79c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain3" fill="#00B5E1" d="M45.6 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.6 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain2" fill="#00B5E1" d="M65.2 84c0 1.7-1.5 3-3.2 3s-3.2-1.4-3.2-3 2.5-4.7 3.2-6.3c.6 1.5 3.2 4.4 3.2 6.2z"/> <path id="rain1" fill="#00B5E1" d="M80.8 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> </svg></code></pre> <pre><code><svg id="rainsuncloud" xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"> <g id="sun" fill="#FFC300"> <path d="M28.4 40.2C28.4 34.5 33 30 38.7 30c3.7 0 7 2 8.7 5 .6-.5 1.7-1 3.4-1.5-2.4-4.3-7-7.2-12.2-7.2-7.7 0-14 6.2-14 14 0 1.5.4 3 1 4.5.4-.5 1.4-1.3 3-2l-.2-2.6zM38.7 23.7c1.2 0 2-1 2-2v-7.3c0-1.2-.8-2-2-2s-2 .8-2 2v7.2c0 1.2.8 2 2 2zM22.2 40.2c0-1.2-1-2-2-2h-7.3c-1.3 0-2.2.8-2.2 2s1 2 2 2h7.3c1.3 0 2.2-.8 2.2-2zM53.3 28.5l5-5c1-1 1-2.3 0-3-.7-1-2-1-3 0l-5 5c-.8.8-.8 2.2 0 3 1 1 2.2 1 3 0zM24 28.5c.8.8 2.2.8 3 0 .8-.8.8-2.2 0-3l-5-5c-1-1-2.3-1-3 0-1 .7-1 2 0 3l5 5z"/> </g> <path id="rain4" fill="#00B5E1" d="M31.8 79c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain3" fill="#00B5E1" d="M45.6 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.6 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain2" fill="#00B5E1" d="M65.2 84c0 1.7-1.5 3-3.2 3s-3.2-1.4-3.2-3 2.5-4.7 3.2-6.3c.6 1.5 3.2 4.4 3.2 6.2z"/> <path id="rain1" fill="#00B5E1" d="M80.8 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> </svg> <svg id="rainsuncloud" xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"> <g id="sun" fill="#FFC300"> <path d="M28.4 40.2C28.4 34.5 33 30 38.7 30c3.7 0 7 2 8.7 5 .6-.5 1.7-1 3.4-1.5-2.4-4.3-7-7.2-12.2-7.2-7.7 0-14 6.2-14 14 0 1.5.4 3 1 4.5.4-.5 1.4-1.3 3-2l-.2-2.6zM38.7 23.7c1.2 0 2-1 2-2v-7.3c0-1.2-.8-2-2-2s-2 .8-2 2v7.2c0 1.2.8 2 2 2zM22.2 40.2c0-1.2-1-2-2-2h-7.3c-1.3 0-2.2.8-2.2 2s1 2 2 2h7.3c1.3 0 2.2-.8 2.2-2zM53.3 28.5l5-5c1-1 1-2.3 0-3-.7-1-2-1-3 0l-5 5c-.8.8-.8 2.2 0 3 1 1 2.2 1 3 0zM24 28.5c.8.8 2.2.8 3 0 .8-.8.8-2.2 0-3l-5-5c-1-1-2.3-1-3 0-1 .7-1 2 0 3l5 5z"/> </g> <path id="rain4" fill="#00B5E1" d="M31.8 79c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain3" fill="#00B5E1" d="M45.6 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.6 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain2" fill="#00B5E1" d="M65.2 84c0 1.7-1.5 3-3.2 3s-3.2-1.4-3.2-3 2.5-4.7 3.2-6.3c.6 1.5 3.2 4.4 3.2 6.2z"/> <path id="rain1" fill="#00B5E1" d="M80.8 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> </svg> <path id="rain2" fill="#00B5E1" d="M65.2 84c0 1.7-1.5 3-3.2 3s-3.2-1.4-3.2-3 2.5-4.7 3.2-6.3c.6 1.5 3.2 4.4 3.2 6.2z"/> <path id="rain1" fill="#00B5E1" d="M80.8 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> </svg> <path id="rain3" fill="#00B5E1" d="M45.6 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.6 1.6 3.2 4.4 3.2 6.2z"/> <path id="rain2" fill="#00B5E1" d="M65.2 84c0 1.7-1.5 3-3.2 3s-3.2-1.4-3.2-3 2.5-4.7 3.2-6.3c.6 1.5 3.2 4.4 3.2 6.2z"/> <path id="rain1" fill="#00B5E1" d="M80.8 90.6c0 1.8-1.5 3.2-3.2 3.2s-3.2-1.5-3.2-3.2 2.5-4.6 3.2-6.2c.5 1.6 3.2 4.4 3.2 6.2z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/> <path id="cloud" fill="#E1EBEB" d="M79.7 47c-2.5 0-4.8.8-6.6 2.3-.3-9.2-8-16.5-17.2-16.5-1.7 0-3.4.3-5 .7-1.7.4-2.8 1-3.4 1.4-3.3 1.7-6 4.6-7.5 8-2-.8-4-1.4-6-1.4s-3.7.4-5.3 1c-1.7.8-2.7 1.7-3.2 2-2.8 2.5-4.6 6-4.6 10 0 7.2 5.7 13 12.8 13h45.8C85.3 67.5 90 62.8 90 57c0-5.5-4.5-10-10.3-10z"/></svg></code></pre>`test` ```bash test test Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id.```';

        $bookworm = new Bookworm();
        $readingTime = $bookworm->estimate($story, false);

        $this->assertEquals('1', $readingTime, 'The code block should be 2 min but returned ' . $readingTime . ' min.');
    }
}
