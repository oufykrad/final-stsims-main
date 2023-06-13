<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ListStatusesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('list_statuses')->delete();
        
        \DB::table('list_statuses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Unknown',
                'type' => 'Progress',
                'color' => 'bg-soft-dark',
                'others' => 'badge-soft-dark',
                'is_active' => 1,
                'created_at' => '2023-05-21 19:13:21',
                'updated_at' => '2023-05-21 19:13:21',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Graduated',
                'type' => 'Progress',
                'color' => 'bg-soft-success',
                'others' => 'badge-soft-success',
                'is_active' => 1,
                'created_at' => '2023-05-21 19:13:21',
                'updated_at' => '2023-05-21 19:13:21',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Terminated',
                'type' => 'Progress',
                'color' => 'bg-soft-danger',
                'others' => 'badge-soft-danger',
                'is_active' => 1,
                'created_at' => '2023-05-21 19:13:21',
                'updated_at' => '2023-05-21 19:13:21',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Non-compliance',
                'type' => 'Progress',
                'color' => 'bg-soft-warning',
                'others' => 'badge-soft-warning',
                'is_active' => 1,
                'created_at' => '2023-05-21 19:16:26',
                'updated_at' => '2023-05-21 19:16:26',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Withdrew',
                'type' => 'Progress',
                'color' => 'bg-soft-info',
                'others' => 'badge-soft-info',
                'is_active' => 1,
                'created_at' => '2023-05-21 19:16:26',
                'updated_at' => '2023-05-21 19:16:26',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Deceased',
                'type' => 'Progress',
                'color' => 'bg-soft-dark',
                'others' => 'badge-soft-dark',
                'is_active' => 1,
                'created_at' => '2023-05-21 19:18:40',
                'updated_at' => '2023-05-21 19:18:40',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Good Standing',
                'type' => 'Ongoing',
                'color' => 'bg-success',
                'others' => 'n/a',
                'is_active' => 1,
                'created_at' => '2023-05-21 19:19:26',
                'updated_at' => '2023-05-21 19:19:26',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Suspended',
                'type' => 'Ongoing',
                'color' => 'bg-danger',
                'others' => 'n/a',
                'is_active' => 1,
                'created_at' => '2023-05-21 19:19:26',
                'updated_at' => '2023-05-21 19:19:26',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Leave of Absence',
                'type' => 'Ongoing',
                'color' => 'bg-info',
                'others' => 'n/a',
                'is_active' => 1,
                'created_at' => '2023-05-21 19:20:42',
                'updated_at' => '2023-05-21 19:20:42',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'No Report',
                'type' => 'Ongoing',
                'color' => 'bg-warning',
                'others' => 'n/a',
                'is_active' => 1,
                'created_at' => '2023-05-21 19:20:42',
                'updated_at' => '2023-05-21 19:20:42',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Pending',
                'type' => 'Benefit Status',
                'color' => 'bg-warning',
                'others' => 'n/a',
                'is_active' => 1,
                'created_at' => '2023-05-23 18:49:22',
                'updated_at' => '2023-05-23 18:49:22',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Waiting',
                'type' => 'Benefit Status',
                'color' => 'bg-info',
                'others' => 'n/a',
                'is_active' => 1,
                'created_at' => '2023-05-23 18:53:51',
                'updated_at' => '2023-05-23 18:53:51',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Released',
                'type' => 'Benefit Status',
                'color' => 'bg-succces',
                'others' => 'n/a',
                'is_active' => 1,
                'created_at' => '2023-05-23 18:54:44',
                'updated_at' => '2023-05-23 18:54:47',
            ),
        ));
        
        
    }
}