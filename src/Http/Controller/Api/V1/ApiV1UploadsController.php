<?php

namespace App\Controller\Api\V1;

use App\Controller\Api\AbstractApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Core\Fileman\FilemanAwareTrait;
use Symfony\Component\Routing\Annotation\Route;
use App\Core\Imageman\ImagemanAwareTrait;
use App\Core\Fileman\FilemanAwareInterface;
use App\Core\Imageman\ImagemanAwareInterface;
use App\Exception\Runtime\Resource\ResourceNotFoundException;


class ApiV1UploadsController extends AbstractApiController implements
    FilemanAwareInterface,
    ImagemanAwareInterface
{
    use FilemanAwareTrait;
    use ImagemanAwareTrait;


    const ROUTE_API_V1_UPLOADS_IMAGE = 'api.v1.uploads.image';


    /**
     * @Route("/api/v1/uploads/image/{url}", name=ApiV1UploadsController::ROUTE_API_V1_UPLOADS_IMAGE,
     *     methods={"GET"},
     *     requirements={
     *         "url"=".+"
     *     }
     * )
     */
    public function image(Request $request, string $url) : Response
    {
        $presets = $request->get('presets') ?: [];
        $presets = (array) $presets;

        $filesystem = $this->fileman->getFilesystem('uploads');

        if (! $this->filesystemHas($filesystem, $url)) {
            throw new ResourceNotFoundException('Image is not found: ' . $url);
        }

        $result = $this->imageman->blueprintImage($url, $presets);

        return $this->api->jsonSuccess($result);
    }
}
