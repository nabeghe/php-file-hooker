<?php declare(strict_types=1);

use Nabeghe\FileHooker\FileHooker;
use PHPUnit\Framework\TestCase;

final class HookerTest extends TestCase
{
    private FileHooker $hooker;

    private object $angler;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        $this->angler = new stdClass();
        $this->hooker = new FileHooker($this->angler);
        $this->hooker->add(__DIR__.'/hooks');
        parent::__construct($name, $data, $dataName);
    }

    public function testDoReference(): void
    {
        $this->hooker->action('do_reference', [], $actual);

        $this->assertSame($this->angler, $actual);
    }

    public function testDoThrow(): void
    {
        $error_message = 'This is error';
        try {
            $this->hooker->action('do_throw', ['message' => $error_message]);
        } catch (Exception $ex) {
            $actual = $ex->getMessage();
        }

        $this->assertSame($error_message, $actual ?? null);
    }

    public function testDoPrint(): void
    {
        ob_start();
        $text = 'Hi';
        $this->hooker->action('do_print', ['text' => $text]);
        $actual = ob_get_contents();
        ob_end_clean();

        $this->assertSame($text, $actual);
    }

    public function testFilterAngler(): void
    {
        $actual = $this->hooker->filter('filter_angler');

        $this->assertSame($this->angler, $actual);
    }

    public function testFilterMultiply(): void
    {
        $actual = $this->hooker->filter('filter_multiply', [2, 3]);

        $this->assertSame(6, $actual);
    }

    public function testFilterSquare(): void
    {
        $actual = $this->hooker->filter('filter_square', [2]);

        $this->assertSame(4, $actual);
    }
}