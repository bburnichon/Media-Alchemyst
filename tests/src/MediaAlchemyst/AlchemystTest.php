<?php

namespace MediaAlchemyst;

use \Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class AlchemystTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Alchemyst
     */
    protected $object;
    protected $specsAudio;
    protected $specsFlash;
    protected $specsImage;
    protected $specsVideo;

    /**
     * @covers MediaAlchemyst\Alchemyst::__construct
     */
    protected function setUp()
    {
        $this->object = new Alchemyst(new DriversContainer(new ParameterBag(array())));

        $this->specsAudio = new Specification\Audio();
        $this->specsFlash = new Specification\Flash();
        $this->specsVideo = new Specification\Video();
        $this->specsVideo->setDimensions(320, 240);
        $this->specsImage = new Specification\Image();
        $this->specsImage->setDimensions(320, 240);
    }

    /**
     * @covers MediaAlchemyst\Alchemyst::open
     * @covers MediaAlchemyst\Alchemyst::close
     */
    public function testOpen()
    {
        $this->object->open(__DIR__ . '/../../files/Audio.mp3');
        $this->object->close();
        $this->object->open(__DIR__ . '/../../files/Audio.mp3');
        $this->object->open(__DIR__ . '/../../files/Test.ogv');
    }

    /**
     * @covers MediaAlchemyst\Alchemyst::open
     * @covers MediaAlchemyst\Exception\FileNotFoundException
     * @expectedException MediaAlchemyst\Exception\FileNotFoundException
     */
    public function testOpenUnknownFile()
    {
        $this->object->open(__DIR__ . '/../../files/invalid.file');
    }

    /**
     * @covers MediaAlchemyst\Alchemyst::turnInto
     * @covers MediaAlchemyst\Exception\LogicException
     * @expectedException MediaAlchemyst\Exception\LogicException
     */
    public function testTurnIntoNoFile()
    {
        $specs = new Specification\Audio();

        $this->object->turnInto(__DIR__ . '/../../files/output', $specs);
    }

    /**
     * @covers MediaAlchemyst\Alchemyst::turnInto
     * @covers MediaAlchemyst\Alchemyst::routeAction
     */
    public function testTurnIntoAudioAudio()
    {
        $this->object->open(__DIR__ . '/../../files/Audio.mp3');

        $dest = __DIR__ . '/../../files/output.flac';

        $this->object->turnInto($dest, $this->specsAudio);

        unlink($dest);

        $this->object->close();
    }

    /**
     * @covers MediaAlchemyst\Alchemyst::turnInto
     * @covers MediaAlchemyst\Alchemyst::routeAction
     */
    public function testTurnIntoFlashImage()
    {
        $this->object->open(__DIR__ . '/../../files/flashfile.swf');

        $dest = __DIR__ . '/../../files/output.png';

        $this->object->turnInto($dest, $this->specsImage);

        unlink($dest);

        $this->object->close();
    }

    /**
     * @covers MediaAlchemyst\Alchemyst::turnInto
     * @covers MediaAlchemyst\Alchemyst::routeAction
     */
    public function testTurnIntoDocumentImage()
    {
        $this->object->open(__DIR__ . '/../../files/Hello.odt');

        $dest = __DIR__ . '/../../files/output.png';

        $this->object->turnInto($dest, $this->specsImage);

        unlink($dest);

        $this->object->close();
    }

    /**
     * @covers MediaAlchemyst\Alchemyst::turnInto
     * @covers MediaAlchemyst\Alchemyst::routeAction
     */
    public function testTurnIntoDocumentFlash()
    {
        $this->object->open(__DIR__ . '/../../files/Hello.odt');

        $dest = __DIR__ . '/../../files/output.swf';

        $this->object->turnInto($dest, $this->specsFlash);

        unlink($dest);

        $this->object->close();
    }

    /**
     * @covers MediaAlchemyst\Alchemyst::turnInto
     * @covers MediaAlchemyst\Alchemyst::routeAction
     */
    public function testTurnIntoImageImage()
    {
        $this->object->open(__DIR__ . '/../../files/photo03.JPG');

        $dest = __DIR__ . '/../../files/output.png';

        $this->object->turnInto($dest, $this->specsImage);

        unlink($dest);

        $this->object->close();
    }

    /**
     * @covers MediaAlchemyst\Alchemyst::turnInto
     * @covers MediaAlchemyst\Alchemyst::routeAction
     */
    public function testTurnIntoVideoImage()
    {
        $this->object->open(__DIR__ . '/../../files/Test.ogv');

        $dest = __DIR__ . '/../../files/output.png';

        $this->object->turnInto($dest, $this->specsImage);

        unlink($dest);

        $this->object->close();
    }

    /**
     * @covers MediaAlchemyst\Alchemyst::turnInto
     * @covers MediaAlchemyst\Alchemyst::routeAction
     */
    public function testTurnIntoVideoVideo()
    {
        $this->object->open(__DIR__ . '/../../files/Test.ogv');

        $dest = __DIR__ . '/../../files/output.webm';

        $this->object->turnInto($dest, $this->specsVideo);

        unlink($dest);

        $this->object->close();
    }

}
