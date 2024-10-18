<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\ClasseRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use DateTime;

#[Route('/student')]
final class StudentController extends AbstractController
{
    #[Route('/', name: 'app_student_index', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return a list of students',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Student::class))
        )
    )]
    #[OA\Tag(name: 'Students')]
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
                'date' => $student->getDate()->format('Y-m-d'),
                'classe' => $student->getClasse()->getName(),
            ];
        }
        $response = $this->json($data);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    #[Route('/new', name: 'app_student_new', methods: ['GET', 'POST'])]
    #[OA\Response(
        response: 201,
        description: 'Create a new student',
        content: new OA\JsonContent(
            ref: new Model(type: Student::class)
        )
    )]
    #[OA\RequestBody(
        description: 'Details of the new student',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'phone', type: 'string'),
                new OA\Property(property: 'place', type: 'string'),
                new OA\Property(property: 'date', type: 'string', format: 'date'),
                new OA\Property(property: 'classe', type: 'integer'),
            ]
        )
    )]
    #[OA\Tag(name: 'Students')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ClasseRepository $classrepository
    ): JsonResponse {
        $student = new Student();
        $content = json_decode($request->getContent());
        $student->setName($content->name);
        $date = new DateTime($content->date);
        $student->setDate($date);
        $student->setPlace($content->place);
        $student->setPhone($content->phone);
        $student->setClasse(
            $classrepository->find(
                $content->
                classe
            )
        );

        $entityManager->persist($student);
        $entityManager->flush();

        $data = [
                'id' => $student->getId(),
                'name' => $student->getName(),
                'date' => $student->getDate()->format('Y-m-d H:i:s'),
                'place' => $student->getPlace(),
                'phone' => $student->getPhone(),
                'classe' => $student->getClasse()->getName(),
        ];

        $response = $this->json($data);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    #[Route('/{id}', name: 'app_student_show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Return a specific student',
        content: new OA\JsonContent(ref: new Model(type: Student::class))
    )]
    #[OA\Tag(name: 'Students')]
    public function show(
        StudentRepository $studentRepository,
        int $id
    ): Response {
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
            'date' => $student->getDate()->format('Y-m-d'),
            'classe' => $student->getClasse()->getName(),
        ];
        $response = $this->json($data);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    #[Route('/edit/{id}', name: 'app_student_edit', methods: ['PUT'])]
    #[OA\Response(
        response: 200,
        description: 'Update an existing student',
        content: new OA\JsonContent(ref: new Model(type: Student::class))
    )]
    #[OA\RequestBody(
        description: 'Fields to update',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'phone', type: 'string'),
                new OA\Property(property: 'place', type: 'string'),
                new OA\Property(property: 'date', type: 'string', format: 'date'),
                new OA\Property(property: 'classe', type: 'integer'),
            ]
        )
    )]
    #[OA\Tag(name: 'Students')]
    public function edit(
        Request $request,
        int $id,
        StudentRepository $studentRepository,
        EntityManagerInterface $entityManager,
        ClasseRepository $classeRepository
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
        $date = new DateTime($content->date);
        $student->setDate($date);
        $student->setClasse(
            $classeRepository->find(
                $content->
                classe
            )
        );

        $entityManager->flush();
        $data = [
            'id' => $student->getId(),
            'name' => $student->getName(),
            'place' => $student->getPlace(),
            'phone' => $student->getPhone(),
            'date' => $student->getDate()->format('Y-m-d H:i:s'),
            'classe' => $student->getClasse()->getName(),
        ];
        $response = $this->json($data);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    #[Route('/delete/{id}', name: 'app_student_delete', methods: ["DELETE"])]
    #[OA\Response(
        response: 200,
        description: 'Delete a student',
        content: new OA\JsonContent(ref: new Model(type: Student::class))
    )]
    #[OA\Tag(name: 'Students')]
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

    #[Route('/api/classes', name: 'get_classes', methods: ['GET'])]
    public function getClasses(
        ClasseRepository $classeRepository
    ): JsonResponse {

        $classes = $classeRepository->findAll();

        $data = array_map(
            fn (
                $classe
            ) => [
            'id' => $classe->getId(),
            'name' => $classe->getName()
            ],
            $classes
        );

        $response = $this->json($data);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}
