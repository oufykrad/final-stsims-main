<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SchoolCoursesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('school_courses')->delete();
        
        \DB::table('school_courses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'years' => 4,
                'type' => 'n/a',
                'certification' => 'COE',
                'validity' => '5',
                'school_id' => 18,
                'course_id' => 47,
                'is_active' => 1,
                'created_at' => '2023-05-24 08:27:24',
                'updated_at' => '2023-05-24 08:27:24',
            ),
        ));
        
        
    }
}