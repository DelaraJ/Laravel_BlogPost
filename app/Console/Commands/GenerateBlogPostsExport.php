<?php

namespace App\Console\Commands;

use App\Exports\BlogPostsExport;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;  
use Illuminate\Console\Command;  

class GenerateBlogPostsExport extends Command  
{  
    protected $signature = 'export:blogs';  
    protected $description = 'Generate weekly Excel export of blog posts';  

    public function handle()  
    {
        $startDate = Carbon::now()->subWeek();

        $filePath = storage_path('app\exports\blogs_' . $startDate->format('Y-m-d') . '_to_' . Carbon::now()->format('Y-m-d') . '.xlsx');  
        
        Excel::store(new BlogPostsExport(startDate: $startDate, endDate: Carbon::now()), 'exports\blogs_' . $startDate->format('Y-m-d') . '_to_' . Carbon::now()->format('Y-m-d') . '.xlsx');  

        $this->info('Blog posts exported to ' . $filePath);  
    }  
}  
