<?php


namespace App\Controller;

use App\Repository\PharmacyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class PharmacyController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response{
        return $this->render('Pharmacy/index.html.twig', []);
    }

    /**
     * @Route("pharmacies/showAll", name="show_all")
     */
    public function showAll(): Response{
        return $this->render('Pharmacy/showAll.html.twig', []);
    }

    /**
     * @Route("pharmacies/getAll", name="get_all")
     */
    public function getAll(SerializerInterface $serializer, PharmacyRepository $repo): JsonResponse
    {
        $models = $repo->findAll();
        $fullData = ['status'=>'success', 'data'=>$models];

        $data = $serializer->serialize($fullData, JsonEncoder::FORMAT);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}