import { createRouter, createWebHistory } from 'vue-router';

// Synchronous imports to ensure instant 0ms page transitions and eliminate chunk 404 / CORS failures across deployments
import DashboardView from '../views/DashboardView.vue';
import AttendanceView from '../views/AttendanceView.vue';
import TeacherAttendanceView from '../views/TeacherAttendanceView.vue';
import StudentView from '../views/StudentView.vue';
import TeacherView from '../views/TeacherView.vue';
import ClassView from '../views/ClassView.vue';
import AcademicYearView from '../views/AcademicYearView.vue';
import CourseSubjectView from '../views/CourseSubjectView.vue';
import TimetableView from '../views/TimetableView.vue';
import ExaminationView from '../views/ExaminationView.vue';
import ParentView from '../views/ParentView.vue';
import FinanceView from '../views/FinanceView.vue';
import LibraryView from '../views/LibraryView.vue';
import FeedbackRequestView from '../views/FeedbackRequestView.vue';
import AnnouncementView from '../views/AnnouncementView.vue';
import ReportView from '../views/ReportView.vue';
import SettingView from '../views/SettingView.vue';
import RoleView from '../views/RoleView.vue';
import SystemLogsView from '../views/SystemLogsView.vue';
import ProfileView from '../views/ProfileView.vue';
import SecurityView from '../views/SecurityView.vue';
import LanguageView from '../views/LanguageView.vue';

const routes = [
  { path: '/', redirect: '/dashboard' },

  // Main pages
  { path: '/attendance', name: 'Attendance', component: AttendanceView },
  { path: '/teacher-attendance', name: 'TeacherAttendance', component: TeacherAttendanceView },
  { path: '/attendance/teachers', redirect: '/teacher-attendance' },
  { path: '/attendances/teachers', redirect: '/teacher-attendance' },
  { path: '/dashboard', name: 'Dashboard', component: DashboardView },
  { path: '/profile', name: 'Profile', component: ProfileView },
  { path: '/security', name: 'Security', component: SecurityView },
  { path: '/language', name: 'Language', component: LanguageView },
  { path: '/settings/personal', redirect: '/profile' },
  { path: '/settings/security', redirect: '/security' },
  { path: '/settings/language', redirect: '/language' },

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
  { path: '/results', redirect: '/examinations' },
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

  // Requests & Feedback (សំណើ និងមតិយោបល់)
  { path: '/requests', name: 'Requests', component: FeedbackRequestView },
  { path: '/feedback', redirect: '/requests' },

  // Announcements
  { path: '/announcements', name: 'Announcements', component: AnnouncementView },

  // Reports
  { path: '/reports', name: 'Reports', component: ReportView },

  // Settings
  { path: '/settings', name: 'Settings', component: SettingView },

  // Roles & Permissions
  { path: '/roles', name: 'Roles', component: RoleView },
  { path: '/permissions', name: 'Permissions', component: RoleView },

  // System Activity Logs (Super Admin Only)
  { path: '/system-logs', name: 'SystemLogs', component: SystemLogsView },

  // SPA alias routes - handle Laravel /spa/* prefix if any
  { path: '/spa/:pathMatch(.*)*', redirect: to => '/' + (to.params.pathMatch || 'dashboard') },

  // Catch-all — redirect to dashboard instead of attendance
  { path: '/:pathMatch(.*)*', redirect: '/dashboard' }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 };
  }
});

router.beforeEach((to, from, next) => {
  const user = window.__INITIAL_USER__ || {};
  const isSuperAdmin = Boolean(user.is_super_admin);
  const role = isSuperAdmin ? 'Super Admin' : (user.role || 'Admin');

  // 1. Super Admin only routes
  if (to.path.startsWith('/system-logs') && !isSuperAdmin) {
    return next('/dashboard');
  }

  // 2. Admin & Super Admin only routes
  const adminOnlyPaths = ['/roles', '/settings', '/teachers', '/academic-years', '/course-subjects', '/parents'];
  if (adminOnlyPaths.some(p => to.path === p || to.path.startsWith(p + '/'))) {
    if (role !== 'Admin' && role !== 'Super Admin') {
      return next('/dashboard');
    }
  }

  // 3. Student restricted routes (Students cannot access admin or teacher management areas)
  if (role === 'Student') {
    const studentBlockedPaths = [
      '/finance', '/students', '/teachers', '/classes', 
      '/teacher-attendance', '/reports', '/academic-years', 
      '/course-subjects', '/parents', '/roles', '/settings', '/system-logs'
    ];
    if (studentBlockedPaths.some(p => to.path === p || to.path.startsWith(p + '/'))) {
      return next('/dashboard');
    }
  }

  next();
});

router.afterEach(() => {
  document.title = 'sala-portal';
});

router.onError((error, to) => {
  console.error('Router navigation error:', error);
  if (
    error.message?.includes('Failed to fetch dynamically imported module') ||
    error.message?.includes('Importing a module script failed') ||
    error.message?.includes('error loading dynamically imported module')
  ) {
    const retryKey = 'chunk_retry_' + (to?.fullPath || window.location.pathname);
    if (!sessionStorage.getItem(retryKey)) {
      sessionStorage.setItem(retryKey, 'true');
      window.location.href = to?.fullPath || window.location.pathname;
    }
  }
});

export default router;
