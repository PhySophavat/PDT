import { createRouter, createWebHistory } from 'vue-router';

// Lazy-loaded Views for Code-Splitting & High Performance Initial Load
const DashboardView = () => import('../views/DashboardView.vue');
const AttendanceView = () => import('../views/AttendanceView.vue');
const TeacherAttendanceView = () => import('../views/TeacherAttendanceView.vue');
const StudentView = () => import('../views/StudentView.vue');
const TeacherView = () => import('../views/TeacherView.vue');
const ClassView = () => import('../views/ClassView.vue');
const AcademicYearView = () => import('../views/AcademicYearView.vue');
const CourseSubjectView = () => import('../views/CourseSubjectView.vue');
const TimetableView = () => import('../views/TimetableView.vue');
const ExaminationView = () => import('../views/ExaminationView.vue');
const ParentView = () => import('../views/ParentView.vue');
const FinanceView = () => import('../views/FinanceView.vue');
const LibraryView = () => import('../views/LibraryView.vue');
const AnnouncementView = () => import('../views/AnnouncementView.vue');
const ReportView = () => import('../views/ReportView.vue');
const SettingView = () => import('../views/SettingView.vue');
const RoleView = () => import('../views/RoleView.vue');

const routes = [
  { path: '/', redirect: '/dashboard' },

  // Main pages
  { path: '/attendance', name: 'Attendance', component: AttendanceView },
  { path: '/teacher-attendance', name: 'TeacherAttendance', component: TeacherAttendanceView },
  { path: '/attendance/teachers', redirect: '/teacher-attendance' },
  { path: '/attendances/teachers', redirect: '/teacher-attendance' },
  { path: '/dashboard', name: 'Dashboard', component: DashboardView },

  // Student routes - sub-paths redirect to list (create/edit handled by modal in future)
  { path: '/students', name: 'Students', component: StudentView },
  { path: '/students/create', redirect: '/students' },
  { path: '/students/:id', redirect: '/students' },
  { path: '/students/:id/edit', redirect: '/students' },
  { path: '/students/export', redirect: '/students' },

  // Teacher routes
  { path: '/teachers', name: 'Teachers', component: TeacherView },
  { path: '/teachers/create', redirect: '/teachers' },
  { path: '/teachers/:id', redirect: '/teachers' },
  { path: '/teachers/:id/edit', redirect: '/teachers' },

  // Class routes
  { path: '/classes', name: 'Classes', component: ClassView },
  { path: '/classes/create', redirect: '/classes' },
  { path: '/classes/:name', redirect: '/classes' },
  { path: '/classes/:name/students', redirect: '/students' },

  // Academic Year routes
  { path: '/academic-years', name: 'AcademicYears', component: AcademicYearView },
  { path: '/academic-years/create', redirect: '/academic-years' },
  { path: '/academic-years/:id/edit', redirect: '/academic-years' },

  // Course Subject routes
  { path: '/course-subjects', name: 'CourseSubjects', component: CourseSubjectView },
  { path: '/course-subjects/create', redirect: '/course-subjects' },
  { path: '/course-subjects/:code', redirect: '/course-subjects' },
  { path: '/course-subjects/:code/edit', redirect: '/course-subjects' },

  // Timetable routes
  { path: '/timetables', name: 'Timetables', component: TimetableView },
  { path: '/timetables/create', redirect: '/timetables' },

  // Examination routes
  { path: '/examinations', name: 'Examinations', component: ExaminationView },
  { path: '/examinations/create', redirect: '/examinations' },
  { path: '/examinations/:id/scores', redirect: '/examinations' },
  { path: '/examinations/:id/report-card', redirect: '/examinations' },

  // Parent routes
  { path: '/parents', name: 'Parents', component: ParentView },
  { path: '/parents/create', redirect: '/parents' },
  { path: '/parents/:id', redirect: '/parents' },
  { path: '/parents/:id/edit', redirect: '/parents' },

  // Finance routes
  { path: '/finance', name: 'Finance', component: FinanceView },
  { path: '/finance/create', redirect: '/finance' },
  { path: '/finance/receipts/:id', redirect: '/finance' },

  // Library routes
  { path: '/library', name: 'Library', component: LibraryView },

  // Announcements
  { path: '/announcements', name: 'Announcements', component: AnnouncementView },

  // Reports
  { path: '/reports', name: 'Reports', component: ReportView },

  // Settings
  { path: '/settings', name: 'Settings', component: SettingView },

  // Roles & Permissions
  { path: '/roles', name: 'Roles', component: RoleView },
  { path: '/permissions', name: 'Permissions', component: RoleView },

  // SPA alias routes - handle Laravel /spa/* prefix if any
  { path: '/spa/:pathMatch(.*)*', redirect: to => '/' + (to.params.pathMatch || 'dashboard') },

  // Catch-all — redirect to dashboard instead of attendance
  { path: '/:pathMatch(.*)*', redirect: '/dashboard' }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
