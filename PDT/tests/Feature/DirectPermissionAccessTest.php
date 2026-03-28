<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectPermissionAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_direct_manage_teacher_permission_can_open_teacher_index(): void
    {
        $user = User::create([
            'name' => 'Teacher Manager',
            'email' => 'teacher-manager@example.com',
            'password' => 'password',
        ]);
        $permission = Permission::create([
            'name' => 'manage-teacher',
            'display_name' => 'Manage Teachers',
        ]);

        $user->permissions()->attach($permission);

        $response = $this->actingAs($user)->get(route('teacher.index'));

        $response->assertOk();
    }

    public function test_user_with_direct_class_and_subject_permissions_can_edit_only_allowed_scores(): void
    {
        $user = User::create([
            'name' => 'Math Operator',
            'email' => 'math-operator@example.com',
            'password' => 'password',
        ]);

        $classPermission = Permission::create([
            'name' => 'class-7A',
            'display_name' => 'Class 7A',
        ]);

        $subjectPermission = Permission::create([
            'name' => 'subject-math',
            'display_name' => 'Math',
        ]);

        $user->permissions()->attach([$classPermission->id, $subjectPermission->id]);

        $studentUser = User::create([
            'name' => 'Student User',
            'email' => 'student-user@example.com',
            'password' => 'password',
        ]);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'gender' => 'female',
            'grade' => '7A',
        ]);

        $subject = Subject::create([
            'student_id' => $student->id,
            'khmer' => 70,
        ]);

        $this->actingAs($user)->get(route('class.index'))->assertOk();
        $this->actingAs($user)->get(route('class.show', '7A'))->assertOk();

        $formResponse = $this->actingAs($user)->get(route('subject.create', $student->id));

        $formResponse->assertOk();
        $formResponse->assertSee('name="math"', false);
        $formResponse->assertDontSee('name="khmer"', false);

        $saveResponse = $this->actingAs($user)->post(route('subject.store', $student->id), [
            'math' => 95,
            'khmer' => 10,
        ]);

        $saveResponse->assertRedirect(route('subject.create', $student->id));

        $subject->refresh();

        $this->assertEquals(95, $subject->math);
        $this->assertEquals(70, $subject->khmer);
    }
}
