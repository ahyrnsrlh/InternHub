<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\DailyLog;
use App\Models\MentorReview;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class InternHubSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@internhub.test'],
            [
                'name' => 'Elena Rodriguez',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'title' => 'Program Admin',
                'department' => 'Program Strategy',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $mentor = User::query()->updateOrCreate(
            ['email' => 'mentor@internhub.test'],
            [
                'name' => 'Marcus Wright',
                'password' => Hash::make('password'),
                'role' => 'mentor',
                'title' => 'Senior Mentor',
                'department' => 'Wealth Management',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $internA = User::query()->updateOrCreate(
            ['email' => 'alex@internhub.test'],
            [
                'name' => 'Alex Rivers',
                'password' => Hash::make('password'),
                'role' => 'intern',
                'title' => 'Tier 1 Intern',
                'department' => 'Strategic Architecture',
                'placement' => 'Product Design',
                'status' => 'active',
                'mentor_id' => $mentor->id,
                'email_verified_at' => now(),
            ]
        );

        $internB = User::query()->updateOrCreate(
            ['email' => 'sarah@internhub.test'],
            [
                'name' => 'Sarah Jenkins',
                'password' => Hash::make('password'),
                'role' => 'intern',
                'title' => 'Marketing Intern',
                'department' => 'Financial Analytics',
                'placement' => 'Financial Analytics',
                'status' => 'active',
                'mentor_id' => $mentor->id,
                'email_verified_at' => now(),
            ]
        );

        foreach ([
            ['name' => 'Investment Banking', 'cohort' => 'Cohort 2024', 'quarter' => 'Q3', 'is_active' => true],
            ['name' => 'Legal Excellence', 'cohort' => 'Cohort 2024', 'quarter' => 'Q4', 'is_active' => true],
            ['name' => 'Product Leadership', 'cohort' => 'Cohort 2025', 'quarter' => 'Q1', 'is_active' => true],
        ] as $programData) {
            Program::query()->updateOrCreate(
                [
                    'name' => $programData['name'],
                    'cohort' => $programData['cohort'],
                    'quarter' => $programData['quarter'],
                ],
                ['is_active' => $programData['is_active']]
            );
        }

        foreach (range(0, 6) as $offset) {
            $date = Carbon::now()->subDays($offset)->toDateString();
            $status = match ($offset) {
                1 => 'late',
                3 => 'on_leave',
                default => 'present',
            };

            AttendanceRecord::query()->updateOrCreate(
                ['user_id' => $internA->id, 'work_date' => $date],
                [
                    'check_in' => $status === 'on_leave' ? null : ($status === 'late' ? '09:15:00' : '08:55:00'),
                    'check_out' => $status === 'on_leave' ? null : '17:05:00',
                    'duration_minutes' => $status === 'on_leave' ? 0 : ($status === 'late' ? 465 : 490),
                    'status' => $status,
                ]
            );
        }

        $logTemplates = [
            ['summary' => 'Refined UI component library for enterprise dashboard.', 'status' => 'approved', 'hours' => 8.5, 'deliverable' => 'ui_library_v2.pdf'],
            ['summary' => 'Conducted stakeholder interview with CDO regarding roadmap.', 'status' => 'pending', 'hours' => 7.0, 'deliverable' => 'interview_notes.docx'],
            ['summary' => 'Quarterly project alignment workshop and sprint planning.', 'status' => 'revision_required', 'hours' => 6.0, 'deliverable' => null],
        ];

        foreach ($logTemplates as $index => $template) {
            $log = DailyLog::query()->updateOrCreate(
                ['user_id' => $internA->id, 'log_date' => Carbon::now()->subDays($index + 1)->toDateString()],
                [
                    'department' => 'Strategic Architecture',
                    'summary' => $template['summary'],
                    'deliverable' => $template['deliverable'],
                    'hours' => $template['hours'],
                    'status' => $template['status'],
                ]
            );

            MentorReview::query()->updateOrCreate(
                ['daily_log_id' => $log->id, 'mentor_id' => $mentor->id],
                [
                    'status' => $template['status'] === 'approved' ? 'approved' : 'pending_review',
                    'comment' => $template['status'] === 'approved'
                        ? 'Great execution and clear outcomes.'
                        : 'Please provide more measurable outcomes in your next revision.',
                    'reviewed_at' => $template['status'] === 'approved' ? now()->subDay() : null,
                ]
            );
        }

        DailyLog::query()->updateOrCreate(
            ['user_id' => $internB->id, 'log_date' => Carbon::now()->subDays(2)->toDateString()],
            [
                'department' => 'Financial Analytics',
                'summary' => 'Prepared monthly expense reconciliation dashboard.',
                'deliverable' => 'expense_reconciliation.xlsx',
                'hours' => 7.5,
                'status' => 'approved',
            ]
        );
    }
}
