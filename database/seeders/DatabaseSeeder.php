<?php
namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Catalog;
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
use App\Models\Level;
use App\Models\Review;
use App\Models\Freelancer;
use App\Models\Friendship;
use App\Models\PaymentMethod;
use App\Models\PaymentCallback;
use App\Models\UserSkills;
use App\Models\EmployeesCompany;
use App\Models\OauthAccount;
use App\Models\Skill;



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

        Level::create([
            'name' => 'Beginner',
            'description' => 'Beginner level with basic knowledge and skills in the field.',
        ]);

        Level::create([
            'name' => 'Entry',
            'description' => 'Entry level with some experience and skills in the field.',
        ]);

        Level::create([
            'name' => 'Expert',
            'description' => 'Expert level with good knowledge and skills in the field.',
        ]);

        $post = Post::create([
            'title' => 'Web Development',
            'description' => 'Create a website or web application for a business or personal use case scenario, with a focus on functionality, user experience, and performance. and great backend service to make application is rapidly',
            'price' => 1000000,
            'level_id' => Level::pluck('id')->random(),
            'required_skills' => ["PHP", "Laravel", "JavaScript", "VueJS"],
            'min_experience_years' => 2,
            'category_id' => $category->id,
            // 'number_of_employee' => 2,
            'posted_by' => $user->id,
            'status'              => 'open',
        ]);

        $job = Job::create([
            'post_id'             => $post->id,
            'number_of_employee'  => '6',
            'duration'            => '1.5',
            'type_job'            => 'full-time',
            'type_salary'         => 'flexible',
            'system'              => 'wfh',
        ]);

        Contract::create([
            'contract_type' => Job::class,
            'contract_type_id' => $job->id,
            'client_id' => User::pluck('id')->random(), 
            'provider_id' => $user->id,
            'contract_date' => now(),
            'status' => 'active',
        ]);

        $company = Company::create([
            'user_id'      => $user->id,
            'name'         => 'bossware',
            'slug'         => 'bossware',
            'description'  => 'A leading software development company',
            'cover_image'  => 'https://source.unsplash.com/random',
            'address'      => 'jl gajayana malang, jawa-timur, indonesia',
            'industry'     => 'Technology',
            'website'      => 'bossware.com',
            'social_links' => json_encode([
                'linkedin' => 'https://linkedin.com/company/bossware',
                'twitter'  => 'https://twitter.com/bossware'
            ]),
            'founded_at'   => now(),
        ]);

        UserProfile::create([
            'user_id'           => $user->id,
            'full_name'         => 'Naufal Maulana Rafiq',
            'portofolio_url'    => 'https://namara.dev/portofolio',
            'bio'               => 'Full-stack web developer dengan pengalaman 3+ tahun dalam membangun aplikasi berbasis Laravel dan React. Siap membantu membangun solusi digital untuk bisnis Anda.',
        ]);

        Catalog::create([
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

        Location::create([
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

       $skill = Skill::create([
            'skill_name' => 'Laravel',
            'description' => 'Laravel adalah framework PHP yang digunakan untuk membangun aplikasi web dengan sintaks yang elegan dan expressive.',
        ]);

        UserSkills::create([
            'user_id' => $user->id,
            'skill_id'=> $skill->id,
        ]);

       $freelancer = Freelancer::create([
            'name' => 'John Doe', // Nama Freelancer
            'description' => 'Freelancer dengan keahlian di bidang Web Development, khususnya menggunakan Laravel dan VueJS.',
            'user_id' => $user->id, // ID user yang terhubung
            'skills' => json_encode(["PHP", "Laravel", "VueJS", "JavaScript"]), // Menyimpan skills dalam format JSON
            'experience_years' => 5, // Pengalaman dalam tahun
            'rating' => 4.5, // Rating freelancer
            'salary' => 8000000, // Gaji yang diinginkan
            'portofolio_id' => $portofolio->id, // ID portofolio yang terhubung
            'category_id' => $category->id, // ID kategori yang terhubung
            'created_at' => now(), // Tanggal pembuatan
            'updated_at' => now(), // Tanggal update
        ]);

        Friendship::create([
            'user_id' => $user->id,
            'friend_id' => $user->id, // dummy
            'status' => 'accepted',
        ]);

        EmployeesCompany::create([
            'company_id' => $company->id,
            'employee_id' => $user->id,
            'position' => 'CTO',
            'status' => 'active'
        ]);

        Review::create([
            'post_id'     => $post->id,
            'reviewer_id' => $user->id,
            'reviewee_id' => $user->id,
            'rating'      => 5,
            'comment'     => 'Kerja cepat dan profesional!',
        ]);





    }
}
