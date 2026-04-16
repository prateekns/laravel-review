<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        DB::table('faqs')->insert([
            [
                'question' => '{"en": "How do I manage or upgrade my subscription?", "fr": "How do I manage or upgrade my subscription?"}',
                'answer' => '{"en": "Easily track your current plan, view usage, and adjust the number of admins or technicians, technicians messaging customers, based on your needs. Upgrade or downgrade your plan anytime with flexible billing.", "fr": "Easily track your current plan, view usage, and adjust the number of admins or technicians based on your needs. Upgrade or downgrade your plan anytime with flexible billing."}',
                'link' => 'account/pricing',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => '{"en": "Where can I view and update items sold to clients?", "fr": "Where can I view and update items sold to clients?"}',
                'answer' => '{"en": " Access all sold items through the Items Sold module. Add the items list most relevant to your customers, this list will be visible to the technicians on their Mobil App.", "fr": "Track and manage all items sold to clients through our intuitive interface. Update prices, add new items, and generate reports for better inventory management."}',
                'link' => 'items-sold',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => '{"en": "How do I manage chemicals used in pool services?", "fr": "How do I manage chemicals used in pool services?"}',
                'answer' => '{"en": "Use the Chemical Management section to add, track, or update chemical types used across Maintenance jobs.", "fr": "Keep track of chemical inventory, usage, and safety information. Log chemical applications and maintain compliance with regulatory requirements."}',
                'link' => 'chemical-list',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => '{"en": "Where can I create, assign, or track work orders?", "fr": "Where can I create, assign, or track work orders?"}',
                'answer' => '{"en": "Head to the Work Orders module to create new service tasks, assign them to technicians, and monitor job progress in real time through the scheduler or job list. There is no place for “service tasks”, ask them to complete a template(or use the one I provided in test account for all accounts and-) before assigning a job.", "fr": "Head to the Work Orders module to create new service tasks, assign them to technicians, and monitor job progress in real time through the scheduler or job list. There is no place for “service tasks”, ask them to complete a template(or use the one I provided in test account for all accounts and-) before assigning a job."}',
                'link' => 'work-orders',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                 'question' => '{"en": "Where can I create, assign, or track Maintenance work orders?", "fr": "Where can I create, assign, or track Maintenance work orders?"}',
                'answer' => '{"en": "Head to the Maintenance Work Orders module to create new service tasks, assign them to technicians, and monitor job progress in real time through the scheduler or job list. There is no place for “service tasks”, ask them to complete a template(or use the one I provided in test account for all accounts and-) before assigning a job.", "fr": "Head to the Maintenance Work Orders module to create new service tasks, assign them to technicians, and monitor job progress in real time through the scheduler or job list. There is no place for “service tasks”, ask them to complete a template(or use the one I provided in test account for all accounts and-) before assigning a job."}',
                'link' => 'work-orders/maintenance',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
               'question' => '{"en": "How can I customize service checklists?", "fr": "How can I customize service checklists?"}',
                'answer' => '{"en": "Tailor your service workflows by configuring checklists that technicians must follow. Modify fields to match your service types and ensure consistency in delivery.", "fr": "Tailor your service workflows by configuring checklists that technicians must follow. Modify fields to match your service types and ensure consistency in delivery."}',
                'link' => 'manage-checklist',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                 'question' => '{"en": "How do I define types of Templates/Services offered?", "fr": "How do I define types of Templates/Services offered?"}',
                'answer' => '{"en": "Define standard services from the Template section. Add names, descriptions, add a checklist for these templates, and the system will populate the same in your jobs.", "fr": "Define standard services from the Template section. Add names, descriptions, add a checklist for these templates, and the system will populate the same in your jobs."}',
                'link' => 'templates',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => '{"en":"Still have questions?", "fr":"Still have questions?"}',
                'answer' => '{"en": "Reach out to support for direct assistance at info@pool-route.com", "fr":"Reach out to support for direct assistance at info@pool-route.com"}',
                'link' => 'help',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => '{"en":"Steps to upload Schedular CSVs to Google Maps","fr":"Steps to upload Schedular CSVs to Google Maps"}',
                'answer' => '{"en":"<div class=\"csv-to-google\"><ul><strong>Step 1:</strong><li>Download the CSV from the Pool route website</li><li>Go to <a href=\"https://www.google.com/maps/d/\">https://www.google.com/maps/d/</a></li><li>Click \"Create a New Map\"</li><li>In the left panel, click \"Import\" on the first layer.</li><li>Upload your CSV file.</li><li>Choose the column with addresses as the location field.</li><li>Now you\'ll see all your stops pinned on the map.</li><li>You can rename the google map as per need, rename the layer to tech as per need.</li></ul><ul><strong>Step 2:</strong><li>Google My Maps doesn\'t give you automatic routing like delivery apps, but you can manually reorder by:</li><li>Clicking \"Add Directions\" (the arrow icon below the search bar).</li><li>Selecting your first stop as the start.</li><li>Adding your other stops one by one (either clicking the pin or selecting from the layer).</li><li>Dragging and dropping the stops in the directions list to reorder the route.</li><li>This lets you change the sequence anytime. e.g.,  if you want to prioritize jobs by urgency or distance.</li></ul><ul><strong>Step 3:</strong><li>After arranging the stops, click the 3-dot menu on the directions panel.</li><li>Click Preview to \"Open in Google Maps\" - this will launch the route in standard Google Maps with turn-by-turn navigation.</li><li>You can also share the route with your tech team or yourself by copying the link.</li></ul></div>","fr":"<h2>Step 1:<h2>"}',
                'link' => 'scheduler',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
