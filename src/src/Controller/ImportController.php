<?php


namespace App\Controller;

use App\Entity\Pharmacy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends AbstractController
{
    private string $directoryForFiles = '../assets/uploads/';

    public function __construct(){
        $filesystem = new Filesystem();
        if(!$filesystem->exists($this->directoryForFiles))
        {
            $filesystem->mkdir($this->directoryForFiles);
        }
    }

    /**
     * @Route("/import", name="import")
     */
    public function index(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $form = $this->getFormForFileUpload();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $statusArray = $this->handleFormWithUploadedFile($form, $slugger, $entityManager);
            if($statusArray['status'] === false)
            {
                $this->addFlash('error', "Wystąpił nieoczekiwany problem z plikiem.");
            }
            else
            {
                $this->addFlash('success', "Pomyślnie stworzono {$statusArray['created']} aptek.");
            }

        }
        return $this->render('Import/index.html.twig', ['upload_form' => $form->createView()]);
    }

    private function getFormForFileUpload() : FormInterface
    {
        return $this->createFormBuilder(['message' => 'test'])
            ->add('import_file', FileType::class, [
                'label' => 'JSON File with Data',
                'required' => true,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/json',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JSON file',
                    ])
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Upload data!'])
            ->getForm();
    }

    private function handleFormWithUploadedFile(FormInterface $form, SluggerInterface $slugger, EntityManagerInterface $entityManager) : array
    {
        $importFile = $form->get('import_file')->getData();
        $newFilename = $this->getSafeNameOfTheFile($importFile, $slugger);

        if ($importFile)
        {
            try {
                $newFilePath = $importFile->move(
                    $this->directoryForFiles,
                    $newFilename
                );
            } catch (FileException $e) {
                $form->get('import_file')->addError(new FormError('Internal server error while handling the file'));
                return ['status' => false, 'message' => 'Invalid file'];
            }

            return $this->importPharmaciesFromFile($newFilePath, $entityManager);
        }
        else
        {
            return ['status' => false, 'message' => 'Invalid file'];
        }
    }

    private function getSafeNameOfTheFile(UploadedFile $importFile, SluggerInterface $slugger){
        $originalFilename = pathinfo($importFile->getClientOriginalName(), PATHINFO_FILENAME);

        $safeFilename = $slugger->slug($originalFilename);
        return $safeFilename.'-'.uniqid().'.'.$importFile->guessExtension();
    }

    private function importPharmaciesFromFile(string $filePath, EntityManagerInterface $entityManager) : array
    {
        $objects = json_decode(file_get_contents($filePath));
        if(count($objects) > 0)
        {
            foreach($objects as $object)
            {
                $this->createPharmacyFromJsonObject($object, $entityManager);
            }
        }

        return ['status' => true, 'created' => count($objects)];
    }

    private function createPharmacyFromJsonObject(object $object, $entityManager)
    {
        $pharmacy = new Pharmacy();

        $pharmacy->setName($object->nazwa ?: '');
        $pharmacy->setPostalCode($object->kod_pocztowy ?: '');
        $pharmacy->setStreet($object->ulica ?: '');
        $pharmacy->setCity($object->miejscowosc ?: '');
        $pharmacy->setLatitude($object->gps_szerokosc ?: 0.0);
        $pharmacy->setLongitude($object->gps_dlugosc ?: 0.0);

        $entityManager->persist($pharmacy);
        $entityManager->flush();
    }
}