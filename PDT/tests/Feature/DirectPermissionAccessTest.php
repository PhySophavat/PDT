<?php

namespace Tests\Feature;

use Carbon\Carbon;
use App\Models\Permission;
use App\Models\Role;
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
        $formResponse->assertSee('name="coefficient"', false);
        $formResponse->assertDontSee('name="khmer"', false);

        $saveResponse = $this->actingAs($user)->post(route('subject.store', $student->id), [
            'math' => 95,
            'khmer' => 10,
            'coefficient' => 2,
            'coefficient_changed' => 1,
        ]);

        $saveResponse->assertRedirect(route('subject.create', $student->id));

        $subject->refresh();

        $this->assertEquals(95, $subject->math);
        $this->assertEquals(70, $subject->khmer);
        $this->assertEquals(2.0, $subject->coefficient);
    }

    public function test_teacher_without_direct_subject_permission_cannot_open_score_form(): void
    {
        $user = User::create([
            'name' => 'Restricted Teacher',
            'email' => 'restricted-teacher@example.com',
            'password' => 'password',
        ]);

        $teacherRole = Role::create([
            'name' => 'teacher',
            'display_name' => 'Teacher',
        ]);
        $user->roles()->attach($teacherRole);

        $classPermission = Permission::create([
            'name' => 'class-7A',
            'display_name' => 'Class 7A',
        ]);
        $user->permissions()->attach($classPermission);

        $studentUser = User::create([
            'name' => 'Score Student',
            'email' => 'score-student@example.com',
            'password' => 'password',
        ]);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'gender' => 'male',
            'grade' => '7A',
        ]);

        $classResponse = $this->actingAs($user)->get(route('class.show', '7A'));
        $classResponse->assertOk();
        $classResponse->assertDontSee(route('subject.create', $student->id));

        $formResponse = $this->actingAs($user)->get(route('subject.create', $student->id));

        $formResponse->assertForbidden();
    }

    public function test_result_page_shows_all_subjects_and_uses_coefficient_for_average(): void
    {
        $user = User::create([
            'name' => 'Result Viewer',
            'email' => 'result-viewer@example.com',
            'password' => 'password',
        ]);

        $classPermission = Permission::create([
            'name' => 'class-7A',
            'display_name' => 'Class 7A',
        ]);
        $user->permissions()->attach($classPermission);

        $studentUser = User::create([
            'name' => 'Full Score Student',
            'email' => 'full-score-student@example.com',
            'password' => 'password',
        ]);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'gender' => 'female',
            'grade' => '7A',
        ]);

        Subject::create([
            'student_id' => $student->id,
            'math' => 10,
            'khmer' => 20,
            'english' => 30,
            'history' => 40,
            'geography' => 50,
            'chemistry' => 60,
            'physics' => 70,
            'biology' => 80,
            'ethics' => 90,
            'sports' => 100,
            'coefficient' => 10,
        ]);

        $response = $this->actingAs($user)->get(route('result.show', '7A'));

        $response->assertOk();
        $response->assertSee('គីមីវិទ្យា');
        $response->assertSee('រូបវិទ្យា');
        $response->assertSee('ជីវវិទ្យា');
        $response->assertSee('សីលធម៌');
        $response->assertSee('កីឡា');
        $response->assertSee('550.00');
        $response->assertSee('55.00');
    }

    public function test_rank_page_filters_by_selected_month(): void
    {
        $user = User::create([
            'name' => 'Month Viewer',
            'email' => 'month-viewer@example.com',
            'password' => 'password',
        ]);

        $classPermission = Permission::create([
            'name' => 'class-7A',
            'display_name' => 'Class 7A',
        ]);
        $user->permissions()->attach($classPermission);

        $januaryUser = User::create([
            'name' => 'January Student',
            'email' => 'january-student@example.com',
            'password' => 'password',
        ]);
        $januaryStudent = Student::create([
            'user_id' => $januaryUser->id,
            'gender' => 'female',
            'grade' => '7A',
        ]);

        $februaryUser = User::create([
            'name' => 'February Student',
            'email' => 'february-student@example.com',
            'password' => 'password',
        ]);
        $februaryStudent = Student::create([
            'user_id' => $februaryUser->id,
            'gender' => 'male',
            'grade' => '7A',
        ]);

        $januarySubject = Subject::create([
            'student_id' => $januaryStudent->id,
            'math' => 10,
            'coefficient' => 1,
        ]);
        $januarySubject->forceFill([
            'created_at' => Carbon::create(2026, 1, 10, 8, 0, 0),
            'updated_at' => Carbon::create(2026, 1, 10, 8, 0, 0),
        ])->save();

        $februarySubject = Subject::create([
            'student_id' => $februaryStudent->id,
            'math' => 20,
            'coefficient' => 1,
        ]);
        $februarySubject->forceFill([
            'created_at' => Carbon::create(2026, 2, 10, 8, 0, 0),
            'updated_at' => Carbon::create(2026, 2, 10, 8, 0, 0),
        ])->save();

        $response = $this->actingAs($user)->get(route('rank.index', ['className' => '7A', 'month' => 1]));

        $response->assertOk();
        $response->assertSee('January Student');
        $response->assertDontSee('February Student');
    }

    public function test_result_page_can_search_students_by_name(): void
    {
        $user = User::create([
            'name' => 'Search Viewer',
            'email' => 'search-viewer@example.com',
            'password' => 'password',
        ]);

        $classPermission = Permission::create([
            'name' => 'class-7A',
            'display_name' => 'Class 7A',
        ]);
        $user->permissions()->attach($classPermission);

        $matchingUser = User::create([
            'name' => 'Sok Dara',
            'email' => 'sok-dara@example.com',
            'password' => 'password',
        ]);
        $matchingStudent = Student::create([
            'user_id' => $matchingUser->id,
            'gender' => 'female',
            'grade' => '7A',
        ]);
        Subject::create([
            'student_id' => $matchingStudent->id,
            'math' => 10,
            'coefficient' => 1,
        ]);

        $hiddenUser = User::create([
            'name' => 'Bopha Chan',
            'email' => 'bopha-chan@example.com',
            'password' => 'password',
        ]);
        $hiddenStudent = Student::create([
            'user_id' => $hiddenUser->id,
            'gender' => 'male',
            'grade' => '7A',
        ]);
        Subject::create([
            'student_id' => $hiddenStudent->id,
            'math' => 20,
            'coefficient' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('result.show', ['className' => '7A', 'search' => 'dara']));

        $response->assertOk();
        $response->assertSee('Sok Dara');
        $response->assertDontSee('Bopha Chan');
    }

    public function test_rank_page_can_search_students_by_name(): void
    {
        $user = User::create([
            'name' => 'Rank Search Viewer',
            'email' => 'rank-search-viewer@example.com',
            'password' => 'password',
        ]);

        $classPermission = Permission::create([
            'name' => 'class-7A',
            'display_name' => 'Class 7A',
        ]);
        $user->permissions()->attach($classPermission);

        $matchingUser = User::create([
            'name' => 'Nita Search',
            'email' => 'nita-search@example.com',
            'password' => 'password',
        ]);
        $matchingStudent = Student::create([
            'user_id' => $matchingUser->id,
            'gender' => 'female',
            'grade' => '7A',
        ]);
        Subject::create([
            'student_id' => $matchingStudent->id,
            'math' => 90,
            'coefficient' => 1,
        ]);

        $hiddenUser = User::create([
            'name' => 'Vanna Hidden',
            'email' => 'vanna-hidden@example.com',
            'password' => 'password',
        ]);
        $hiddenStudent = Student::create([
            'user_id' => $hiddenUser->id,
            'gender' => 'male',
            'grade' => '7A',
        ]);
        Subject::create([
            'student_id' => $hiddenStudent->id,
            'math' => 50,
            'coefficient' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('rank.index', ['className' => '7A', 'search' => 'nita']));

        $response->assertOk();
        $response->assertSee('Nita Search');
        $response->assertDontSee('Vanna Hidden');
    }

    public function test_class_page_can_search_students_by_name(): void
    {
        $user = User::create([
            'name' => 'Class Search Viewer',
            'email' => 'class-search-viewer@example.com',
            'password' => 'password',
        ]);

        $classPermission = Permission::create([
            'name' => 'class-7A',
            'display_name' => 'Class 7A',
        ]);
        $user->permissions()->attach($classPermission);

        $matchingUser = User::create([
            'name' => 'Malis Search',
            'email' => 'malis-search@example.com',
            'password' => 'password',
        ]);
        Student::create([
            'user_id' => $matchingUser->id,
            'gender' => 'female',
            'grade' => '7A',
        ]);

        $hiddenUser = User::create([
            'name' => 'Sreypov Hidden',
            'email' => 'sreypov-hidden@example.com',
            'password' => 'password',
        ]);
        Student::create([
            'user_id' => $hiddenUser->id,
            'gender' => 'female',
            'grade' => '7A',
        ]);

        $response = $this->actingAs($user)->get(route('class.show', ['name' => '7A', 'search' => 'malis']));

        $response->assertOk();
        $response->assertSee('Malis Search');
        $response->assertDontSee('Sreypov Hidden');
    }

    public function test_student_index_can_search_students_by_name(): void
    {
        $user = User::create([
            'name' => 'Student Search Viewer',
            'email' => 'student-search-viewer@example.com',
            'password' => 'password',
        ]);

        $permission = Permission::create([
            'name' => 'view-student',
            'display_name' => 'View Student',
        ]);
        $user->permissions()->attach($permission);

        $matchingUser = User::create([
            'name' => 'Ravy Search',
            'email' => 'ravy-search@example.com',
            'password' => 'password',
        ]);
        Student::create([
            'user_id' => $matchingUser->id,
            'gender' => 'female',
            'grade' => '7A',
        ]);

        $hiddenUser = User::create([
            'name' => 'Davy Hidden',
            'email' => 'davy-hidden@example.com',
            'password' => 'password',
        ]);
        Student::create([
            'user_id' => $hiddenUser->id,
            'gender' => 'male',
            'grade' => '8A',
        ]);

        $response = $this->actingAs($user)->get(route('students.index', ['search' => 'ravy']));

        $response->assertOk();
        $response->assertSee('Ravy Search');
        $response->assertDontSee('Davy Hidden');
    }

    public function test_existing_coefficient_is_not_changed_unless_user_explicitly_changes_it(): void
    {
        $user = User::create([
            'name' => 'Coefficient Keeper',
            'email' => 'coefficient-keeper@example.com',
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
            'name' => 'Stable Coefficient Student',
            'email' => 'stable-coefficient@example.com',
            'password' => 'password',
        ]);
        $student = Student::create([
            'user_id' => $studentUser->id,
            'gender' => 'female',
            'grade' => '7A',
        ]);

        $subject = Subject::create([
            'student_id' => $student->id,
            'math' => 50,
            'coefficient' => 10,
        ]);

        $saveResponse = $this->actingAs($user)->post(route('subject.store', $student->id), [
            'math' => 80,
            'coefficient' => 100,
            'coefficient_changed' => 0,
        ]);

        $saveResponse->assertRedirect(route('subject.create', $student->id));

        $subject->refresh();

        $this->assertEquals(80, $subject->math);
        $this->assertEquals(10.0, $subject->coefficient);
    }
}
