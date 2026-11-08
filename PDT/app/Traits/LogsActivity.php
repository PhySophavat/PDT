<?php

namespace App\Traits;

use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function (Model $model) {
            $module = $model->getActivityModule();
            $newValues = $model->getAttributes();
            ActivityLogger::log(
                action: 'CREATED',
                module: $module,
                targetId: $model->getKey(),
                description: "បានបង្កើតទិន្នន័យថ្មីក្នុងផ្នែក «{$module}» (ID: {$model->getKey()})",
                oldValues: null,
                newValues: $newValues
            );
        });

        static::updated(function (Model $model) {
            $changes = $model->getChanges();
            $original = $model->getOriginal();

            $oldValues = [];
            $newValues = [];

            foreach ($changes as $key => $newValue) {
                // Ignore timestamp columns from activity diffs
                if (in_array($key, ['updated_at', 'remember_token'])) {
                    continue;
                }
                $oldValues[$key] = $original[$key] ?? null;
                $newValues[$key] = $newValue;
            }

            // Only log if meaningful columns changed
            if (!empty($newValues)) {
                $module = $model->getActivityModule();
                ActivityLogger::log(
                    action: 'UPDATED',
                    module: $module,
                    targetId: $model->getKey(),
                    description: "បានកែប្រែទិន្នន័យក្នុងផ្នែក «{$module}» (ID: {$model->getKey()})",
                    oldValues: $oldValues,
                    newValues: $newValues
                );
            }
        });

        static::deleted(function (Model $model) {
            $module = $model->getActivityModule();
            $oldValues = $model->getOriginal();
            ActivityLogger::log(
                action: 'DELETED',
                module: $module,
                targetId: $model->getKey(),
                description: "បានលុបទិន្នន័យចេញពីផ្នែក «{$module}» (ID: {$model->getKey()})",
                oldValues: $oldValues,
                newValues: null
            );
        });
    }

    /**
     * Determine Khmer module name for this model
     */
    public function getActivityModule(): string
    {
        if (property_exists($this, 'activityModule') && !empty($this->activityModule)) {
            return $this->activityModule;
        }

        $map = [
            'Book' => 'បណ្ណាល័យ',
            'BookBorrow' => 'ការខ្ចី-សងសៀវភៅ',
            'CourseSubject' => 'មុខវិជ្ជា',
            'Student' => 'ព័ត៌មានសិស្ស',
            'Teacher' => 'ព័ត៌មានគ្រូបង្រៀន',
            'SchoolClass' => 'ថ្នាក់រៀន',
            'ClassRoom' => 'ថ្នាក់រៀន',
            'Attendance' => 'វត្តមានសិស្ស',
            'TeacherAttendance' => 'វត្តមានគ្រូបង្រៀន',
            'Examination' => 'ការប្រឡង',
            'ExamScore' => 'ពិន្ទុប្រឡង',
            'Invoice' => 'វិក្កយបត្រ / ហិរញ្ញវត្ថុ',
            'Payment' => 'ការទូទាត់ប្រាក់',
            'SchoolExpense' => 'ចំណាយសាលា',
            'SystemSetting' => 'ការកំណត់ប្រព័ន្ធ',
            'Announcement' => 'សេចក្តីជូនដំណឹង',
            'User' => 'គណនីអ្នកប្រើប្រាស់',
            'Role' => 'តួនាទី និងសិទ្ធិ',
        ];

        $class = class_basename($this);
        return $map[$class] ?? $class;
    }
}

