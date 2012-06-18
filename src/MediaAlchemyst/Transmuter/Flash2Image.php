<?php

namespace MediaAlchemyst\Transmuter;

use MediaAlchemyst\Specification;
use MediaAlchemyst\Exception;
use MediaVorus\Media\Media;

class Flash2Image extends Provider
{

    public function execute(Specification\Provider $spec, Media $source, $dest)
    {
        if ( ! $spec instanceof Specification\Image) {
            throw new Exception\SpecNotSupportedException('SwfTools only accept Image specs');
        }

        $tmpDest = tempnam(sys_get_temp_dir(), 'swfrender');

        try {
            $this->container->getSwfRender()
              ->render($source->getFile(), $tmpDest, null);

            $image = $this->container->getImagine()->open($tmpDest);

            if ($spec->getWidth() && $spec->getHeight()) {

                $media = $this->container['mediavorus']->guess(new \SplFileInfo($tmpDest));

                $box = $this->boxFromImageSpec($spec, $media);

                if ($spec->getResizeMode() == Specification\Image::RESIZE_MODE_OUTBOUND) {
                    /* @var $image \Imagine\Gmagick\Image */
                    $image = $image->thumbnail($box, Image\ImageInterface::THUMBNAIL_OUTBOUND);
                } else {
                    $image = $image->resize($box);
                }

                unset($media);
            }

            $options = array(
              'quality'          => $spec->getQuality(),
              'resolution-units' => $spec->getResolutionUnit(),
              'resolution-x'     => $spec->getResolutionX(),
              'resolution-y'     => $spec->getResolutionY(),
            );

            $image->save($dest, $options);

            unlink($tmpDest);
        } catch (\SwfTools\Exception\Exception $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        } catch (\Imagine\Exception\Exception $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        } catch (\MediaVorus\Exception\Exception $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
