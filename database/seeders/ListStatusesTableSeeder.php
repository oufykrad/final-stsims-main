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
                'color' => 'bg-success',
                'others' => 'n/a',
                'is_active' => 1,
                'created_at' => '2023-05-23 18:54:44',
                'updated_at' => '2023-05-23 18:54:47',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'Waiting',
                'type' => 'Qualifier',
                'color' => 'bg-warning',
                'others' => 'n/a',
                'is_active' => 1,
                'created_at' => '2023-05-23 18:54:44',
                'updated_at' => '2023-05-23 18:54:47',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'Deferment',
                'type' => 'Qualifier',
                'color' => 'bg-danger',
                'others' => 'n/a',
                'is_active' => 1,
                'created_at' => '2023-05-23 18:54:44',
                'updated_at' => '2023-05-23 18:54:47',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'Not Avail',
                'type' => 'Qualifier',
                'color' => 'bg-dark',
                'others' => 'n/a',
                'is_active' => 1,
                'created_at' => '2023-05-23 18:54:44',
                'updated_at' => '2023-05-23 18:54:47',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'Enrolled',
                'type' => 'Qualifier ',
                'color' => 'bg-success',
                'others' => 'n/a',
                'is_active' => 1,
                'created_at' => '2023-05-23 18:54:44',
                'updated_at' => '2023-05-23 18:54:47',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'Completed',
                'type' => 'Qualifier Status',
                'color' => 'bg-success',
                'others' => 'n/a',
                'is_active' => 1,
                'created_at' => '2023-05-23 18:54:44',
                'updated_at' => '2023-05-23 18:54:47',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'Lacking',
                'type' => 'Qualifier Status',
                'color' => 'bg-warning',
                'others' => 'n/a',
                'is_active' => 1,
                'created_at' => '2023-05-23 18:54:44',
                'updated_at' => '2023-05-23 18:54:47',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'Potential',
                'type' => 'Qualifier Status',
                'color' => 'bg-danger',
                'others' => 'n/a',
                'is_active' => 1,
                'created_at' => '2023-05-23 18:54:44',
                'updated_at' => '2023-05-23 18:54:47',
            ),
        ));
        
        
    }
}