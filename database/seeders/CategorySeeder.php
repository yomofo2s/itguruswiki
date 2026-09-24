<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /** Knowledge base categories. Safe to run repeatedly. */
    public function run(): void
    {
        $categories = [
            ['study', 'Study in Germany', 'Admission, blocked accounts, universities, language courses and student life.', 'graduation'],
            ['visa-residence', 'Visa & Residence', 'Visa types, residence permits, Blue Card, appointments at the Ausländerbehörde.', 'passport'],
            ['work-careers', 'Work & Careers', 'Job search, CVs, recognition of qualifications, contracts and workers\' rights.', 'briefcase'],
            ['it-careers', 'IT Careers', 'Getting into tech in Germany: roles, salaries, certifications and interviews.', 'code'],
            ['family-reunion', 'Family Reunion', 'Bringing spouses and children, required documents and A1 German.', 'family'],
            ['housing', 'Housing', 'Finding a flat, Anmeldung, Schufa, rental contracts and deposits.', 'home'],
            ['health-insurance', 'Health & Insurance', 'Public vs. private health insurance, doctors and other important insurance.', 'health'],
            ['everyday-life', 'Everyday Life', 'Banking, taxes, driving licence, shopping and settling in.', 'sparkles'],
        ];

        foreach ($categories as $i => [$slug, $name, $description, $icon]) {
            Category::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'description' => $description, 'icon' => $icon, 'sort_order' => ($i + 1) * 10],
            );
        }
    }
}
