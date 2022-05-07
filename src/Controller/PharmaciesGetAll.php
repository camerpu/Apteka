<?php


namespace App\Controller;

use App\Repository\PharmacyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PharmaciesGetAll extends AbstractController
{
    public function __invoke(PharmacyRepository $repository)
    {
        $models = $repository->findAll();

        return $models;
    }
}