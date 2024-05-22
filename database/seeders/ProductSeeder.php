<?php

namespace Database\Seeders;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobseeker50connections = Product::create([
            "product_code" => "prod_Q9MAuf2OsSsfTz",
            "name" => "1-50 connections",
            "description" => "Up to 50 connections",
        ]);
        $jobseeker50connections->product_plan()->create([
            'price' => 8.99,
            'recurring' => "monthly",
            "mode" => "subscription",
            "unit_label" => "jobseeker",
            "lookup_key" => "jobseeker-50-connections",
            "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1ah7tk354tdjNJLGquC2RgNLsyowZwKa3ahJZ5V5mczjFt5JNYo7w2CMt#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
            "connection_count" => 50
        ]);

        $jobseeker100connections = Product::create([
            "product_code" => "prod_Q9MBD3GRdWYuO0",
            "name" => "51-100 connections",
            "description" => "Up to 100 connections",
        ]);
        $jobseeker100connections->product_plan()->create([
            'price' => 16.99,
            'recurring' => "monthly",
            "mode" => "subscription",
            "unit_label" => "jobseeker",
            "lookup_key" => "jobseeker-100-connections",
            "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1H3WMfnh0dlY6lUJFUDBgDzIkbRS0QR5HZknodYAnADhn8WBj8K9Y88XG#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
            "connection_count" => 100
        ]);

        $jobseeker200connections = Product::create([
            "product_code" => "prod_Q9MCgKvNDlLz8L",
            "name" => "101-200 connections",
            "description" => "Up to 200 connections",
        ]);
        $jobseeker200connections->product_plan()->create([
            'price' => 27.99,
            'recurring' => "monthly",
            "mode" => "subscription",
            "unit_label" => "jobseeker",
            "lookup_key" => "https://checkout.stripe.com/c/pay/cs_test_a1Nar31Glt2xTGmvWJGRcFLXrcPBqvIoYQbuoqKRzihEjskrJyA5UG1Fis#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
            "checkout_url" => "",
            "connection_count" => 200
        ]);

        $jobseeker300connections = Product::create([
            "product_code" => "prod_Q9ME3NlZLgVX5N",
            "name" => "201-300 connections",
            "description" => "Up to 300 connections",
        ]);
        $jobseeker300connections->product_plan()->create([
            'price' => 36.99,
            'recurring' => "monthly",
            "mode" => "subscription",
            "unit_label" => "jobseeker",
            "lookup_key" => "jobseeker-300-connections",
            "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1Bqb5PZCPvz11jM4EGYMPHYhZwLxdQTvaOFrU1YFZAneWWl98PO0MH0U6#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
            "connection_count" => 300
        ]);
    }
}
