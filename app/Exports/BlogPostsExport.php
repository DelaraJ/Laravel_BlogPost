<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Blog;
use Carbon\Carbon;

class BlogPostsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;  
    protected $endDate;  

    public function __construct(Carbon $startDate, Carbon $endDate)  
    {   
        $this->startDate = $startDate; 
        $this->endDate = $endDate;  
    }  

    public function collection(): Collection
    {
        return Blog::with(['user', 'tags'])
            ->whereBetween('created_at', [
                $this->startDate->startOfDay(), 
                $this->endDate->endOfDay()
                ])
            ->get();
    }
    
    public function headings(): array {
        return [  
            'Title',  
            'Content',  
            'Author',  
            'Author Email',  
            'Tags',  
            'Likes Count',  
            'Create Date',
            'Update Date',
        ];
    }
    
    public function map($blog): array {
        return [  
            $blog->title,  
            $blog->content,  
            $blog->user->name,  
            $blog->user->email,  
            $blog->tags->pluck('name')->implode(', '),  
            $blog->like_count,
            $blog->created_at,
            $blog->updated_at,
        ];
    }
}
