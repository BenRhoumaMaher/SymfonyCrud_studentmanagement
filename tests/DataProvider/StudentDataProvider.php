<?php

namespace App\Tests\DataProvider;

class StudentDataProvider
{
    public static function studentListDataProvider(): array
    {
        return [
            [30],
        ];
    }

    public static function newStudentDataProvider(): array
    {
        return [
            [
                [
                    'name' => 'Maher Ben Rhouma',
                    'phone' => '1234567890',
                    'place' => 'Somewhere',
                    'date' => '2024-10-15',
                    'classe' => 6,
                ],
                200
            ],
        ];
    }

    public static function studentIdDataProvider(): array
    {
        return [
            [11, 200],
            [999, 404]
        ];
    }

    public static function editStudentDataProvider(): array
    {
        return [
            [
                2,
                [
                    'name' => 'Maher Updated',
                    'phone' => '0987654321',
                    'place' => 'Updated Place',
                    'date' => '2024-10-13',
                    'classe' => 5,
                ],
                200
            ],
        ];
    }

    public static function deleteStudentDataProvider(): array
    {
        return [
            [15, 200],
            [999, 404]
        ];
    }

    public static function classListDataProvider(): array
    {
        return [
            [30],
        ];
    }
}
