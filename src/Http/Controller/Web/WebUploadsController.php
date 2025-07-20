<?php

namespace App\Controller\Web;

use Gumlet\ImageResize;
use App\Core\Imageman\FrontImageman;
use Symfony\Component\HttpFoundation\Response;
use App\Core\Fileman\FilemanAwareTrait;
use Symfony\Component\Routing\Annotation\Route;
use App\Core\Imageman\ImagemanAwareTrait;
use App\Core\Fileman\FilemanAwareInterface;
use App\Core\Imageman\ImagemanAwareInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Exception\Runtime\Resource\ResourceNotFoundException;


class WebUploadsController extends AbstractWebController implements
    FilemanAwareInterface,
    ImagemanAwareInterface
{
    use FilemanAwareTrait;
    use ImagemanAwareTrait;


    const ROUTE_WEB_UPLOADS_IMAGE = 'web.uploads.image';


    /**
     * @Route(
     *     "/uploads/image/{presetResize}-{presetConvert}{args}/{filepath}",
     *     name=WebUploadsController::ROUTE_WEB_UPLOADS_IMAGE,
     *     methods={"GET"},
     *     requirements={
     *         "presetResize"="[a-zA-Z0-9_.]+",
     *         "presetConvert"="[a-zA-Z0-9_.]+",
     *         "args"="([-][^/]+)*",
     *         "filepath"=".+",
     *     })
     */
    public function image(
        string $presetResize,
        string $presetConvert,
        string $args,
        string $filepath
    ) : Response
    {
        if (! isset(FrontImageman::LIST_PRESET_RESIZE[ $presetResize ])) {
            throw new ResourceNotFoundException('Unknown `resize` preset: ' . $presetResize);
        }

        if (! isset(FrontImageman::LIST_PRESET_CONVERT[ $presetConvert ])) {
            throw new ResourceNotFoundException('Unknown `convert` preset: ' . $presetConvert);
        }

        $filesystem = $this->fileman->getFilesystem('uploads');

        // > [ 'extension' => '.min.jpg' ]
        $pi = _php_pathinfo($filepath);

        $dirname = $pi[ 'dirname' ];

        $basename = $pi[ 'basename' ];
        $filepathDstOriginal = $dirname . '/' . $presetResize . $args . '/' . $basename;

        $filename = $pi[ 'filename' ];
        $filepathSrc = $dirname . '/' . $filename;
        $filepathDst = $dirname . '/' . $presetResize . $args . '/' . $filename;

        $isOriginalResize = $presetResize === FrontImageman::PRESET_RESIZE_FULL;
        $isOriginalConvert = $presetConvert === FrontImageman::PRESET_CONVERT_ORIGINAL;
        $isOriginal = $isOriginalResize && $isOriginalConvert;

        if ($this->filesystemHas($filesystem, $filepathDstOriginal)) {
            $imageMimeType = $this->filesystemMimeType($filesystem, $filepathDstOriginal);
            $imageContent = $this->filesystemRead($filesystem, $filepathDstOriginal);

        } else {
            $extension = $isOriginalConvert
                ? $pi[ 'extension' ]
                : FrontImageman::LIST_PRESET_CONVERT_SRC[ $presetConvert ];

            $filepathSrc .= '.' . $extension;
            $filepathDst .= '.' . FrontImageman::LIST_PRESET_CONVERT_DST[ $extension ][ $presetConvert ];

            if ($this->filesystemHas($filesystem, $filepathDst)) {
                $imageMimeType = $this->filesystemMimeType($filesystem, $filepathDstOriginal);
                $imageContent = $this->filesystemRead($filesystem, $filepathDstOriginal);

            } else {

                if (! $this->filesystemHas($filesystem, $filepathSrc)) {
                    throw new ResourceNotFoundException('Image is not found: ' . $filepathSrc);
                }

                $_args = explode('-', $args);
                $_args = array_slice($_args, 1);

                $imageMimeType = $this->filesystemMimeType($filesystem, $filepathSrc);
                $imageContent = $this->filesystemRead($filesystem, $filepathSrc);

                if (! $isOriginalResize) {
                    /** @var ImageResize $imageResize */

                    [ $presetResize => $imageResize ] = $this->imageman->resizeImage(
                        $imageContent,
                        [ $presetResize ],
                        $_args
                    );
                }

                if (! $isOriginal) {
                    ob_start();

                    $this->imageman->convertImage(
                        $imageResize ?? $imageContent,
                        $presetConvert,
                        $imageMimeTypeOutput,
                        $imageMimeType
                    );

                    $imageContent = ob_get_clean();

                    $this->filesystemWrite($filesystem, $filepathDst, $imageContent);
                }
            }
        }

        $response = new Response();
        $response->setContent($imageContent);

        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $basename);

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', $imageMimeTypeOutput ?? $imageMimeType);
        $response->headers->set("Content-Length", strlen($imageContent));

        $response->setPublic();
        $response->headers->removeCacheControlDirective('must-revalidate');
        $response->headers->removeCacheControlDirective('no-store');
        $response->headers->addCacheControlDirective('max-age', '3600');
        $response->headers->addCacheControlDirective('s-maxage', '3600');
        $response->headers->set('strict-transport-security', 'max-age=3600; includeSubDomains');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }
}
