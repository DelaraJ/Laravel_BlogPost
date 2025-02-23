<?php

namespace App\Console\Commands;  

use Illuminate\Console\Command;  
use Illuminate\Support\Facades\Validator;  
use App\Exports\BlogPostsExport;
use App\Models\Blog;
use Maatwebsite\Excel\Facades\Excel;  
use Carbon\Carbon;  

class GenerateBlogPostsExportBetweenDates extends Command  
{  
    protected $signature = 'export:blogs-between {start_date} {end_date}';  
    protected $description = 'Generate Excel export of blog posts between specified dates';  

    private const EXPORT_PATH = 'exports/blogs_%s_to_%s.xlsx'; 

    private function storeExcel(Carbon $start, Carbon $end): void {
        $fileName = sprintf(self::EXPORT_PATH, $start->format('Y-m-d'), $end->format('Y-m-d'));  
        $filePath = storage_path("app/{$fileName}"); 

        Excel::store(new BlogPostsExport(startDate: $start, endDate: $end), $fileName);  
        
        $this->info('Blog posts exported to ' . $filePath);  
    }

    public function handle()  
    {  
        $start = Carbon::parse($this->argument('start_date'));  
        $end = Carbon::parse($this->argument('end_date')); 

        $current = $start->copy();

        while ($current <= $end) {  

            $weekEnd = $current->copy()->endOfWeek(); 
            if ($weekEnd > $end) {  
                $weekEnd = $end; // Adjust to the end date if it exceeds the specified end date  
            }  

            $isAnyBlogBetweenDates = Blog::whereBetween('created_at', [$current, $weekEnd])->exists();  
            
           if ($isAnyBlogBetweenDates == true) {  
               $this->storeExcel($current, $weekEnd);  
           } 
           else {  
               $this->info('No blog posts found for the week starting ' . $current->toDateString());  
           }
           
            $current->addWeek()->startOfWeek();   
        }
    }  

}
