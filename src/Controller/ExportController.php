<?php


namespace App\Controller;


use App\Exception\NotValidFileType;
use App\Repository\PharmacyRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ExportController extends AbstractController
{
    private array $validFileTypes = ['json', 'csv', 'xml'];

    /**
     * @Route("/export/all/{fileType}", name="export_all")
     * @throws Exception
     */
    public function exportAll(string $fileType, PharmacyRepository $repo, SerializerInterface $serializer)
    {
        $allData = $this->getAllPharmacies($repo);
        return $this->serializeDataAndGetResponse($allData, $fileType, $serializer);
    }

    /**
     * @Route("/export/filtered/{fileType}", name="export_filtered")
     * @throws Exception
     */
    public function exportFiltered(string $fileType, Request $request, PharmacyRepository $repo, SerializerInterface $serializer)
    {
        $allData = $this->getFilteredPharmacies($repo, $request);
        return $this->serializeDataAndGetResponse($allData, $fileType, $serializer);
    }

    private function serializeDataAndGetResponse(array $data, string $fileType, SerializerInterface $serializer)
    {
        if(!$this->notValidFileType($fileType))
        {
            return new Response('Not valid filetype');
        }

        try {
            $serializedData = $serializer->serialize($data, $this->getTypeOfSerialization($fileType));
        } catch (Exception $e) {
            return new Response($e->getMessage());
        }

        return $this->getResponseWithFile($fileType, $serializedData);
    }

    private function notValidFileType(string $fileType) : bool
    {
        return in_array($fileType, $this->validFileTypes);
    }

    private function getAllPharmacies(PharmacyRepository $repo) : array
    {
        return $repo->findAll();
    }

    public function getTypeOfSerialization(string $fileType) : string
    {
        if($fileType === 'json')
        {
            return JsonEncoder::FORMAT;
        }
        else if($fileType === 'csv')
        {
            return CsvEncoder::FORMAT;
        }
        else if($fileType === 'xml')
        {
            return XmlEncoder::FORMAT;
        }
        else
        {
            throw new NotValidFileType();
        }
    }

    private function getResponseWithFile(string $fileType, string $serializedData) : BinaryFileResponse
    {
        $tmpFileName = (new Filesystem())->tempnam(sys_get_temp_dir(), 'sb_');
        $tmpFile = fopen($tmpFileName, 'wb+');
        if (!\is_resource($tmpFile))
        {
            throw new \RuntimeException('Unable to create a temporary file.');
        }

        fwrite($tmpFile, $serializedData);

        $randomFileName = $this->getRandomAndSafeFileName($fileType);
        $response = $this->file($tmpFileName, $randomFileName);
        $response->headers->set('Content-type', 'application/' . $fileType);
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $randomFileName . '"');

        fclose($tmpFile);

        return $response;
    }

    public function getRandomAndSafeFileName(string $fileType) : string
    {
        return 'exported-data-' . uniqid() . '.' . $fileType;
    }

    public function getFilteredPharmacies(PharmacyRepository $repo, Request $request)
    {
        return $repo->findByParamsFromRequest($request);
    }
}