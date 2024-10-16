<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/student')]
final class StudentController extends AbstractController
{
    #[Route('/', name: 'app_student_index', methods: ['GET'])]
    public function index(StudentRepository $studentRepository): JsonResponse
    {
        $students = $studentRepository->findAll();
        $data = [];
        foreach ($students as $student) {
            $data[] = [
                'id' => $student->getId(),
                'name' => $student->getName(),
                'phone' => $student->getPhone(),
                'place' => $student->getPlace(),
            ];
        }
        $response = $this->json($data);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    #[Route('/new', name: 'app_student_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $student = new Student();
        $content = json_decode($request->getContent());
        $student->setName($content->name);
        $student->setPlace($content->place);
        $student->setPhone($content->phone);

        $entityManager->persist($student);
        $entityManager->flush();

        $data = [
            'id' => $student->getId(),
            'name' => $student->getName(),
            'phone' => $student->getPhone(),
            'place' => $student->getPlace(),
        ];

        $response = $this->json($data);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    #[Route('/{id}', name: 'app_student_show', methods: ['GET'])]
    public function show(StudentRepository $studentRepository, int $id): Response
    {
        $student = $studentRepository->find($id);

        if (!$student) {
            return $this->json(
                'No student found for id' . $id,
                404
            );
        }
        $data =  [
            'id' => $student->getId(),
            'name' => $student->getName(),
            'phone' => $student->getPhone(),
            'place' => $student->getPlace(),
        ];
        $response = $this->json($data);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    #[Route('/edit/{id}', name: 'app_student_edit', methods: ['PUT'])]
    public function edit(
        Request $request,
        int $id,
        StudentRepository $studentRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $student = $studentRepository->find($id);

        if (!$student) {
            return $this->json(
                'No student found for id' . $id,
                404
            );
        }
        $content = json_decode($request->getContent());
        $student->setName($content->name);
        $student->setPlace($content->place);
        $student->setPhone($content->phone);

        $entityManager->flush();
        $data = [
            'id' => $student->getId(),
            'name' => $student->getName(),
            'phone' => $student->getPhone(),
            'place' => $student->getPlace(),
        ];
        $response = $this->json($data);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    #[Route('/delete/{id}', name: 'app_student_delete', methods: ["DELETE"])]
    public function delete(
        Request $request,
        int $id,
        studentRepository $studentRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $student = $studentRepository->find($id);
        if (!$student) {
            return $this->json(
                'No student found for id' . $id,
                404
            );
        }
        $entityManager->remove($student);
        $entityManager->flush();
        return $this->json(
            'Student with id '
            . $id .
            ' has been deleted successfully'
        );

    }
}
