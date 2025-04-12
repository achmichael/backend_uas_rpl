<?php
namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\catalog;
use App\Models\Category;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Job;
use App\Models\Location;
use App\Models\Portofolio;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Certificate;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::create([
            'role_name'   => 'admin',
            'description' => 'Administrator',
        ]);

        Role::create([
            'role_name'   => 'company',
            'description' => 'Company',
        ]);

        Role::create([
            'role_name'   => 'freelancer',
            'description' => 'Freelancer',
        ]);

        Role::create([
            'role_name'   => 'government',
            'description' => 'Government',
        ]);

        Role::create([
            'role_name'   => 'client',
            'description' => 'Individual',
        ]);

        $user = User::create([
            'role_id'   => 1,
            'username'  => 'admin',
            'email'     => 'admin@gmail.com',
            'password'  => bcrypt('password'),
        ]);

        $category = Category::create([
            'category_name' => 'Web Development',
            // 'description' => 'Create a website or web application for a business or personal use case scenario, with a focus on functionality, user experience, and performance. and great backend service to make application is rapidly',
        ]);

        $post = Post::create([
            'title' => 'Web Development',
            'description' => 'Create a website or web application for a business or personal use case scenario, with a focus on functionality, user experience, and performance. and great backend service to make application is rapidly',
            'price' => 1000000,
            'required_skills' => ["PHP", "Laravel", "JavaScript", "VueJS"],
            'min_experience_years' => 2,
            'category_id' => $category->id,
            'number_of_employee' => 2,
            'posted_by' => $user->id,

        ]);

        $job = Job::create([
            'id_post'             => $post->id,
            'min_experience_year' => '2',
            'number_of_employee'  => '6',
            'duration'            => '1.5',
            'status'              => 'open',
            'type_job'            => 'full-time',
            'type_salary'         => 'flexible',
            'system'              => 'wfh',
        ]);

        Contract::create([
            'contract_type' => Job::class,
            'contract_type_id' => $job->id,
            'provider_id' => $user->id,
            'contract_date' => now(),
            'status' => 'active',
        ]);
        
        $company = Company::create([
            'user_id' => $user->id,
            'post_id' => $post->id,
            'name'    => 'bossware',
            'image'   => 'https://source.unsplash.com/random',
            'addres'  => 'jl gajayana malang, jawa-timur, indonesia',
            'phone'   => '082241370247',
            'email'   => 'bossware@gmail.com',
            'website' => 'bossware.com',
            'founded' => '2002',
        ]);


        $profile = UserProfile::create([
            'user_id'           => $user->id,
            'full_name'         => 'Naufal Maulana Rafiq',
            'portofolio_url'    => 'https://namara.dev/portofolio',
            'bio'               => 'Full-stack web developer dengan pengalaman 3+ tahun dalam membangun aplikasi berbasis Laravel dan React. Siap membantu membangun solusi digital untuk bisnis Anda.',
        ]);

        $catalog = Catalog::create([
            'user_id'       => $user->id,
            'catalog_name'  => 'Jasa Pembuatan Website Profesional',
            'price'         => 1500000,
            'description'   => 'Layanan pembuatan website custom untuk bisnis, portofolio, atau toko online. Desain responsif dan SEO friendly. Estimasi pengerjaan 5-7 hari.',
        ]);

        $portofolio = Portofolio::create([
            'user_id'   => $user->id,
            'title'     => 'my portofolio',
            'url'       => 'my-portofolio.com',
        ]);

        $location = Location::create([
            'user_id'       => $user->id,
            'accuracy'      => 5,
            'latitude'      => -6.2,
            'longitude'     => 106.817,
            'altitude'      => 20.5,
            'heading'       => 90,
            'speed'         => 2.3,
            'altitudeAccuracy' => 0,

        ]);

        Certificate::create([
            'user_id'           => $user->id,
            'certificate_name'  => 'Laravel Web Development Certificate',
            'expiration_date'   => '2026-12-31',
            'category'          => 'Web Development',
            'file_path'         => 'certificates/laravel-web-cert.pdf',
            'description'       => 'Certificate for completing Laravel course',
            'status'            => 'active',
        ]);







    }
}
