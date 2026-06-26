<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContactMessageStatus;
use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\CareerPost;
use App\Models\Center;
use App\Models\ContactMessage;
use App\Models\Service;
use App\Models\Tariff;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $metrics = [
            'total_centers' => Center::query()->count(),
            'operational_centers' => Center::query()->operational()->count(),
            'expansion_centers' => Center::query()->expansion()->count(),
            'total_services' => Service::query()->count(),
            'total_tariffs' => Tariff::query()->count(),
            'total_bookings' => Booking::query()->count(),
            'pending_bookings' => Booking::query()->pending()->count(),
            'total_contact_messages' => ContactMessage::query()->count(),
            'unread_contact_messages' => ContactMessage::query()->status(ContactMessageStatus::NEW)->count(),
            'published_blog_posts' => BlogPost::query()->published()->count(),
            'open_career_posts' => CareerPost::query()->open()->count(),
        ];

        return view('admin.dashboard', [
            'metrics' => $metrics,
            'cards' => $this->cards($metrics),
        ]);
    }

    /**
     * @param  array<string, int>  $metrics
     * @return array<int, array<string, string|int>>
     */
    private function cards(array $metrics): array
    {
        return [
            [
                'label' => 'Total centers',
                'value' => $metrics['total_centers'],
                'icon' => 'building-2',
                'tone' => 'slate',
            ],
            [
                'label' => 'Operational centers',
                'value' => $metrics['operational_centers'],
                'icon' => 'badge-check',
                'tone' => 'green',
            ],
            [
                'label' => 'Under construction',
                'value' => $metrics['expansion_centers'],
                'icon' => 'construction',
                'tone' => 'amber',
            ],
            [
                'label' => 'Total services',
                'value' => $metrics['total_services'],
                'icon' => 'clipboard-check',
                'tone' => 'slate',
            ],
            [
                'label' => 'Total tariffs',
                'value' => $metrics['total_tariffs'],
                'icon' => 'banknote',
                'tone' => 'slate',
            ],
            [
                'label' => 'Total bookings',
                'value' => $metrics['total_bookings'],
                'icon' => 'calendar-days',
                'tone' => 'slate',
            ],
            [
                'label' => 'Pending bookings',
                'value' => $metrics['pending_bookings'],
                'icon' => 'calendar-clock',
                'tone' => 'orange',
            ],
            [
                'label' => 'Contact messages',
                'value' => $metrics['total_contact_messages'],
                'icon' => 'inbox',
                'tone' => 'slate',
            ],
            [
                'label' => 'Unread messages',
                'value' => $metrics['unread_contact_messages'],
                'icon' => 'mail-warning',
                'tone' => 'orange',
            ],
            [
                'label' => 'Published posts',
                'value' => $metrics['published_blog_posts'],
                'icon' => 'newspaper',
                'tone' => 'green',
            ],
            [
                'label' => 'Open vacancies',
                'value' => $metrics['open_career_posts'],
                'icon' => 'briefcase-business',
                'tone' => 'green',
            ],
        ];
    }
}
