<?php

namespace Database\Seeders;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // jobseeker subscription

        $jobseeker9connections = Product::create([
            "product_code" => "prod_QGSccjWbRQR4DZ",
            "name" => "1-9 connections",
            "description" => "Up to 9 connections",
        ]);
        $jobseeker9connections->product_plans()->create([
            'price_code' => 'price_1PPvhLChe3vlhgX37yYy1cDE',
            'price' => 1.00,
            'recurring' => "month",
            "mode" => "subscription",
            "unit_label" => "jobseeker",
            "lookup_key" => "jobseeker-9-connections",
            "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1bvVxyXJ0nx5VXmxnjDLDC59QbsyVDvoMbuHM0zk1PH96amHahAXrO4TN#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
            "connection_count" => 9
        ]);

        $jobseeker10connections = Product::create([
            "product_code" => "prod_QGSdKsyCcIdTO8",
            "name" => "10 connections",
            "description" => "Up to 10 connections",
        ]);
        $jobseeker10connections->product_plans()->create([
            'price_code' => 'price_1PPviGChe3vlhgX33iZh2Z7F',
            'price' => 1.50,
            'recurring' => "month",
            "mode" => "subscription",
            "unit_label" => "jobseeker",
            "lookup_key" => "jobseeker-10-connections",
            "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1mBKCT93dEb5i7ay6SsL6KtiHpxNN1mcj5Pshy9p7n8iqYtUP3zjk6AtY#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
            "connection_count" => 10
        ]);

        $jobseeker20connections = Product::create([
            "product_code" => "prod_QGSeFsLBfKsonn",
            "name" => "20 connections",
            "description" => "Up to 20 connections",
        ]);
        $jobseeker20connections->product_plans()->create([
            'price_code' => 'price_1PPvjOChe3vlhgX3cnT1DwDU',
            'price' => 3.00,
            'recurring' => "month",
            "mode" => "subscription",
            "unit_label" => "jobseeker",
            "lookup_key" => "jobseeker-20-connections",
            "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1T99iYubxIGhokSV5ZXoN8zLgJUDOicGdvEGAYm9NBF1dsqoYfI6P0dL0#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
            "connection_count" => 20
        ]);

        $jobseeker40connections = Product::create([
            "product_code" => "prod_QGSfTaOjDONstL",
            "name" => "40 connections",
            "description" => "Up to 40 connections",
        ]);
        $jobseeker40connections->product_plans()->create([
            'price_code' => 'price_1PPvkDChe3vlhgX3tOE37Lqk',
            'price' => 6.00,
            'recurring' => "month",
            "mode" => "subscription",
            "unit_label" => "jobseeker",
            "lookup_key" => "jobseeker-40-connections",
            "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1VGYt6NGXVHCX3O7B21VDQMScQKsyuvYan2friMRZkIQj4KSFnYo1p394#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
            "connection_count" => 40
        ]);

        $jobseeker60connections = Product::create([
            "product_code" => "prod_QGSgsW5TLTuoro",
            "name" => "40 connections",
            "description" => "Up to 40 connections",
        ]);
        $jobseeker60connections->product_plans()->create([
            'price_code' => 'price_1PPvlIChe3vlhgX3iCelrsES',
            'price' => 9.00,
            'recurring' => "month",
            "mode" => "subscription",
            "unit_label" => "jobseeker",
            "lookup_key" => "jobseeker-60-connections",
            "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1V8pn1POcNnlaE89qDEpTC6bV0Tax4Aj2llpK3OTICcb4oGMGt3rQ3PsI#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
            "connection_count" => 60
        ]);

        $jobseeker80connections = Product::create([
            "product_code" => "prod_QGShTcip1o6LyJ",
            "name" => "80 connections",
            "description" => "Up to 80 connections",
        ]);
        $jobseeker80connections->product_plans()->create([
            'price_code' => 'price_1PPvm6Che3vlhgX3npDHj6Q0',
            'price' => 12.00,
            'recurring' => "month",
            "mode" => "subscription",
            "unit_label" => "jobseeker",
            "lookup_key" => "jobseeker-80-connections",
            "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1Ut5WJSTVUd35k0GARryV5byiNCHjWUkZlZn2etDymaDKVzzV2vpxqJTX#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
            "connection_count" => 60
        ]);

        $jobseeker100connections = Product::create([
            "product_code" => "prod_QGShJedsVJD3Rn",
            "name" => "100 connections",
            "description" => "Up to 100 connections",
        ]);
        $jobseeker100connections->product_plans()->create([
            "price_code" => "price_1PPvmVChe3vlhgX3JigrDcim",
            "price" => 15.00,
            "recurring" => "month",
            "mode" => "subscription",
            "unit_label" => "jobseeker",
            "lookup_key" => "jobseeker-100-connections",
            "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1V7NmofQF3ZoGMk7ZwOkWCPuoL48w2znGoqDIaaoysrMVroCj4YJxmSfO#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl"
        ]);

        $jobseeker150connections = Product::create([
            "product_code" => "prod_QGSisdsXztA3Fj",
            "name" => "150 connections",
            "description" => "Up to 150 connections",
        ]);
        $jobseeker150connections->product_plans()->create([
            "price_code" => "price_1PPvnGChe3vlhgX3mA9RUHi6",
            "price" => 22.50,
            "recurring" => "month",
            "mode" => "subscription",
            "unit_label" => "jobseeker",
            "lookup_key" => "jobseeker-150-connections",

            "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a11b47zPuAQbGA2I88v2Fv9MHqrJIF6E3tiME1KU8qr85GAjCaWuFFQohV#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl"
        ]);

        $jobseeker200connections = Product::create([
            "product_code" => "prod_QGSjDZWoCIHgmE",
            "name" => "200 connections",
            "description" => "Up to 200 connections",
        ]);
        $jobseeker200connections->product_plans()->create([
            "price_code" => "price_1PPvoAChe3vlhgX3hlCNk73f",
            "price" => 30.00,
            'recurring' => "month",
            "mode" => "subscription",
            "unit_label" => "jobseeker",
            "lookup_key" => "jobseeker-200-connections",
            "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1kUW3jlwMnXbauXEC5OlRkGkGgxLhS9jeqQVPV9Kj8SRbmDCgO7AcLkPY#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl"

        ]);

        $jobseeker250connections = Product::create([
            "product_code" => "prod_QGSkUK3J5p5yM4",
            "name" => "250 connections",
            "description" => "Up to 250 connections",
        ]);
        $jobseeker250connections->product_plans()->create([
            'price_code' => 'price_1PPvopChe3vlhgX3BGkq2LPf',
            'price' => 37.50,
            'recurring' => "month",
            "mode" => "subscription",
            "unit_label" => "jobseeker",
            "lookup_key" => "jobseeker-250-connections",
            "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a14NTgjmzJ2XkrWXK1XxhnSGzPoK9CG2RnhDk4jhAtz6e4FEfJfti3O01z#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
            "connection_count" => 250
        ]);

        $jobseeker300connections = Product::create([
            "product_code" => "prod_QGSk1p9K1freu6",
            "name" => "300 connections",
            "description" => "Up to 300 connections",
        ]);
        $jobseeker300connections->product_plans()->create([
            'price_code' => 'price_1PPvpLChe3vlhgX391uV1lYx',
            'price' => 45.00,
            'recurring' => "month",
            "mode" => "subscription",
            "unit_label" => "jobseeker",
            "lookup_key" => "jobseeker-300-connections",
            "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1FJAGDZVUKTWRw3dvmwEVusAzrc1uLaloZYGnIv1cUnE9mUOqmp8NRDFj#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
            "connection_count" => 300
        ]);

        // employer
        $posting10 = Product::create([
            "product_code" => "prod_QEaCk0aloAns2e",
            "name" => "Posting (10 applicants)",
            "description" => "Ideal for small businesses or specialized roles, this plan ensures you receive a manageable number of high-quality applicants. Pay only for the applicants you receive, making it a cost-effective solution for targeted recruitment.",
        ]);
        $posting10->product_plans()->create([
            "price_code" => "price_1PO72CChe3vlhgX33HU8Nvv9",
            "price" => 499.00,
            "recurring" => "month",
            "mode" => "subscription",
            "unit_label" => "employer",
            "lookup_key" => "na",
            "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a19YmIyJxStx9966M6rXLzF8vbkzCbkmwi7Xl3rJ7Nw5p4Ttxo0EAPWy47#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
            "post_count" => 10,

        ]);

        $posting30 = Product::create([
            "product_code" => "prod_QEaDseKEl7WejA",
            "name" => "Posting (30 applicants)",
            "description" => "Perfect for growing businesses with moderate hiring needs, this plan offers a balance between cost and quantity. Receive a steady stream of candidates without overspending, ensuring you find the right fit for your team.",
        ]);
        $posting30->product_plans()->create([
            "price_code" => "price_1PO732Che3vlhgX3Eta28c13",
            "price" => 999.00,
            "recurring" => "month",
            "mode" => "subscription",
            "unit_label" => "employer",
            "lookup_key" => "na",
            "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a16uG9vX0ug1q0g0sxzK5xl8TjhX0wqcPYryOyOJq87nMdc1vf6o1OyBA7#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
            "post_count" => 30,
        ]);

        $posting50 = Product::create([
            "product_code" => "prod_QEaErbv658hhDw",
            "name" => "Posting (50 applicants)",
            "description" => "Best suited for larger organizations or high-volume recruitment, this plan provides a substantial number of applicants. Invest in this plan to efficiently meet your hiring demands and access a broad pool of potential candidates."
        ]);
        $posting50->product_plans()->create([
            "price_code" => "price_1PO73fChe3vlhgX3eSqpcmlf",
            "price" =>  499.00,
            "recurring" => "month",
            "mode" => "subscription",
            "unit_label" => "employer",
            "lookup_key" => "na",
            "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1s3glXMDnbers1fiFJ4fo3koskbhU1styfWlUfbvleVFvCpncXf9pBS3Z#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
            "post_count" => 50,
        ]);

        $singlePlan = Product::create([
            "product_code" => "prod_Q9MH6RlQ6syLeL",
            "name" => "Single Plan",
            "description" => "1 post and 10 applicants per post per month. Perfect for small businesses or occasional hiring needs.",
        ]);

        $singleProductPlan = [
            [
                "price_code" => "price_1PPvJSChe3vlhgX3na97xNFU",
                "price" => 20.00,
                "mode" => "subscription",
                "unit_label" => "employer",
                "lookup_key" => "na",
                "recurring" => "month",
                "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1svkiqyHyHkrA4tVZ9pBdwrAsXnU01p1eFZeHsq4vJ5pgE78Y2O254wsq#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
                "post_count" => 1,
                "applicant_count" => 10,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "price_code" => "price_1PPvJlChe3vlhgX3Us9ZTSjU",
                "price" => 117.00,
                "mode" => "subscription",
                "unit_label" => "employer",
                "lookup_key" => "na",
                "recurring" => "year",
                "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1iCQG1ZLN4zkMvxh45TJvGuNDe3LTRM1xhKl3Bi9KrVoQbSCvN2EdEng3#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
                "post_count" => 1,
                "applicant_count" => 10,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
        ];

        $singlePlan->product_plans()->insert($singleProductPlan);

        $familyPlan = Product::create([
            "product_code" => "prod_Q9MJloheLiCl2U",
            "name" => "Family Plan",
            "description" => "3 posts and 15 applicants per post per month. Ideal for moderate hiring demands.",
        ]);
        $familyProductPlan = [
            [
                "price_code" => "price_1PPvLIChe3vlhgX3cRLSZmIK",
                "price" => 49.00,
                "mode" => "subscription",
                "unit_label" => "employer",
                "lookup_key" => "na",
                "recurring" => "month",
                "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1YKXeQZ6fNiCCsCoCbCE8jTDV5cUuaWmzfJpsrZ29qJ8UdfZCuVTYGQJW#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
                "post_count" => 3,
                "applicant_count" => 15,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "price_code" => "price_1PPvLZChe3vlhgX3yDexWY88",
                "price" => 298.00,
                "mode" => "subscription",
                "unit_label" => "employer",
                "lookup_key" => "na",
                "recurring" => "year",
                "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1XwC0pjnA4ZVnInofwiMrQ9N16SvRFF13ZwgTqdZfq3gdShFG8GyvzcMV#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
                "post_count" => 3,
                "applicant_count" => 15,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ]
        ];
        $familyPlan->product_plans()->insert($familyProductPlan);

        $extendedPlan = Product::create([
            "product_code" => "prod_Q9MMMH7NaIMZaW",
            "name" => "Extended Plan",
            "description" => "10 job posts with up to 20 applicants per post per month. Best for larger businesses with significant recruitment needs.",
        ]);

        $extendedProductPlan = [
            [
                "price_code" => "price_1PPvMSChe3vlhgX3MRjalT36",
                "price" => 69.00,
                "mode" => "subscription",
                "unit_label" => "employer",
                "lookup_key" => "na",
                "recurring" => "month",
                "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1lVJl8hc8MjYdiigwc0wIovvb5fGzJl6xpVIVbNflELaCSNW11K7gignN#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
                "post_count" => 10,
                "applicant_count" => 20,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                "price_code" => "price_1PPvMiChe3vlhgX3lVBA7ohc",
                "price" => 417.00,
                "mode" => "subscription",
                "unit_label" => "employer",
                "lookup_key" => "na",
                "recurring" => "year",
                "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1ncwjuNMiJLHYB0roX705QFQVtFUosWvcBmXPQIRHLrk7yURnAga07Qt8#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
                "post_count" => 10,
                "applicant_count" => 20,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]
        ];
        $extendedPlan->product_plans()->insert($extendedProductPlan);

        $company1 = Product::create([
            "product_code" => "prod_Q9MOSJQlkICTyh",
            "name" => "Company Plan #1",
            "description" => "Includes 20 posts and 30 applicants per post per month. Ideal for growing businesses.",
        ]);

        $company1ProductPlan = [
            [
                "price_code" => "price_1PPvNJChe3vlhgX3KAVRVH7s",
                "price" => 104.00,
                "mode" => "subscription",
                "unit_label" => "employer",
                "lookup_key" => "na",
                "recurring" => "month",
                "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1qqkicfUyK6XB3NfN1HnU1RcEJwZ8bEpbTG7quJPlhwAJMdTSDO2VwPjr#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
                "post_count" => 20,
                "applicant_count" => 30,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                "price_code" => "price_1PPvNSChe3vlhgX3Mvevupt6",
                "price" => 627.00,
                "recurring" => "year",
                "mode" => "subscription",
                "unit_label" => "employer",
                "lookup_key" => "na",
                "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1hdqTI6NGrgNX7bboo2Nmg87zXULAvSmiP2DyRbXHLp5FvfDcLNM9U7js#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
                "post_count" => 20,
                "applicant_count" => 30,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]
        ];

        $company1->product_plans()->insert($company1ProductPlan);

        $company2 = Product::create([
            "product_code" => "prod_Q9MPY7scX06iRZ",
            "name" => "Company Plan #2",
            "description" => "Includes 50 posts and 50 applicants per post per month. Perfect for large-scale hiring.",
        ]);

        $company2ProductPlan = [
            [
                "price_code" => "price_1PPvQZChe3vlhgX3gZKZwEk7",
                "price" => 199.00,
                "recurring" => "month",
                "mode" => "subscription",
                "unit_label" => "employer",
                "lookup_key" => "na",
                "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1Hr9Uv7yLZ4PSIDDVXJkMjH11QMSobDmQDYl3r3Anneta4gjW4Yy4nSey#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
                "post_count" => 50,
                "applicant_count" => 50,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ],
            [
                "price_code" => "price_1PPvQnChe3vlhgX3wbzysQry",
                "price" => 1, 194.00,
                "recurring" => "year",
                "mode" => "subscription",
                "unit_label" => "employer",
                "lookup_key" => "na",
                "checkout_url" => "https://checkout.stripe.com/c/pay/cs_test_a1ZW6OAH4JkCvujxyGZUu4xb9ITqakDQiO6lg9CFLduSll9dvUDD4OBMYj#fid2cGd2ZndsdXFsamtQa2x0cGBrYHZ2QGtkZ2lgYSc%2FY2RpdmApJ2R1bE5gfCc%2FJ3VuWnFgdnFaMDRVTE80MUZtYDZzaW1iXTZrcVVqQGN8M3VgVGA2Vmw3NzVAcDIyQ1BXTjdgMTFpfW1MT3FNcTF0Q1xocEhmY24wUWhOdFdoT01QczJ0YW8zRk5Kan83X2s1NVFzV0dJNlBoJyknY3dqaFZgd3Ngdyc%2FcXdwYCknaWR8anBxUXx1YCc%2FJ3Zsa2JpYFpscWBoJyknYGtkZ2lgVWlkZmBtamlhYHd2Jz9xd3BgeCUl",
                "post_count" => 50,
                "applicant_count" => 50,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ]
        ];
        $company2->product_plans()->insert($company2ProductPlan);
    }
}
