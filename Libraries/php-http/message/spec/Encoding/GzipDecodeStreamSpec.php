<?php

namespace spec\Http\Message\Encoding;

use Psr\Http\Message\StreamInterface;
use PhpSpec\Exception\Example\SkippingException;
use PhpSpec\ObjectBehavior;

class GzipDecodeStreamSpec extends ObjectBehavior
{
    use StreamBehavior, ZlibStreamBehavior;

    function let(StreamInterface $stream)
    {
        if (defined('HHVM_VERSION')) {
            throw new SkippingException('Skipping test as zlib is not working on hhvm');
        }

        $this->beConstructedWith($stream);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Http\Message\Encoding\GzipDecodeStream');
    }

    function it_reads()
    {
        // "This is a test stream" | deflate
        $stream = new MemoryStream(gzencode('This is a test stream'));
        $this->beConstructedWith($stream);

        $this->read(4)->shouldReturn('This');
    }

    function it_gets_content()
    {
        // "This is a test stream" | deflate
        $stream = new MemoryStream(gzencode('This is a test stream'));
        $this->beConstructedWith($stream);

        $this->getContents()->shouldReturn('This is a test stream');
    }
}
