<?php
namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        \App\Models\Role::create([
            'role_name'   => 'admin',
            'description' => 'Administrator',
        ]);

        \App\Models\Role::create([
            'role_name'   => 'company',
            'description' => 'Company',
        ]);
        
        \App\Models\Role::create([
            'role_name'   => 'freelancer',
            'description' => 'Freelancer',
        ]);

        \App\Models\Role::create([
            'role_name'   => 'government',
            'description' => 'Government',
        ]);

        \App\Models\Role::create([
            'role_name'   => 'client',
            'description' => 'Individual',
        ]); 

        $user = \App\Models\User::create([
            'role_id'   => 1,
            'username'  => 'admin',
            'email'     => 'admin@gmail.com',
            'password'  => bcrypt('password'),
        ]);

        $category = \App\Models\Category::create([
            'category_name' => 'Web Development',
            // 'description' => 'Create a website or web application for a business or personal use case scenario, with a focus on functionality, user experience, and performance. and great backend service to make application is rapidly',
        ]);

        \App\Models\Post::create([
            'title' => 'Web Development',
            'description' => 'Create a website or web application for a business or personal use case scenario, with a focus on functionality, user experience, and performance. and great backend service to make application is rapidly',
            'price' => 1000000,
            'category_id' => $category->id,
            'number_of_employee' => 2,
            'posted_by' => $user->id,
        ]);
    }
}
